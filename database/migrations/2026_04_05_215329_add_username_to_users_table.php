<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek dan tambahkan kolom username jika belum ada
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('name');
            }
            
            // Cek dan tambahkan kolom phone jika belum ada
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            // Cek dan tambahkan kolom address jika belum ada
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            // Cek dan tambahkan kolom status jika belum ada
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username']);
            // Hanya drop username, jangan drop phone/address/status karena mungkin dari migrasi sebelumnya
        });
    }
};