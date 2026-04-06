<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Konfirmasi pengambilan laptop oleh user
     */
    public function takeItem($id)
    {
        DB::beginTransaction();
        
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            
            // Validasi kepemilikan
            if ($peminjaman->user_id != auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses'
                ], 403);
            }
            
            // Validasi status harus approved
            if ($peminjaman->status != 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman belum disetujui oleh petugas'
                ], 400);
            }
            
            // Update status menjadi aktif
            $peminjaman->update([
                'status' => 'aktif',
                'waktu_diambil' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengambilan laptop berhasil dikonfirmasi'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update index method to include statistics
     */
    public function index(Request $request)
    {
        $query = Peminjaman::where('user_id', auth()->id());
        
        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('laptop', function($q2) use ($search) {
                      $q2->where('merk', 'like', "%{$search}%")
                         ->orWhere('model', 'like', "%{$search}%");
                  });
            });
        }
        
        $peminjaman = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Statistics for user dashboard
        $statistics = [
            'pending' => Peminjaman::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'approved' => Peminjaman::where('user_id', auth()->id())->where('status', 'approved')->count(),
            'completed' => Peminjaman::where('user_id', auth()->id())->where('status', 'selesai')->count(),
            'rejected' => Peminjaman::where('user_id', auth()->id())->where('status', 'ditolak')->count(),
        ];
        
        return view('user.peminjaman.index', compact('peminjaman', 'statistics'));
    }

    

    public function create($laptop_id = null)
    {
        $laptop = null;
        if ($laptop_id) {
            $laptop = Laptop::findOrFail($laptop_id);
        }
        return view('user.peminjaman.create', compact('laptop'));
    }

public function store(Request $request)
{
    $request->validate([
        'laptop_id' => 'required|exists:laptops,id',
        'tanggal_pinjam' => 'required|date',
        'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
        'tujuan' => 'required',
        'keterangan' => 'nullable|string'
    ]);

    $peminjamanAktifCount = Peminjaman::where('user_id', auth()->id())
        ->whereIn('status', ['approved', 'aktif'])
        ->whereNull('tanggal_kembali')
        ->count();

    if ($peminjamanAktifCount >= 2) {
        return back()->with('error', 'Anda sudah memiliki 2 peminjaman aktif.');
    }

    $laptop = Laptop::findOrFail($request->laptop_id);
    
    if ($laptop->status !== 'tersedia') {
        return back()->with('error', 'Alat tidak tersedia untuk dipinjam.');
    }

    DB::beginTransaction();
    
    try {
        $peminjaman = Peminjaman::create([
            'user_id' => auth()->id(),
            'laptop_id' => $request->laptop_id,
            'nama_alat' => $request->nama_alat ?? $laptop->merk . ' ' . $laptop->model,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'tujuan' => $request->tujuan,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
            'denda' => 0,
            'is_denda_dibayar' => 0
        ]);


        DB::commit();

        return redirect()->route('user.peminjaman.index')
            ->with('success', 'Peminjaman berhasil diajukan. Menunggu persetujuan petugas.');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function show($id)
    {
        $peminjaman = Peminjaman::with('laptop')->findOrFail($id);
        
        if ($peminjaman->user_id != auth()->id()) {
            abort(403);
        }
        
        return view('user.peminjaman.show', compact('peminjaman'));
    }

    public function destroy($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            
            if ($peminjaman->status != 'pending') {
                return redirect()->route('user.peminjaman.index')
                    ->with('error', 'Peminjaman tidak dapat dibatalkan karena sudah diproses.');
            }
            
            if ($peminjaman->user_id != auth()->id()) {
                return redirect()->route('user.peminjaman.index')
                    ->with('error', 'Anda tidak memiliki akses.');
            }
            
            DB::beginTransaction();
            
            $peminjaman->update(['status' => 'batal']);
            Laptop::where('id', $peminjaman->laptop_id)->update(['status' => 'tersedia']);
            
            DB::commit();
            
            return redirect()->route('user.peminjaman.index')
                ->with('success', 'Peminjaman berhasil dibatalkan');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('user.peminjaman.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            
            if ($peminjaman->status != 'batal') {
                return redirect()->route('user.peminjaman.index')
                    ->with('error', 'Hanya peminjaman yang sudah dibatalkan yang dapat dihapus.');
            }
            
            if ($peminjaman->user_id != auth()->id()) {
                return redirect()->route('user.peminjaman.index')
                    ->with('error', 'Anda tidak memiliki akses.');
            }
            
            $peminjaman->forceDelete();
            
            return redirect()->route('user.peminjaman.index')
                ->with('success', 'Data peminjaman berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()->route('user.peminjaman.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    
}