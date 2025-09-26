<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'a@a.com',
            'password' => Hash::make('11112222'),
            'role' => 'admin',
            'contact' => '+1234567890',
        ]);
    }
}
