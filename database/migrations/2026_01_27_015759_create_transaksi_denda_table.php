<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi_denda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peminjaman_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->decimal('total_denda', 10, 2)->default(0);
            $table->decimal('denda_dibayar', 10, 2)->default(0);
            $table->string('status_pembayaran')->default('belum_lunas'); // lunas, belum_lunas, sebagian
            $table->string('status_transaksi')->default('pending'); // pending, proses_cek, selesai, dibatalkan
            $table->string('kondisi_barang')->nullable(); // baik, rusak_ringan, rusak_berat, hilang
            $table->text('catatan_cek')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->dateTime('waktu_cek')->nullable();
            $table->dateTime('waktu_pembayaran')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes
            $table->index('peminjaman_id');
            $table->index('user_id');
            $table->index('status_pembayaran');
            $table->index('status_transaksi');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_denda');
    }
};