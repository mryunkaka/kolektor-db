<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        // Payment sukses untuk admin
        Payment::create([
            'id_users' => $admin->id_users,
            'nominal' => 100000,
            'status' => 'success',
            'unique_code' => 123,
            'payment_method' => 'bank_transfer',
        ]);

        // Payment pending untuk user
        Payment::create([
            'id_users' => $user->id_users,
            'nominal' => 50000,
            'status' => 'pending',
            'unique_code' => 456,
            'payment_method' => 'bank_transfer',
        ]);
    }
}
