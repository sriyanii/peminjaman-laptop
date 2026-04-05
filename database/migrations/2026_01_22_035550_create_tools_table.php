<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah tabel sudah ada
        if (!Schema::hasTable('tools')) {
            Schema::create('tools', function (Blueprint $table) {
                $table->id();
                $table->string('kode_alat')->unique();
                $table->string('nama_alat');
                $table->string('merk');
                $table->string('type');
                $table->string('serial_number')->nullable();
                $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
                $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'maintenance'])->default('tersedia');
                $table->enum('kondisi', ['baik', 'sedang', 'buruk'])->default('baik');
                $table->string('lokasi')->nullable();
                $table->date('tanggal_pembelian')->nullable();
                $table->decimal('harga', 15, 2)->nullable();
                $table->string('masa_garansi')->nullable();
                $table->text('spesifikasi')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};