<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\BillCategory;
use App\Models\Building;
use App\Models\Flat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BillCategoryIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $houseOwner;
    protected $building;
    protected $flat;
    protected $billCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create house owner
        $this->houseOwner = User::factory()->create([
            'role' => 'house_owner',
            'email' => 'houseowner@test.com',
            'password' => bcrypt('password')
        ]);

        // Create building
        $this->building = Building::create([
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Test Country',
            'description' => 'Test building description',
            'owner_id' => $this->houseOwner->id,
        ]);

        // Create flat
        $this->flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'description' => 'Test flat description',
            'status' => 'available',
        ]);

        // Create bill category
        $this->billCategory = BillCategory::create([
            'name' => 'Electricity',
            'description' => 'Monthly electricity bills',
            'is_active' => true,
            'icon' => 'bi-lightning',
            'color' => '#ffc107',
        ]);
    }

    /** @test */
    public function house_owner_can_view_bill_categories()
    {
        $this->actingAs($this->houseOwner);

        $response = $this->get('/house-owner/bill-categories');

        $response->assertStatus(200);
        $response->assertSee('Bill Categories');
        $response->assertSee('Electricity');
    }

    /** @test */
    public function house_owner_can_create_bill_category()
    {
        $this->actingAs($this->houseOwner);

        $categoryData = [
            'name' => 'Water Bill',
            'description' => 'Monthly water bills',
            'is_active' => true,
            'icon' => 'bi-droplet',
            'color' => '#0d6efd',
        ];

        $response = $this->post('/house-owner/bill-categories', $categoryData);

        $response->assertRedirect('/house-owner/bill-categories');
        $this->assertDatabaseHas('bill_categories', [
            'name' => 'Water Bill',
            'description' => 'Monthly water bills',
        ]);
    }

    /** @test */
    public function house_owner_can_view_flats_with_bill_categories()
    {
        $this->actingAs($this->houseOwner);

        // Create a bill for the flat
        Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->billCategory->id,
            'title' => 'Electricity Bill',
            'description' => 'Monthly electricity bill',
            'amount' => 2500.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->get('/house-owner/flats');

        $response->assertStatus(200);
        $response->assertSee('Bill Categories & Amounts');
        $response->assertSee('Electricity');
        $response->assertSee('৳2,500.00');
    }

    /** @test */
    public function house_owner_can_create_bill_from_flat_view()
    {
        $this->actingAs($this->houseOwner);

        $billData = [
            'flat_id' => $this->flat->id,
            'category_id' => $this->billCategory->id,
            'title' => 'Electricity Bill',
            'description' => 'Monthly electricity bill',
            'amount' => 2500.00,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ];

        $response = $this->post('/house-owner/bills', $billData);

        $response->assertRedirect('/house-owner/bills');
        $this->assertDatabaseHas('bills', [
            'flat_id' => $this->flat->id,
            'category_id' => $this->billCategory->id,
            'title' => 'Electricity Bill',
            'amount' => 2500.00,
        ]);
    }

    /** @test */
    public function house_owner_can_view_bills_by_category()
    {
        $this->actingAs($this->houseOwner);

        // Create bills with different categories
        Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->billCategory->id,
            'title' => 'Electricity Bill',
            'description' => 'Monthly electricity bill',
            'amount' => 2500.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $waterCategory = BillCategory::create([
            'name' => 'Water Bill',
            'description' => 'Monthly water bills',
            'is_active' => true,
        ]);

        Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $waterCategory->id,
            'title' => 'Water Bill',
            'description' => 'Monthly water bill',
            'amount' => 800.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->get('/house-owner/bills?category_id=' . $this->billCategory->id);

        $response->assertStatus(200);
        $response->assertSee('Electricity Bill');
        $response->assertDontSee('Water Bill');
    }

    /** @test */
    public function admin_can_view_all_bill_categories()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/bills');

        $response->assertStatus(200);
        $response->assertSee('All Bills');
    }

    /** @test */
    public function admin_can_assign_tenant_to_flat()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);

        $tenant = User::factory()->create([
            'role' => 'tenant',
            'email' => 'tenant@test.com',
            'password' => bcrypt('password')
        ]);

        $this->actingAs($admin);

        $assignmentData = [
            'tenant_id' => $tenant->id,
            'flat_id' => $this->flat->id,
        ];

        $response = $this->post('/admin/tenants/assign', $assignmentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('tenant_assignments', [
            'tenant_id' => $tenant->id,
            'flat_id' => $this->flat->id,
        ]);
    }

    /** @test */
    public function flat_shows_categorized_bills_correctly()
    {
        $this->actingAs($this->houseOwner);

        // Create multiple bills with different categories
        $electricityCategory = BillCategory::create([
            'name' => 'Electricity',
            'description' => 'Monthly electricity bills',
            'is_active' => true,
        ]);

        $waterCategory = BillCategory::create([
            'name' => 'Water',
            'description' => 'Monthly water bills',
            'is_active' => true,
        ]);

        Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $electricityCategory->id,
            'title' => 'Electricity Bill',
            'description' => 'Monthly electricity bill',
            'amount' => 2500.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $electricityCategory->id,
            'title' => 'Electricity Bill 2',
            'description' => 'Another electricity bill',
            'amount' => 3000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $waterCategory->id,
            'title' => 'Water Bill',
            'description' => 'Monthly water bill',
            'amount' => 800.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->get('/house-owner/flats');

        $response->assertStatus(200);
        $response->assertSee('Electricity');
        $response->assertSee('Water');
        $response->assertSee('৳5,500.00'); // Total for electricity (2500 + 3000)
        $response->assertSee('৳800.00'); // Total for water
    }

    /** @test */
    public function bill_category_validation_works()
    {
        $this->actingAs($this->houseOwner);

        $invalidData = [
            'name' => '', // Required field missing
            'description' => 'Test description',
        ];

        $response = $this->post('/house-owner/bill-categories', $invalidData);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function bill_creation_validation_works()
    {
        $this->actingAs($this->houseOwner);

        $invalidData = [
            'flat_id' => $this->flat->id,
            'category_id' => $this->billCategory->id,
            'title' => '', // Required field missing
            'amount' => 'invalid', // Invalid amount
            'due_date' => 'invalid-date', // Invalid date
        ];

        $response = $this->post('/house-owner/bills', $invalidData);

        $response->assertSessionHasErrors(['title', 'amount', 'due_date']);
    }

    /** @test */
    public function multi_tenant_isolation_works()
    {
        // Create another house owner
        $anotherHouseOwner = User::factory()->create([
            'role' => 'house_owner',
            'email' => 'another@test.com',
            'password' => bcrypt('password')
        ]);

        // Create building for another house owner
        $anotherBuilding = Building::create([
            'name' => 'Another Building',
            'address' => '456 Another Street',
            'city' => 'Another City',
            'state' => 'Another State',
            'postal_code' => '54321',
            'country' => 'Another Country',
            'description' => 'Another building description',
            'owner_id' => $anotherHouseOwner->id,
        ]);

        // Create flat for another house owner
        $anotherFlat = Flat::create([
            'building_id' => $anotherBuilding->id,
            'flat_number' => 'B-201',
            'floor' => 2,
            'rent_amount' => 20000.00,
            'description' => 'Another flat description',
            'status' => 'available',
        ]);

        // Create bill for another house owner's flat
        Bill::create([
            'flat_id' => $anotherFlat->id,
            'category_id' => $this->billCategory->id,
            'title' => 'Another Owner Bill',
            'description' => 'Bill for another owner',
            'amount' => 5000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        // Login as first house owner
        $this->actingAs($this->houseOwner);

        $response = $this->get('/house-owner/flats');

        $response->assertStatus(200);
        $response->assertDontSee('Another Owner Bill');
        $response->assertDontSee('B-201');
    }
}
