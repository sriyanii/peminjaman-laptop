<?php
/**
 * Category Controller (Admin)
 * 
 * Controller ini bertanggung jawab untuk mengelola operasi CRUD (Create, Read, Update, Delete)
 * untuk data kategori laptop. Kategori digunakan untuk mengelompokkan laptop berdasarkan
 * jenis, merk, atau klasifikasi tertentu.
 * 
 * @package App\Http\Controllers\Admin
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     * 
     * Method ini mengambil semua data kategori dari database dengan urutan
     * terbaru (berdasarkan created_at) dan menampilkannya dalam bentuk pagination.
     * 
     * @param Request $request Objek request (opsional, dapat digunakan untuk filter)
     * @return View View daftar kategori dengan data categories
     * 
     * @example
     * GET /admin/categories
     */
    public function index(Request $request): View
    {
        // Ambil semua kategori dengan urutan terbaru, pagination 10 data per halaman
        $categories = Category::latest()->paginate(10);
        
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menampilkan form untuk membuat kategori baru.
     * 
     * Method ini menampilkan form kosong untuk membuat kategori baru
     * dengan mode 'create' yang akan digunakan oleh view untuk menentukan
     * aksi form dan judul halaman.
     * 
     * @return View View form kategori dengan mode create
     */
    public function create(): View
    {
        return view('admin.categories.form', ['mode' => 'create']);
    }

    /**
     * Menyimpan data kategori baru ke database.
     * 
     * Method ini melakukan validasi data kategori dan menyimpannya ke database.
     * Validasi memastikan kode_kategori unik karena digunakan sebagai identifier.
     * 
     * @param Request $request Objek request yang berisi data kategori
     * @return RedirectResponse Redirect ke halaman daftar kategori dengan pesan sukses
     * 
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi data input kategori
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:10|unique:kategori_alat',
            'nama_kategori' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
        ]);

        // Simpan data kategori baru
        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail kategori beserta daftar laptop yang terkait.
     * 
     * Method ini menampilkan informasi detail dari kategori tertentu termasuk
     * jumlah laptop yang menggunakan kategori ini dan 5 laptop terbaru
     * yang termasuk dalam kategori tersebut.
     * 
     * @param int|string $id ID kategori yang akan ditampilkan
     * @return View View form kategori dengan mode show dan data kategori
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function show($id): View
    {
        // Ambil data kategori dengan jumlah laptop terkait
        // dan ambil 5 laptop terbaru dari kategori ini
        $category = Category::withCount('laptops')  // Ganti 'tools' dengan 'laptops'
            ->with(['laptops' => function($query) {  // Ganti 'tools' dengan 'laptops'
                $query->limit(5)->orderBy('nama_laptop');  // Ganti 'nama_alat' dengan 'nama_laptop'
            }])
            ->findOrFail($id);
        
        return view('admin.categories.form', [
            'mode' => 'show',
            'category' => $category
        ]);
    }

    /**
     * Menampilkan form untuk mengedit kategori.
     * 
     * Method ini menampilkan form edit dengan data kategori yang sudah ada
     * untuk dapat dimodifikasi oleh admin.
     * 
     * @param int|string $id ID kategori yang akan diedit
     * @return View View form kategori dengan mode edit dan data kategori
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function edit($id): View
    {
        // Ambil data kategori yang akan diedit
        $category = Category::findOrFail($id);
        
        return view('admin.categories.form', [
            'mode' => 'edit',
            'category' => $category
        ]);
    }

    /**
     * Mengupdate data kategori yang sudah ada.
     * 
     * Method ini melakukan update data kategori dengan validasi yang memastikan
     * kode_kategori unik kecuali untuk kategori yang sedang diupdate (ignore on update).
     * 
     * @param Request $request Objek request yang berisi data kategori yang diupdate
     * @param int|string $id ID kategori yang akan diupdate
     * @return RedirectResponse Redirect ke halaman daftar kategori dengan pesan sukses
     * 
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Validasi data dengan aturan unique yang mengabaikan ID saat ini
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:10|unique:kategori_alat,kode_kategori,' . $id,
            'nama_kategori' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
        ]);

        // Ambil dan update data kategori
        $category = Category::findOrFail($id);
        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori dari database.
     * 
     * Method ini menghapus kategori setelah memastikan bahwa kategori tersebut
     * tidak sedang digunakan oleh laptop apapun. Jika masih ada laptop yang
     * menggunakan kategori ini, penghapusan akan ditolak.
     * 
     * @param int|string $id ID kategori yang akan dihapus
     * @return RedirectResponse Redirect ke halaman daftar kategori dengan pesan sukses/error
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function destroy($id): RedirectResponse
    {
        // Ambil data kategori dengan jumlah laptop yang menggunakan kategori ini
        $category = Category::withCount('laptops')->findOrFail($id);
        
        // Cek apakah kategori sedang digunakan oleh laptop
        if ($category->laptops_count > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $category->laptops_count . ' laptop.');
        }
        
        // Hapus kategori jika tidak digunakan
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}