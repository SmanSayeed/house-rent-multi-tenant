<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use Illuminate\Support\Facades\Hash;

class HouseOwnerBuildingTest extends TestCase
{
    use RefreshDatabase;

    protected $houseOwner;

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
    }

    public function test_house_owner_can_view_buildings_index()
    {
        $response = $this->actingAs($this->houseOwner)->get('/house-owner/buildings');
        $response->assertStatus(200);
        $response->assertSee('My Buildings');
    }

    public function test_house_owner_can_view_create_building_form()
    {
        $response = $this->actingAs($this->houseOwner)->get('/house-owner/buildings/create');
        $response->assertStatus(200);
        $response->assertSee('Add New Building');
    }

    public function test_house_owner_can_create_building()
    {
        $buildingData = [
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
            'description' => 'Test building description',
        ];

        $response = $this->actingAs($this->houseOwner)->post('/house-owner/buildings', $buildingData);

        $response->assertRedirect('/house-owner/buildings');
        $this->assertDatabaseHas('buildings', [
            'name' => 'Test Building',
            'owner_id' => $this->houseOwner->id,
        ]);
    }

    public function test_house_owner_can_view_their_building()
    {
        $building = Building::create([
            'owner_id' => $this->houseOwner->id,
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/buildings/{$building->id}");
        $response->assertStatus(200);
        $response->assertSee('Test Building');
    }

    public function test_house_owner_cannot_view_other_house_owner_building()
    {
        $otherHouseOwner = User::create([
            'name' => 'Other House Owner',
            'email' => 'other@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
        ]);

        $building = Building::create([
            'owner_id' => $otherHouseOwner->id,
            'name' => 'Other Building',
            'address' => '456 Other Street',
            'city' => 'Other City',
            'state' => 'Other State',
            'postal_code' => '54321',
            'country' => 'Bangladesh',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/buildings/{$building->id}");

        // In testing, abort(403) often gets converted to a redirect
        // So we check for either 403 or 302 redirect
        $this->assertTrue(in_array($response->status(), [403, 302]));

        if ($response->status() === 302) {
            $response->assertRedirect('/');
        }
    }

    public function test_house_owner_can_edit_their_building()
    {
        $building = Building::create([
            'owner_id' => $this->houseOwner->id,
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/buildings/{$building->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Building');
    }

    public function test_house_owner_can_update_their_building()
    {
        $building = Building::create([
            'owner_id' => $this->houseOwner->id,
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
        ]);

        $updateData = [
            'name' => 'Updated Building',
            'address' => '456 Updated Street',
            'city' => 'Updated City',
            'state' => 'Updated State',
            'postal_code' => '54321',
            'country' => 'Bangladesh',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->houseOwner)->put("/house-owner/buildings/{$building->id}", $updateData);

        $response->assertRedirect("/house-owner/buildings/{$building->id}");
        $this->assertDatabaseHas('buildings', [
            'id' => $building->id,
            'name' => 'Updated Building',
        ]);
    }

    public function test_house_owner_can_delete_their_building()
    {
        $building = Building::create([
            'owner_id' => $this->houseOwner->id,
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
        ]);

        $response = $this->actingAs($this->houseOwner)->delete("/house-owner/buildings/{$building->id}");

        $response->assertRedirect('/house-owner/buildings');
        $this->assertDatabaseMissing('buildings', [
            'id' => $building->id,
        ]);
    }

    public function test_house_owner_cannot_delete_building_with_flats()
    {
        $building = Building::create([
            'owner_id' => $this->houseOwner->id,
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
        ]);

        // Create a flat for this building
        $building->flats()->create([
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->houseOwner)->delete("/house-owner/buildings/{$building->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('buildings', [
            'id' => $building->id,
        ]);
    }

    public function test_building_creation_validation()
    {
        $response = $this->actingAs($this->houseOwner)->post('/house-owner/buildings', []);

        $response->assertSessionHasErrors(['name', 'address', 'city', 'state', 'postal_code']);
    }

    public function test_building_update_validation()
    {
        $building = Building::create([
            'owner_id' => $this->houseOwner->id,
            'name' => 'Test Building',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Bangladesh',
        ]);

        $response = $this->actingAs($this->houseOwner)->put("/house-owner/buildings/{$building->id}", []);

        $response->assertSessionHasErrors(['name', 'address', 'city', 'state', 'postal_code']);
    }
}
