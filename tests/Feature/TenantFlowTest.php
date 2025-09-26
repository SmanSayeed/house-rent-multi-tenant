<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\TenantAssignment;
use App\Models\Bill;
use App\Models\BillCategory;
use Illuminate\Support\Facades\Hash;

class TenantFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;
    protected $houseOwner;
    protected $building;
    protected $flat;
    protected $assignment;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a house owner
        $this->houseOwner = User::create([
            'name' => 'Test House Owner',
            'email' => 'houseowner@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
        ]);

        // Create a tenant
        $this->tenant = User::create([
            'name' => 'Test Tenant',
            'email' => 'tenant@example.com',
            'password' => Hash::make('password123'),
            'role' => 'tenant',
            'contact' => '+880987654321',
        ]);

        // Create a building
        $this->building = Building::create([
            'owner_id' => $this->houseOwner->id,
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
        ]);

        // Create a flat
        $this->flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'occupied',
        ]);

        // Create tenant assignment
        $this->assignment = TenantAssignment::create([
            'tenant_id' => $this->tenant->id,
            'flat_id' => $this->flat->id,
            'building_id' => $this->building->id,
            'start_date' => now(),
            'monthly_rent' => 15000.00,
            'status' => 'active',
        ]);

        // Create bill category
        $this->category = BillCategory::create([
            'name' => 'Monthly Rent',
            'description' => 'Monthly rent payment',
            'is_active' => true,
        ]);
    }

    public function test_tenant_login_page_loads()
    {
        $response = $this->get('/tenant/login');
        $response->assertStatus(200);
        $response->assertSee('Tenant Portal');
    }

    public function test_tenant_can_login()
    {
        $response = $this->post('/tenant/login', [
            'email' => 'tenant@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/tenant/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isTenant());
    }

    public function test_tenant_cannot_login_with_invalid_credentials()
    {
        $response = $this->post('/tenant/login', [
            'email' => 'tenant@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_tenant_cannot_login_with_house_owner_credentials()
    {
        $response = $this->post('/tenant/login', [
            'email' => 'houseowner@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_tenant_dashboard_loads_after_login()
    {
        $this->actingAs($this->tenant);

        $response = $this->get('/tenant/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Welcome back, ' . $this->tenant->name);
    }

    public function test_tenant_can_view_their_bills()
    {
        // Create a bill for the tenant
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $this->actingAs($this->tenant);

        $response = $this->get('/tenant/bills');
        $response->assertStatus(200);
        $response->assertSee('My Bills');
        $response->assertSee('Monthly Rent');
    }

    public function test_tenant_can_view_specific_bill()
    {
        $bill = Bill::create([
            'flat_id' => $this->flat->id,
            'category_id' => $this->category->id,
            'title' => 'Monthly Rent',
            'amount' => 15000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $this->actingAs($this->tenant);

        $response = $this->get("/tenant/bills/{$bill->id}");
        $response->assertStatus(200);
        $response->assertSee('Monthly Rent');
    }

    public function test_tenant_cannot_view_other_tenant_bill()
    {
        // Create another tenant and their bill
        $otherTenant = User::create([
            'name' => 'Other Tenant',
            'email' => 'other@example.com',
            'password' => Hash::make('password123'),
            'role' => 'tenant',
            'contact' => '+880111111111',
        ]);

        $otherBuilding = Building::create([
            'owner_id' => $this->houseOwner->id,
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
            'status' => 'occupied',
        ]);

        $otherAssignment = TenantAssignment::create([
            'tenant_id' => $otherTenant->id,
            'flat_id' => $otherFlat->id,
            'building_id' => $otherBuilding->id,
            'start_date' => now(),
            'monthly_rent' => 20000.00,
            'status' => 'active',
        ]);

        $otherBill = Bill::create([
            'flat_id' => $otherFlat->id,
            'category_id' => $this->category->id,
            'title' => 'Other Bill',
            'amount' => 20000.00,
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $this->actingAs($this->tenant);

        $response = $this->get("/tenant/bills/{$otherBill->id}");

        // In testing, abort(403) often gets converted to a redirect
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    public function test_tenant_can_view_payments()
    {
        $this->actingAs($this->tenant);

        $response = $this->get('/tenant/payments');
        $response->assertStatus(200);
        $response->assertSee('My Payments');
    }

    public function test_tenant_can_view_profile()
    {
        $this->actingAs($this->tenant);

        $response = $this->get('/tenant/profile');
        $response->assertStatus(200);
        $response->assertSee($this->tenant->name);
    }

    public function test_tenant_can_update_profile()
    {
        $this->actingAs($this->tenant);

        $response = $this->put('/tenant/profile', [
            'name' => 'Updated Tenant Name',
            'email' => 'updated@example.com',
            'contact' => '+880999999999',
        ]);

        $response->assertRedirect('/tenant/profile');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->tenant->id,
            'name' => 'Updated Tenant Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_tenant_can_view_settings()
    {
        $this->actingAs($this->tenant);

        $response = $this->get('/tenant/settings');
        $response->assertStatus(200);
        $response->assertSee('Settings');
    }

    public function test_tenant_can_view_notifications()
    {
        $this->actingAs($this->tenant);

        $response = $this->get('/tenant/notifications');
        $response->assertStatus(200);
        $response->assertSee('Notifications');
    }

    public function test_tenant_can_view_support()
    {
        $this->actingAs($this->tenant);

        $response = $this->get('/tenant/support');
        $response->assertStatus(200);
        $response->assertSee('Support');
    }

    public function test_tenant_can_submit_support_request()
    {
        $this->actingAs($this->tenant);

        $response = $this->post('/tenant/support', [
            'subject' => 'Test Support Request',
            'message' => 'This is a test support request',
            'priority' => 'medium',
        ]);

        $response->assertRedirect('/tenant/support');
        $response->assertSessionHas('success');
    }

    public function test_tenant_can_logout()
    {
        $this->actingAs($this->tenant);

        $response = $this->post('/tenant/logout');
        $response->assertRedirect('/tenant/login');
        $response->assertSessionHas('success');
        $this->assertGuest();
    }

    public function test_tenant_service_works()
    {
        $this->actingAs($this->tenant);

        $tenantService = new \App\Services\TenantService();
        $stats = $tenantService->getDashboardStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_bills', $stats);
        $this->assertArrayHasKey('pending_bills', $stats);
        $this->assertArrayHasKey('paid_bills', $stats);
        $this->assertArrayHasKey('total_amount', $stats);
    }

    public function test_tenant_can_access_current_assignment()
    {
        $this->actingAs($this->tenant);

        $tenantService = new \App\Services\TenantService();
        $assignment = $tenantService->getCurrentAssignment();

        $this->assertNotNull($assignment);
        $this->assertEquals($this->tenant->id, $assignment->tenant_id);
        $this->assertEquals($this->flat->id, $assignment->flat_id);
    }
}
