<?php
/**
 * Transaction Controller (Admin)
 * 
 * Controller ini bertanggung jawab untuk mengelola semua operasi terkait transaksi denda
 * dalam aplikasi peminjaman laptop. Meliputi pembuatan transaksi denda, manajemen status
 * pembayaran, pengecekan kondisi barang, serta laporan transaksi.
 * 
 * @package App\Http\Controllers\Admin
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiDenda;
use App\Models\User;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi denda dengan filter dan statistik.
     * 
     * Method ini menampilkan data transaksi denda yang dapat difilter berdasarkan:
     * - Pencarian berdasarkan ID transaksi atau nama/email user
     * - Status pembayaran (lunas, belum_lunas)
     * - Status transaksi (pending, proses_cek, selesai)
     * - Rentang tanggal transaksi
     * 
     * Juga menampilkan statistik ringkasan:
     * - Total transaksi
     * - Jumlah transaksi lunas/belum lunas
     * - Total nominal denda
     * - Total denda yang telah dibayar
     * 
     * @param Request $request Objek request yang berisi parameter filter
     * @return View View daftar transaksi dengan data transactions dan statistics
     * 
     * @example
     * GET /admin/transactions?status=lunas&start_date=2024-01-01&end_date=2024-12-31
     */
    public function index(Request $request): View
    {
        // Inisialisasi query dengan eager loading relasi yang diperlukan
        $query = TransaksiDenda::with(['user', 'peminjaman', 'petugas'])
            ->latest();
        
        // Filter berdasarkan pencarian ID atau nama/email user
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter berdasarkan status pembayaran (lunas/belum lunas)
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status_pembayaran', $request->status);
        }
        
        // Filter berdasarkan status transaksi (pending/proses_cek/selesai)
        if ($request->has('status_transaksi') && !empty($request->status_transaksi)) {
            $query->where('status_transaksi', $request->status_transaksi);
        }
        
        // Filter berdasarkan tanggal mulai
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        // Filter berdasarkan tanggal selesai
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Ambil data dengan pagination 20 item per halaman
        $transactions = $query->paginate(20);
        
        // Statistik ringkasan untuk dashboard transaksi
        $statistics = [
            'total' => TransaksiDenda::count(),
            'total_lunas' => TransaksiDenda::where('status_pembayaran', 'lunas')->count(),
            'total_belum_lunas' => TransaksiDenda::where('status_pembayaran', 'belum_lunas')->count(),
            'total_pending' => TransaksiDenda::where('status_transaksi', 'pending')->count(),
            'total_proses' => TransaksiDenda::where('status_transaksi', 'proses_cek')->count(),
            'total_selesai' => TransaksiDenda::where('status_transaksi', 'selesai')->count(),
            'total_nominal' => TransaksiDenda::sum('total_denda'),
            'total_terbayar' => TransaksiDenda::sum('denda_dibayar'),
        ];
        
        // Format nilai nominal ke dalam format mata uang Rupiah
        foreach (['total_nominal', 'total_terbayar'] as $key) {
            $statistics[$key . '_formatted'] = 'Rp ' . number_format($statistics[$key], 0, ',', '.');
        }
        
        return view('admin.transactions.index', compact('transactions', 'statistics'));
    }

    /**
     * Menampilkan form untuk membuat transaksi denda baru.
     * 
     * Method ini menyiapkan data yang diperlukan untuk form pembuatan transaksi:
     * - Daftar user dengan role 'user' untuk dropdown pemilih user
     * - Daftar peminjaman yang memiliki denda > 0 dan belum memiliki transaksi denda
     * 
     * @return View View form pembuatan transaksi dengan data users dan peminjaman
     */
    public function create(): View
    {
        // Ambil semua user dengan role user
        $users = User::where('role', 'user')
                    ->orderBy('name')
                    ->get();
        
        // Ambil peminjaman yang memiliki denda > 0 dan belum memiliki transaksi denda
        $peminjaman = Peminjaman::where('denda', '>', 0)
                    ->whereDoesntHave('transaksiDenda')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('admin.transactions.create', compact('users', 'peminjaman'));
    }

    /**
     * Menyimpan data transaksi denda baru ke database.
     * 
     * Method ini membuat transaksi denda baru berdasarkan peminjaman yang dipilih.
     * Transaksi akan dibuat dengan status awal:
     * - status_pembayaran: 'belum_lunas'
     * - status_transaksi: 'proses_cek'
     * Operasi dilakukan dalam transaksi database untuk menjaga konsistensi data.
     * 
     * @param Request $request Objek request yang berisi data transaksi
     * @return RedirectResponse Redirect ke halaman detail transaksi dengan pesan sukses/error
     * 
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     * @throws \Exception Jika terjadi error saat menyimpan data
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi data input
        $validated = $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'total_denda' => 'required|numeric|min:0',
            'kondisi_barang' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan_cek' => 'nullable|string|max:500',
        ]);
        
        // Memulai transaksi database
        DB::beginTransaction();
        
        try {
            // Ambil data peminjaman yang terkait
            $peminjaman = Peminjaman::findOrFail($validated['peminjaman_id']);
            
            // Buat transaksi denda baru
            $transaction = TransaksiDenda::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'petugas_id' => auth()->id(),
                'total_denda' => $validated['total_denda'],
                'denda_dibayar' => 0,
                'status_pembayaran' => 'belum_lunas',
                'status_transaksi' => 'proses_cek',
                'kondisi_barang' => $validated['kondisi_barang'],
                'catatan_cek' => $validated['catatan_cek'] ?? 'Pengecekan oleh admin',
                'waktu_cek' => now(),
            ]);
            
            // Commit transaksi jika semua operasi berhasil
            DB::commit();
            
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Transaksi denda berhasil dibuat.');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail transaksi denda berdasarkan ID.
     * 
     * Method ini menampilkan informasi lengkap transaksi denda beserta relasi:
     * - User yang melakukan peminjaman
     * - Detail peminjaman dan laptop yang dipinjam
     * - Petugas yang melakukan pengecekan
     * 
     * @param int|string $id ID transaksi yang akan ditampilkan
     * @return View View detail transaksi dengan data transaction
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function show($id): View
    {
        // Ambil data transaksi dengan relasi yang diperlukan
        $transaction = TransaksiDenda::with(['user', 'peminjaman.laptop', 'petugas'])
            ->findOrFail($id);
        
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Menampilkan form untuk mengedit transaksi denda.
     * 
     * Method ini menampilkan form edit transaksi dengan validasi status:
     * Transaksi yang sudah selesai (status_transaksi = 'selesai') tidak dapat diedit.
     * 
     * @param int|string $id ID transaksi yang akan diedit
     * @return View|RedirectResponse View form edit atau redirect dengan pesan error
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function edit($id): View|RedirectResponse
    {
        // Ambil data transaksi yang akan diedit
        $transaction = TransaksiDenda::findOrFail($id);
        
        // Cek apakah transaksi sudah selesai
        if ($transaction->status_transaksi === 'selesai') {
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Transaksi yang sudah selesai tidak bisa diedit.');
        }
        
        // Ambil data pendukung untuk form edit
        $users = User::where('role', 'user')->orderBy('name')->get();
        $peminjaman = Peminjaman::where('denda', '>', 0)->orderBy('created_at', 'desc')->get();
        
        return view('admin.transactions.edit', compact('transaction', 'users', 'peminjaman'));
    }

    /**
     * Mengupdate data transaksi denda yang sudah ada.
     * 
     * Method ini melakukan update data transaksi dengan validasi status:
     * Transaksi yang sudah selesai tidak dapat diupdate.
     * 
     * @param Request $request Objek request yang berisi data transaksi yang diupdate
     * @param int|string $id ID transaksi yang akan diupdate
     * @return RedirectResponse Redirect ke halaman detail transaksi dengan pesan sukses/error
     * 
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     * @throws \Exception Jika terjadi error saat update data
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Ambil data transaksi yang akan diupdate
        $transaction = TransaksiDenda::findOrFail($id);
        
        // Cek apakah transaksi sudah selesai
        if ($transaction->status_transaksi === 'selesai') {
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Transaksi yang sudah selesai tidak bisa diupdate.');
        }
        
        // Validasi data input
        $validated = $request->validate([
            'total_denda' => 'required|numeric|min:0',
            'kondisi_barang' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan_cek' => 'nullable|string|max:500',
        ]);
        
        // Memulai transaksi database
        DB::beginTransaction();
        
        try {
            // Update data transaksi
            $transaction->update([
                'total_denda' => $validated['total_denda'],
                'kondisi_barang' => $validated['kondisi_barang'],
                'catatan_cek' => $validated['catatan_cek'] ?? $transaction->catatan_cek,
                'waktu_cek' => now(),
            ]);
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil diperbarui.');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus transaksi denda dari database.
     * 
     * Method ini menghapus transaksi dengan validasi:
     * Transaksi yang sudah lunas tidak dapat dihapus untuk menjaga integritas data keuangan.
     * 
     * @param int|string $id ID transaksi yang akan dihapus
     * @return RedirectResponse Redirect ke halaman daftar transaksi dengan pesan sukses/error
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     * @throws \Exception Jika terjadi error saat menghapus data
     */
    public function destroy($id): RedirectResponse
    {
        // Ambil data transaksi yang akan dihapus
        $transaction = TransaksiDenda::findOrFail($id);
        
        // Cek apakah transaksi sudah lunas
        if ($transaction->status_pembayaran === 'lunas') {
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('error', 'Transaksi yang sudah lunas tidak bisa dihapus.');
        }
        
        try {
            // Memulai transaksi database
            DB::beginTransaction();
            
            // Hapus transaksi
            $transaction->delete();
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transaksi berhasil dihapus.');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat transaksi user tertentu.
     * 
     * Method ini menampilkan semua transaksi denda yang terkait dengan user
     * yang dipilih, beserta detail peminjaman yang terkait.
     * 
     * @param User $user Objek User yang akan ditampilkan riwayat transaksinya
     * @return View View riwayat transaksi user dengan data user dan transactions
     * 
     * @example
     * GET /admin/transactions/user/5
     */
    public function userTransactions(User $user): View
    {
        // Ambil semua transaksi user dengan relasi peminjaman
        $transactions = TransaksiDenda::where('user_id', $user->id)
            ->with('peminjaman')
            ->latest()
            ->paginate(15);
        
        return view('admin.transactions.user-history', compact('user', 'transactions'));
    }

    /**
     * Memproses pembayaran denda.
     * 
     * Method ini dapat ditambahkan untuk menangani proses pembayaran denda,
     * mengupdate status pembayaran dan mencatat jumlah yang dibayarkan.
     * 
     * @param Request $request Objek request berisi jumlah pembayaran
     * @param int|string $id ID transaksi yang akan diproses pembayarannya
     * @return RedirectResponse Redirect ke halaman detail transaksi dengan pesan sukses/error
     */
    public function processPayment(Request $request, $id): RedirectResponse
    {
        // Validasi jumlah pembayaran
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,online',
            'catatan_pembayaran' => 'nullable|string|max:500',
        ]);
        
        try {
            DB::beginTransaction();
            
            $transaction = TransaksiDenda::findOrFail($id);
            
            // Hitung total pembayaran baru
            $totalDibayar = $transaction->denda_dibayar + $request->jumlah_bayar;
            
            // Tentukan status pembayaran
            $statusPembayaran = $totalDibayar >= $transaction->total_denda ? 'lunas' : 'belum_lunas';
            
            // Update transaksi
            $transaction->update([
                'denda_dibayar' => $totalDibayar,
                'status_pembayaran' => $statusPembayaran,
                'metode_pembayaran' => $request->metode_pembayaran,
                'catatan_pembayaran' => $request->catatan_pembayaran,
                'waktu_pembayaran' => now(),
                'petugas_penerima' => auth()->id(),
            ]);
            
            // Jika sudah lunas, update status transaksi menjadi selesai
            if ($statusPembayaran === 'lunas') {
                $transaction->update(['status_transaksi' => 'selesai']);
            }
            
            DB::commit();
            
            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Pembayaran berhasil diproses.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Mengekspor laporan transaksi ke format Excel/PDF.
     * 
     * Method ini dapat ditambahkan untuk mengekspor data transaksi
     * ke dalam format yang dapat diunduh (Excel, PDF, CSV).
     * 
     * @param Request $request Parameter filter untuk laporan
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Response file export
     */
    public function export(Request $request)
    {
        // Ambil data berdasarkan filter
        $query = TransaksiDenda::with(['user', 'peminjaman']);
        
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $transactions = $query->get();
        
        // Logic untuk export ke Excel/PDF
        // Contoh menggunakan library Excel atau PDF
        
        return back()->with('info', 'Fitur export akan segera tersedia.');
    }
}