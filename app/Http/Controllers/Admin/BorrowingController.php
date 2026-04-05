<?php
/**
 * Borrowing Controller (Admin)
 * 
 * Controller ini bertanggung jawab untuk mengelola semua operasi terkait peminjaman laptop
 * di sisi administrator, termasuk menampilkan, membuat, mengupdate, menyetujui, dan menolak peminjaman.
 * 
 * @package App\Http\Controllers\Admin
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\User;
use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BorrowingController extends Controller
{
    /**
     * Menampilkan daftar semua peminjaman dengan filter pencarian dan status.
     * 
     * Method ini menampilkan data peminjaman yang dapat difilter berdasarkan:
     * - Pencarian berdasarkan nama user, email, atau detail laptop
     * - Status peminjaman (pending, approved, returned, etc)
     * Data ditampilkan dalam bentuk pagination 15 item per halaman.
     * 
     * @param Request $request Objek request yang berisi parameter filter (search, status)
     * @return View View daftar peminjaman dengan data borrowings
     * 
     * @example
     * GET /admin/borrowings?search=john&status=approved
     */
    public function index(Request $request): View
    {
        // Inisialisasi query dengan eager loading relasi user dan laptop
        $query = Borrowing::with(['user', 'laptop']);

        // Filter berdasarkan pencarian
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Pencarian berdasarkan data user
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                // Pencarian berdasarkan data laptop
                ->orWhereHas('laptop', function($laptopQuery) use ($search) {
                    $laptopQuery->where('merk', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%")
                                ->orWhere('serial_number', 'like', "%{$search}%");
                });
            });
        }

        // Filter berdasarkan status peminjaman
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Ambil data dengan urutan terbaru dan pagination
        $borrowings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.borrowings.index', compact('borrowings'));
    }

    /**
     * Menampilkan form untuk membuat peminjaman baru.
     * 
     * Method ini menyiapkan data yang diperlukan untuk form peminjaman:
     * - Daftar user dengan role 'user' yang diurutkan berdasarkan nama
     * - Daftar laptop yang tersedia (status 'tersedia') untuk dipinjam
     * 
     * @return View View form peminjaman dengan data users dan laptops
     */
    public function create(): View
    {
        // Ambil semua user dengan role 'user'
        $users = User::where('role', 'user')->orderBy('name')->get();
        
        // Ambil semua laptop yang tersedia untuk dipinjam
        $laptops = Laptop::where('status', 'tersedia')
            ->orderBy('merk')
            ->orderBy('model')
            ->get();

        return view('admin.borrowings.form', compact('users', 'laptops'));
    }

    /**
     * Menyimpan data peminjaman baru ke database.
     * 
     * Method ini melakukan validasi data, mengecek ketersediaan laptop,
     * mengupdate status laptop menjadi 'dipinjam', dan membuat record peminjaman baru.
     * Semua operasi dilakukan dalam transaksi database untuk menjaga konsistensi data.
     * 
     * @param Request $request Objek request yang berisi data peminjaman
     * @return RedirectResponse Redirect ke halaman daftar peminjaman dengan pesan sukses/error
     * 
     * @throws \Exception Jika laptop tidak tersedia
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi data input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'laptop_id' => 'required|exists:laptops,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'tujuan' => 'required|in:meeting,presentasi,training,work_from_home,proyek,lainnya',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Cek ketersediaan laptop
            $laptop = Laptop::where('id', $validated['laptop_id'])
                ->where('status', 'tersedia')
                ->first();

            if (!$laptop) {
                throw new \Exception('Laptop tidak tersedia.');
            }

            // Update status laptop menjadi dipinjam
            $laptop->update(['status' => 'dipinjam']);

            // Buat record peminjaman baru dengan status approved
            $borrowing = Borrowing::create(array_merge($validated, [
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'waktu_approve' => now(),
            ]));

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return redirect()->route('admin.borrowings.index')
                ->with('success', 'Peminjaman berhasil dibuat dan disetujui.');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail peminjaman berdasarkan ID.
     * 
     * Method ini menampilkan informasi lengkap peminjaman beserta relasi
     * user, laptop, dan approver (admin yang menyetujui).
     * 
     * @param int|string $id ID peminjaman yang akan ditampilkan
     * @return View View detail peminjaman dengan data borrowing
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function show($id): View
    {
        // Ambil data peminjaman dengan relasi yang diperlukan
        $borrowing = Borrowing::with(['user', 'laptop', 'approver'])
            ->findOrFail($id);

        return view('admin.borrowings.show', compact('borrowing'));
    }

    /**
     * Menampilkan form edit peminjaman.
     * 
     * Method ini menampilkan form untuk mengedit data peminjaman yang sudah ada.
     * Data user dan laptop ditampilkan untuk dipilih kembali.
     * 
     * @param int|string $id ID peminjaman yang akan diedit
     * @return View View form peminjaman dengan data borrowing, users, dan laptops
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function edit($id): View
    {
        // Ambil data peminjaman yang akan diedit
        $borrowing = Borrowing::findOrFail($id);
        
        // Ambil semua user dengan role 'user'
        $users = User::where('role', 'user')->orderBy('name')->get();
        
        // Ambil semua laptop (termasuk yang sedang dipinjam)
        $laptops = Laptop::orderBy('merk')
            ->orderBy('model')
            ->get();

        return view('admin.borrowings.form', compact('borrowing', 'users', 'laptops'));
    }

    /**
     * Mengupdate data peminjaman yang sudah ada.
     * 
     * Method ini melakukan update data peminjaman dengan penanganan khusus
     * jika terjadi perubahan laptop yang dipinjam. Status laptop lama akan
     * dikembalikan menjadi 'tersedia' dan laptop baru akan diupdate menjadi 'dipinjam'.
     * Operasi dilakukan dalam transaksi database.
     * 
     * @param Request $request Objek request yang berisi data peminjaman yang diupdate
     * @param int|string $id ID peminjaman yang akan diupdate
     * @return RedirectResponse Redirect ke halaman daftar peminjaman dengan pesan sukses/error
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Ambil data peminjaman yang akan diupdate
        $borrowing = Borrowing::findOrFail($id);

        // Validasi data input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'laptop_id' => 'required|exists:laptops,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'tujuan' => 'required|in:meeting,presentasi,training,work_from_home,proyek,lainnya',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Jika laptop yang dipinjam berubah
            if ($borrowing->laptop_id != $validated['laptop_id']) {
                // Kembalikan status laptop lama menjadi tersedia
                $oldLaptop = Laptop::find($borrowing->laptop_id);
                if ($oldLaptop) {
                    $oldLaptop->update(['status' => 'tersedia']);
                }

                // Update status laptop baru menjadi dipinjam
                $newLaptop = Laptop::find($validated['laptop_id']);
                if ($newLaptop) {
                    $newLaptop->update(['status' => 'dipinjam']);
                }
            }

            // Update data peminjaman
            $borrowing->update($validated);

            // Commit transaksi
            DB::commit();

            return redirect()->route('admin.borrowings.index')
                ->with('success', 'Peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menyetujui peminjaman yang masih pending.
     * 
     * Method ini mengubah status peminjaman dari 'pending' menjadi 'approved',
     * mengupdate status laptop menjadi 'dipinjam', dan mencatat admin yang menyetujui
     * beserta waktu persetujuan. Operasi dilakukan dalam transaksi database.
     * 
     * @param int|string $id ID peminjaman yang akan disetujui
     * @return RedirectResponse Redirect kembali ke halaman sebelumnya dengan pesan sukses/error
     * 
     * @throws \Exception Jika status peminjaman bukan 'pending'
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function approve($id): RedirectResponse
    {
        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Ambil data peminjaman dengan relasi laptop
            $borrowing = Borrowing::with('laptop')->findOrFail($id);

            // Validasi status peminjaman
            if ($borrowing->status !== 'pending') {
                throw new \Exception('Status peminjaman tidak valid untuk disetujui.');
            }

            // Update status laptop menjadi dipinjam
            if ($borrowing->laptop) {
                $borrowing->laptop->update(['status' => 'dipinjam']);
            }

            // Update status peminjaman menjadi approved
            $borrowing->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'waktu_approve' => now(),
            ]);

            // Commit transaksi
            DB::commit();

            return redirect()->back()->with('success', 'Peminjaman disetujui.');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menolak peminjaman yang masih pending.
     * 
     * Method ini mengubah status peminjaman menjadi 'ditolak' dan
     * mencatat alasan penolakan yang diberikan oleh admin.
     * 
     * @param Request $request Objek request yang berisi alasan penolakan
     * @param int|string $id ID peminjaman yang akan ditolak
     * @return RedirectResponse Redirect kembali ke halaman sebelumnya dengan pesan sukses/error
     * 
     * @throws \Exception Jika status peminjaman bukan 'pending'
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function reject(Request $request, $id): RedirectResponse
    {
        // Validasi alasan penolakan
        $request->validate([
            'alasan_ditolak' => 'required|string|max:500',
        ]);

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Ambil data peminjaman
            $borrowing = Borrowing::findOrFail($id);

            // Validasi status peminjaman
            if ($borrowing->status !== 'pending') {
                throw new \Exception('Status peminjaman tidak valid untuk ditolak.');
            }

            // Update status peminjaman menjadi ditolak dengan alasan
            $borrowing->update([
                'status' => 'ditolak',
                'alasan_ditolak' => $request->alasan_ditolak,
            ]);

            // Commit transaksi
            DB::commit();

            return redirect()->back()->with('success', 'Peminjaman ditolak.');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}