<?php
namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Laptop;  // Ganti Tool dengan Laptop
use App\Models\TransaksiDenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    /**
     * Halaman daftar pengembalian (2 tab)
     */
public function index(Request $request)
{
    if (auth()->user()->role !== 'petugas') {
        abort(403);
    }
    
    // Hitung statistik
    $total_pengembalian = Peminjaman::whereIn('status', ['aktif', 'approved'])
        ->whereNotNull('tanggal_kembali_rencana')
        ->whereNull('tanggal_kembali')
        ->count();
    
    $terlambat = Peminjaman::whereIn('status', ['approved', 'aktif'])
        ->whereNull('tanggal_kembali')
        ->whereDate('tanggal_kembali_rencana', '<', now())
        ->count();
    
    $hari_ini = Peminjaman::whereIn('status', ['approved', 'aktif'])
        ->whereNull('tanggal_kembali')
        ->whereDate('tanggal_kembali_rencana', now())
        ->count();
    
    // Tab 1: Peminjaman Aktif - yang belum dikembalikan
    $query_aktif = Peminjaman::with(['user', 'laptop'])
        ->whereIn('status', ['approved', 'aktif'])
        ->whereNull('tanggal_kembali')
        ->orderBy('tanggal_kembali_rencana', 'asc');
    
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query_aktif->where(function($q) use ($search) {
            $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
              ->orWhereHas('laptop', function($q) use ($search) {
                  $q->where('merk', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
              });
        });
    }
    
    $peminjaman_aktif = $query_aktif->paginate(10)->withPath(route('petugas.pengembalian.index'));
    
    // Tab 2: Pengembalian yang sudah diproses tapi belum lunas denda
    // Ambil peminjaman yang sudah punya tanggal_kembali tapi status masih 'aktif' (karena denda)
    $pengembalian_pending = Peminjaman::with(['user', 'laptop'])
        ->whereNotNull('tanggal_kembali')
        ->where('status', 'aktif')
        ->where('denda', '>', 0)
        ->where('is_denda_dibayar', false)
        ->orderBy('updated_at', 'desc')
        ->paginate(10)
        ->withPath(route('petugas.pengembalian.index'));
    
    $total_pending = Peminjaman::whereNotNull('tanggal_kembali')
        ->where('status', 'aktif')
        ->where('denda', '>', 0)
        ->where('is_denda_dibayar', false)
        ->count();
    
    return view('petugas.pengembalian.index', compact(
        'peminjaman_aktif',
        'pengembalian_pending',
        'total_pengembalian',
        'terlambat',
        'hari_ini',
        'total_pending'
    ));
}

    /**
     * Form proses pengembalian langsung
     */
    public function proses($id)
    {
        // Gunakan laptop() bukan tool()
        $peminjaman = Peminjaman::with([
                'user:id,name,email',
                'laptop:id,merk,model,serial_number'  // ← ubah dari 'tool' ke 'laptop'
            ])
            ->where('id', $id)
            ->whereIn('status', ['approved', 'aktif'])
            ->whereNull('tanggal_kembali')
            ->firstOrFail();

        return view('petugas.pengembalian.proses', compact('peminjaman'));
    }

public function prosesStore(Request $request, $id)
{
    // DEBUG: Tampilkan semua yang diterima
    //dd([
        //'method' => $request->method(),
        //'id_from_route' => $id,
        //'all_request' => $request->all(),
        //'headers' => $request->headers->all(),
        //'user' => auth()->user()->name
    //]);

    try {
        DB::beginTransaction();
        
        // Cari peminjaman
        $peminjaman = Peminjaman::with('laptop')
            ->where('id', $id)
            ->whereIn('status', ['approved', 'aktif']) // Status yang valid
            ->whereNull('tanggal_kembali')
            ->first();
        
        if (!$peminjaman) {
            return back()->with('error', 'Data peminjaman tidak ditemukan atau sudah diproses.');
        }
        
        // Validasi input
        $request->validate([
            'tanggal_kembali' => 'required|date|before_or_equal:today',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan' => 'nullable|string|max:500',
        ]);
        
        // Hitung denda
        $tglRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        $tglKembali = Carbon::parse($request->tanggal_kembali);
        
        $dendaTerlambat = 0;
        if ($tglKembali->gt($tglRencana)) {
            $hari = $tglRencana->diffInDays($tglKembali);
            $dendaTerlambat = $hari * 10000;
        }
        
        $dendaKerusakan = match($request->kondisi) {
            'rusak_ringan' => 50000,
            'rusak_berat' => 200000,
            'hilang' => 1000000,
            default => 0,
        };
        
        $totalDenda = $dendaTerlambat + $dendaKerusakan;
        
        // Tentukan status - gunakan status yang ADA di enum
        $statusBaru = 'selesai'; // Default selesai
        if ($totalDenda > 0) {
            // Karena tidak ada status 'aktif_denda', gunakan 'aktif' dengan catatan
            $statusBaru = 'aktif'; 
        }
        
        // Update peminjaman
        $peminjaman->update([
            'tanggal_kembali' => $request->tanggal_kembali,
            'catatan_pengembalian' => 'Kondisi: ' . $request->kondisi . 
                ' | Denda: Rp ' . number_format($totalDenda, 0, ',', '.') .
                ($request->catatan ? ' | Catatan: ' . $request->catatan : ''),
            'denda' => $totalDenda,
            'is_denda_dibayar' => $totalDenda == 0 ? 1 : 0,
            'status' => $statusBaru,
            'approved_by' => auth()->id(),
            'waktu_pengembalian' => now(),
        ]);
        
        // Update status laptop
        if ($peminjaman->laptop) {
            $statusLaptop = match($request->kondisi) {
                'hilang' => 'hilang',
                'rusak_ringan', 'rusak_berat' => 'maintenance',
                default => 'tersedia'
            };
            
            $peminjaman->laptop->update([
                'status' => $statusLaptop,
                'kondisi' => $request->kondisi
            ]);
        }
        
        // Buat transaksi denda jika ada
        if ($totalDenda > 0) {
            TransaksiDenda::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'petugas_id' => auth()->id(),
                'total_denda' => $totalDenda,
                'denda_dibayar' => 0,
                'status_pembayaran' => 'belum_lunas',
                'status_transaksi' => 'proses_cek',
                'kondisi_barang' => $request->kondisi,
                'catatan_cek' => $request->catatan ?? 'Proses pengembalian oleh petugas',
                'waktu_cek' => now(),
            ]);
        }
        
        DB::commit();
        
        $message = '✅ Pengembalian berhasil.';
        if ($totalDenda > 0) {
            $message .= ' Total denda: Rp ' . number_format($totalDenda, 0, ',', '.') . 
                       '. Status peminjaman: Aktif (menunggu pembayaran denda)';
        }
        
        return redirect()->route('petugas.pengembalian.index')->with('success', $message);
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error prosesStore: ' . $e->getMessage());
        return back()->with('error', '❌ Gagal: ' . $e->getMessage())->withInput();
    }
}

    /**
     * Form konfirmasi pengembalian pending
     */
    public function konfirmasi($id)
{
    if (auth()->user()->role !== 'petugas') {
        abort(403);
    }
    
    $peminjaman = Peminjaman::with(['user', 'laptop'])
        ->where('id', $id)
        ->where('status', 'aktif') // Gunakan status 'aktif' untuk yang kena denda
        ->where('denda', '>', 0)
        ->where('is_denda_dibayar', false)
        ->firstOrFail();
    
    return view('petugas.pengembalian.konfirmasi', compact('peminjaman'));
}

    /**
     * Simpan konfirmasi pengembalian pending
     */
 public function konfirmasiStore(Request $request, $id)
{
    if (auth()->user()->role !== 'petugas') {
        abort(403);
    }
    
    try {
        DB::beginTransaction();
        
        $peminjaman = Peminjaman::with('laptop')
            ->where('id', $id)
            ->where('status', 'aktif')
            ->where('denda', '>', 0)
            ->where('is_denda_dibayar', false)
            ->firstOrFail();
        
        $request->validate([
            'status_konfirmasi' => 'required|in:setuju,tolak',
            'denda_final' => 'required_if:status_konfirmasi,setuju|nullable|numeric|min:0',
            'catatan_petugas' => 'nullable|string|max:500',
        ]);
        
        if ($request->status_konfirmasi == 'setuju') {
            // Setujui pembayaran denda
            $denda_final = $request->denda_final ?? $peminjaman->denda;
            
            $peminjaman->update([
                'denda' => $denda_final,
                'is_denda_dibayar' => true,
                'status' => 'selesai',
                'approved_by' => auth()->id(),
                'catatan_pengembalian' => $peminjaman->catatan_pengembalian . 
                    "\n[Pembayaran denda dikonfirmasi: Rp " . number_format($denda_final, 0, ',', '.') . "]",
            ]);
            
            // Update transaksi denda
            $transaksi = TransaksiDenda::where('peminjaman_id', $peminjaman->id)->first();
            if ($transaksi) {
                $transaksi->update([
                    'total_denda' => $denda_final,
                    'denda_dibayar' => $denda_final,
                    'status_pembayaran' => 'lunas',
                    'status_transaksi' => 'selesai',
                ]);
            }
            
            $message = '✅ Pembayaran denda dikonfirmasi. Peminjaman selesai.';
            
        } else {
            // Tolak pembayaran
            $peminjaman->update([
                'catatan_pengembalian' => $peminjaman->catatan_pengembalian . 
                    "\n[Pembayaran denda DITOLAK: " . ($request->catatan_petugas ?: 'Dokumen tidak valid') . "]",
            ]);
            
            $message = '⚠️ Pembayaran denda ditolak.';
        }
        
        DB::commit();
        
        return redirect()->route('petugas.pengembalian.index')->with('success', $message);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', '❌ Gagal: ' . $e->getMessage())->withInput();
    }
}

    // Helper functions tetap sama
    private function hitungDendaTerlambat($peminjaman, $tanggalKembali)
    {
        if (!$peminjaman->tanggal_kembali_rencana) return 0;
        
        $tglRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        $tglKembali = Carbon::parse($tanggalKembali);
        
        if ($tglKembali > $tglRencana) {
            $hari = $tglRencana->diffInDays($tglKembali);
            return $hari * 10000;
        }
        return 0;
    }

    private function hitungDendaKerusakan($kondisi)
    {
        return match($kondisi) {
            'rusak_ringan' => 50000,
            'rusak_berat' => 200000,
            'hilang' => 1000000,
            default => 0,
        };
    }
}