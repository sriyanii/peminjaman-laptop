<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user_id = auth()->id();
        
        $peminjaman = Peminjaman::with('tool')
            ->where('user_id', $user_id)
            ->whereIn('status', ['selesai', 'ditolak', 'batal'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('user.riwayat.index', compact('peminjaman'));
    }
}