<?php

namespace App\Http\Controllers\HouseOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\HouseOwner\FlatRequest;
use App\Models\Building;
use App\Models\Flat;
use App\Services\HouseOwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FlatController extends Controller
{
    protected $houseOwnerService;

    public function __construct(HouseOwnerService $houseOwnerService)
    {
        $this->houseOwnerService = $houseOwnerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['building_id', 'status', 'search']);
            $flats = $this->houseOwnerService->getFlats($filters);
            $buildings = $this->houseOwnerService->getBuildings();

            Log::info('House owner viewed flats list', [
                'user_id' => Auth::id(),
                'flats_count' => $flats->count(),
                'filters' => $filters
            ]);

            return view('house-owner.flats.index', compact('flats', 'buildings'));
        } catch (\Exception $e) {
            Log::error('Error fetching flats for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load flats. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $buildingId = $request->get('building');
        $buildings = $this->houseOwnerService->getBuildings();

        return view('house-owner.flats.create', compact('buildings', 'buildingId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlatRequest $request)
    {
        try {
            $validated = $request->validated();

            // Ensure the building belongs to the authenticated house owner
            $building = Building::where('id', $validated['building_id'])
                ->where('owner_id', Auth::id())
                ->first();

            if (!$building) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected building does not belong to you.');
            }

            $flat = Flat::create([
                'building_id' => $validated['building_id'],
                'flat_number' => $validated['flat_number'],
                'floor' => $validated['floor'],
                'rent_amount' => $validated['rent_amount'],
                'description' => $validated['description'],
                'status' => $validated['status'],
            ]);

            Log::info('House owner created new flat', [
                'user_id' => Auth::id(),
                'flat_id' => $flat->id,
                'flat_number' => $flat->flat_number,
                'building_id' => $flat->building_id
            ]);

            return redirect()->route('house-owner.flats.index')
                ->with('success', 'Flat created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating flat for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create flat. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Flat $flat)
    {
        try {
            // Ensure the flat belongs to the authenticated house owner
            if ($flat->building->owner_id !== Auth::id()) {
                Log::warning('House owner attempted to access unauthorized flat', [
                    'user_id' => Auth::id(),
                    'flat_id' => $flat->id
                ]);

                abort(403, 'Unauthorized access to flat.');
            }

            $flat->load(['building', 'tenantAssignments.tenant', 'bills']);

            Log::info('House owner viewed flat details', [
                'user_id' => Auth::id(),
                'flat_id' => $flat->id,
                'flat_number' => $flat->flat_number
            ]);

            return view('house-owner.flats.show', compact('flat'));
        } catch (\Exception $e) {
            Log::error('Error fetching flat details for house owner', [
                'user_id' => Auth::id(),
                'flat_id' => $flat->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load flat details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Flat $flat)
    {
        // Ensure the flat belongs to the authenticated house owner
        if ($flat->building->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to flat.');
        }

        $buildings = $this->houseOwnerService->getBuildings();

        return view('house-owner.flats.edit', compact('flat', 'buildings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FlatRequest $request, Flat $flat)
    {
        try {
            // Ensure the flat belongs to the authenticated house owner
            if ($flat->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to flat.');
            }

            $validated = $request->validated();

            // Ensure the new building belongs to the authenticated house owner
            $building = Building::where('id', $validated['building_id'])
                ->where('owner_id', Auth::id())
                ->first();

            if (!$building) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected building does not belong to you.');
            }

            $flat->update([
                'building_id' => $validated['building_id'],
                'flat_number' => $validated['flat_number'],
                'floor' => $validated['floor'],
                'rent_amount' => $validated['rent_amount'],
                'description' => $validated['description'],
                'status' => $validated['status'],
            ]);

            Log::info('House owner updated flat', [
                'user_id' => Auth::id(),
                'flat_id' => $flat->id,
                'flat_number' => $flat->flat_number
            ]);

            return redirect()->route('house-owner.flats.show', $flat)
                ->with('success', 'Flat updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating flat for house owner', [
                'user_id' => Auth::id(),
                'flat_id' => $flat->id,
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update flat. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Flat $flat)
    {
        try {
            // Ensure the flat belongs to the authenticated house owner
            if ($flat->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to flat.');
            }

            // Check if flat has active tenant assignments
            if ($flat->tenantAssignments()->where('status', 'active')->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete flat with active tenants. Please remove tenants first.');
            }

            $flatNumber = $flat->flat_number;
            $flat->delete();

            Log::info('House owner deleted flat', [
                'user_id' => Auth::id(),
                'flat_id' => $flat->id,
                'flat_number' => $flatNumber
            ]);

            return redirect()->route('house-owner.flats.index')
                ->with('success', 'Flat deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting flat for house owner', [
                'user_id' => Auth::id(),
                'flat_id' => $flat->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to delete flat. Please try again.');
        }
    }
}
