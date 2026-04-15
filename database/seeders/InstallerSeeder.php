<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InstallerSeeder extends Seeder
{
    public function run(): void
    {
        // Check if installer already exists
        $existingInstaller = User::where('email', 'installer@ispbilling.com')->first();
        
        if (!$existingInstaller) {
            User::create([
                'first_name' => 'John',
                'last_name' => 'Technician',
                'middle_name' => 'D.',
                'username' => 'installer_tech',
                'email' => 'installer@ispbilling.com',
                'phone_number' => '09123456789',
                'password' => Hash::make('installer123'),
                'is_admin' => 2,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Default installer account created successfully!');
            $this->command->info('📧 Email: installer@ispbilling.com');
            $this->command->info('🔑 Password: installer123');
        } else {
            $this->command->warn('⚠️  Installer account already exists. Skipping...');
        }
    }
}
