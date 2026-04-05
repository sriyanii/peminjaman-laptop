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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('laptop_id')->constrained('laptops')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana');
            $table->date('tanggal_kembali')->nullable();
            $table->enum('status', ['pending', 'approved', 'aktif', 'selesai', 'ditolak', 'batal', 'terlambat'])->default('pending');
            $table->enum('tujuan', ['meeting', 'presentasi', 'training', 'work_from_home', 'proyek', 'lainnya'])->default('lainnya');
            $table->text('keterangan')->nullable();
            $table->text('alasan_ditolak')->nullable();
            $table->text('catatan_pengembalian')->nullable();
            $table->decimal('denda', 10, 2)->default(0);
            $table->boolean('is_denda_dibayar')->default(false);
            $table->dateTime('waktu_approve')->nullable();
            $table->dateTime('waktu_pengambilan')->nullable();
            $table->dateTime('waktu_pengembalian')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('tanggal_pinjam');
            $table->index('user_id');
            $table->index('laptop_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};