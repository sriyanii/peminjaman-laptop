<?php

namespace Database\Seeders;

use App\Models\KategoriAlat;
use Illuminate\Database\Seeder;

class KategoriAlatSeeder extends Seeder
{
    public function run(): void
    {
        $kategories = [
            ['kode_kategori' => 'LP01', 'nama_kategori' => 'Laptop Gaming', 'deskripsi' => 'Laptop untuk gaming'],
            ['kode_kategori' => 'LP02', 'nama_kategori' => 'Laptop Bisnis', 'deskripsi' => 'Laptop untuk keperluan bisnis'],
            ['kode_kategori' => 'LP03', 'nama_kategori' => 'Laptop Ultrabook', 'deskripsi' => 'Laptop tipis dan ringan'],
            ['kode_kategori' => 'LP04', 'nama_kategori' => 'Laptop 2-in-1', 'deskripsi' => 'Laptop convertible'],
        ];

        foreach ($kategories as $kategori) {
            KategoriAlat::firstOrCreate(
                ['kode_kategori' => $kategori['kode_kategori']],
                $kategori
            );
        }
        
        echo "✅ Kategori alat seeded successfully!\n";
    }
}