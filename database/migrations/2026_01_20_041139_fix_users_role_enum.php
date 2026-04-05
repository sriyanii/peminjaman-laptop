<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add 'user' to ENUM temporarily
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','petugas','peminjam','user') DEFAULT 'peminjam'");
        
        // 2. Update existing 'peminjam' to 'user'
        DB::table('users')->where('role', 'peminjam')->update(['role' => 'user']);
        
        // 3. Remove 'peminjam' from ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','petugas','user') DEFAULT 'user'");
        
        // 4. Update default untuk user yang baru
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'user'");
    }

    public function down(): void
    {
        // Untuk rollback, kembalikan ke semula
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','petugas','peminjam') DEFAULT 'peminjam'");
        DB::table('users')->where('role', 'user')->update(['role' => 'peminjam']);
    }
};