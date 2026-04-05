<?php
// app/Http/Controllers/User/PengembalianController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Laptop;  // Ganti Tool dengan Laptop
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with('laptop')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'aktif'])
            ->whereNull('tanggal_kembali')
            ->orderBy('tanggal_kembali_rencana', 'asc')
            ->get();

        return view('user.pengembalian.index', compact('peminjaman'));
    }

    public function create($peminjaman_id = null)
    {
        if ($peminjaman_id) {
            $peminjaman = Peminjaman::with('laptop')
                ->where('id', $peminjaman_id)
                ->where('user_id', auth()->id())
                ->whereIn('status', ['approved', 'aktif'])
                ->whereNull('tanggal_kembali')
                ->firstOrFail();

            return view('user.pengembalian.form', compact('peminjaman'));
        }

        $peminjaman = Peminjaman::with('laptop')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'aktif'])
            ->whereNull('tanggal_kembali')
            ->get();

        return view('user.pengembalian.pilih', compact('peminjaman'));
    }

    public function store(Request $request, $peminjaman_id)
    {
        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::with('laptop')
                ->where('id', $peminjaman_id)
                ->where('user_id', auth()->id())
                ->whereIn('status', ['approved', 'aktif'])
                ->whereNull('tanggal_kembali')
                ->firstOrFail();

            $request->validate([
                'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
                'catatan' => 'nullable|string|max:500',
            ]);

            // Update peminjaman
            $peminjaman->update([
                'tanggal_kembali' => now(),
                'catatan_pengembalian' => 'Kondisi: ' . $request->kondisi . 
                    ($request->catatan ? ' | Catatan: ' . $request->catatan : ''),
                'status' => 'pending_return',
                'waktu_pengembalian' => now(),
            ]);

            DB::commit();

            return redirect()->route('user.pengembalian.index')
                ->with('success', 'Pengembalian berhasil diajukan, menunggu konfirmasi petugas.');

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

        return view('user.pengembalian.show', compact('peminjaman'));
    }
}