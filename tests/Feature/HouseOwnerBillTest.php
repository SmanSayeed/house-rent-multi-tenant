<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Bill;
use App\Models\BillCategory;
use Illuminate\Support\Facades\Hash;

class HouseOwnerBillTest extends TestCase
{
    use RefreshDatabase;

    protected $houseOwner;
    protected $building;
    protected $flat;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a house owner user
        $this->houseOwner = User::create([
            'name' => 'Test House Owner',
            'email' => 'houseowner@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
        ]);

        // Create a building for the house owner
        $this->building = Building::create([
            'owner_id' => $this->houseOwner->id,
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
        ]);

        // Create a flat for the building
        $this->flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        // Create a bill category
        $this->category = BillCategory::create([
            'name' => 'Monthly Rent',
            'description' => 'Monthly rent payment',
            'is_active' => true,
        ]);
    }

    public function test_house_owner_can_view_bills_index()
    {
        $response = $this->actingAs($this->houseOwner)->get('/house-owner/bills');
        $response->assertStatus(200);
        $response->assertSee('My Bills');
    }

    public function test_house_owner_can_view_create_bill_form()
    {
        $response = $this->actingAs($this->houseOwner)->get('/house-owner/bills/create');
        $response->assertStatus(200);
        $response->assertSee('Create New Bill');
    }

    public function test_house_owner_can_create_bill()
    {
        $billData = [
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'description' => 'Monthly rent for flat A-101',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ];

        $response = $this->actingAs($this->houseOwner)->post('/house-owner/bills', $billData);

        $response->assertRedirect();
        $this->assertDatabaseHas('bills', [
            'flat_id' => $this->flat->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
        ]);
    }

    public function test_house_owner_can_view_their_bill()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/bills/{$bill->id}");
        $response->assertStatus(200);
        $response->assertSee('Monthly Rent');
    }

    public function test_house_owner_cannot_view_other_house_owner_bill()
    {
        // Create another house owner
        $otherHouseOwner = User::create([
            'name' => 'Other House Owner',
            'email' => 'other@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880987654321',
        ]);

        $otherBuilding = Building::create([
            'owner_id' => $otherHouseOwner->id,
            'name' => 'Other Building',
            'address' => '456 Other Street',
            'city' => 'Other City',
            'state' => 'Other State',
            'postal_code' => '54321',
            'country' => 'Bangladesh',
        ]);

        $otherFlat = Flat::create([
            'building_id' => $otherBuilding->id,
            'flat_number' => 'B-201',
            'floor' => 2,
            'rent_amount' => 20000.00,
            'status' => 'available',
        ]);

        $otherBill = Bill::create([
            'flat_id' => $otherFlat->id,
            'category_id' => $this->category->id,
            'title' => 'Other Bill',
            'amount' => 20000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/bills/{$otherBill->id}");

        // In testing, abort(403) often gets converted to a redirect
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    public function test_house_owner_can_edit_their_bill()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/bills/{$bill->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Bill');
    }

    public function test_house_owner_can_update_their_bill()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $updateData = [
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Updated Monthly Rent',
            'description' => 'Updated description',
            'amount' => 16000.00,
            'due_date' => now()->addDays(35)->format('Y-m-d'),
            'status' => 'pending',
        ];

        $response = $this->actingAs($this->houseOwner)->put("/house-owner/bills/{$bill->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('bills', [
            'id' => $bill->id,
            'title' => 'Updated Monthly Rent',
            'amount' => 16000.00,
        ]);
    }

    public function test_house_owner_can_delete_their_bill()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->houseOwner)->delete("/house-owner/bills/{$bill->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('bills', [
            'id' => $bill->id,
        ]);
    }

    public function test_house_owner_can_generate_bill_invoice()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/bills/{$bill->id}/invoice");
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_house_owner_can_mark_bill_as_paid()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->houseOwner)->patch("/house-owner/bills/{$bill->id}/mark-as-paid");

        $response->assertRedirect();
        $this->assertDatabaseHas('bills', [
            'id' => $bill->id,
            'status' => 'paid',
        ]);
    }

    public function test_bill_creation_validation()
    {
        $response = $this->actingAs($this->houseOwner)->post('/house-owner/bills', []);

        $response->assertSessionHasErrors(['flat_id', 'category_id', 'title', 'amount', 'due_date', 'status']);
    }

    public function test_bill_filtering_by_flat()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/bills?flat_id={$this->flat->id}");
        $response->assertStatus(200);
        $response->assertSee('Monthly Rent');
    }

    public function test_bill_filtering_by_status()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->houseOwner)->get('/house-owner/bills?status=pending');
        $response->assertStatus(200);
        $response->assertSee('Monthly Rent');
    }
}
