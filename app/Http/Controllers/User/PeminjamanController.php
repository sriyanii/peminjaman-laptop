<?php
// app/Http/Controllers/User/PeminjamanController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Laptop;  // Ganti Tool dengan Laptop
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with('laptop')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->paginate(10);

        return view('user.peminjaman.index', compact('peminjaman'));
    }

    public function create($laptop_id = null)
    {
        $laptop = null;
        if ($laptop_id) {
            $laptop = Laptop::where('id', $laptop_id)  // Ganti Tool dengan Laptop
                ->where('status', 'tersedia')
                ->firstOrFail();
        }
        
        $laptops = Laptop::where('status', 'tersedia')  // Ganti Tool dengan Laptop
            ->orderBy('merk')
            ->orderBy('model')
            ->get();
        
        return view('user.peminjaman.form', compact('laptops', 'laptop'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'laptop_id' => 'required|exists:laptops,id',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'tujuan' => 'required|in:meeting,presentasi,training,work_from_home,proyek,lainnya',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Cek ketersediaan laptop
            $laptop = Laptop::where('id', $request->laptop_id)  // Ganti Tool dengan Laptop
                ->where('status', 'tersedia')
                ->first();

            if (!$laptop) {
                throw new \Exception('Laptop tidak tersedia untuk dipinjam.');
            }

            // Cek apakah user punya peminjaman aktif
            $active_borrow = Peminjaman::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'approved', 'aktif'])
                ->whereNull('tanggal_kembali')
                ->first();

            if ($active_borrow) {
                throw new \Exception('Anda masih memiliki peminjaman aktif.');
            }

            // Buat peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'laptop_id' => $request->laptop_id,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
                'tujuan' => $request->tujuan,
                'keterangan' => $request->keterangan,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('user.peminjaman.index')
                ->with('success', 'Peminjaman berhasil diajukan, menunggu persetujuan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with(['laptop'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('user.peminjaman.show', compact('peminjaman'));
    }

    /**
 * Menghapus riwayat peminjaman yang sudah selesai
 */
public function destroy($id)
{
    try {
        $peminjaman = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'selesai')
            ->findOrFail($id);
        
        $peminjaman->delete();
        
        return redirect()->route('user.peminjaman.index')
            ->with('success', 'Riwayat peminjaman berhasil dihapus.');
            
    } catch (\Exception $e) {
        return redirect()->route('user.peminjaman.index')
            ->with('error', 'Gagal menghapus riwayat peminjaman.');
    }
}
}