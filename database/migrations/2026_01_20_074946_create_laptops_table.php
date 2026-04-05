<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laptops', function (Blueprint $table) {
            $table->id();
            $table->string('merk', 50);
            $table->string('model', 100);
            $table->string('processor', 100);
            $table->string('ram', 20);
            $table->string('storage', 50);
            $table->string('serial_number')->unique();
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'maintenance'])->default('tersedia');
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->year('tahun_pembelian');
            $table->text('keterangan')->nullable();
            $table->string('gambar')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->string('warna', 30)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('baterai_kondisi', 20)->nullable();
            $table->string('garansi', 50)->nullable();
            $table->date('garansi_berakhir')->nullable();
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->decimal('harga_sewa_harian', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('merk');
            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laptops');
    }
};