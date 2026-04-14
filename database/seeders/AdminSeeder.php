<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'middle_name' => null,
            'last_name' => 'User',
            'username' => 'admin',
            'email' => 'admin@ispbilling.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
