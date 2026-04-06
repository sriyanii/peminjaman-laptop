<?php


namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TransaksiDenda;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransaksiDendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransaksiDenda::with(['peminjaman.laptop', 'user', 'petugas'])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc');
        
        // Filter status
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        
        $transaksis = $query->paginate(15);
        
        $total_transaksi = TransaksiDenda::whereNull('deleted_at')->count();
        $total_nominal_denda = TransaksiDenda::whereNull('deleted_at')->sum('total_denda');
        $total_dibayar = TransaksiDenda::whereNull('deleted_at')->sum('denda_dibayar');
        $total_belum_bayar = TransaksiDenda::whereNull('deleted_at')
            ->whereIn('status_pembayaran', ['belum_lunas', 'sebagian_lunas'])
            ->sum('total_denda');
        
        return view('petugas.transaksi.index', compact(
            'transaksis', 
            'total_transaksi', 
            'total_nominal_denda', 
            'total_dibayar', 
            'total_belum_bayar'
        ));
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaksi = TransaksiDenda::with(['peminjaman.laptop', 'user', 'petugas'])
            ->findOrFail($id);
        
        return view('petugas.transaksi.show', compact('transaksi'));
    }
    
    /**
     * Get data transaksi untuk AJAX
     */
    public function getData($id)
    {
        try {
            $transaksi = TransaksiDenda::findOrFail($id);
            
            $totalDenda = abs($transaksi->total_denda ?? 0);
            $dendaDibayar = abs($transaksi->denda_dibayar ?? 0);
            $sisa = max(0, $totalDenda - $dendaDibayar);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $transaksi->id,
                    'total_denda' => $totalDenda,
                    'dibayar' => $dendaDibayar,
                    'sisa' => $sisa,
                    'status' => $transaksi->status_pembayaran
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('getData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * Process payment with AJAX
     */
    public function bayar(Request $request, $id)
    {
        Log::info('=== BAYAR METHOD DIPANGGIL ===');
        Log::info('ID: ' . $id);
        Log::info('Request all: ', $request->all());
        Log::info('User: ' . auth()->id());
        
        try {
            $request->validate([
                'jumlah_bayar' => 'required|numeric|min:1000',
                'metode_pembayaran' => 'required|in:tunai,transfer,qris'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . json_encode($e->errors())
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Cari transaksi
            $transaksi = TransaksiDenda::with(['peminjaman'])->find($id);
            
            if (!$transaksi) {
                throw new \Exception('Transaksi dengan ID ' . $id . ' tidak ditemukan');
            }
            
            Log::info('Transaksi ditemukan:', [
                'id' => $transaksi->id,
                'total_denda' => $transaksi->total_denda,
                'denda_dibayar' => $transaksi->denda_dibayar,
                'status_pembayaran' => $transaksi->status_pembayaran
            ]);
            
            // Cek status
            if ($transaksi->status_pembayaran == 'lunas') {
                throw new \Exception('Transaksi sudah lunas');
            }
            
            // Hitung sisa tagihan (gunakan nilai absolut)
            $totalDenda = abs($transaksi->total_denda);
            $dendaDibayar = abs($transaksi->denda_dibayar);
            $sisaTagihan = $totalDenda - $dendaDibayar;
            $jumlahBayar = $request->jumlah_bayar;
            
            Log::info('Perhitungan:', [
                'totalDenda' => $totalDenda,
                'dendaDibayar' => $dendaDibayar,
                'sisaTagihan' => $sisaTagihan,
                'jumlahBayar' => $jumlahBayar
            ]);
            
            if ($jumlahBayar > $sisaTagihan) {
                throw new \Exception('Jumlah pembayaran (' . number_format($jumlahBayar) . ') melebihi sisa tagihan (' . number_format($sisaTagihan) . ')');
            }
            
            $dendaBaru = $dendaDibayar + $jumlahBayar;
            
            // Update status
            $updateData = [
                'denda_dibayar' => $dendaBaru,
                'metode_pembayaran' => $request->metode_pembayaran,
                'waktu_pembayaran' => now(),
                'petugas_id' => auth()->id()
            ];
            
            if ($dendaBaru >= $totalDenda) {
                $updateData['status_pembayaran'] = 'lunas';
                $updateData['status_transaksi'] = 'selesai';
                $updateData['waktu_selesai'] = now();
            } else {
                $updateData['status_pembayaran'] = 'sebagian_lunas';
            }
            
            Log::info('Update data:', $updateData);
            
            $transaksi->update($updateData);
            
            Log::info('Transaksi updated successfully');
            
            // Update peminjaman jika ada kolom is_denda_dibayar
            if ($transaksi->peminjaman) {
                try {
                    $transaksi->peminjaman->update(['is_denda_dibayar' => 1]);
                    Log::info('Peminjaman updated: ' . $transaksi->peminjaman_id);
                } catch (\Exception $e) {
                    Log::warning('Could not update peminjaman: ' . $e->getMessage());
                }
            }
            
            DB::commit();
            
            $sisaBaru = $totalDenda - $dendaBaru;
            
            Log::info('Pembayaran berhasil, sisa: ' . $sisaBaru);
            
            return response()->json([
                'success' => true,
                'message' => '✅ Pembayaran berhasil! Sisa tagihan: Rp ' . number_format($sisaBaru, 0, ',', '.'),
                'sisa_tagihan' => $sisaBaru,
                'status' => $dendaBaru >= $totalDenda ? 'lunas' : 'sebagian_lunas'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ERROR bayar denda: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show form bayar (Non-AJAX version)
     */
    public function formBayar($id)
    {
        try {
            $transaksi = TransaksiDenda::with(['peminjaman.laptop', 'user'])
                ->findOrFail($id);
            
            $totalDenda = abs($transaksi->total_denda);
            $dendaDibayar = abs($transaksi->denda_dibayar);
            $sisa = max(0, $totalDenda - $dendaDibayar);
            
            return view('petugas.transaksi.form-bayar', compact('transaksi', 'sisa'));
            
        } catch (\Exception $e) {
            return redirect()->route('petugas.transaksi.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }
    }
    
    /**
     * Proses pembayaran dari form (Non-AJAX)
     */
    public function prosesBayar(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris'
        ]);
        
        DB::beginTransaction();
        
        try {
            $transaksi = TransaksiDenda::with(['peminjaman'])->findOrFail($id);
            
            if ($transaksi->status_pembayaran == 'lunas') {
                return redirect()->route('petugas.transaksi.index')
                    ->with('error', 'Transaksi sudah lunas');
            }
            
            $totalDenda = abs($transaksi->total_denda);
            $dendaDibayar = abs($transaksi->denda_dibayar);
            $sisaTagihan = $totalDenda - $dendaDibayar;
            $jumlahBayar = $request->jumlah_bayar;
            
            if ($jumlahBayar > $sisaTagihan) {
                return redirect()->back()
                    ->with('error', 'Jumlah pembayaran melebihi sisa tagihan')
                    ->withInput();
            }
            
            $dendaBaru = $dendaDibayar + $jumlahBayar;
            
            // Update status
            if ($dendaBaru >= $totalDenda) {
                $transaksi->update([
                    'denda_dibayar' => $dendaBaru,
                    'status_pembayaran' => 'lunas',
                    'status_transaksi' => 'selesai',
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'waktu_pembayaran' => now(),
                    'waktu_selesai' => now(),
                    'petugas_id' => auth()->id()
                ]);
            } else {
                $transaksi->update([
                    'denda_dibayar' => $dendaBaru,
                    'status_pembayaran' => 'sebagian_lunas',
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'waktu_pembayaran' => now(),
                    'petugas_id' => auth()->id()
                ]);
            }
            
            DB::commit();
            
            $sisaBaru = $totalDenda - $dendaBaru;
            
            if ($sisaBaru > 0) {
                return redirect()->route('petugas.transaksi.show', $id)
                    ->with('success', "✅ Pembayaran berhasil! Sisa tagihan: Rp " . number_format($sisaBaru, 0, ',', '.'));
            } else {
                return redirect()->route('petugas.transaksi.index')
                    ->with('success', '✅ Pembayaran lunas! Transaksi selesai.');
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('prosesBayar error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Cetak struk transaksi
     */
    public function cetakStruk($id)
    {
        try {
            $transaksi = TransaksiDenda::with(['peminjaman.laptop', 'user', 'petugas'])
                ->findOrFail($id);
            
            return view('petugas.transaksi.struk', compact('transaksi'));
            
        } catch (\Exception $e) {
            return redirect()->route('petugas.transaksi.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }
    }
    
    /**
     * Soft delete transaksi
     */
    public function destroy($id)
    {
        try {
            $transaksi = TransaksiDenda::findOrFail($id);
            $transaksi->delete();
            
            return redirect()->route('petugas.transaksi.index')
                ->with('success', 'Transaksi berhasil dihapus');
                
        } catch (\Exception $e) {
            Log::error('destroy error: ' . $e->getMessage());
            return redirect()->route('petugas.transaksi.index')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
    
    /**
     * Restore soft deleted transaksi
     */
    public function restore($id)
    {
        try {
            $transaksi = TransaksiDenda::withTrashed()->findOrFail($id);
            $transaksi->restore();
            
            return redirect()->route('petugas.transaksi.index')
                ->with('success', 'Transaksi berhasil dipulihkan');
                
        } catch (\Exception $e) {
            Log::error('restore error: ' . $e->getMessage());
            return redirect()->route('petugas.transaksi.index')
                ->with('error', 'Gagal memulihkan transaksi: ' . $e->getMessage());
        }
    }
}