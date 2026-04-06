<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\TransaksiDenda;
use App\Models\Laptop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan semua kegiatan
     */
    public function index(Request $request)
    {
        // Filter tanggal
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // ==================== STATISTIK UMUM ====================
        $statistik = [
            'total_user' => User::where('role', 'user')->count(),
            'total_laptop' => Laptop::count(),
            'total_peminjaman' => Peminjaman::count(),
            'total_denda' => TransaksiDenda::sum('total_denda'),
            'denda_lunas' => TransaksiDenda::where('status_pembayaran', 'lunas')->sum('total_denda'),
            'denda_belum_lunas' => TransaksiDenda::where('status_pembayaran', 'belum_lunas')->sum('total_denda'),
            'pendapatan' => TransaksiDenda::where('status_pembayaran', 'lunas')->sum('denda_dibayar'),
        ];
        
        // ==================== PEMINJAMAN PER STATUS ====================
        $peminjaman_per_status = Peminjaman::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();
        
        // ==================== PEMINJAMAN PER BULAN ====================
        $peminjaman_per_bulan = Peminjaman::select(
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
        
        // ==================== LAPORAN PEMINJAMAN DETAIL ====================
        $laporan_peminjaman = Peminjaman::with(['user', 'laptop'])
            ->when($request->filled('start_date'), function($q) use ($startDate) {
                $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($request->filled('end_date'), function($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($request->filled('status'), function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('user_id'), function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->all());
        
        // ==================== LAPORAN TRANSAKSI DENDA ====================
        $laporan_denda = TransaksiDenda::with(['user', 'petugas', 'peminjaman.laptop'])
            ->when($request->filled('start_date'), function($q) use ($startDate) {
                $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($request->filled('end_date'), function($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($request->filled('status_pembayaran'), function($q) use ($request) {
                $q->where('status_pembayaran', $request->status_pembayaran);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->all());
        
        // ==================== LAPORAN LAPTOP ====================
        $laporan_laptop = Laptop::withCount(['peminjaman as total_peminjaman' => function($q) {
                $q->whereNull('deleted_at');
            }])
            ->orderBy('merk')
            ->get();
        
        // ==================== LAPORAN USER AKTIF ====================
        $user_aktif = User::where('role', 'user')
            ->withCount(['peminjaman as total_peminjaman' => function($q) {
                $q->whereNull('deleted_at');
            }])
            ->withSum(['peminjaman as total_denda' => function($q) {
                $q->whereNull('deleted_at');
            }], 'denda')
            ->orderBy('total_peminjaman', 'desc')
            ->limit(10)
            ->get();
        
        // ==================== GRAFIK DATA ====================
        // Data untuk chart peminjaman harian (30 hari terakhir)
        $chart_peminjaman = Peminjaman::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();
        
        // Data untuk chart denda per bulan
        $chart_denda = TransaksiDenda::select(
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(total_denda) as total_denda'),
                DB::raw('SUM(denda_dibayar) as total_dibayar')
            )
            ->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()])
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();
        
        // Data untuk chart status laptop
        $chart_laptop_status = Laptop::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();
        
        // Daftar user untuk filter
        $users = User::where('role', 'user')->orderBy('name')->get();
        
        return view('admin.laporan.index', compact(
            'statistik',
            'peminjaman_per_status',
            'peminjaman_per_bulan',
            'laporan_peminjaman',
            'laporan_denda',
            'laporan_laptop',
            'user_aktif',
            'chart_peminjaman',
            'chart_denda',
            'chart_laptop_status',
            'users',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Export laporan ke Excel
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Ambil data untuk export
        $data = Peminjaman::with(['user', 'laptop'])
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Export ke CSV
        $filename = "laporan_peminjaman_{$startDate}_to_{$endDate}.csv";
        $handle = fopen('php://temp', 'w+');
        
        // Header CSV
        fputcsv($handle, ['ID', 'Kode', 'Peminjam', 'Laptop', 'Tgl Pinjam', 'Tgl Rencana', 'Tgl Kembali', 'Status', 'Denda']);
        
        // Data CSV
        foreach ($data as $item) {
            fputcsv($handle, [
                $item->id,
                $item->kode_peminjaman ?? 'PINJ-' . $item->id,
                $item->user->name ?? '-',
                $item->laptop->merk . ' ' . $item->laptop->model ?? '-',
                $item->tanggal_pinjam,
                $item->tanggal_kembali_rencana,
                $item->tanggal_kembali ?? '-',
                $item->status,
                $item->denda ?? 0
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    
    /**
     * Export laporan denda ke Excel
     */
    public function exportDenda(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        $data = TransaksiDenda::with(['user', 'peminjaman.laptop'])
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = "laporan_denda_{$startDate}_to_{$endDate}.csv";
        $handle = fopen('php://temp', 'w+');
        
        fputcsv($handle, ['ID', 'Peminjam', 'Laptop', 'Total Denda', 'Dibayar', 'Sisa', 'Status', 'Tanggal']);
        
        foreach ($data as $item) {
            fputcsv($handle, [
                $item->id,
                $item->user->name ?? '-',
                $item->peminjaman->laptop->merk . ' ' . $item->peminjaman->laptop->model ?? '-',
                $item->total_denda,
                $item->denda_dibayar,
                $item->total_denda - $item->denda_dibayar,
                $item->status_pembayaran,
                $item->created_at->format('d/m/Y')
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}