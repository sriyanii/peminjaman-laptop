<?php
/**
 * Laporan Controller (Petugas)
 * 
 * Controller ini bertanggung jawab untuk menampilkan laporan peminjaman laptop
 * yang sudah selesai (status 'selesai'). Petugas dapat melihat dan memfilter
 * laporan berdasarkan rentang tanggal peminjaman.
 * 
 * @package App\Http\Controllers\Petugas
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan peminjaman yang sudah selesai.
     * 
     * Method ini menampilkan data peminjaman dengan status 'selesai' (laptop sudah dikembalikan)
     * yang dapat difilter berdasarkan:
     * - Tanggal mulai peminjaman (tanggal_pinjam >= tanggal_mulai)
     * - Tanggal selesai peminjaman (tanggal_pinjam <= tanggal_selesai)
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param Request $request Objek request yang berisi parameter filter (tanggal_mulai, tanggal_selesai)
     * @return View View laporan dengan data laporan, total_denda, dan total_peminjaman
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * 
     * @example
     * GET /petugas/laporan?tanggal_mulai=2024-01-01&tanggal_selesai=2024-12-31
     */
public function index(Request $request): View
{
    if (auth()->user()->role !== 'petugas') {
        abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
    }
    
    $query = Peminjaman::with(['user', 'laptop']);
    
    // Filter status (biarkan semua status, tidak hanya selesai)
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
    
    $laporan = $query->orderBy('tanggal_pinjam', 'desc')->paginate(20);
    
    $total_denda = $laporan->sum('denda');
    $total_peminjaman = $laporan->total();
    
    return view('petugas.laporan.index', compact('laporan', 'total_denda', 'total_peminjaman'));
}
    /**
     * Menampilkan detail laporan peminjaman berdasarkan ID.
     * 
     * Method ini dapat ditambahkan untuk melihat detail peminjaman
     * yang sudah selesai.
     * 
     * @param int|string $id ID peminjaman yang akan ditampilkan
     * @return View View detail laporan
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function show($id): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        // Ambil data peminjaman dengan relasi user, laptop, dan transaksi denda
        $peminjaman = Peminjaman::with(['user', 'laptop', 'transaksiDenda'])
            ->where('status', 'selesai')
            ->findOrFail($id);
        
        return view('petugas.laporan.show', compact('peminjaman'));
    }

    /**
     * Mengekspor laporan ke format Excel/CSV.
     * 
     * Method ini dapat ditambahkan untuk mengekspor data laporan
     * ke dalam format yang dapat diunduh.
     * 
     * @param Request $request Parameter filter untuk ekspor
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Response file export
     */
    public function export(Request $request)
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        // Ambil data berdasarkan filter
        $query = Peminjaman::with(['user', 'laptop'])
            ->where('status', 'selesai');
        
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_mulai);
        }
        
        if ($request->has('tanggal_selesai') && $request->tanggal_selesai != '') {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_selesai);
        }
        
        $laporan = $query->orderBy('tanggal_pinjam', 'desc')->get();
        
        // Logic untuk export ke Excel/CSV
        // Contoh menggunakan library Maatwebsite\Excel
        
        return back()->with('info', 'Fitur export laporan akan segera tersedia.');
    }

    /**
     * Mencetak laporan dalam format PDF.
     * 
     * Method ini dapat ditambahkan untuk mencetak laporan
     * dalam format PDF yang siap dicetak.
     * 
     * @param Request $request Parameter filter untuk laporan
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Response file PDF
     */
    public function print(Request $request)
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        // Ambil data berdasarkan filter
        $query = Peminjaman::with(['user', 'laptop'])
            ->where('status', 'selesai');
        
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_mulai);
        }
        
        if ($request->has('tanggal_selesai') && $request->tanggal_selesai != '') {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_selesai);
        }
        
        $laporan = $query->orderBy('tanggal_pinjam', 'desc')->get();
        
        $total_denda = $laporan->sum('denda');
        $total_peminjaman = $laporan->count();
        
        // Generate PDF (menggunakan library seperti Barryvdh\DomPDF)
        // $pdf = \PDF::loadView('petugas.laporan.pdf', compact('laporan', 'total_denda', 'total_peminjaman', 'request'));
        // return $pdf->stream('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
        
        // Placeholder return
        return back()->with('info', 'Fitur cetak laporan akan segera tersedia.');
    }

    /**
     * Menampilkan ringkasan laporan per bulan.
     * 
     * Method ini dapat ditambahkan untuk menampilkan laporan
     * dalam bentuk ringkasan per bulan.
     * 
     * @param int|null $year Tahun yang akan ditampilkan
     * @return View View ringkasan laporan per bulan
     */
    public function monthlySummary(?int $year = null): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        // Jika tahun tidak ditentukan, gunakan tahun sekarang
        $year = $year ?? now()->year;
        
        // Data laporan per bulan
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $peminjaman = Peminjaman::where('status', 'selesai')
                ->whereYear('tanggal_pinjam', $year)
                ->whereMonth('tanggal_pinjam', $month)
                ->get();
            
            $monthlyData[$month] = [
                'bulan' => date('F', mktime(0, 0, 0, $month, 1)),
                'jumlah' => $peminjaman->count(),
                'total_denda' => $peminjaman->sum('denda'),
                'rata_rata_denda' => $peminjaman->count() > 0 
                    ? $peminjaman->sum('denda') / $peminjaman->count() 
                    : 0,
            ];
        }
        
        $total_tahun = [
            'jumlah' => Peminjaman::where('status', 'selesai')
                ->whereYear('tanggal_pinjam', $year)
                ->count(),
            'total_denda' => Peminjaman::where('status', 'selesai')
                ->whereYear('tanggal_pinjam', $year)
                ->sum('denda'),
        ];
        
        return view('petugas.laporan.monthly', compact('monthlyData', 'total_tahun', 'year'));
    }

    /**
     * Menampilkan laporan peminjaman terlambat.
     * 
     * Method ini dapat ditambahkan untuk menampilkan laporan
     * peminjaman yang pernah terlambat dikembalikan.
     * 
     * @param Request $request Parameter filter
     * @return View View laporan peminjaman terlambat
     */
    public function lateReturns(Request $request): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        $query = Peminjaman::with(['user', 'laptop'])
            ->where('status', 'selesai')
            ->where('denda', '>', 0); // Peminjaman yang memiliki denda (terlambat)
        
        // Filter tanggal
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->whereDate('tanggal_kembali', '>=', $request->tanggal_mulai);
        }
        
        if ($request->has('tanggal_selesai') && $request->tanggal_selesai != '') {
            $query->whereDate('tanggal_kembali', '<=', $request->tanggal_selesai);
        }
        
        $laporan = $query->orderBy('tanggal_kembali', 'desc')->paginate(20);
        
        $total_denda = $laporan->sum('denda');
        $total_peminjaman = $laporan->total();
        
        return view('petugas.laporan.late-returns', compact('laporan', 'total_denda', 'total_peminjaman'));
    }
}