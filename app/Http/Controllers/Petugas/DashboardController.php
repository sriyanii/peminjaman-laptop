<?php
/**
 * Dashboard Controller (Petugas)
 * 
 * Controller ini bertanggung jawab untuk menampilkan dashboard petugas dengan
 * berbagai statistik dan grafik terkait peminjaman laptop. Menyediakan informasi
 * penting seperti jumlah peminjaman aktif, pending, selesai, terlambat, serta
 * visualisasi data dalam bentuk grafik batang dan lingkaran.
 * 
 * @package App\Http\Controllers\Petugas
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama petugas.
     * 
     * Method ini mengumpulkan berbagai data statistik untuk ditampilkan di dashboard petugas:
     * - Statistik peminjaman (total, aktif, pending, selesai, terlambat, hari ini)
     * - Statistik pengguna (jumlah peminjam aktif)
     * - Statistik laptop (jumlah laptop tersedia)
     * - Data peminjaman terbaru (10 data terakhir)
     * - Data grafik batang (peminjaman 7 hari terakhir)
     * - Data grafik lingkaran (distribusi status peminjaman)
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param Request $request Objek request (opsional, untuk filter tambahan)
     * @return View View dashboard petugas dengan semua data statistik
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * 
     * @example
     * GET /petugas/dashboard
     */
    public function index(Request $request): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses dashboard
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        // ========== DATA STATISTIK UTAMA PEMINJAMAN ==========
        
        /**
         * Total seluruh peminjaman dalam sistem
         * Menghitung semua record peminjaman tanpa memandang status
         */
        $total_peminjaman = Peminjaman::count();
        
        /**
         * Jumlah peminjaman yang sedang aktif
         * Kriteria: status 'approved' atau 'aktif' dan belum ada tanggal kembali
         * Ini menunjukkan laptop yang sedang dipinjam dan belum dikembalikan
         */
        $peminjaman_aktif = Peminjaman::whereIn('status', ['approved', 'aktif'])
            ->whereNull('tanggal_kembali')
            ->count();
        
        /**
         * Jumlah peminjaman yang masih menunggu persetujuan
         * Status 'pending' berarti peminjaman belum disetujui oleh admin/petugas
         */
        $peminjaman_pending = Peminjaman::where('status', 'pending')->count();
        
        /**
         * Jumlah peminjaman yang sudah selesai
         * Status 'selesai' berarti laptop sudah dikembalikan dan proses selesai
         */
        $peminjaman_selesai = Peminjaman::where('status', 'selesai')->count();
        
        // ========== DATA PEMINJAMAN HARI INI ==========
        
        /**
         * Jumlah peminjaman yang dibuat pada hari ini
         * Berguna untuk melihat aktivitas peminjaman harian
         */
        $peminjaman_hari_ini = Peminjaman::whereDate('created_at', now()->toDateString())->count();
        
        // ========== DATA KETERLAMBATAN ==========
        
        /**
         * Jumlah peminjaman yang terlambat dikembalikan
         * Kriteria: status aktif, belum dikembalikan, dan melewati tanggal kembali rencana
         * Ini penting untuk penanganan denda dan reminder
         */
        $peminjaman_terlambat = Peminjaman::whereIn('status', ['approved', 'aktif'])
            ->whereNull('tanggal_kembali')
            ->whereDate('tanggal_kembali_rencana', '<', now())
            ->count();
        
        // ========== DATA PENGGUNA AKTIF ==========
        
        /**
         * Jumlah user dengan role 'user' yang sedang melakukan peminjaman aktif
         * Menghitung user unik yang memiliki setidaknya satu peminjaman aktif
         */
        $total_peminjam = User::where('role', 'user')
            ->whereHas('peminjaman', function($q) {
                $q->whereIn('status', ['approved', 'aktif'])
                  ->whereNull('tanggal_kembali');
            })->count();
        
        // ========== DATA KETERSEDIAAN LAPTOP ==========
        
        /**
         * Jumlah laptop yang tersedia untuk dipinjam
         * Status 'tersedia' berarti laptop siap digunakan
         */
        $laptop_tersedia = Laptop::where('status', 'tersedia')->count();
        
        // ========== DATA PEMINJAMAN TERBARU ==========
        
        /**
         * Mengambil 10 peminjaman terakhir beserta relasi user dan laptop
         * Digunakan untuk menampilkan aktivitas peminjaman terkini di dashboard
         */
        $recent_peminjaman = Peminjaman::with(['user', 'laptop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // ========== DATA UNTUK GRAFIK BATANG (7 HARI TERAKHIR) ==========
        
        /**
         * Menyiapkan data untuk grafik batang peminjaman 7 hari terakhir
         * $barLabels: array berisi nama hari (Sen, Sel, Rab, Kam, Jum, Sab, Min)
         * $barData: array berisi jumlah peminjaman per hari
         * 
         * Loop dari 6 hari yang lalu hingga hari ini untuk mendapatkan data
         * yang berurutan secara kronologis
         */
        $barLabels = [];
        $barData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $barLabels[] = $date->format('D'); // Format nama hari singkat
            
            // Hitung jumlah peminjaman pada tanggal tersebut
            $count = Peminjaman::whereDate('created_at', $date->toDateString())->count();
            $barData[] = $count;
        }
        
        // ========== DATA UNTUK GRAFIK LINGKARAN (STATUS PEMINJAMAN) ==========
        
        /**
         * Menyiapkan data untuk grafik lingkaran (donut chart)
         * Menampilkan distribusi status peminjaman:
         * - Index 0: Peminjaman aktif (sedang dipinjam)
         * - Index 1: Peminjaman selesai (sudah dikembalikan)
         * - Index 2: Peminjaman pending (menunggu persetujuan)
         * - Index 3: Peminjaman terlambat (melewati batas waktu)
         * 
         * Menggunakan null coalescing operator (?? 0) untuk menghindari error
         * jika variabel belum terdefinisi
         */
        $donutData = [
            $peminjaman_aktif ?? 0,      // Dipinjam
            $peminjaman_selesai ?? 0,    // Selesai
            $peminjaman_pending ?? 0,    // Menunggu
            $peminjaman_terlambat ?? 0   // Terlambat
        ];
        
        // Kirim semua data ke view dashboard petugas
        return view('petugas.dashboard', compact(
            'total_peminjaman',
            'peminjaman_aktif',
            'peminjaman_pending',
            'peminjaman_selesai',
            'peminjaman_terlambat',
            'peminjaman_hari_ini',
            'total_peminjam',
            'laptop_tersedia',
            'recent_peminjaman',
            'barLabels',
            'barData',
            'donutData'
        ));
    }

    /**
     * Menampilkan data statistik peminjaman per bulan (helper method).
     * 
     * Method ini dapat ditambahkan untuk menampilkan grafik peminjaman
     * dalam skala bulanan untuk analisis tren.
     * 
     * @param int|null $year Tahun yang akan ditampilkan (default: tahun sekarang)
     * @return array Array berisi label bulan dan data peminjaman
     */
    public function getMonthlyStatistics(?int $year = null): array
    {
        // Jika tahun tidak ditentukan, gunakan tahun sekarang
        $year = $year ?? now()->year;
        
        $monthlyLabels = [];
        $monthlyData = [];
        
        // Loop untuk 12 bulan dalam setahun
        for ($month = 1; $month <= 12; $month++) {
            // Format label bulan (Jan, Feb, Mar, ...)
            $monthlyLabels[] = Carbon::createFromDate($year, $month, 1)->format('M');
            
            // Hitung total peminjaman per bulan
            $count = Peminjaman::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
            
            $monthlyData[] = $count;
        }
        
        return [
            'labels' => $monthlyLabels,
            'data' => $monthlyData,
            'year' => $year,
            'total_year' => array_sum($monthlyData)
        ];
    }

    /**
     * Menampilkan data statistik denda per bulan (helper method).
     * 
     * Method ini dapat ditambahkan untuk menampilkan grafik denda
     * yang terkumpul per bulan.
     * 
     * @param int|null $year Tahun yang akan ditampilkan (default: tahun sekarang)
     * @return array Array berisi label bulan dan data denda
     */
    public function getFineStatistics(?int $year = null): array
    {
        // Jika tahun tidak ditentukan, gunakan tahun sekarang
        $year = $year ?? now()->year;
        
        $monthlyLabels = [];
        $monthlyData = [];
        
        // Loop untuk 12 bulan dalam setahun
        for ($month = 1; $month <= 12; $month++) {
            // Format label bulan
            $monthlyLabels[] = Carbon::createFromDate($year, $month, 1)->format('M');
            
            // Hitung total denda per bulan dari peminjaman yang sudah selesai
            $totalDenda = Peminjaman::whereYear('tanggal_kembali', $year)
                ->whereMonth('tanggal_kembali', $month)
                ->where('status', 'selesai')
                ->sum('denda');
            
            $monthlyData[] = $totalDenda;
        }
        
        return [
            'labels' => $monthlyLabels,
            'data' => $monthlyData,
            'year' => $year,
            'total_fine_year' => array_sum($monthlyData)
        ];
    }

    /**
     * Menampilkan dashboard dengan filter periode (helper method).
     * 
     * Method ini dapat ditambahkan untuk memberikan fleksibilitas
     * filter tanggal pada dashboard.
     * 
     * @param Request $request Objek request dengan parameter start_date dan end_date
     * @return View View dashboard dengan data statistik berdasarkan filter
     */
    public function filteredDashboard(Request $request): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        // Validasi filter tanggal
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        // Inisialisasi query dengan filter
        $query = Peminjaman::query();
        
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Statistik berdasarkan filter
        $total_peminjaman = $query->count();
        $peminjaman_aktif = (clone $query)->whereIn('status', ['approved', 'aktif'])
            ->whereNull('tanggal_kembali')
            ->count();
        $peminjaman_pending = (clone $query)->where('status', 'pending')->count();
        $peminjaman_selesai = (clone $query)->where('status', 'selesai')->count();
        
        // Data untuk grafik
        $barLabels = [];
        $barData = [];
        
        // Jika ada filter tanggal, gunakan rentang yang difilter
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $days = $start->diffInDays($end);
            
            for ($i = 0; $i <= $days; $i++) {
                $date = $start->copy()->addDays($i);
                $barLabels[] = $date->format('d M');
                $count = Peminjaman::whereDate('created_at', $date->toDateString())->count();
                $barData[] = $count;
            }
        } else {
            // Default: 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $barLabels[] = $date->format('D');
                $count = Peminjaman::whereDate('created_at', $date->toDateString())->count();
                $barData[] = $count;
            }
        }
        
        $recent_peminjaman = Peminjaman::with(['user', 'laptop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('petugas.dashboard', compact(
            'total_peminjaman',
            'peminjaman_aktif',
            'peminjaman_pending',
            'peminjaman_selesai',
            'recent_peminjaman',
            'barLabels',
            'barData',
            'request'
        ));
    }

    /**
     * Mengekspor data dashboard ke format PDF (helper method).
     * 
     * Method ini dapat ditambahkan untuk mengekspor laporan dashboard
     * ke dalam format PDF yang dapat diunduh.
     * 
     * @param Request $request Parameter filter untuk laporan
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Response file PDF
     */
    public function exportDashboard(Request $request)
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        // Ambil data statistik
        $statistics = [
            'total_peminjaman' => Peminjaman::count(),
            'peminjaman_aktif' => Peminjaman::whereIn('status', ['approved', 'aktif'])->count(),
            'peminjaman_pending' => Peminjaman::where('status', 'pending')->count(),
            'peminjaman_selesai' => Peminjaman::where('status', 'selesai')->count(),
            'peminjaman_terlambat' => Peminjaman::whereIn('status', ['approved', 'aktif'])
                ->whereNull('tanggal_kembali')
                ->whereDate('tanggal_kembali_rencana', '<', now())
                ->count(),
            'laptop_tersedia' => Laptop::where('status', 'tersedia')->count(),
        ];
        
        // Generate PDF (menggunakan library seperti Barryvdh\DomPDF)
        // $pdf = \PDF::loadView('petugas.dashboard-export', compact('statistics'));
        // return $pdf->download('dashboard-laporan-' . now()->format('Y-m-d') . '.pdf');
        
        // Placeholder return
        return back()->with('info', 'Fitur export dashboard akan segera tersedia.');
    }
}