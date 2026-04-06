<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Tambah kolom kode_peminjaman setelah id
            $table->string('kode_peminjaman')->unique()->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn('kode_peminjaman');
        });
    }
};