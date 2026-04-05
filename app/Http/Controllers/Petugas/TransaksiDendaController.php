<?php
namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\TransaksiDenda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiDendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403);
        }

        $query = TransaksiDenda::with(['user', 'peminjaman.laptop'])  // Tambahkan laptop
            ->orderBy('created_at', 'desc');

        // Filter search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('peminjaman', function ($q2) use ($search) {
                    $q2->where('kode_peminjaman', 'like', "%{$search}%");
                });
            });
        }

        // Filter status pembayaran
        if ($request->status) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter status transaksi
        if ($request->status_transaksi) {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        $transaksis = $query->paginate(15);

        // Statistik dashboard
        $total_transaksi = TransaksiDenda::count();
        $total_denda = TransaksiDenda::sum('total_denda');
        $total_pending = TransaksiDenda::where('status_pembayaran', 'belum_lunas')->count();
        $total_proses = TransaksiDenda::where('status_transaksi', 'proses_cek')->count();
        $total_lunas = TransaksiDenda::where('status_pembayaran', 'lunas')
            ->where('status_transaksi', 'selesai')
            ->count();
        $total_pendapatan = TransaksiDenda::where('status_pembayaran', 'lunas')
            ->where('status_transaksi', 'selesai')
            ->sum('denda_dibayar');
        $total_denda_belum_lunas = TransaksiDenda::where('status_pembayaran', 'belum_lunas')
            ->sum('total_denda');
        $transaksi_pending = TransaksiDenda::where('status_transaksi', 'pending')->count();

        return view('petugas.transaksi.index', compact(
            'transaksis',
            'total_transaksi',
            'total_denda',
            'total_pending',
            'total_proses',
            'total_lunas',
            'total_pendapatan',
            'total_denda_belum_lunas',
            'transaksi_pending'
        ));
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403);
        }

        // GANTI 'tool' DENGAN 'laptop'
        $transaksi = TransaksiDenda::with(['user', 'peminjaman.laptop', 'petugas'])  // ← ubah tool ke laptop
            ->findOrFail($id);

        return view('petugas.transaksi.show', compact('transaksi'));
    }

    /**
     * Show form pembayaran
     */
    public function pembayaran($id)
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403);
        }

        $transaksi = TransaksiDenda::with(['user', 'peminjaman.laptop'])  // ← ubah tool ke laptop
            ->where('status_pembayaran', 'belum_lunas')
            ->findOrFail($id);

        return view('petugas.transaksi.pembayaran', compact('transaksi'));
    }

    /**
     * Process payment
     */
    public function prosesPembayaran(Request $request, $id)
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $transaksi = TransaksiDenda::where('status_pembayaran', 'belum_lunas')
                ->findOrFail($id);

            $request->validate([
                'jumlah_dibayar' => 'required|numeric|min:' . ($transaksi->total_denda - $transaksi->denda_dibayar),
                'metode_pembayaran' => 'required|in:tunai,transfer,qris',
                'catatan' => 'nullable|string|max:500',
            ]);

            $sisa = $transaksi->total_denda - $transaksi->denda_dibayar;
            $jumlah_dibayar = $request->jumlah_dibayar;
            $kembalian = $jumlah_dibayar - $sisa;

            // Update transaksi
            $transaksi->update([
                'denda_dibayar' => $transaksi->denda_dibayar + $sisa,
                'status_pembayaran' => 'lunas',
                'status_transaksi' => 'selesai',
                'metode_pembayaran' => $request->metode_pembayaran,
                'catatan_pembayaran' => $request->catatan,
                'waktu_bayar' => now(),
                'petugas_id' => auth()->id(),
            ]);

            // Update peminjaman
            $peminjaman = Peminjaman::find($transaksi->peminjaman_id);
            if ($peminjaman) {
                $peminjaman->update([
                    'is_denda_dibayar' => 1,
                    'status' => 'selesai'
                ]);
            }

            DB::commit();

            return redirect()->route('petugas.transaksi.index')
                ->with('success', '✅ Pembayaran berhasil diproses. Kembalian: Rp ' . number_format($kembalian, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function export(Request $request)
    {
        $format = $request->format;

        // GANTI 'alat' DENGAN 'laptop'
        $transaksis = TransaksiDenda::with(['peminjaman.user', 'peminjaman.laptop'])  // ← ubah alat ke laptop
            ->latest()
            ->get();

        if($format == 'excel'){
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\TransaksiDendaExport($transaksis),
                'transaksi_denda.xlsx'
            );
        }

        if($format == 'pdf'){
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'petugas.transaksi.export_pdf',
                compact('transaksis')
            );

            return $pdf->download('transaksi_denda.pdf');
        }
    }

    /**
     * Cetak struk pembayaran
     */
    public function cetakStruk($id)
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403);
        }

        // GANTI 'tool' DENGAN 'laptop'
        $transaksi = TransaksiDenda::with(['user', 'peminjaman.laptop', 'petugas'])  // ← ubah tool ke laptop
            ->where('status_pembayaran', 'lunas')
            ->where('status_transaksi', 'selesai')
            ->findOrFail($id);

        // Jika request AJAX, return HTML untuk modal
        if (request()->ajax()) {
            return view('petugas.transaksi.struk', compact('transaksi'));
        }

        // Jika request biasa, return view untuk cetak
        return view('petugas.transaksi.cetak-struk', compact('transaksi'));
    }

    /**
     * Create transaksi from peminjaman
     */
    public function createTransaksi($peminjaman_id)
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403);
        }

        // GANTI 'tool' DENGAN 'laptop'
        $peminjaman = Peminjaman::with(['user', 'laptop'])  // ← ubah tool ke laptop
            ->where('denda', '>', 0)
            ->findOrFail($peminjaman_id);

        // Cek apakah sudah ada transaksi
        $existing = TransaksiDenda::where('peminjaman_id', $peminjaman_id)->first();
        if ($existing) {
            return redirect()->route('petugas.transaksi.show', $existing->id)
                ->with('info', 'Transaksi sudah ada');
        }

        return view('petugas.transaksi.create', compact('peminjaman'));
    }

    /**
     * Store transaksi from peminjaman
     */
    public function storeTransaksi(Request $request, $peminjaman_id)
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::with('user')
                ->where('denda', '>', 0)
                ->findOrFail($peminjaman_id);

            // Cek apakah sudah ada transaksi
            $existing = TransaksiDenda::where('peminjaman_id', $peminjaman_id)->first();
            if ($existing) {
                return redirect()->route('petugas.transaksi.show', $existing->id);
            }

            $request->validate([
                'total_denda' => 'required|numeric|min:0',
                'kondisi_barang' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
                'catatan_cek' => 'nullable|string|max:500',
            ]);

            // Buat transaksi baru
            $transaksi = TransaksiDenda::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'petugas_id' => auth()->id(),
                'total_denda' => $request->total_denda,
                'denda_dibayar' => 0,
                'status_pembayaran' => 'belum_lunas',
                'status_transaksi' => 'proses_cek',
                'kondisi_barang' => $request->kondisi_barang,
                'catatan_cek' => $request->catatan_cek,
                'waktu_cek' => now(),
            ]);

            DB::commit();

            return redirect()->route('petugas.transaksi.show', $transaksi->id)
                ->with('success', '✅ Transaksi denda berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal: ' . $e->getMessage())->withInput();
        }
    }
}