<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Admin
        User::create([
            'name' => 'Administrator',
            'phone' => '081234567890',
            'password' => Hash::make('password123'), // gunakan hash
            'role' => 'admin',
            'active_until' => now()->addYears(10), // aktif panjang
        ]);

        // Buat User Biasa
        User::create([
            'name' => 'User Biasa',
            'phone' => '089876543210',
            'password' => Hash::make('password123'), // gunakan hash
            'role' => 'user',
            'active_until' => now()->addMonth(), // 1 bulan aktif
        ]);
    }
}
