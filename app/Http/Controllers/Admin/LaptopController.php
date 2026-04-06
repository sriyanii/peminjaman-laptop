<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laptop;
use App\Models\Category; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaptopController extends Controller
{
    /**
     * Display a listing of laptops.
     */
    public function index(Request $request)
    {
        $query = Laptop::query();

        // Filter berdasarkan status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter pencarian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('merk', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('processor', 'like', "%{$search}%");
            });
        }

        // Ambil data dengan pagination
        $laptops = $query->orderBy('created_at', 'desc')->paginate(12);

        // Hitung statistik
        $statistics = [
            'total_tools' => Laptop::count(),
            'available_tools' => Laptop::where('status', 'tersedia')->count(),
            'borrowed_tools' => Laptop::where('status', 'dipinjam')->count(),
            'maintenance_tools' => Laptop::where('status', 'maintenance')->count(),
            'broken_tools' => Laptop::where('status', 'rusak')->count(),
        ];

        return view('admin.laptops.index', compact('laptops', 'statistics'));
    }

    /**
     * Show form for creating new laptop.
     */
public function create()
{
    $mode = 'create';
    return view('admin.laptops.form', compact('mode'));
}

    /**
     * Store a newly created laptop.
     */
    public function store(Request $request)
    {
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
            'keterangan' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'lokasi' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:30',
            'os' => 'nullable|string|max:50',
            'baterai_kondisi' => 'nullable|string|max:20',
            'garansi' => 'nullable|string|max:50',
            'garansi_berakhir' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_sewa_harian' => 'nullable|numeric|min:0',
        ]);

        try {
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('laptops', $filename, 'public');
                $validated['gambar'] = 'storage/' . $path;
            }

            Laptop::create($validated);

            return redirect()->route('admin.laptops.index')
                ->with('success', 'Laptop berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan laptop: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display laptop details.
     */
public function show($id)
{
    $laptop = Laptop::findOrFail($id);
    $mode = 'show';
    return view('admin.laptops.form', compact('laptop', 'mode'));
}

    /**
     * Show form for editing laptop.
     */
public function edit($id)
{
    $laptop = Laptop::findOrFail($id);
    $mode = 'edit';
    return view('admin.laptops.form', compact('laptop', 'mode'));
}

    /**
     * Update laptop data.
     */
    public function update(Request $request, $id)
    {
        $laptop = Laptop::findOrFail($id);

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
            'keterangan' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'lokasi' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:30',
            'os' => 'nullable|string|max:50',
            'baterai_kondisi' => 'nullable|string|max:20',
            'garansi' => 'nullable|string|max:50',
            'garansi_berakhir' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_sewa_harian' => 'nullable|numeric|min:0',
            'hapus_gambar' => 'nullable|boolean',
        ]);

        try {
            // Handle hapus gambar
            if ($request->has('hapus_gambar') && $request->hapus_gambar == 1) {
                if ($laptop->gambar && Storage::disk('public')->exists(str_replace('storage/', '', $laptop->gambar))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $laptop->gambar));
                }
                $validated['gambar'] = null;
            }

            // Handle upload gambar baru
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($laptop->gambar && Storage::disk('public')->exists(str_replace('storage/', '', $laptop->gambar))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $laptop->gambar));
                }
                
                $file = $request->file('gambar');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('laptops', $filename, 'public');
                $validated['gambar'] = 'storage/' . $path;
            }

            $laptop->update($validated);

            return redirect()->route('admin.laptops.index')
                ->with('success', 'Laptop berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui laptop: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete laptop.
     */
    public function destroy($id)
    {
        try {
            $laptop = Laptop::findOrFail($id);
            
            // Hapus gambar jika ada
            if ($laptop->gambar && Storage::disk('public')->exists(str_replace('storage/', '', $laptop->gambar))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $laptop->gambar));
            }
            
            $laptop->delete();

            return redirect()->route('admin.laptops.index')
                ->with('success', 'Laptop berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus laptop: ' . $e->getMessage());
        }
    }

    /**
     * Get laptop details for AJAX.
     */
    public function detail($id)
    {
        $laptop = Laptop::with('category')->findOrFail($id);
        return response()->json($laptop);
    }
}