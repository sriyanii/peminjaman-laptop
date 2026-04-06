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
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'lama_hari')) {
                $table->integer('lama_hari')->default(0)->after('tanggal_kembali_rencana');
            }
            if (!Schema::hasColumn('peminjaman', 'harga_sewa')) {
                $table->decimal('harga_sewa', 15, 0)->default(0)->after('lama_hari');
            }
            if (!Schema::hasColumn('peminjaman', 'total_tagihan')) {
                $table->decimal('total_tagihan', 15, 0)->nullable()->after('denda');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['lama_hari', 'harga_sewa', 'total_tagihan']);
        });
    }
};
