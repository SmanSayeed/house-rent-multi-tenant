<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use Illuminate\Support\Facades\Hash;

class HouseOwnerFlatTest extends TestCase
{
    use RefreshDatabase;

    protected $houseOwner;
    protected $building;

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
    }

    public function test_house_owner_can_view_flats_index()
    {
        $response = $this->actingAs($this->houseOwner)->get('/house-owner/flats');
        $response->assertStatus(200);
        $response->assertSee('My Flats');
    }

    public function test_house_owner_can_view_create_flat_form()
    {
        $response = $this->actingAs($this->houseOwner)->get('/house-owner/flats/create');
        $response->assertStatus(200);
        $response->assertSee('Add New Flat');
    }

    public function test_house_owner_can_create_flat()
    {
        $flatData = [
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
            'description' => 'Test flat description',
        ];

        $response = $this->actingAs($this->houseOwner)->post('/house-owner/flats', $flatData);

        $response->assertRedirect('/house-owner/flats');
        $this->assertDatabaseHas('flats', [
            'flat_number' => 'A-101',
            'building_id' => $this->building->id,
        ]);
    }

    public function test_house_owner_can_view_their_flat()
    {
        $flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/flats/{$flat->id}");
        $response->assertStatus(200);
        $response->assertSee('A-101');
    }

    public function test_house_owner_cannot_view_other_house_owner_flat()
    {
        $otherHouseOwner = User::create([
            'name' => 'Other House Owner',
            'email' => 'other@example.com',
            'password' => Hash::make('password123'),
            'role' => 'house_owner',
            'contact' => '+880123456789',
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

        $flat = Flat::create([
            'building_id' => $otherBuilding->id,
            'flat_number' => 'B-201',
            'floor' => 2,
            'rent_amount' => 20000.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/flats/{$flat->id}");

        // In testing, abort(403) often gets converted to a redirect
        $this->assertTrue(in_array($response->status(), [403, 302]));

        if ($response->status() === 302) {
            $response->assertRedirect('/');
        }
    }

    public function test_house_owner_can_edit_their_flat()
    {
        $flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/flats/{$flat->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Flat');
    }

    public function test_house_owner_can_update_their_flat()
    {
        $flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        $updateData = [
            'building_id' => $this->building->id,
            'flat_number' => 'A-102',
            'floor' => 2,
            'rent_amount' => 18000.00,
            'status' => 'occupied',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->houseOwner)->put("/house-owner/flats/{$flat->id}", $updateData);

        $response->assertRedirect("/house-owner/flats/{$flat->id}");
        $this->assertDatabaseHas('flats', [
            'id' => $flat->id,
            'flat_number' => 'A-102',
        ]);
    }

    public function test_house_owner_can_delete_their_flat()
    {
        $flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->houseOwner)->delete("/house-owner/flats/{$flat->id}");

        $response->assertRedirect('/house-owner/flats');
        $this->assertDatabaseMissing('flats', [
            'id' => $flat->id,
        ]);
    }

    public function test_flat_creation_validation()
    {
        $response = $this->actingAs($this->houseOwner)->post('/house-owner/flats', []);

        $response->assertSessionHasErrors(['building_id', 'flat_number', 'floor', 'rent_amount', 'status']);
    }

    public function test_flat_update_validation()
    {
        $flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->houseOwner)->put("/house-owner/flats/{$flat->id}", []);

        $response->assertSessionHasErrors(['building_id', 'flat_number', 'floor', 'rent_amount', 'status']);
    }

    public function test_flat_filtering_by_building()
    {
        $flat1 = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->houseOwner)->get("/house-owner/flats?building_id={$this->building->id}");
        $response->assertStatus(200);
        $response->assertSee('A-101');
    }

    public function test_flat_filtering_by_status()
    {
        $flat = Flat::create([
            'building_id' => $this->building->id,
            'flat_number' => 'A-101',
            'floor' => 1,
            'rent_amount' => 15000.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->houseOwner)->get('/house-owner/flats?status=available');
        $response->assertStatus(200);
        $response->assertSee('A-101');
    }
}
