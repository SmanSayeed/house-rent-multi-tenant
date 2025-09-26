<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HouseOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create house owner user
        User::create([
            'name' => 'John Smith',
            'email' => 'b@b.com',
            'password' => Hash::make('11112222'),
            'role' => 'house_owner',
            'contact' => '+1234567891',
        ]);
    }
}
