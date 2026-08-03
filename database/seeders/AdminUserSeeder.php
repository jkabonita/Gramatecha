<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user for testing
        User::firstOrCreate(
            ['email' => 'admin@gramatecha.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@gramatecha.com');
        $this->command->info('Password: password');
    }
}
