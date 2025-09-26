<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Services\HouseOwnerService;
use Illuminate\Support\Facades\Hash;

class HouseOwnerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_house_owner_registration_page_loads()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('House Owner Registration');
    }

    public function test_house_owner_login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('House Owner Login');
    }

    public function test_house_owner_can_register()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'contact' => '+880123456789',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/house-owner/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'house_owner',
        ]);
    }

    public function test_house_owner_can_login()
    {
        // Create a house owner user
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
        ]);

        $loginData = [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $loginData);

        $response->assertRedirect('/house-owner/dashboard');
        $this->assertAuthenticated();
    }

    public function test_house_owner_dashboard_loads_after_login()
    {
        $user = User::create([
            'name' => 'Test Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
        ]);

        $response = $this->actingAs($user)->get('/house-owner/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_house_owner_service_works()
    {
        $user = User::create([
            'name' => 'Service Test Owner',
            'email' => 'service@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
        ]);

        $this->actingAs($user);

        $service = new HouseOwnerService();
        $stats = $service->getDashboardStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_buildings', $stats);
        $this->assertArrayHasKey('total_flats', $stats);
        $this->assertArrayHasKey('total_tenants', $stats);
        $this->assertArrayHasKey('total_bills', $stats);
        $this->assertArrayHasKey('total_payments', $stats);
    }

    public function test_user_role_methods_work()
    {
        $houseOwner = User::create([
            'name' => 'House Owner',
            'email' => 'houseowner@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
        ]);

        $this->assertTrue($houseOwner->isHouseOwner());
        $this->assertFalse($houseOwner->isAdmin());
        $this->assertFalse($houseOwner->isTenant());
    }

    public function test_house_owner_can_logout()
    {
        $user = User::create([
            'name' => 'Logout Test Owner',
            'email' => 'logout@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
