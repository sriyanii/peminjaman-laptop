<?php
/**
 * Laptop Controller (Admin)
 * 
 * Controller ini bertanggung jawab untuk mengelola semua operasi CRUD (Create, Read, Update, Delete)
 * untuk data laptop dalam sistem peminjaman. Meliputi manajemen data laptop, pencarian,
 * filter status, serta validasi sebelum penghapusan.
 * 
 * @package App\Http\Controllers\Admin
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laptop;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LaptopController extends Controller
{
    /**
     * Menampilkan daftar semua laptop dengan filter dan statistik.
     * 
     * Method ini menampilkan data laptop yang dapat difilter berdasarkan:
     * - Pencarian berdasarkan merk, model, processor, atau serial number
     * - Status laptop (tersedia, dipinjam, rusak, maintenance)
     * 
     * Juga menampilkan statistik ringkasan:
     * - Total laptop
     * - Jumlah laptop tersedia
     * - Jumlah laptop dipinjam
     * - Jumlah laptop dalam maintenance/rusak
     * 
     * @param Request $request Objek request yang berisi parameter filter (search, status)
     * @return View View daftar laptop dengan data laptops dan stats
     * 
     * @example
     * GET /admin/tools?search=asus&status=tersedia
     */
    public function index(Request $request): View
    {
        // Inisialisasi query dengan eager loading relasi category
        $query = Laptop::with('category');
        
        // Filter berdasarkan pencarian teks
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('merk', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('processor', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan status laptop
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        // Ambil data dengan urutan terbaru dan pagination 15 item per halaman
        $laptops = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistik ringkasan untuk ditampilkan di dashboard
        $stats = [
            'total_laptops' => Laptop::count(),
            'available_laptops' => Laptop::where('status', 'tersedia')->count(),
            'borrowed_laptops' => Laptop::where('status', 'dipinjam')->count(),
            'maintenance_laptops' => Laptop::whereIn('status', ['rusak', 'maintenance'])->count(),
        ];
        
        return view('admin.tools.index', compact('laptops', 'stats'));
    }

    /**
     * Menampilkan form untuk membuat laptop baru.
     * 
     * Method ini menyiapkan data yang diperlukan untuk form pembuatan laptop:
     * - Daftar kategori laptop yang diurutkan berdasarkan nama
     * - Mode 'create' untuk menentukan aksi form
     * 
     * @return View View form laptop dengan data categories dan mode create
     */
    public function create(): View
    {
        // Ambil semua kategori untuk dropdown pilihan kategori
        $categories = KategoriAlat::orderBy('nama_kategori')->get();
        $mode = 'create';
        
        return view('admin.tools.form', compact('categories', 'mode'));
    }

    /**
     * Menyimpan data laptop baru ke database.
     * 
     * Method ini melakukan validasi data laptop, menghasilkan kode laptop otomatis
     * jika tidak diisi, dan menyimpan data ke database. Semua operasi dilakukan
     * dalam transaksi database untuk menjaga konsistensi data.
     * 
     * @param Request $request Objek request yang berisi data laptop
     * @return RedirectResponse Redirect ke halaman daftar laptop dengan pesan sukses/error
     * 
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     * @throws \Exception Jika terjadi error saat menyimpan data
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi semua field yang diperlukan
        $validated = $request->validate([
            'merk' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'processor' => 'required|string|max:100',
            'ram' => 'required|string|max:20',
            'storage' => 'required|string|max:50',
            'serial_number' => 'required|string|unique:laptops',
            'status' => 'required|in:tersedia,dipinjam,rusak,maintenance',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'tahun_pembelian' => 'required|integer|min:2000|max:' . date('Y'),
            'category_id' => 'nullable|exists:kategori_alat,id',
            'lokasi' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:30',
            'os' => 'nullable|string|max:50',
            'baterai_kondisi' => 'nullable|string|max:20',
            'garansi' => 'nullable|string|max:50',
            'garansi_berakhir' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_sewa_harian' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Generate kode laptop otomatis jika tidak diisi
            if (empty($validated['kode_alat'])) {
                $lastLaptop = Laptop::orderBy('id', 'desc')->first();
                $nextId = $lastLaptop ? $lastLaptop->id + 1 : 1;
                $validated['kode_alat'] = 'LTP-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            // Simpan data laptop baru
            $laptop = Laptop::create($validated);

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return redirect()->route('admin.tools.index')
                ->with('success', 'Laptop berhasil ditambahkan.');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return back()->with('error', 'Gagal menambah laptop: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail laptop berdasarkan ID.
     * 
     * Method ini menampilkan informasi lengkap laptop beserta relasi kategorinya.
     * 
     * @param int|string $id ID laptop yang akan ditampilkan
     * @return View View detail laptop dengan data laptop
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function show($id): View
    {
        // Ambil data laptop dengan relasi kategori
        $laptop = Laptop::with('category')->findOrFail($id);
        
        return view('admin.tools.show', compact('laptop'));
    }

    /**
     * Menampilkan form untuk mengedit laptop.
     * 
     * Method ini menampilkan form edit dengan data laptop yang sudah ada
     * dan daftar kategori untuk dipilih.
     * 
     * @param int|string $id ID laptop yang akan diedit
     * @return View View form laptop dengan data laptop, categories, dan mode edit
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function edit($id): View
    {
        // Ambil data laptop yang akan diedit
        $laptop = Laptop::findOrFail($id);
        
        // Ambil semua kategori untuk dropdown
        $categories = KategoriAlat::orderBy('nama_kategori')->get();
        $mode = 'edit';
        
        return view('admin.tools.form', compact('laptop', 'categories', 'mode'));
    }

    /**
     * Mengupdate data laptop yang sudah ada.
     * 
     * Method ini melakukan update data laptop dengan validasi yang memastikan
     * serial_number unik kecuali untuk laptop yang sedang diupdate (ignore on update).
     * Operasi dilakukan dalam transaksi database.
     * 
     * @param Request $request Objek request yang berisi data laptop yang diupdate
     * @param int|string $id ID laptop yang akan diupdate
     * @return RedirectResponse Redirect ke halaman daftar laptop dengan pesan sukses/error
     * 
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     * @throws \Exception Jika terjadi error saat update data
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Ambil data laptop yang akan diupdate
        $laptop = Laptop::findOrFail($id);

        // Validasi data dengan aturan unique yang mengabaikan ID saat ini
        $validated = $request->validate([
            'merk' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'processor' => 'required|string|max:100',
            'ram' => 'required|string|max:20',
            'storage' => 'required|string|max:50',
            'serial_number' => 'required|string|unique:laptops,serial_number,' . $id,
            'status' => 'required|in:tersedia,dipinjam,rusak,maintenance',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'tahun_pembelian' => 'required|integer|min:2000|max:' . date('Y'),
            'category_id' => 'nullable|exists:kategori_alat,id',
            'lokasi' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:30',
            'os' => 'nullable|string|max:50',
            'baterai_kondisi' => 'nullable|string|max:20',
            'garansi' => 'nullable|string|max:50',
            'garansi_berakhir' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_sewa_harian' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Update data laptop
            $laptop->update($validated);

            // Commit transaksi
            DB::commit();

            return redirect()->route('admin.tools.index')
                ->with('success', 'Laptop berhasil diperbarui.');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui laptop: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus laptop dari database.
     * 
     * Method ini menghapus laptop setelah memastikan bahwa laptop tersebut
     * tidak sedang dalam status dipinjam. Jika laptop masih aktif dipinjam,
     * penghapusan akan ditolak untuk menjaga integritas data.
     * 
     * @param int|string $id ID laptop yang akan dihapus
     * @return RedirectResponse Redirect ke halaman daftar laptop dengan pesan sukses/error
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     * @throws \Exception Jika laptop sedang dipinjam
     */
    public function destroy($id): RedirectResponse
    {
        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Ambil data laptop yang akan dihapus
            $laptop = Laptop::findOrFail($id);
            
            // Cek apakah laptop sedang dipinjam (status aktif dan belum dikembalikan)
            $activeBorrowing = \App\Models\Peminjaman::where('laptop_id', $id)
                ->whereIn('status', ['approved', 'aktif'])
                ->whereNull('tanggal_kembali')
                ->exists();
                
            if ($activeBorrowing) {
                throw new \Exception('Tidak dapat menghapus laptop yang sedang dipinjam.');
            }

            // Hapus data laptop
            $laptop->delete();

            // Commit transaksi
            DB::commit();

            return redirect()->route('admin.tools.index')
                ->with('success', 'Laptop berhasil dihapus.');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus laptop: ' . $e->getMessage());
        }
    }

    /**
     * Mengubah status laptop secara cepat (helper method).
     * 
     * Method ini dapat ditambahkan untuk memudahkan perubahan status laptop
     * tanpa harus membuka form edit lengkap.
     * 
     * @param Request $request Objek request berisi status baru
     * @param int|string $id ID laptop yang akan diubah statusnya
     * @return RedirectResponse Redirect ke halaman sebelumnya dengan pesan sukses/error
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        // Validasi status baru
        $request->validate([
            'status' => 'required|in:tersedia,dipinjam,rusak,maintenance'
        ]);

        try {
            DB::beginTransaction();

            $laptop = Laptop::findOrFail($id);
            
            // Jika status diubah dari dipinjam ke tersedia, pastikan tidak ada peminjaman aktif
            if ($laptop->status == 'dipinjam' && $request->status == 'tersedia') {
                $activeBorrowing = \App\Models\Peminjaman::where('laptop_id', $id)
                    ->whereIn('status', ['approved', 'aktif'])
                    ->whereNull('tanggal_kembali')
                    ->exists();
                    
                if ($activeBorrowing) {
                    throw new \Exception('Tidak dapat mengubah status ke tersedia karena laptop sedang dipinjam.');
                }
            }
            
            $laptop->update(['status' => $request->status]);
            
            DB::commit();
            
            return back()->with('success', 'Status laptop berhasil diperbarui menjadi ' . $request->status);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat peminjaman laptop (helper method).
     * 
     * Method ini dapat ditambahkan untuk menampilkan riwayat peminjaman
     * dari laptop tertentu.
     * 
     * @param int|string $id ID laptop yang akan dilihat riwayatnya
     * @return View View riwayat peminjaman laptop
     */
    public function history($id): View
    {
        $laptop = Laptop::with(['peminjaman.user'])->findOrFail($id);
        $peminjaman = $laptop->peminjaman()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.tools.history', compact('laptop', 'peminjaman'));
    }
}