<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->admin()->create([
            'first_name' => 'Admin',
            'middle_name' => null,
            'last_name' => 'User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone_number' => '1234567890',
            'address' => '123 Admin Street, City, State 12345',
            'age' => 30,
            'is_admin' => 1,
            'email_verified_at' => now(),
        ]);

        // Create normal test user
        User::factory()->normalUser()->create([
            'first_name' => 'Test',
            'middle_name' => 'User',
            'last_name' => 'Account',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone_number' => '0987654321',
            'address' => '456 Test Avenue, City, State 54321',
            'age' => 25,
            'is_admin' => 0,
            'email_verified_at' => now(),
        ]);

        // Create additional normal users
        User::factory(5)->normalUser()->create();
    }
}
