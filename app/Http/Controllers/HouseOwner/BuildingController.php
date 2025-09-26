<?php

namespace App\Http\Controllers\HouseOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\HouseOwner\BuildingRequest;
use App\Models\Building;
use App\Services\HouseOwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BuildingController extends Controller
{
    protected $houseOwnerService;

    public function __construct(HouseOwnerService $houseOwnerService)
    {
        $this->houseOwnerService = $houseOwnerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $buildings = $this->houseOwnerService->getBuildings();

            Log::info('House owner viewed buildings list', [
                'user_id' => Auth::id(),
                'buildings_count' => $buildings->count()
            ]);

            return view('house-owner.buildings.index', compact('buildings'));
        } catch (\Exception $e) {
            Log::error('Error fetching buildings for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load buildings. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('house-owner.buildings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuildingRequest $request)
    {
        try {
            $validated = $request->validated();

            $building = Building::create([
                'owner_id' => Auth::id(),
                'name' => $validated['name'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'postal_code' => $validated['postal_code'],
                'country' => $validated['country'] ?? 'Bangladesh',
                'description' => $validated['description'],
            ]);

            Log::info('House owner created new building', [
                'user_id' => Auth::id(),
                'building_id' => $building->id,
                'building_name' => $building->name
            ]);

            return redirect()->route('house-owner.buildings.index')
                ->with('success', 'Building created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating building for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create building. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building)
    {
        try {
            // Ensure the building belongs to the authenticated house owner
            if ($building->owner_id !== Auth::id()) {
                Log::warning('House owner attempted to access unauthorized building', [
                    'user_id' => Auth::id(),
                    'building_id' => $building->id
                ]);

                abort(403, 'Unauthorized access to building.');
            }

            $building->load(['flats', 'flats.tenantAssignments.tenant']);

            Log::info('House owner viewed building details', [
                'user_id' => Auth::id(),
                'building_id' => $building->id,
                'flats_count' => $building->flats->count()
            ]);

            return view('house-owner.buildings.show', compact('building'));
        } catch (\Exception $e) {
            Log::error('Error fetching building details for house owner', [
                'user_id' => Auth::id(),
                'building_id' => $building->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load building details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Building $building)
    {
        // Ensure the building belongs to the authenticated house owner
        if ($building->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to building.');
        }

        return view('house-owner.buildings.edit', compact('building'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BuildingRequest $request, Building $building)
    {
        try {
            // Ensure the building belongs to the authenticated house owner
            if ($building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to building.');
            }

            $validated = $request->validated();

            $building->update([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'postal_code' => $validated['postal_code'],
                'country' => $validated['country'] ?? 'Bangladesh',
                'description' => $validated['description'],
            ]);

            Log::info('House owner updated building', [
                'user_id' => Auth::id(),
                'building_id' => $building->id,
                'building_name' => $building->name
            ]);

            return redirect()->route('house-owner.buildings.show', $building)
                ->with('success', 'Building updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating building for house owner', [
                'user_id' => Auth::id(),
                'building_id' => $building->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update building. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building)
    {
        try {
            // Ensure the building belongs to the authenticated house owner
            if ($building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to building.');
            }

            // Check if building has flats
            if ($building->flats()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete building with existing flats. Please remove all flats first.');
            }

            $buildingName = $building->name;
            $building->delete();

            Log::info('House owner deleted building', [
                'user_id' => Auth::id(),
                'building_id' => $building->id,
                'building_name' => $buildingName
            ]);

            return redirect()->route('house-owner.buildings.index')
                ->with('success', 'Building deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting building for house owner', [
                'user_id' => Auth::id(),
                'building_id' => $building->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to delete building. Please try again.');
        }
    }
}
