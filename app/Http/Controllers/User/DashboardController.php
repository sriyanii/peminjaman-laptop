<?php
// app/Http/Controllers/User/DashboardController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Laptop;  // Ganti Tool dengan Laptop
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $total_peminjaman = Peminjaman::where('user_id', auth()->id())->count();
        
        $peminjaman_aktif = Peminjaman::where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'aktif'])
            ->whereNull('tanggal_kembali')
            ->count();
        
        $peminjaman_selesai = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'selesai')
            ->count();
        
        $peminjaman_ditolak = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'ditolak')
            ->count();
        
        $total_laptop = Laptop::where('status', 'tersedia')->count();  // Ganti Tool dengan Laptop
        
        $peminjaman_terbaru = Peminjaman::with('laptop')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('user.dashboard', compact(
            'total_peminjaman',
            'peminjaman_aktif',
            'peminjaman_selesai',
            'peminjaman_ditolak',
            'total_laptop',
            'peminjaman_terbaru'
        ));
    }
}