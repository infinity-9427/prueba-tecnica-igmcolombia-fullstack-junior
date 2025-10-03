<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@invoice.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Manager user
        $manager = User::create([
            'name' => 'Invoice Manager',
            'email' => 'manager@invoice.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('manager');

        // Regular user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'user@invoice.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');

        // Accountant user
        $accountant = User::create([
            'name' => 'Jane Smith',
            'email' => 'accountant@invoice.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $accountant->assignRole('accountant');

        $this->command->info('Users created and roles assigned successfully!');
    }
}