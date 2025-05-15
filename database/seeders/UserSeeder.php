<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // Check if email already exists
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Agent User
        User::firstOrCreate(
            ['email' => 'agent@example.com'],
            [
                'name' => 'Agent User',
                'password' => Hash::make('password'),
                'role' => 'agent',
            ]
        );

        // Landlord User
        User::firstOrCreate(
            ['email' => 'landlord@example.com'],
            [
                'name' => 'Landlord User',
                'password' => Hash::make('password'),
                'role' => 'landlord',
            ]
        );

        // Create 10 random users using factory
        User::factory(10)->create();
    }
}
