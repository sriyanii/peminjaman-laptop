<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_alat', 20)->unique();
            $table->string('nama_alat', 100);
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_alat')->onDelete('set null');
            $table->string('merk', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->year('tahun_produksi')->nullable();
            $table->json('spesifikasi')->nullable();
            $table->string('serial_number', 50)->unique()->nullable();
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'maintenance'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'maintenance', 'hapus'])->default('tersedia');
            $table->decimal('harga_sewa_perhari', 12, 2);
            $table->decimal('denda_perhari', 12, 2)->default(0);
            $table->integer('stok')->default(1);
            $table->integer('dipinjam')->default(0);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};