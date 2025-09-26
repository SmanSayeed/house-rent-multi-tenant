<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\BillCategory;
use App\Models\Building;
use App\Models\Flat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create house owner
        $houseOwner = User::firstOrCreate(
            ['email' => 'houseowner@test.com'],
            [
                'name' => 'Test House Owner',
                'email' => 'houseowner@test.com',
                'password' => Hash::make('password'),
                'role' => 'house_owner',
                'contact' => '+8801234567890'
            ]
        );

        // Create building
        $building = Building::firstOrCreate(
            ['owner_id' => $houseOwner->id],
            [
                'name' => 'Test Building',
                'address' => '123 Test Street',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'country' => 'Test Country',
                'description' => 'Test building for demonstration',
                'owner_id' => $houseOwner->id,
            ]
        );

        // Create flat
        $flat = Flat::firstOrCreate(
            ['flat_number' => 'A-101', 'building_id' => $building->id],
            [
                'building_id' => $building->id,
                'flat_number' => 'A-101',
                'floor' => 1,
                'rent_amount' => 15000.00,
                'description' => 'Test flat for demonstration',
                'status' => 'available',
            ]
        );

        // Create another flat
        $flat2 = Flat::firstOrCreate(
            ['flat_number' => 'A-102', 'building_id' => $building->id],
            [
                'building_id' => $building->id,
                'flat_number' => 'A-102',
                'floor' => 1,
                'rent_amount' => 12000.00,
                'description' => 'Another test flat',
                'status' => 'occupied',
            ]
        );

        // Get bill categories
        $electricityCategory = BillCategory::where('name', 'Electricity')->first();
        $waterCategory = BillCategory::where('name', 'Water Bill')->first();
        $gasCategory = BillCategory::where('name', 'Gas Bill')->first();

        if ($electricityCategory) {
            // Create bills for flat 1
            Bill::firstOrCreate(
                ['flat_id' => $flat->id, 'category_id' => $electricityCategory->id, 'title' => 'Electricity Bill - January'],
                [
                    'flat_id' => $flat->id,
                    'category_id' => $electricityCategory->id,
                    'title' => 'Electricity Bill - January',
                    'description' => 'Monthly electricity bill for January',
                    'amount' => 2500.00,
                    'due_date' => now()->addDays(30),
                    'status' => 'pending',
                ]
            );

            Bill::firstOrCreate(
                ['flat_id' => $flat->id, 'category_id' => $electricityCategory->id, 'title' => 'Electricity Bill - February'],
                [
                    'flat_id' => $flat->id,
                    'category_id' => $electricityCategory->id,
                    'title' => 'Electricity Bill - February',
                    'description' => 'Monthly electricity bill for February',
                    'amount' => 2800.00,
                    'due_date' => now()->addDays(30),
                    'status' => 'paid',
                ]
            );
        }

        if ($waterCategory) {
            Bill::firstOrCreate(
                ['flat_id' => $flat->id, 'category_id' => $waterCategory->id, 'title' => 'Water Bill - January'],
                [
                    'flat_id' => $flat->id,
                    'category_id' => $waterCategory->id,
                    'title' => 'Water Bill - January',
                    'description' => 'Monthly water bill for January',
                    'amount' => 800.00,
                    'due_date' => now()->addDays(30),
                    'status' => 'pending',
                ]
            );
        }

        if ($gasCategory) {
            Bill::firstOrCreate(
                ['flat_id' => $flat->id, 'category_id' => $gasCategory->id, 'title' => 'Gas Bill - January'],
                [
                    'flat_id' => $flat->id,
                    'category_id' => $gasCategory->id,
                    'title' => 'Gas Bill - January',
                    'description' => 'Monthly gas bill for January',
                    'amount' => 1200.00,
                    'due_date' => now()->addDays(30),
                    'status' => 'pending',
                ]
            );
        }

        // Create bills for flat 2
        if ($electricityCategory) {
            Bill::firstOrCreate(
                ['flat_id' => $flat2->id, 'category_id' => $electricityCategory->id, 'title' => 'Electricity Bill - January (A-102)'],
                [
                    'flat_id' => $flat2->id,
                    'category_id' => $electricityCategory->id,
                    'title' => 'Electricity Bill - January (A-102)',
                    'description' => 'Monthly electricity bill for January - Flat A-102',
                    'amount' => 2200.00,
                    'due_date' => now()->addDays(30),
                    'status' => 'pending',
                ]
            );
        }

        if ($waterCategory) {
            Bill::firstOrCreate(
                ['flat_id' => $flat2->id, 'category_id' => $waterCategory->id, 'title' => 'Water Bill - January (A-102)'],
                [
                    'flat_id' => $flat2->id,
                    'category_id' => $waterCategory->id,
                    'title' => 'Water Bill - January (A-102)',
                    'description' => 'Monthly water bill for January - Flat A-102',
                    'amount' => 750.00,
                    'due_date' => now()->addDays(30),
                    'status' => 'paid',
                ]
            );
        }

        $this->command->info('Test data created successfully!');
        $this->command->info('House Owner: houseowner@test.com / password');
        $this->command->info('Building: Test Building');
        $this->command->info('Flats: A-101, A-102');
        $this->command->info('Bills: Multiple bills with different categories created');
    }
}
