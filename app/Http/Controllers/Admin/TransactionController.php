<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiDenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi denda.
     */
    public function index(Request $request): View
    {
        $query = TransaksiDenda::with(['user', 'peminjaman.laptop', 'petugas'])
            ->orderBy('created_at', 'desc');
        
        // Filter pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('peminjaman', function($pinjamQuery) use ($search) {
                      $pinjamQuery->where('kode_peminjaman', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter status pembayaran
        if ($request->has('status_pembayaran') && !empty($request->status_pembayaran)) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        
        // Filter status transaksi
        if ($request->has('status_transaksi') && !empty($request->status_transaksi)) {
            $query->where('status_transaksi', $request->status_transaksi);
        }
        
        $transaksis = $query->paginate(15);
        
        // Statistik dashboard
        $total_transaksi = TransaksiDenda::count();
        $total_pendapatan = TransaksiDenda::where('status_pembayaran', 'lunas')
            ->where('status_transaksi', 'selesai')
            ->sum('denda_dibayar');
        $total_denda_belum_lunas = TransaksiDenda::where('status_pembayaran', 'belum_lunas')
            ->sum(DB::raw('total_denda - denda_dibayar'));
        $transaksi_pending = TransaksiDenda::where('status_transaksi', 'pending')->count();
        
        return view('admin.transactions.index', compact(
            'transaksis',
            'total_transaksi',
            'total_pendapatan',
            'total_denda_belum_lunas',
            'transaksi_pending'
        ));
    }
    
    /**
     * Menampilkan detail transaksi.
     */
    public function show($id): View
    {
        $transaction = TransaksiDenda::with(['user', 'peminjaman.laptop', 'petugas'])
            ->findOrFail($id);
        
        return view('admin.transactions.show', compact('transaction'));
    }
    
    /**
     * Menghapus transaksi (HANYA jika belum lunas).
     */
    public function destroy($id): RedirectResponse
    {
        $transaction = TransaksiDenda::findOrFail($id);
        
        // Cek apakah transaksi sudah lunas
        if ($transaction->status_pembayaran === 'lunas') {
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Transaksi yang sudah lunas tidak bisa dihapus.');
        }
        
        try {
            DB::beginTransaction();
            $transaction->delete();
            DB::commit();
            
            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transaksi berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
    
    /**
     * Export transaksi ke Excel/PDF.
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:excel,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status_pembayaran' => 'nullable|in:lunas,sebagian,belum_lunas',
        ]);
        
        $query = TransaksiDenda::with(['user', 'peminjaman.laptop', 'petugas']);
        
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        if ($request->status_pembayaran) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        
        $transaksis = $query->get();
        
        if ($request->format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\TransaksiDendaExport($transaksis),
                'transaksi_denda_' . date('Y-m-d') . '.xlsx'
            );
        }
        
        if ($request->format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'admin.transactions.export_pdf',
                compact('transaksis')
            );
            return $pdf->download('transaksi_denda_' . date('Y-m-d') . '.pdf');
        }
        
        return redirect()->back()->with('error', 'Format tidak didukung.');
    }
}