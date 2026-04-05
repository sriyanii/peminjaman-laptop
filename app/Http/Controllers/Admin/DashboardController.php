<?php
/**
 * Dashboard Controller (Admin)
 * 
 * Controller ini bertanggung jawab untuk menampilkan data statistik dan ringkasan
 * dari aplikasi peminjaman laptop. Menyediakan berbagai metrik penting seperti
 * jumlah user, laptop, peminjaman, serta data grafik untuk visualisasi.
 * 
 * @package App\Http\Controllers\Admin
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Laptop;
use App\Models\TransaksiDenda;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama admin.
     * 
     * Method ini mengumpulkan berbagai data statistik untuk ditampilkan di dashboard:
     * - Statistik user (total user, petugas)
     * - Statistik laptop (total, tersedia, dipinjam, rusak)
     * - Statistik peminjaman (total, pending, aktif, selesai, terlambat, hari ini)
     * - Data grafik peminjaman 7 hari terakhir
     * - Data peminjaman terbaru (10 data terakhir)
     * 
     * @return View View dashboard dengan semua data statistik
     */
    public function index(): View
    {
        // ========== DATA STATISTIK USER ==========
        // Menghitung total seluruh user dalam sistem
        $total_users = User::count();
        
        // Menghitung jumlah user dengan role petugas
        $total_petugas = User::where('role', 'petugas')->count();
        
        // ========== DATA STATISTIK LAPTOP ==========
        // Total semua laptop dalam database
        $total_laptop = Laptop::count();
        
        // Jumlah laptop yang tersedia untuk dipinjam
        $laptop_tersedia = Laptop::where('status', 'tersedia')->count();
        
        // Jumlah laptop yang sedang dipinjam
        $laptop_dipinjam = Laptop::where('status', 'dipinjam')->count();
        
        // Jumlah laptop yang rusak atau dalam maintenance
        $laptop_rusak = Laptop::whereIn('status', ['rusak', 'maintenance'])->count();
        
        // ========== DATA STATISTIK PEMINJAMAN ==========
        // Total seluruh peminjaman (semua status)
        $total_peminjaman = Peminjaman::count();
        
        // Peminjaman yang masih menunggu persetujuan
        $peminjaman_pending = Peminjaman::where('status', 'pending')->count();
        
        // Peminjaman yang sedang aktif (disetujui dan belum dikembalikan)
        $peminjaman_aktif = Peminjaman::whereIn('status', ['approved', 'aktif'])->count();
        
        // Peminjaman yang sudah selesai (laptop sudah dikembalikan)
        $peminjaman_selesai = Peminjaman::where('status', 'selesai')->count();
        
        // Menghitung peminjaman yang terlambat dikembalikan
        // Kondisi: status aktif/approved, belum dikembalikan, dan melewati tanggal kembali rencana
        $peminjaman_terlambat = Peminjaman::whereIn('status', ['approved', 'aktif'])
            ->whereNull('tanggal_kembali')
            ->whereDate('tanggal_kembali_rencana', '<', now())
            ->count();
        
        // Peminjaman yang dibuat pada hari ini
        $peminjaman_hari_ini = Peminjaman::whereDate('created_at', now()->toDateString())->count();
        
        // Menghitung jumlah user yang sedang melakukan peminjaman aktif
        $total_peminjam = User::whereHas('peminjaman', function($q) {
            $q->whereIn('status', ['approved', 'aktif']);
        })->count();
        
        // ========== DATA GRAFIK BATANG 7 HARI TERAKHIR ==========
        // Menyiapkan data untuk grafik peminjaman 7 hari terakhir
        $barLabels = []; // Label hari (Sen, Sel, Rab, ...)
        $barData = [];   // Data jumlah peminjaman per hari
        
        // Loop untuk 7 hari terakhir (dari 6 hari lalu hingga hari ini)
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $barLabels[] = $date->format('D'); // Format nama hari (Sen, Sel, Rab, dll)
            
            // Hitung jumlah peminjaman pada tanggal tersebut
            $count = Peminjaman::whereDate('created_at', $date->toDateString())->count();
            $barData[] = $count;
        }
        
        // ========== DATA PEMINJAMAN TERBARU ==========
        // Ambil 10 peminjaman terakhir dengan relasi user dan laptop
        $recent_peminjaman = Peminjaman::with(['user', 'laptop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Kirim semua data ke view dashboard
        return view('admin.dashboard', compact(
            'total_users',
            'total_petugas',
            'total_laptop',
            'laptop_tersedia',
            'laptop_dipinjam',
            'laptop_rusak',
            'total_peminjaman',
            'peminjaman_pending',
            'peminjaman_aktif',
            'peminjaman_selesai',
            'peminjaman_terlambat',
            'peminjaman_hari_ini',
            'total_peminjam',
            'recent_peminjaman',
            'barLabels',
            'barData'
        ));
    }

    /**
     * Menampilkan halaman dashboard khusus admin.
     * 
     * Method ini merupakan alias dari method index() untuk konsistensi routing.
     * Digunakan ketika ada kebutuhan untuk membedakan dashboard admin dengan
     * dashboard role lainnya.
     * 
     * @return View View dashboard dengan semua data statistik
     * 
     * @see DashboardController::index()
     */
    public function adminDashboard(): View
    {
        // Memanggil method index untuk menampilkan dashboard admin
        return $this->index();
    }

    /**
     * Menampilkan halaman laporan ringkasan dengan data peminjaman detail.
     * 
     * Method ini menyiapkan data untuk halaman laporan yang menampilkan:
     * - Ringkasan statistik penting
     * - Daftar detail peminjaman dengan filter
     * - Data untuk ekspor
     * 
     * @param Request $request Request dengan parameter filter (start_date, end_date, status)
     * @return View View halaman laporan dengan data statistik dan peminjaman
     */
    public function laporan(Request $request): View
    {
        // ========== DATA STATISTIK ==========
        // Menghitung total laptop
        $total_laptops = Laptop::count();
        
        // Menghitung total user
        $total_users = User::count();
        
        // Menghitung total peminjaman
        $total_peminjaman = Peminjaman::count();
        
        // Menghitung total pendapatan dari denda (jumlah semua denda)
        $total_transaksi = TransaksiDenda::sum('total_denda') ?? 0;
        
        // ========== DATA PEMINJAMAN DENGAN FILTER ==========
        $query = Peminjaman::with(['user', 'laptop']);
        
        // Filter berdasarkan tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        // Filter berdasarkan tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Ambil data dengan pagination (15 item per halaman)
        $borrowings = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Kirim data ke view laporan
        return view('admin.laporan.index', compact(
            'total_laptops',
            'total_users',
            'total_peminjaman',
            'total_transaksi',
            'borrowings'
        ));
    }

    /**
     * (Opsional) Menampilkan data peminjaman per bulan untuk grafik.
     * 
     * Method ini dapat ditambahkan jika diperlukan untuk menampilkan
     * grafik peminjaman per bulan.
     * 
     * @param int|null $year Tahun yang akan ditampilkan (default: tahun sekarang)
     * @return array Array berisi label bulan dan data peminjaman
     */
    public function getMonthlyData(?int $year = null): array
    {
        // Jika tahun tidak ditentukan, gunakan tahun sekarang
        $year = $year ?? now()->year;
        
        $monthlyLabels = [];
        $monthlyData = [];
        
        // Loop untuk 12 bulan
        for ($month = 1; $month <= 12; $month++) {
            // Format label bulan (Jan, Feb, Mar, ...)
            $monthlyLabels[] = date('M', mktime(0, 0, 0, $month, 1));
            
            // Hitung peminjaman per bulan
            $count = Peminjaman::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
            
            $monthlyData[] = $count;
        }
        
        return [
            'labels' => $monthlyLabels,
            'data' => $monthlyData,
            'year' => $year
        ];
    }

    /**
     * (Opsional) Mengekspor laporan ke format PDF.
     * 
     * Method ini dapat ditambahkan jika diperlukan untuk mengekspor
     * laporan dashboard ke dalam format PDF.
     * 
     * @param Request $request Parameter filter laporan (tanggal mulai, tanggal selesai)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Response file PDF
     */
    public function exportPdf(Request $request)
    {
        // Validasi parameter filter
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        // Ambil data peminjaman berdasarkan filter
        $query = Peminjaman::with(['user', 'laptop']);
        
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $peminjaman = $query->orderBy('created_at', 'desc')->get();
        
        // Generate PDF (menggunakan library seperti Barryvdh\DomPDF)
        // $pdf = \PDF::loadView('admin.laporan.pdf', compact('peminjaman', 'request'));
        // return $pdf->download('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
        
        // Placeholder return
        return back()->with('info', 'Fitur export PDF akan segera tersedia.');
    }
}