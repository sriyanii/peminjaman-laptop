<?php
/**
 * Peminjaman Controller (Petugas)
 * 
 * Controller ini bertanggung jawab untuk mengelola operasi terkait peminjaman laptop
 * dari sisi petugas. Meliputi menampilkan daftar peminjaman dengan berbagai filter,
 * mengupdate status peminjaman, serta menghapus data peminjaman dengan validasi yang tepat.
 * 
 * @package App\Http\Controllers\Petugas
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman dengan berbagai filter.
     * 
     * Method ini menampilkan data peminjaman yang dapat difilter berdasarkan:
     * - Status peminjaman (pending, approved, ditolak, selesai, aktif, batal)
     * - Rentang tanggal peminjaman (tanggal_mulai - tanggal_selesai)
     * - Nama atau email peminjam
     * 
     * Juga menampilkan statistik ringkasan:
     * - Total peminjaman
     * - Peminjaman disetujui
     * - Peminjaman menunggu
     * - Peminjaman ditolak
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param Request $request Objek request yang berisi parameter filter
     * @return View View daftar peminjaman dengan data peminjaman dan statistik
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * 
     * @example
     * GET /petugas/peminjaman?status=pending&tanggal_mulai=2024-01-01&peminjam=john
     */
    public function index(Request $request): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        /**
         * Inisialisasi query dengan eager loading relasi user dan laptop
         * Diurutkan berdasarkan waktu pembuatan terbaru
         * 
         * Catatan: Relasi 'tool' telah diubah menjadi 'laptop' sesuai dengan
         * struktur database aplikasi peminjaman laptop
         */
        $query = Peminjaman::with(['user', 'laptop'])
            ->orderBy('created_at', 'desc');

        // ========== FILTER BERDASARKAN STATUS ==========
        /**
         * Filter status peminjaman
         * Nilai yang valid: pending, approved, ditolak, selesai, aktif, batal
         */
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // ========== FILTER BERDASARKAN TANGGAL ==========
        /**
         * Filter tanggal mulai peminjaman
         * Menampilkan peminjaman dengan tanggal pinjam >= tanggal_mulai
         */
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_mulai);
        }

        /**
         * Filter tanggal selesai peminjaman
         * Menampilkan peminjaman dengan tanggal pinjam <= tanggal_selesai
         */
        if ($request->has('tanggal_selesai') && $request->tanggal_selesai) {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_selesai);
        }

        // ========== FILTER BERDASARKAN PEMINJAM ==========
        /**
         * Filter berdasarkan nama atau email peminjam
         * Menggunakan whereHas untuk mencari di relasi user
         * Pencarian bersifat partial (LIKE %keyword%)
         */
        if ($request->has('peminjam') && $request->peminjam) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->peminjam . '%')
                  ->orWhere('email', 'like', '%' . $request->peminjam . '%');
            });
        }

        /**
         * Mengambil data peminjaman dengan pagination 10 data per halaman
         * 
         * @var \Illuminate\Pagination\LengthAwarePaginator $peminjaman
         */
        $peminjaman = $query->paginate(10);

        // ========== STATISTIK RINGKASAN ==========
        
        /**
         * Total seluruh peminjaman dalam sistem
         */
        $totalPeminjaman = Peminjaman::count();
        
        /**
         * Jumlah peminjaman yang telah disetujui
         */
        $peminjamanDisetujui = Peminjaman::where('status', 'approved')->count();
        
        /**
         * Jumlah peminjaman yang masih menunggu persetujuan
         */
        $peminjamanMenunggu = Peminjaman::where('status', 'pending')->count();
        
        /**
         * Jumlah peminjaman yang ditolak
         */
        $peminjamanDitolak = Peminjaman::where('status', 'ditolak')->count();

        // Kirim data ke view
        return view('petugas.peminjaman.index', compact(
            'peminjaman',
            'totalPeminjaman',
            'peminjamanDisetujui',
            'peminjamanMenunggu',
            'peminjamanDitolak'
        ));
    }
    
    /**
     * Mengupdate status peminjaman.
     * 
     * Method ini mengubah status peminjaman dan menangani perubahan status laptop
     * yang terkait. Operasi dilakukan dalam transaksi database untuk menjaga
     * konsistensi data antara peminjaman dan laptop.
     * 
     * Status yang dapat diupdate:
     * - pending -> approved / ditolak / batal
     * - approved -> selesai / batal
     * - aktif -> selesai / batal
     * 
     * Method ini juga menangani update status laptop:
     * - Jika status menjadi 'approved': laptop diubah menjadi 'dipinjam'
     * - Jika status menjadi 'ditolak', 'selesai', atau 'batal': laptop diubah menjadi 'tersedia'
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param Request $request Objek request yang berisi status baru dan catatan
     * @param int|string $id ID peminjaman yang akan diupdate statusnya
     * @return RedirectResponse Redirect ke halaman daftar peminjaman dengan pesan sukses/error
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     * @throws \Exception Jika terjadi error saat update data
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        // Ambil data peminjaman dengan relasi laptop
        $peminjaman = Peminjaman::with('laptop')->findOrFail($id);

        // Validasi input status dan catatan
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,ditolak,selesai,aktif,batal',
            'catatan' => 'nullable|string|max:500',
            'alasan_ditolak' => 'nullable|string|max:500',
        ]);

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            $newStatus = $validated['status'];

            // Siapkan data yang akan diupdate
            $updateData = [
                'status' => $newStatus,
                'approved_by' => auth()->id(),
            ];

            /**
             * Penanganan khusus berdasarkan status baru:
             * - approved: catat waktu persetujuan
             * - ditolak: simpan alasan penolakan
             */
            if ($newStatus == 'approved') {
                $updateData['waktu_approve'] = now();
            } elseif ($newStatus == 'ditolak') {
                $updateData['alasan_ditolak'] = $validated['alasan_ditolak'];
            }

            // Update status peminjaman
            $peminjaman->update($updateData);

            /**
             * Update status laptop berdasarkan status peminjaman
             * - approved: laptop menjadi dipinjam
             * - ditolak/selesai/batal: laptop kembali tersedia
             */
            if ($peminjaman->laptop) {
                $laptop = $peminjaman->laptop;
                
                if ($newStatus == 'approved') {
                    $laptop->status = 'dipinjam';
                } elseif (in_array($newStatus, ['ditolak', 'selesai', 'batal'])) {
                    $laptop->status = 'tersedia';
                }
                $laptop->save();
            }

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Status peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail peminjaman (helper method).
     * 
     * Method ini dapat ditambahkan untuk melihat detail peminjaman
     * secara lengkap.
     * 
     * @param int|string $id ID peminjaman yang akan ditampilkan
     * @return View View detail peminjaman
     */
    public function show($id): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        // Ambil data peminjaman dengan relasi lengkap
        $peminjaman = Peminjaman::with(['user', 'laptop', 'approver'])
            ->findOrFail($id);
        
        return view('petugas.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Menampilkan form edit peminjaman (helper method).
     * 
     * Method ini dapat ditambahkan untuk mengedit data peminjaman
     * jika diperlukan.
     * 
     * @param int|string $id ID peminjaman yang akan diedit
     * @return View View form edit peminjaman
     */
    public function edit($id): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        $peminjaman = Peminjaman::findOrFail($id);
        
        return view('petugas.peminjaman.edit', compact('peminjaman'));
    }

    /**
     * Menghapus data peminjaman.
     * 
     * Method ini menghapus data peminjaman dengan validasi:
     * - Jika peminjaman memiliki denda belum lunas, penghapusan ditolak
     * - Jika peminjaman masih aktif, status laptop dikembalikan menjadi tersedia
     * - Menghapus transaksi denda terkait jika ada
     * 
     * Operasi dilakukan dalam transaksi database untuk menjaga konsistensi data.
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param int|string $id ID peminjaman yang akan dihapus
     * @return RedirectResponse Redirect ke halaman daftar peminjaman dengan pesan sukses/error
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     * @throws \Exception Jika peminjaman memiliki denda belum lunas
     */
    public function destroy($id): RedirectResponse
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        try {
            // Memulai transaksi database
            DB::beginTransaction();
            
            // Ambil data peminjaman dengan relasi laptop
            $peminjaman = Peminjaman::with('laptop')->findOrFail($id);
            
            /**
             * Validasi: Cek apakah ada transaksi denda terkait yang belum lunas
             * Jika ada, penghapusan ditolak untuk menjaga integritas data keuangan
             */
            $transaksiDenda = \App\Models\TransaksiDenda::where('peminjaman_id', $id)->first();
            if ($transaksiDenda && $transaksiDenda->status_pembayaran != 'lunas') {
                throw new \Exception('Tidak dapat menghapus peminjaman yang memiliki denda belum lunas.');
            }
            
            /**
             * Jika peminjaman masih aktif (status approved/aktif dan belum dikembalikan),
             * kembalikan status laptop menjadi tersedia
             */
            if (in_array($peminjaman->status, ['approved', 'aktif']) && !$peminjaman->tanggal_kembali) {
                if ($peminjaman->laptop) {
                    $peminjaman->laptop->update(['status' => 'tersedia']);
                }
            }
            
            // Hapus transaksi denda terkait jika ada
            if ($transaksiDenda) {
                $transaksiDenda->delete();
            }
            
            // Hapus data peminjaman
            $peminjaman->delete();
            
            // Commit transaksi jika semua operasi berhasil
            DB::commit();
            
            return redirect('/petugas/peminjaman')
                ->with('success', 'Data peminjaman berhasil dihapus.');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return redirect('/petugas/peminjaman')
                ->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Membatalkan peminjaman (helper method).
     * 
     * Method ini dapat ditambahkan untuk membatalkan peminjaman
     * tanpa perlu menghapus data.
     * 
     * @param int|string $id ID peminjaman yang akan dibatalkan
     * @return RedirectResponse Redirect ke halaman daftar peminjaman
     */
    public function cancel($id): RedirectResponse
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        try {
            DB::beginTransaction();
            
            $peminjaman = Peminjaman::with('laptop')->findOrFail($id);
            
            // Hanya peminjaman dengan status pending yang bisa dibatalkan
            if ($peminjaman->status !== 'pending') {
                throw new \Exception('Hanya peminjaman dengan status pending yang dapat dibatalkan.');
            }
            
            $peminjaman->update([
                'status' => 'batal',
                'approved_by' => auth()->id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Peminjaman berhasil dibatalkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat peminjaman user tertentu (helper method).
     * 
     * Method ini dapat ditambahkan untuk melihat riwayat peminjaman
     * dari user tertentu.
     * 
     * @param int $userId ID user yang akan dilihat riwayatnya
     * @return View View riwayat peminjaman user
     */
    public function userHistory($userId): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        $peminjaman = Peminjaman::with(['user', 'laptop'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $user = \App\Models\User::findOrFail($userId);
        
        return view('petugas.peminjaman.user-history', compact('peminjaman', 'user'));
    }
}