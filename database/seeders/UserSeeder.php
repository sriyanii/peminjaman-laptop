<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
public function run(): void
{
    $users = [
        [
            'name' => 'Administrator',
            'email' => 'admin@rental.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ],
        [
            'name' => 'Petugas Rental',
            'email' => 'petugas@rental.com',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'status' => 'active',
            'email_verified_at' => now(),
        ],
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 123',
            'status' => 'active',
            'email_verified_at' => now(),
        ],
    ];

    foreach ($users as $userData) {
        User::firstOrCreate(
            ['email' => $userData['email']],
            $userData
        );
    }
    
    echo "✅ Users seeded successfully!\n";
}
}