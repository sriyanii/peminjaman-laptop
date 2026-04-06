<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $peminjaman = Peminjaman::with(['user', 'laptop'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'aktif'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user.pengembalian.index', compact('peminjaman'));
    }
    
    public function create($peminjaman_id = null)
    {
        if ($peminjaman_id) {
            $peminjaman = Peminjaman::with(['user', 'laptop'])
                ->where('user_id', auth()->id())
                ->where('id', $peminjaman_id)
                ->whereIn('status', ['approved', 'aktif'])
                ->firstOrFail();
                
            return view('user.pengembalian.form', compact('peminjaman'));
        }
        
        $peminjamanList = Peminjaman::with(['user', 'laptop'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'aktif'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user.pengembalian.create', compact('peminjamanList'));
    }
    
    public function store(Request $request, $peminjaman)
    {
        $request->validate([
            'tanggal_kembali' => 'required|date',
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat',
            'keterangan_rusak' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $peminjaman = Peminjaman::with(['user', 'laptop'])
                ->where('user_id', auth()->id())
                ->where('id', $peminjaman)
                ->whereIn('status', ['approved', 'aktif'])
                ->firstOrFail();
            
            $tanggal_kembali = Carbon::parse($request->tanggal_kembali);
            $tanggal_rencana = Carbon::parse($peminjaman->tanggal_kembali_rencana);
            
            $denda_telat = 0;
            $telat_hari = 0;
            if ($tanggal_kembali > $tanggal_rencana) {
                $telat_hari = $tanggal_kembali->diffInDays($tanggal_rencana);
                $denda_telat = $telat_hari * 10000;
            }
            
            $denda_kerusakan = 0;
            if ($request->kondisi_alat == 'rusak_ringan') {
                $denda_kerusakan = $peminjaman->harga_sewa * 0.5;
            } elseif ($request->kondisi_alat == 'rusak_berat') {
                $denda_kerusakan = $peminjaman->harga_sewa;
            }
            
            $total_denda = $denda_telat + $denda_kerusakan;
            $total_tagihan = $peminjaman->harga_sewa + $total_denda;
            
            $catatan = "Kondisi: " . $request->kondisi_alat;
            if ($request->keterangan_rusak) {
                $catatan .= " | Kerusakan: " . $request->keterangan_rusak;
            }
            
            $peminjaman->update([
                'tanggal_kembali' => $request->tanggal_kembali,
                'catatan_pengembalian' => $catatan,
                'denda' => $total_denda,
                'total_tagihan' => $total_tagihan,
                'status' => 'selesai',
                'waktu_pengembalian' => now()
            ]);
            
            // Update status laptop
            $laptop = Laptop::find($peminjaman->laptop_id);
            if ($laptop) {
                $statusLaptop = $request->kondisi_alat == 'baik' ? 'tersedia' : 'perbaikan';
                $laptop->update(['status' => $statusLaptop]);
            }
            
            DB::commit();
            
            return redirect()->route('user.pengembalian.index')
                ->with('success', 'Pengembalian berhasil diproses. Total tagihan: Rp ' . number_format($total_tagihan, 0, ',', '.'));
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $peminjaman = Peminjaman::with(['user', 'laptop'])
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
            
        return view('user.pengembalian.show', compact('peminjaman'));
    }
}