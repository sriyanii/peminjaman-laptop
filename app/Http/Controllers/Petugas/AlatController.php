<?php
/**
 * Alat/Laptop Controller (Petugas)
 * 
 * Controller ini bertanggung jawab untuk mengelola operasi CRUD (Create, Read, Update, Delete)
 * data laptop dari sisi petugas. Petugas memiliki akses terbatas dibandingkan admin,
 * dengan otorisasi role 'petugas' yang diterapkan di setiap method.
 * 
 * @package App\Http\Controllers\Petugas
 * @author Your Name
 * @since 1.0.0
 */

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    /**
     * Menampilkan daftar laptop dengan filter dan statistik (untuk petugas).
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
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param Request $request Objek request yang berisi parameter filter (search, status)
     * @return View View daftar laptop dengan data laptops dan statistics
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * 
     * @example
     * GET /petugas/alat?search=asus&status=tersedia
     */
/**
 * Menampilkan daftar laptop dengan filter dan statistik (untuk petugas).
 */
public function index(Request $request): View
{
    // Otorisasi: Hanya petugas yang dapat mengakses
    if (auth()->user()->role !== 'petugas') {
        abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
    }

    // Inisialisasi query untuk laptop
    $query = Laptop::query();
    
    // Filter berdasarkan pencarian teks
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('merk', 'like', '%' . $search . '%')
              ->orWhere('model', 'like', '%' . $search . '%')
              ->orWhere('processor', 'like', '%' . $search . '%')
              ->orWhere('serial_number', 'like', '%' . $search . '%');
        });
    }
    
    // Filter berdasarkan status laptop
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    // TAMBAHKAN: Filter berdasarkan kondisi laptop (baik, rusak_ringan, rusak_berat)
    if ($request->filled('kondisi')) {
        $query->where('kondisi', $request->kondisi);
    }
    
    // Statistik ringkasan untuk dashboard petugas
    $statistics = [
        'total_tools' => Laptop::count(),
        'available_tools' => Laptop::where('status', 'tersedia')->count(),
        'borrowed_tools' => Laptop::where('status', 'dipinjam')->count(),
        'maintenance_tools' => Laptop::whereIn('status', ['rusak', 'maintenance'])->count(),
        // TAMBAHKAN: Statistik berdasarkan kondisi
        'good_condition' => Laptop::where('kondisi', 'baik')->count(),
        'light_damage' => Laptop::where('kondisi', 'rusak_ringan')->count(),
        'heavy_damage' => Laptop::where('kondisi', 'rusak_berat')->count(),
    ];
    
    // Ambil data dengan urutan terbaru dan pagination 10 item per halaman
    $laptops = $query->orderBy('created_at', 'desc')->paginate(10);
    
    return view('petugas.alat.index', compact('laptops', 'statistics'));
}

    /**
     * Menampilkan form untuk membuat laptop baru (petugas).
     * 
     * Method ini menampilkan form kosong untuk membuat laptop baru
     * dengan mode 'create' yang akan digunakan oleh view.
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @return View View form laptop dengan mode create
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     */
    public function create(): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }
        
        $mode = 'create';
        return view('petugas.alat.form', compact('mode'));
    }

    /**
     * Menyimpan data laptop baru ke database (petugas).
     * 
     * Method ini melakukan validasi data laptop, menangani upload gambar,
     * dan menyimpan data ke database. Gambar akan disimpan di folder public/images.
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param Request $request Objek request yang berisi data laptop
     * @return RedirectResponse Redirect ke halaman daftar laptop dengan pesan sukses
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     */
    public function store(Request $request): RedirectResponse
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

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
            'lokasi' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:30',
            'os' => 'nullable|string|max:50',
            'baterai_kondisi' => 'nullable|string|max:20',
            'garansi' => 'nullable|string|max:50',
            'garansi_berakhir' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_sewa_harian' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Siapkan data untuk disimpan (exclude gambar dari mass assignment dulu)
        $data = $validated;
        unset($data['gambar']); // Hapus gambar dari data untuk diproses terpisah
        
        // Handle upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Tentukan path penyimpanan di folder public/images
            $path = public_path('images');
            
            // Buat folder jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Pindahkan file ke folder tujuan
            $file->move($path, $filename);
            
            // Simpan path relatif untuk database
            $data['gambar'] = 'images/' . $filename;
        }
        
        // Simpan data laptop ke database
        Laptop::create($data);

        return redirect()->route('petugas.alat.index')
            ->with('success', 'Data laptop berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail laptop berdasarkan ID (petugas).
     * 
     * Method ini menampilkan informasi lengkap laptop dalam mode 'show'
     * yang bersifat read-only.
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param int|string $id ID laptop yang akan ditampilkan
     * @return View View form laptop dengan mode show dan data laptop
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function show($id): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        // Ambil data laptop berdasarkan ID
        $laptop = Laptop::findOrFail($id);
        $mode = 'show';
        
        return view('petugas.alat.form', compact('laptop', 'mode'));
    }

    /**
     * Menampilkan form untuk mengedit laptop (petugas).
     * 
     * Method ini menampilkan form edit dengan data laptop yang sudah ada
     * untuk dapat dimodifikasi oleh petugas.
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param int|string $id ID laptop yang akan diedit
     * @return View View form laptop dengan mode edit dan data laptop
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function edit($id): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        // Ambil data laptop yang akan diedit
        $laptop = Laptop::findOrFail($id);
        $mode = 'edit';
        
        return view('petugas.alat.form', compact('laptop', 'mode'));
    }

    /**
     * Mengupdate data laptop yang sudah ada (petugas).
     * 
     * Method ini melakukan update data laptop dengan validasi yang memastikan
     * serial_number unik kecuali untuk laptop yang sedang diupdate.
     * Juga menangani update gambar (menghapus gambar lama jika ada dan mengupload yang baru).
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param Request $request Objek request yang berisi data laptop yang diupdate
     * @param int|string $id ID laptop yang akan diupdate
     * @return RedirectResponse Redirect ke halaman daftar laptop dengan pesan sukses
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

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
            'lokasi' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:30',
            'os' => 'nullable|string|max:50',
            'baterai_kondisi' => 'nullable|string|max:20',
            'garansi' => 'nullable|string|max:50',
            'garansi_berakhir' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_sewa_harian' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Siapkan data untuk diupdate (exclude gambar dari mass assignment dulu)
        $data = $validated;
        unset($data['gambar']);
        
        // Handle upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($laptop->gambar && file_exists(public_path($laptop->gambar))) {
                unlink(public_path($laptop->gambar));
            }
            
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Tentukan path penyimpanan di folder public/images
            $path = public_path('images');
            
            // Buat folder jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Pindahkan file ke folder tujuan
            $file->move($path, $filename);
            
            // Simpan path relatif untuk database
            $data['gambar'] = 'images/' . $filename;
        }
        
        // Update data laptop
        $laptop->update($data);

        return redirect()->route('petugas.alat.index')
            ->with('success', 'Data laptop berhasil diperbarui.');
    }

    /**
     * Menghapus laptop dari database (petugas).
     * 
     * Method ini menghapus laptop setelah menghapus file gambar yang terkait
     * untuk membersihkan storage.
     * 
     * Method ini hanya dapat diakses oleh user dengan role 'petugas'.
     * 
     * @param int|string $id ID laptop yang akan dihapus
     * @return RedirectResponse Redirect ke halaman daftar laptop dengan pesan sukses
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Jika role bukan petugas (403)
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function destroy($id): RedirectResponse
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        // Ambil data laptop yang akan dihapus
        $laptop = Laptop::findOrFail($id);
        
        // Hapus file gambar jika ada untuk membersihkan storage
        if ($laptop->gambar && file_exists(public_path($laptop->gambar))) {
            unlink(public_path($laptop->gambar));
        }
        
        // Hapus data laptop dari database
        $laptop->delete();

        return redirect()->route('petugas.alat.index')
            ->with('success', 'Data laptop berhasil dihapus.');
    }

    /**
     * Menampilkan statistik laptop (helper method).
     * 
     * Method ini dapat ditambahkan untuk menampilkan dashboard statistik
     * khusus petugas tentang kondisi laptop.
     * 
     * @return View View statistik laptop
     */
    public function statistics(): View
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        $statistics = [
            'total' => Laptop::count(),
            'by_status' => [
                'tersedia' => Laptop::where('status', 'tersedia')->count(),
                'dipinjam' => Laptop::where('status', 'dipinjam')->count(),
                'rusak' => Laptop::where('status', 'rusak')->count(),
                'maintenance' => Laptop::where('status', 'maintenance')->count(),
            ],
            'by_kondisi' => [
                'baik' => Laptop::where('kondisi', 'baik')->count(),
                'rusak_ringan' => Laptop::where('kondisi', 'rusak_ringan')->count(),
                'rusak_berat' => Laptop::where('kondisi', 'rusak_berat')->count(),
            ],
            'by_tahun' => Laptop::selectRaw('tahun_pembelian, count(*) as total')
                ->groupBy('tahun_pembelian')
                ->orderBy('tahun_pembelian', 'desc')
                ->get(),
        ];

        return view('petugas.alat.statistics', compact('statistics'));
    }

    /**
     * Mengekspor data laptop ke format Excel/CSV (helper method).
     * 
     * Method ini dapat ditambahkan untuk mengekspor data laptop
     * yang dapat diunduh oleh petugas.
     * 
     * @param Request $request Parameter filter untuk ekspor
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Response file export
     */
    public function export(Request $request)
    {
        // Otorisasi: Hanya petugas yang dapat mengakses
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'Akses ditolak. Hanya petugas yang diizinkan.');
        }

        // Ambil data berdasarkan filter
        $query = Laptop::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('merk', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%');
            });
        }
        
        $laptops = $query->orderBy('created_at', 'desc')->get();
        
        // Logic untuk export ke Excel/CSV
        // Contoh menggunakan library Excel atau CSV
        
        return back()->with('info', 'Fitur export akan segera tersedia.');
    }

        /**
     * Get laptop detail via AJAX for petugas
     */
    public function getDetail($id): JsonResponse
    {
        try {
            $laptop = Laptop::findOrFail($id);
            return response()->json($laptop);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }
}