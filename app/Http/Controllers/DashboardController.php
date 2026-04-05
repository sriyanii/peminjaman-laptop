<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Laptop;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard berdasarkan role
     */
    public function redirectToDashboard()
    {
        $user = auth()->user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'petugas':
                return redirect()->route('petugas.dashboard');
            case 'staff':
                return redirect()->route('staff.dashboard');
            default:
                return redirect()->route('user.dashboard');
        }
    }

    /**
     * Dashboard Admin
     */
    public function adminDashboard()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            // DATA DINAMIS DARI DATABASE
            $totalUsers = User::count();
            $totalLaptops = Laptop::count();
            $availableLaptops = Laptop::where('status', 'tersedia')->count();
            
            // Peminjaman
            $activeBorrowings = Peminjaman::where('status', 'dipinjam')->count();
            $completedBorrowings = Peminjaman::where('status', 'selesai')->count();
            $overdueBorrowings = Peminjaman::where('status', 'dipinjam')
                ->where('tanggal_kembali_rencana', '<', now())
                ->count();
            $todayBorrowings = Peminjaman::whereDate('tanggal_pinjam', today())->count();
            $totalBorrowings = Peminjaman::count();
            $pendingRequests = Peminjaman::where('status', 'pending')->count();
            
            // Data statistics
            $statistics = [
                'total_users' => $totalUsers,
                'total_laptops' => $totalLaptops,
                'available_laptops' => $availableLaptops,
                'active_borrowings' => $activeBorrowings,
                'completed_borrowings' => $completedBorrowings,
                'overdue_borrowings' => $overdueBorrowings,
                'today_borrowings' => $todayBorrowings,
                'active_users' => User::where('status', 'aktif')->count(),
                'total_borrowings' => $totalBorrowings,
                'pending_requests' => $pendingRequests,
                'admin_users' => User::where('role', 'admin')->count(),
                'petugas_users' => User::where('role', 'petugas')->count(),
                'user_users' => User::where('role', 'user')->count(),
                'new_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
            ];
            
            // Recent borrowings dengan join - PASTIKAN INI DIPANGGIL
            $recentBorrowings = Peminjaman::with(['user', 'laptop'])
                ->latest()
                ->limit(5)
                ->get();
            
            // Top laptops yang sering dipinjam (gunakan query manual untuk sementara)
            $topLaptops = Laptop::selectRaw('laptops.*, COUNT(peminjaman.id) as borrowings_count')
                ->leftJoin('peminjaman', 'laptops.id', '=', 'peminjaman.laptop_id')
                ->where('peminjaman.status', 'selesai')
                ->groupBy('laptops.id')
                ->orderBy('borrowings_count', 'desc')
                ->limit(5)
                ->get();
            
            // Recent activities
            $recentActivities = Peminjaman::with('user')
                ->select('id', 'user_id', 'status', 'updated_at')
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($peminjaman) {
                    $activityTypes = [
                        'dipinjam' => ['color' => 'bg-primary', 'icon' => 'fas fa-hand-holding', 'desc' => 'meminjam laptop'],
                        'selesai' => ['color' => 'bg-success', 'icon' => 'fas fa-check-circle', 'desc' => 'mengembalikan laptop'],
                        'pending' => ['color' => 'bg-warning', 'icon' => 'fas fa-clock', 'desc' => 'mengajukan peminjaman'],
                    ];
                    
                    $type = $activityTypes[$peminjaman->status] ?? ['color' => 'bg-secondary', 'icon' => 'fas fa-info-circle', 'desc' => 'memperbarui status'];
                    
                    return [
                        'user' => $peminjaman->user->name ?? 'User',
                        'description' => $type['desc'],
                        'color' => $type['color'],
                        'icon' => $type['icon'],
                        'time' => $peminjaman->updated_at
                    ];
                });
            
            // DEBUG: Cek apakah data ada
            // dd($recentBorrowings);
            
            return view('admin.dashboard', compact(
                'statistics',
                'recentBorrowings',  // PASTIKAN INI ADA
                'topLaptops',
                'recentActivities'
            ));
            
        } catch (\Exception $e) {
            // Fallback jika ada error
            $statistics = [
                'total_users' => User::count(),
                'total_laptops' => Laptop::count(),
                'available_laptops' => Laptop::where('status', 'tersedia')->count(),
                'active_borrowings' => 0,
                'completed_borrowings' => 0,
                'overdue_borrowings' => 0,
                'today_borrowings' => 0,
                'active_users' => User::count(),
                'total_borrowings' => 0,
                'pending_requests' => 0,
                'admin_users' => User::where('role', 'admin')->count(),
                'petugas_users' => User::where('role', 'petugas')->count(),
                'user_users' => User::where('role', 'user')->count(),
                'new_users' => 0,
            ];
            
            // Inisialisasi variabel kosong untuk fallback
            $recentBorrowings = collect(); // collection kosong
            $topLaptops = collect();
            $recentActivities = collect();
            
            return view('admin.dashboard', compact(
                'statistics',
                'recentBorrowings',
                'topLaptops',
                'recentActivities'
            ))->with('error', 'Data tidak dapat dimuat: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard Petugas
     */
    public function petugasDashboard()
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Unauthorized action.');
        }
        
        // Data khusus petugas
        $pendingRequests = Peminjaman::where('status', 'pending')->count();
        $todayBorrowings = Peminjaman::whereDate('tanggal_pinjam', today())->count();
        $todayReturns = Peminjaman::whereDate('tanggal_kembali_aktual', today())->count();
        
        return view('dashboard.petugas', compact(
            'pendingRequests',
            'todayBorrowings',
            'todayReturns'
        ));
    }

    /**
     * Dashboard Staff
     */
    public function staffDashboard()
    {
        if (auth()->user()->role !== 'staff') {
            abort(403, 'Unauthorized action.');
        }
        
        // Data khusus staff
        $userBorrowings = Peminjaman::where('user_id', auth()->id())->count();
        $activeUserBorrowings = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->count();
        
        return view('dashboard.staff', compact(
            'userBorrowings',
            'activeUserBorrowings'
        ));
    }

    /**
     * Dashboard User Umum
     */
public function userDashboard()
{
    $user_id = auth()->id();
    
    // Dapatkan user data
    $user = User::find($user_id);
    
    // Statistik untuk user
    $peminjaman_aktif = Peminjaman::where('user_id', $user_id)
        ->whereIn('status', ['approved', 'aktif'])
        ->whereNull('tanggal_kembali')
        ->count();
    
    $peminjaman_selesai = Peminjaman::where('user_id', $user_id)
        ->where('status', 'selesai')
        ->count();
    
    $peminjaman_pending = Peminjaman::where('user_id', $user_id)
        ->where('status', 'pending')
        ->count();
    
    $peminjaman_ditolak = Peminjaman::where('user_id', $user_id)
        ->whereIn('status', ['ditolak', 'batal'])
        ->count();
    
    // Total laptop yang tersedia (diambil dari data admin)
    $total_laptop = Laptop::where('status', 'tersedia')->count();
    
    // Peminjaman terbaru
    $peminjaman_terbaru = Peminjaman::where('user_id', $user_id)
        ->with('laptop')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    return view('user.dashboard', compact(
        'user',
        'peminjaman_aktif',
        'peminjaman_selesai',
        'peminjaman_pending',
        'peminjaman_ditolak',
        'total_laptop',
        'peminjaman_terbaru'
    ));
}

public function storePeminjamanUser(Request $request)
{
    if (!in_array(auth()->user()->role, ['user', 'peminjam'])) {
        abort(403, 'Unauthorized access.');
    }

    $request->validate([
        'laptop_id' => 'required|exists:tools,id',
        'tanggal_pinjam' => 'required|date|after_or_equal:today',
        'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
        'tujuan' => 'required|in:meeting,presentasi,training,work_from_home,proyek,lainnya',
        'keterangan' => 'nullable|string|max:500',
    ]);

    try {
        DB::beginTransaction();

        $tool = \App\Models\Tool::findOrFail($request->laptop_id);
        
        // Cek status alat
        if ($tool->status !== 'tersedia') {
            return back()->with('error', 'Alat tidak tersedia untuk dipinjam. Status: ' . $tool->status);
        }

        // Cek apakah user sudah memiliki peminjaman aktif
        $peminjamanAktif = \App\Models\Peminjaman::where('user_id', auth()->id())
            ->where('status', 'approved')
            ->whereNull('tanggal_kembali')
            ->count();
        
        if ($peminjamanAktif >= 2) {
            return back()->with('error', 'Anda sudah memiliki 2 peminjaman aktif. Silakan kembalikan terlebih dahulu.');
        }

        // Simpan peminjaman dengan status 'pending' (menunggu approval)
        $peminjaman = \App\Models\Peminjaman::create([
            'user_id' => auth()->id(),
            'laptop_id' => $request->laptop_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'tujuan' => $request->tujuan,
            'keterangan' => $request->keterangan,
            'status' => 'pending', // Status awal: menunggu approval admin
            'denda' => 0.00,
            'is_denda_dibayar' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update status alat menjadi 'pending' (bukan langsung 'dipinjam')
        $tool->status = 'pending'; // Atau biarkan 'tersedia' sampai disetujui
        $tool->save();

        DB::commit();

        // Redirect ke halaman peminjaman saya
        return redirect()->route('user.my-borrowings.index')
            ->with('success', 'Permohonan peminjaman berhasil diajukan. Status: Menunggu persetujuan admin.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Gagal menyimpan peminjaman: ' . $e->getMessage(), [
            'exception' => $e,
            'request' => $request->all(),
            'user_id' => auth()->id()
        ]);
        
        return back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage())
                     ->withInput();
    }
}
    /**
     * Laporan Admin
     */
    public function laporan()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('admin.borrowings');
    }

    /**
     * Pengaturan Admin
     */
    public function pengaturan()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('admin.pengaturan');
    }

    /**
     * Peminjaman Petugas
     */
    public function petugasPeminjaman()
    {
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('petugas.peminjaman');
    }

    /**
     * Profile Index
     */
    public function profileIndex()
    {
        return view('profile.index');
    }

    /**
     * Profile Edit
     */
    public function profileEdit()
    {
        return view('profile.edit');
    }

    /**
     * Profile Update
     */
    public function profileUpdate(Request $request)
    {
        // Logic update profile
        return back()->with('success', 'Profile berhasil diperbarui');
    }
}