<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // <-- Import the User model
use Illuminate\Support\Facades\Hash; // <-- Import Hash

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Using firstOrCreate to avoid creating duplicate admins if seeder is run multiple times
        User::firstOrCreate(
            [
                'email' => 'admin@example.com' // The unique field to check for
            ],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // Set a default password
                'role' => 'Admin', // Assign the 'Admin' role as per your helpers.php
                'status' => '1', // Set status to '1' for active
                'email_verified_at' => now(), // Pre-verify the admin's email
            ]
        );
    }
}