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
        // Create admin@admin.com user for testing
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create admin@gramatecha.com user for testing
        User::firstOrCreate(
            ['email' => 'admin@gramatecha.com'],
            [
                'name' => 'Gramatecha Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin users created successfully!');
        $this->command->info('Email: admin@admin.com | Password: password');
        $this->command->info('Email: admin@gramatecha.com | Password: password');
    }
}
