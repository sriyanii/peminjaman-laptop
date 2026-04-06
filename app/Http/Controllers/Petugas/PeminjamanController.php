<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Laptop;
use App\Models\User;
use App\Models\TransaksiDenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'laptop']);
        
        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_selesai);
        }
        
        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('laptop', function($q2) use ($search) {
                      $q2->where('merk', 'like', "%{$search}%")
                         ->orWhere('model', 'like', "%{$search}%");
                  });
            });
        }
        
        $peminjaman = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $statistics = [
            'total' => Peminjaman::count(),
            'pending' => Peminjaman::where('status', 'pending')->count(),
            'active' => Peminjaman::whereIn('status', ['approved', 'aktif'])->whereNull('tanggal_kembali')->count(),
            'returned' => Peminjaman::where('status', 'selesai')->count(),
        ];
        
        return view('petugas.peminjaman.index', compact('peminjaman', 'statistics'));
    }

    public function create()
    {
        $laptops = Laptop::where('status', 'tersedia')->get();
        $users = User::where('role', 'user')->get();
        return view('petugas.peminjaman.form', compact('laptops', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'laptop_id' => 'required|exists:laptops,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'tujuan' => 'required|in:meeting,presentasi,training,work_from_home,proyek,lainnya',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        
        try {
            $laptop = Laptop::findOrFail($request->laptop_id);
            if ($laptop->status !== 'tersedia') {
                return redirect()->back()->with('error', 'Laptop sedang tidak tersedia');
            }
            
            $tanggal_pinjam = Carbon::parse($request->tanggal_pinjam);
            $tanggal_kembali = Carbon::parse($request->tanggal_kembali_rencana);
            $lama_hari = max($tanggal_pinjam->diffInDays($tanggal_kembali), 1);
            $harga_sewa = ($laptop->harga_sewa_harian ?? 50000) * $lama_hari;
            
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'laptop_id' => $request->laptop_id,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
                'lama_hari' => $lama_hari,
                'harga_sewa' => $harga_sewa,
                'tujuan' => $request->tujuan,
                'keterangan' => $request->keterangan,
                'status' => 'pending'
            ]);
            
            DB::commit();
            
            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Peminjaman berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menambah peminjaman: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with(['user', 'laptop'])->findOrFail($id);
        return view('petugas.peminjaman.show', compact('peminjaman'));
    }

    public function edit($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if (!in_array($peminjaman->status, ['pending', 'ditolak'])) {
            return redirect()->route('petugas.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah diproses tidak dapat diedit');
        }
        
        $laptops = Laptop::where('status', 'tersedia')->get();
        $users = User::where('role', 'user')->get();
        return view('petugas.peminjaman.form', compact('peminjaman', 'laptops', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'laptop_id' => 'required|exists:laptops,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'tujuan' => 'required|in:meeting,presentasi,training,work_from_home,proyek,lainnya',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            
            if (!in_array($peminjaman->status, ['pending', 'ditolak'])) {
                throw new \Exception('Peminjaman yang sudah diproses tidak dapat diedit');
            }
            
            $laptop = Laptop::findOrFail($request->laptop_id);
            
            $tanggal_pinjam = Carbon::parse($request->tanggal_pinjam);
            $tanggal_kembali = Carbon::parse($request->tanggal_kembali_rencana);
            $lama_hari = max($tanggal_pinjam->diffInDays($tanggal_kembali), 1);
            $harga_sewa = ($laptop->harga_sewa_harian ?? 50000) * $lama_hari;
            
            $peminjaman->update([
                'user_id' => $request->user_id,
                'laptop_id' => $request->laptop_id,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
                'lama_hari' => $lama_hari,
                'harga_sewa' => $harga_sewa,
                'tujuan' => $request->tujuan,
                'keterangan' => $request->keterangan
            ]);
            
            DB::commit();
            
            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Peminjaman berhasil diupdate');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengupdate peminjaman: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            
            // Hanya bisa hapus jika status pending, ditolak, atau selesai
            if (in_array($peminjaman->status, ['pending', 'ditolak', 'selesai'])) {
                $peminjaman->delete();
                return redirect()->route('petugas.peminjaman.index')
                    ->with('success', 'Peminjaman berhasil dihapus');
            }
            
            return redirect()->route('petugas.peminjaman.index')
                ->with('error', 'Peminjaman dengan status "' . $peminjaman->status . '" tidak dapat dihapus');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
        }
    }

    public function getData($id)
    {
        try {
            $peminjaman = Peminjaman::with(['user', 'laptop'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $peminjaman->id,
                    'kode_peminjaman' => 'PINJ-' . date('Ymd', strtotime($peminjaman->created_at)) . '-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT),
                    'peminjam' => $peminjaman->user->name ?? '-',
                    'laptop' => $peminjaman->laptop ? ($peminjaman->laptop->merk . ' ' . $peminjaman->laptop->model) : '-',
                    'harga_sewa' => $peminjaman->harga_sewa ?? 0,
                    'tanggal_pinjam' => $peminjaman->tanggal_pinjam ? Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : null,
                    'tanggal_kembali_rencana' => $peminjaman->tanggal_kembali_rencana ? Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') : null,
                    'status' => $peminjaman->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,ditolak',
            'alasan_ditolak' => 'required_if:status,ditolak|nullable|string'
        ]);

        DB::beginTransaction();
        
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            
            $peminjaman->update([
                'status' => $request->status,
                'approved_by' => auth()->id(),
                'waktu_approve' => now()
            ]);
            
            if ($request->status == 'ditolak' && $request->filled('alasan_ditolak')) {
                $peminjaman->update(['alasan_ditolak' => $request->alasan_ditolak]);
                Laptop::where('id', $peminjaman->laptop_id)->update(['status' => 'tersedia']);
            } elseif ($request->status == 'approved') {
                Laptop::where('id', $peminjaman->laptop_id)->update(['status' => 'dipinjam']);
            }
            
            DB::commit();
            
            return response()->json(['success' => true]);
                
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

/**
 * Proses pengembalian oleh petugas - bisa kapan saja
 */
public function prosesPengembalian(Request $request)
{
    $request->validate([
        'peminjaman_id' => 'required|exists:peminjaman,id',
        'tanggal_kembali' => 'required|date',
        'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
        'keterangan_rusak' => 'nullable|string',
        'catatan' => 'nullable|string',
        // Pembayaran denda opsional (bisa dibayar nanti via menu Transaksi)
        'metode_pembayaran' => 'nullable|string',
        'jumlah_bayar' => 'nullable|numeric|min:0'
    ]);

    DB::beginTransaction();
    
    try {
        $peminjaman = Peminjaman::with(['user', 'laptop'])->findOrFail($request->peminjaman_id);
        
        // ✅ Bisa diproses jika status approved/aktif (tidak peduli tanggal)
        if (!in_array($peminjaman->status, ['approved', 'aktif'])) {
            throw new \Exception('Peminjaman tidak dalam status yang dapat dikembalikan. Status: ' . $peminjaman->status);
        }
        
        $tanggal_kembali = Carbon::parse($request->tanggal_kembali);
        $tanggal_rencana = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        
        // ===== HITUNG DENDA KETERLAMBATAN =====
        $denda_telat = 0;
        $telat_hari = 0;
        
        // ✅ Hitung denda hanya jika kembali LEBAT dari rencana
        if ($tanggal_kembali->gt($tanggal_rencana)) {
            $telat_hari = $tanggal_kembali->diffInDays($tanggal_rencana);
            $denda_telat = $telat_hari * 10000; // Rp 10.000/hari
        }
        
        // ===== HITUNG DENDA KERUSAKAN / HILANG =====
        $denda_kerusakan = 0;
        $keterangan_kerusakan = '';
        
        switch ($request->kondisi_alat) {
            case 'rusak_ringan':
                $denda_kerusakan = $peminjaman->harga_sewa * 0.5;
                $keterangan_kerusakan = 'Rusak Ringan (50% dari harga sewa)';
                break;
            case 'rusak_berat':
                $denda_kerusakan = $peminjaman->harga_sewa;
                $keterangan_kerusakan = 'Rusak Berat (100% dari harga sewa)';
                break;
            case 'hilang':
                // ✅ Denda hilang = 100% harga laptop (bisa disesuaikan)
                $denda_kerusakan = $peminjaman->laptop->harga_beli ?? ($peminjaman->harga_sewa * 10);
                $keterangan_kerusakan = 'HILANG - Ganti rugi 100% harga laptop';
                break;
            case 'baik':
            default:
                // Tidak ada denda kerusakan
                break;
        }
        
        $total_denda = $denda_telat + $denda_kerusakan;
        $total_tagihan = $peminjaman->harga_sewa + $total_denda;
        
        // ===== CATATAN PENGEMBALIAN =====
        $catatan = "Kondisi: {$request->kondisi_alat}";
        if ($request->keterangan_rusak) {
            $catatan .= " | Detail: {$request->keterangan_rusak}";
        }
        if ($request->catatan) {
            $catatan .= " | Catatan: {$request->catatan}";
        }
        if ($telat_hari > 0) {
            $catatan .= " | Terlambat: {$telat_hari} hari";
        }
        
        // ===== UPDATE PEMINJAMAN =====
        $peminjaman->update([
            'tanggal_kembali' => $request->tanggal_kembali,
            'catatan_pengembalian' => $catatan,
            'denda' => $total_denda,
            'total_tagihan' => $total_tagihan,
            // Mark denda dibayar hanya jika user bayar lunas saat ini
            'is_denda_dibayar' => $total_denda > 0 && $request->jumlah_bayar >= $total_denda ? 1 : 0,
            'status' => 'selesai',
            'waktu_pengembalian' => now()
        ]);
        
        // ===== BUAT TRANSAKSI DENDA JIKA ADA =====
        $transaksi = null;
        if ($total_denda > 0) {
            $transaksi = TransaksiDenda::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'jumlah_denda' => $total_denda,
                'jumlah_dibayar' => $request->jumlah_bayar > 0 ? min($request->jumlah_bayar, $total_denda) : 0,
                'metode_pembayaran' => $request->metode_pembayaran ?? null,
                'status' => $request->jumlah_bayar >= $total_denda ? 'lunas' : 'belum_bayar',
                'tanggal_bayar' => $request->jumlah_bayar > 0 ? now() : null,
                'petugas_id' => auth()->id(),
                'keterangan' => trim("Telat: {$telat_hari} hari, " . ($keterangan_kerusakan ?: 'Tidak ada kerusakan'))
            ]);
        }
        
        // ===== UPDATE STATUS LAPTOP =====
        $laptop = $peminjaman->laptop;
        if ($laptop) {
            if ($request->kondisi_alat == 'hilang') {
                // Laptop hilang → status non-aktif
                $laptop->update(['status' => 'hilang']);
            } elseif ($request->kondisi_alat == 'baik') {
                $laptop->update(['status' => 'tersedia']);
            } else {
                $laptop->update(['status' => 'perbaikan']);
            }
        }
        
$peminjaman->user->update(['status' => 'active']); 
        
        DB::commit();
        
        // Generate struk untuk response
        $strukHtml = $this->generateStruk($peminjaman, $request, $telat_hari, $denda_telat, $denda_kerusakan, $total_tagihan, $keterangan_kerusakan);
        
        $message = 'Pengembalian berhasil diproses';
        if ($total_denda > 0) {
            $sisa = $total_denda - ($request->jumlah_bayar ?? 0);
            if ($sisa > 0) {
                $message .= ". Tersisa denda Rp " . number_format($sisa, 0, ',', '.') . " - silakan lunasi di menu Transaksi.";
            } else {
                $message .= ". Denda telah lunas.";
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'struk_html' => $strukHtml,
            'transaksi_id' => $transaksi?->id
        ]);
            
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Gagal memproses pengembalian: ' . $e->getMessage()
        ], 500);
    }
}
    
    private function generateStruk($peminjaman, $request, $telat_hari, $denda_telat, $denda_kerusakan, $total_tagihan, $keterangan_kerusakan)
    {
        $statusBayar = $request->jumlah_bayar >= $total_tagihan ? 'LUNAS' : 'BELUM LUNAS';
        $kodePeminjaman = 'PINJ-' . date('Ymd', strtotime($peminjaman->created_at)) . '-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT);
        
        $html = '<div class="text-center mb-3">
            <h4>STRUK PENGEMBALIAN</h4>
            <small>' . date('d/m/Y H:i:s') . '</small>
        </div>
        <hr>
        <table class="table table-sm table-borderless">
            <tr><td width="40%">ID Peminjaman</td><td><strong>#' . $peminjaman->id . '</strong></td></tr>
            <tr><td>Kode Peminjaman</td><td><strong>' . $kodePeminjaman . '</strong></td></tr>
            <tr><td>Peminjam</td><td><strong>' . ($peminjaman->user->name ?? '-') . '</strong></td></tr>
            <tr><td>Laptop</td><td><strong>' . ($peminjaman->laptop->merk ?? '') . ' ' . ($peminjaman->laptop->model ?? '-') . '</strong></td></tr>
            <tr><td>Tanggal Pinjam</td><td>' . Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') . '</td></tr>
            <tr><td>Tanggal Kembali</td><td>' . Carbon::parse($request->tanggal_kembali)->format('d/m/Y') . '</td></tr>
        </table>
        <hr>
        <table class="table table-sm">
            <tr><td>Harga Sewa</td><td class="text-end">Rp ' . number_format($peminjaman->harga_sewa, 0, ',', '.') . '</td></tr>';
        
        if ($denda_telat > 0) {
            $html .= '<tr><td>Denda Keterlambatan (' . $telat_hari . ' hari)</td><td class="text-end">Rp ' . number_format($denda_telat, 0, ',', '.') . '</td></tr>';
        }
        
        if ($denda_kerusakan > 0) {
            $html .= '<tr><td>Denda Kerusakan</td><td class="text-end">Rp ' . number_format($denda_kerusakan, 0, ',', '.') . '</td></tr>';
            if ($keterangan_kerusakan) {
                $html .= '<tr><td colspan="2"><small class="text-muted">' . $keterangan_kerusakan . '</small></td></tr>';
            }
        }
        
        $html .= '<tr class="border-top"><td><strong>TOTAL</strong></td><td class="text-end"><strong>Rp ' . number_format($total_tagihan, 0, ',', '.') . '</strong></td></tr>
            <tr><td>Status Pembayaran</td><td class="text-end"><span class="badge bg-' . ($statusBayar == 'LUNAS' ? 'success' : 'warning') . '">' . $statusBayar . '</span></td></tr>
        </table>
        <hr>
        <div class="text-center text-muted small">
            <i class="fas fa-check-circle text-success"></i> Terima kasih telah mengembalikan laptop<br>
            Simpan struk ini sebagai bukti pengembalian
        </div>';
        
        return $html;
    }

public function approve($id)
{
    DB::beginTransaction();
    try {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status != 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman sudah diproses'
            ], 400);
        }
        
        $laptop = Laptop::findOrFail($peminjaman->laptop_id);
        
        if ($laptop->status != 'tersedia') {
            return response()->json([
                'success' => false,
                'message' => 'Laptop sedang tidak tersedia. Status saat ini: ' . $laptop->status
            ], 400);
        }
        
        // Update status peminjaman
        $peminjaman->update([
            'status' => 'approved',  // ← Status ini
            'approved_by' => auth()->id(),
            'waktu_approve' => now()
        ]);
        
        // Update status laptop
        $laptop->update(['status' => 'dipinjam']);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => '✅ Peminjaman berhasil disetujui!'
        ]);
        
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
    
public function reject(Request $request, $id)
{
    $request->validate([
        'alasan_ditolak' => 'required|string|min:5'
    ]);
    
    DB::beginTransaction();
    try {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status != 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman sudah diproses'
            ], 400);
        }
        
        // Update status peminjaman
        $peminjaman->update([
            'status' => 'ditolak',  // ← Status ini
            'alasan_ditolak' => $request->alasan_ditolak,
            'approved_by' => auth()->id(),
            'waktu_approve' => now()
        ]);
        
        // Kembalikan status laptop
        Laptop::where('id', $peminjaman->laptop_id)->update(['status' => 'tersedia']);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => '❌ Peminjaman ditolak.'
        ]);
        
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
    
public function confirmPickup($id)
{
    DB::beginTransaction();
    try {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status != 'approved') {
            return response()->json(['success' => false, 'message' => 'Peminjaman belum disetujui'], 400);
        }
        
        $peminjaman->update([
            'status' => 'aktif',
            'waktu_diambil' => now()
        ]);
        
        // ✅ PERBAIKAN: Gunakan 'active' bukan 'aktif'
        $peminjaman->user->update(['status' => 'active']);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => '📦 Pengambilan laptop dikonfirmasi.'
        ]);
        
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    /**
     * Show form pengembalian
     */
    public function showReturnForm($id)
    {
        $peminjaman = Peminjaman::with(['user', 'laptop'])->findOrFail($id);
        
        // Cek apakah bisa dikembalikan
        if (!in_array($peminjaman->status, ['approved', 'aktif'])) {
            return redirect()->route('petugas.peminjaman.index')
                ->with('error', 'Peminjaman tidak dapat dikembalikan karena status: ' . $peminjaman->status);
        }
        
        return view('petugas.peminjaman.pengembalian', compact('peminjaman'));
    }


public function prosesTransaksi(Request $request, $id)
{
    $request->validate([
        'tanggal_kembali' => 'required|date',
        'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
        'catatan' => 'nullable|string',
        'jumlah_bayar' => 'nullable|numeric|min:0',
        'metode_pembayaran' => 'nullable|in:tunai,transfer,qris'
    ]);

    DB::beginTransaction();
    
    try {
        $peminjaman = Peminjaman::with(['user', 'laptop'])->findOrFail($id);
        
        if (!in_array($peminjaman->status, ['approved', 'aktif'])) {
            throw new \Exception('Status peminjaman tidak valid: ' . $peminjaman->status);
        }
        
        // ============================================================
        // 1. AMBIL HARGA SEWA PER HARI DARI TABEL LAPTOPS
        // ============================================================
        $hargaSewaPerHari = $peminjaman->laptop->harga_sewa_harian ?? 50000;
        
        // ============================================================
        // 2. HITUNG LAMA PEMINJAMAN (jumlah hari pinjam)
        // ============================================================
        $tanggalPinjam = Carbon::parse($peminjaman->tanggal_pinjam);
        $tanggalRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        $lamaHari = max(1, $tanggalPinjam->diffInDays($tanggalRencana));
        
        // ============================================================
        // 3. HITUNG TOTAL BIAYA SEWA (harga_per_hari × lama_hari)
        // ============================================================
        $totalBiayaSewa = $hargaSewaPerHari * $lamaHari;
        
        // ============================================================
        // 4. HITUNG DENDA KETERLAMBATAN
        // ============================================================
        $tanggalKembali = Carbon::parse($request->tanggal_kembali);
        $hariTerlambat = 0;
        $dendaTelat = 0;
        
        if ($tanggalKembali->gt($tanggalRencana)) {
            $hariTerlambat = $tanggalKembali->diffInDays($tanggalRencana);
            $dendaTelat = $hariTerlambat * 10000; // Rp 10.000 per hari
        }
        
        // ============================================================
        // 5. HITUNG DENDA KERUSAKAN (berdasarkan kondisi)
        // ============================================================
        $dendaKerusakan = 0;
        $persenDenda = 0;
        
        switch ($request->kondisi) {
            case 'rusak_ringan':
                $dendaKerusakan = $totalBiayaSewa * 0.5;  // 50% dari total biaya sewa
                $persenDenda = 50;
                break;
            case 'rusak_berat':
                $dendaKerusakan = $totalBiayaSewa;       // 100% dari total biaya sewa
                $persenDenda = 100;
                break;
            case 'hilang':
                $dendaKerusakan = $totalBiayaSewa * 10;   // 10x dari total biaya sewa
                $persenDenda = 1000;
                break;
            case 'baik':
            default:
                $dendaKerusakan = 0;
                $persenDenda = 0;
                break;
        }
        
        // ============================================================
        // 6. TOTAL DENDA = denda telat + denda kerusakan
        // ============================================================
        $totalDenda = $dendaTelat + $dendaKerusakan;
        
        // ============================================================
        // 7. TOTAL TAGIHAN = total biaya sewa + total denda
        // ============================================================
        $totalTagihan = $totalBiayaSewa + $totalDenda;
        
        // ============================================================
        // 8. PEMBAYARAN
        // ============================================================
        $jumlahBayar = (float)($request->jumlah_bayar ?? 0);
        
        // Tentukan status pembayaran
        if ($totalDenda == 0 && $totalBiayaSewa == 0) {
            $statusPembayaran = 'tidak_ada_biaya';
            $statusTransaksi = 'selesai';
        } elseif ($jumlahBayar >= $totalTagihan) {
            $statusPembayaran = 'lunas';
            $statusTransaksi = 'selesai';
        } elseif ($jumlahBayar > 0) {
            $statusPembayaran = 'sebagian_lunas';
            $statusTransaksi = 'pending';
        } else {
            $statusPembayaran = 'belum_lunas';
            $statusTransaksi = 'pending';
        }
        
        // ============================================================
        // 9. UPDATE PEMINJAMAN
        // ============================================================
        $peminjaman->update([
            'tanggal_kembali' => $request->tanggal_kembali,
            'denda' => $totalDenda,
            'hari_terlambat' => $hariTerlambat,
            'kondisi_saat_kembali' => $request->kondisi,
            'catatan_pengembalian' => $request->catatan,
            'status' => 'selesai',
            'waktu_pengembalian' => now()
        ]);
        
        // ============================================================
        // 10. UPDATE STATUS LAPTOP
        // ============================================================
        $laptop = $peminjaman->laptop;
        if ($laptop) {
            if ($request->kondisi == 'hilang') {
                $laptop->update(['status' => 'hilang']);
            } elseif ($request->kondisi == 'baik') {
                $laptop->update(['status' => 'tersedia']);
            } else {
                $laptop->update(['status' => 'maintenance']);
            }
        }
        
        // ============================================================
        // 11. BUAT TRANSAKSI DENDA
        // ============================================================
        $transaksi = TransaksiDenda::create([
            'peminjaman_id' => $peminjaman->id,
            'user_id' => $peminjaman->user_id,
            'petugas_id' => auth()->id(),
            'total_denda' => $totalDenda,
            'denda_dibayar' => $jumlahBayar,
            'status_pembayaran' => $statusPembayaran,
            'status_transaksi' => $statusTransaksi,
            'kondisi_barang' => $request->kondisi,
            'catatan_cek' => $request->catatan ?? 'Pengembalian oleh petugas',
            'waktu_cek' => now(),
            'waktu_pembayaran' => $jumlahBayar > 0 ? now() : null,
            'waktu_selesai' => $statusTransaksi == 'selesai' ? now() : null
        ]);
        
        DB::commit();
        
        // ============================================================
        // 12. RESPONSE
        // ============================================================
        $message = '✅ Pengembalian berhasil!';
        $message .= "\n📊 Total Biaya Sewa: Rp " . number_format($totalBiayaSewa, 0, ',', '.');
        
        if ($hariTerlambat > 0) {
            $message .= "\n⏰ Denda Telat ({$hariTerlambat} hari): Rp " . number_format($dendaTelat, 0, ',', '.');
        }
        
        if ($dendaKerusakan > 0) {
            $message .= "\n🔧 Denda Kerusakan ({$persenDenda}%): Rp " . number_format($dendaKerusakan, 0, ',', '.');
        }
        
        if ($totalDenda > 0) {
            $sisa = $totalDenda - $jumlahBayar;
            if ($sisa > 0) {
                $message .= "\n💰 Sisa denda: Rp " . number_format($sisa, 0, ',', '.');
            } else {
                $message .= "\n✅ Denda telah lunas.";
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'transaksi_id' => $transaksi->id,
            'data' => [
                'harga_sewa_per_hari' => $hargaSewaPerHari,
                'lama_hari' => $lamaHari,
                'total_biaya_sewa' => $totalBiayaSewa,
                'denda_telat' => $dendaTelat,
                'denda_kerusakan' => $dendaKerusakan,
                'total_denda' => $totalDenda,
                'total_tagihan' => $totalTagihan,
                'dibayar' => $jumlahBayar,
                'sisa' => max(0, $totalTagihan - $jumlahBayar)
            ]
        ]);
        
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Proses transaksi gagal: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
    
    /**
     * Proses pembayaran denda dari menu Transaksi (opsional, setelah pengembalian)
     */
    public function bayarDenda(Request $request, $transaksiId)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris'
        ]);
        
        DB::beginTransaction();
        
        try {
            $transaksi = TransaksiDenda::with(['peminjaman', 'user'])
                ->where('status_pembayaran', '!=', 'lunas')
                ->findOrFail($transaksiId);
            
            $dendaLama = $transaksi->denda_dibayar;
            $dendaBaru = $dendaLama + $request->jumlah_bayar;
            $totalDenda = $transaksi->total_denda;
            
            if ($dendaBaru > $totalDenda) {
                throw new \Exception('Jumlah pembayaran melebihi total denda');
            }
            
            // Update status
            if ($dendaBaru >= $totalDenda) {
                $statusPembayaran = 'lunas';
                $statusTransaksi = 'selesai';
                $waktuSelesai = now();
            } else {
                $statusPembayaran = 'sebagian_lunas';
                $statusTransaksi = 'pending';
                $waktuSelesai = null;
            }
            
            $transaksi->update([
                'denda_dibayar' => $dendaBaru,
                'status_pembayaran' => $statusPembayaran,
                'status_transaksi' => $statusTransaksi,
                'metode_pembayaran' => $request->metode_pembayaran,
                'waktu_pembayaran' => now(),
                'waktu_selesai' => $waktuSelesai,
                'petugas_id' => auth()->id()
            ]);
            
            // Update peminjaman jika denda lunas
            if ($statusPembayaran == 'lunas') {
                $transaksi->peminjaman()->update([
                    'is_denda_dibayar' => true
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran denda berhasil! Sisa tagihan: Rp ' . 
                             number_format($totalDenda - $dendaBaru, 0, ',', '.'),
                'sisa_tagihan' => $totalDenda - $dendaBaru
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

/**
 * Lihat riwayat transaksi denda (untuk menu Transaksi)
 */
public function daftarTransaksi(Request $request)
{
    $query = TransaksiDenda::with(['peminjaman', 'user', 'petugas'])
        ->orderBy('created_at', 'desc');
    
    // Filter status
    if ($request->filled('status')) {
        $query->where('status_pembayaran', $request->status);
    }
    
    // Filter tanggal
    if ($request->filled('tanggal_mulai')) {
        $query->whereDate('created_at', '>=', $request->tanggal_mulai);
    }
    if ($request->filled('tanggal_selesai')) {
        $query->whereDate('created_at', '<=', $request->tanggal_selesai);
    }
    
    // Pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('peminjaman', function($q2) use ($search) {
                $q2->where('kode_peminjaman', 'like', "%{$search}%")
                   ->orWhere('id', 'like', "%{$search}%");
            });
        });
    }
    
    // ✅ Gunakan nama variabel $transaksis (sesuai view)
    $transaksis = $query->paginate(15);
    
    // Statistik
    $total_transaksi = TransaksiDenda::count();
    $total_nominal_denda = TransaksiDenda::sum('total_denda');
    $total_dibayar = TransaksiDenda::sum('denda_dibayar');
    $total_belum_bayar = TransaksiDenda::where('status_pembayaran', 'belum_lunas')->sum('total_denda');
    
    // ✅ Kirim semua variabel yang dibutuhkan view
    return view('petugas.transaksi.index', [
        'transaksis' => $transaksis,  // ← pastikan nama variabelnya 'transaksis'
        'total_transaksi' => $total_transaksi,
        'total_nominal_denda' => $total_nominal_denda,
        'total_dibayar' => $total_dibayar,
        'total_belum_bayar' => $total_belum_bayar
    ]);
}
    
    /**
     * Detail transaksi denda
     */
    public function detailTransaksi($id)
    {
        $transaksi = TransaksiDenda::with(['peminjaman', 'peminjaman.user', 'peminjaman.laptop', 'petugas'])
            ->findOrFail($id);
        
        return view('petugas.transaksi.show', compact('transaksi'));
    }
    
    /**
     * Cetak struk transaksi
     */
    public function cetakStruk($id)
    {
        $transaksi = TransaksiDenda::with(['peminjaman', 'peminjaman.user', 'peminjaman.laptop'])
            ->findOrFail($id);
        
        return view('petugas.transaksi.struk', compact('transaksi'));
    }


}