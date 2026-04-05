<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan kategori sudah ada
        $kategoriGaming = KategoriAlat::where('kode_kategori', 'LP01')->first();
        $kategoriUltrabook = KategoriAlat::where('kode_kategori', 'LP03')->first();
        $kategoriBisnis = KategoriAlat::where('kode_kategori', 'LP02')->first();

        // Jika kategori tidak ada, buat dulu
        if (!$kategoriGaming) {
            $kategoriGaming = KategoriAlat::create([
                'kode_kategori' => 'LP01',
                'nama_kategori' => 'Laptop Gaming'
            ]);
        }
        
        if (!$kategoriUltrabook) {
            $kategoriUltrabook = KategoriAlat::create([
                'kode_kategori' => 'LP03',
                'nama_kategori' => 'Laptop Ultrabook'
            ]);
        }
        
        if (!$kategoriBisnis) {
            $kategoriBisnis = KategoriAlat::create([
                'kode_kategori' => 'LP02',
                'nama_kategori' => 'Laptop Bisnis'
            ]);
        }

        $laptops = [
            [
                'kode_alat' => 'ASUS-ROG-001',
                'nama_alat' => 'ASUS ROG Zephyrus G14',
                'kategori_id' => $kategoriGaming->id,
                'merk' => 'ASUS',
                'model' => 'ROG Zephyrus G14',
                'tahun_produksi' => 2023,
                'spesifikasi' => json_encode([
                    'processor' => 'AMD Ryzen 9 7940HS',
                    'ram' => '32GB DDR5',
                    'storage' => '1TB NVMe SSD',
                    'gpu' => 'NVIDIA RTX 4060 8GB',
                    'display' => '14-inch QHD+ 165Hz'
                ]),
                'serial_number' => 'SN-ASUS-001',
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'harga_sewa_perhari' => 150000,
                'denda_perhari' => 50000,
                'stok' => 3,
                'dipinjam' => 0,
            ],
            [
                'kode_alat' => 'DELL-XPS-001',
                'nama_alat' => 'Dell XPS 13',
                'kategori_id' => $kategoriUltrabook->id,
                'merk' => 'Dell',
                'model' => 'XPS 13',
                'tahun_produksi' => 2023,
                'spesifikasi' => json_encode([
                    'processor' => 'Intel Core i7-1360P',
                    'ram' => '16GB LPDDR5',
                    'storage' => '512GB SSD',
                    'gpu' => 'Intel Iris Xe',
                    'display' => '13.4-inch FHD+'
                ]),
                'serial_number' => 'SN-DELL-001',
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'harga_sewa_perhari' => 120000,
                'denda_perhari' => 40000,
                'stok' => 2,
                'dipinjam' => 0,
            ],
            [
                'kode_alat' => 'LENOVO-THINK-001',
                'nama_alat' => 'Lenovo ThinkPad X1 Carbon',
                'kategori_id' => $kategoriBisnis->id,
                'merk' => 'Lenovo',
                'model' => 'ThinkPad X1 Carbon',
                'tahun_produksi' => 2023,
                'spesifikasi' => json_encode([
                    'processor' => 'Intel Core i7-1365U',
                    'ram' => '16GB LPDDR5',
                    'storage' => '1TB SSD',
                    'gpu' => 'Intel Iris Xe',
                    'display' => '14-inch WUXGA'
                ]),
                'serial_number' => 'SN-LENOVO-001',
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'harga_sewa_perhari' => 130000,
                'denda_perhari' => 45000,
                'stok' => 4,
                'dipinjam' => 0,
            ],
        ];

        foreach ($laptops as $laptop) {
            Alat::firstOrCreate(
                ['kode_alat' => $laptop['kode_alat']],
                $laptop
            );
        }
        
        echo "✅ Alat/laptop seeded successfully!\n";
    }
}