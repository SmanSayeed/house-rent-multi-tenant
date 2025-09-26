<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\BillCategory;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\TenantAssignment;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample bill categories
        $rentCategory = BillCategory::create([
            'name' => 'Rent',
            'description' => 'Monthly rent payment',
            'is_active' => true,
        ]);

        $utilityCategory = BillCategory::create([
            'name' => 'Utilities',
            'description' => 'Electricity, water, gas bills',
            'is_active' => true,
        ]);

        $maintenanceCategory = BillCategory::create([
            'name' => 'Maintenance',
            'description' => 'Building maintenance and repairs',
            'is_active' => true,
        ]);

        // Get house owner
        $houseOwner = User::where('role', 'house_owner')->first();

        // Create sample buildings
        $building1 = Building::create([
            'owner_id' => $houseOwner->id,
            'name' => 'Sunrise Apartments',
            'address' => '123 Main Street',
            'city' => 'Dhaka',
            'state' => 'Dhaka',
            'postal_code' => '1000',
            'country' => 'Bangladesh',
            'description' => 'Modern apartment complex in the heart of Dhaka',
        ]);

        $building2 = Building::create([
            'owner_id' => $houseOwner->id,
            'name' => 'Garden View Complex',
            'address' => '456 Park Avenue',
            'city' => 'Chittagong',
            'state' => 'Chittagong',
            'postal_code' => '4000',
            'country' => 'Bangladesh',
            'description' => 'Luxury apartments with garden view',
        ]);

        // Create sample flats
        $flat1 = Flat::create([
            'building_id' => $building1->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'description' => '2 bedroom apartment',
            'status' => 'occupied',
        ]);

        $flat2 = Flat::create([
            'building_id' => $building1->id,
            'flat_number' => 'A-102',
            'floor' => 1,
            'rent_amount' => 12000.00,
            'description' => '1 bedroom apartment',
            'status' => 'available',
        ]);

        $flat3 = Flat::create([
            'building_id' => $building2->id,
            'flat_number' => 'B-201',
            'floor' => 2,
            'rent_amount' => 20000.00,
            'description' => '3 bedroom apartment',
            'status' => 'occupied',
        ]);

        // Create sample tenant
        $tenant = User::create([
            'name' => 'Ahmed Rahman',
            'email' => 'tenant@example.com',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'contact' => '+880123456789',
        ]);

        // Create tenant assignment
        TenantAssignment::create([
            'tenant_id' => $tenant->id,
            'flat_id' => $flat1->id,
            'building_id' => $building1->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
            'monthly_rent' => 15000.00,
            'status' => 'active',
        ]);

        // Create sample bills
        $bill1 = Bill::create([
            'flat_id' => $flat1->id,
            'category_id' => $rentCategory->id,
            'title' => 'Monthly Rent - January 2024',
            'description' => 'Monthly rent for flat A-101',
            'amount' => 15000.00,
            'due_date' => now()->addDays(15),
            'status' => 'pending',
        ]);

        $bill2 = Bill::create([
            'flat_id' => $flat1->id,
            'category_id' => $utilityCategory->id,
            'title' => 'Electricity Bill - January 2024',
            'description' => 'Monthly electricity consumption',
            'amount' => 2500.00,
            'due_date' => now()->addDays(10),
            'status' => 'pending',
        ]);

        $bill3 = Bill::create([
            'flat_id' => $flat3->id,
            'category_id' => $rentCategory->id,
            'title' => 'Monthly Rent - January 2024',
            'description' => 'Monthly rent for flat B-201',
            'amount' => 20000.00,
            'due_date' => now()->addDays(20),
            'status' => 'paid',
        ]);

        // Create sample payments
        Payment::create([
            'bill_id' => $bill3->id,
            'tenant_id' => $tenant->id,
            'amount' => 20000.00,
            'status' => 'paid',
            'payment_method' => 'Bank Transfer',
            'transaction_id' => 'TXN123456789',
            'notes' => 'Payment received successfully',
            'paid_at' => now()->subDays(5),
        ]);

        Payment::create([
            'bill_id' => $bill1->id,
            'tenant_id' => $tenant->id,
            'amount' => 15000.00,
            'status' => 'pending',
            'payment_method' => 'Cash',
            'notes' => 'Payment pending',
        ]);
    }
}
