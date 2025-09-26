<?php

namespace App\Http\Controllers\HouseOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\HouseOwner\TenantAssignmentRequest;
use App\Models\Building;
use App\Models\Flat;
use App\Models\TenantAssignment;
use App\Models\User;
use App\Services\HouseOwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
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
            $filters = $request->only(['building_id', 'flat_id', 'status', 'search']);
            $tenants = $this->houseOwnerService->getTenants($filters);
            $buildings = $this->houseOwnerService->getBuildings();
            $flats = $this->houseOwnerService->getFlats();

            Log::info('House owner viewed tenants list', [
                'user_id' => Auth::id(),
                'tenants_count' => $tenants->count(),
                'filters' => $filters
            ]);

            return view('house-owner.tenants.index', compact('tenants', 'buildings', 'flats'));
        } catch (\Exception $e) {
            Log::error('Error fetching tenants for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load tenants. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $flatId = $request->get('flat');    
        $buildings = $this->houseOwnerService->getBuildings();
        $flats = $this->houseOwnerService->getFlats();

        // Get available tenants (users with tenant role who are not currently assigned)
        $availableTenants = User::tenants()
            ->whereDoesntHave('tenantAssignments', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        return view('house-owner.tenants.create', compact('buildings', 'flats', 'availableTenants', 'flatId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantAssignmentRequest $request)
    {
        try {
            $validated = $request->validated();

            // Ensure the flat and building belong to the authenticated house owner
            $flat = Flat::where('id', $validated['flat_id'])
                ->whereHas('building', function ($query) {
                    $query->where('owner_id', Auth::id());
                })
                ->first();

            if (!$flat) {
                Log::error('Flat not found or does not belong to house owner', [
                    'flat_id' => $validated['flat_id'],
                    'house_owner_id' => Auth::id()
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected flat does not belong to you.');
            }

            // Check if the flat is already occupied
            $existingAssignment = TenantAssignment::where('flat_id', $validated['flat_id'])
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This flat is already occupied by another tenant.');
            }

            $assignment = TenantAssignment::create([
                'tenant_id' => $validated['tenant_id'],
                'flat_id' => $validated['flat_id'],
                'building_id' => $validated['building_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'monthly_rent' => $validated['monthly_rent'],
                'status' => $validated['status'],
            ]);

            // Update flat status to occupied
            $flat->update(['status' => 'occupied']);

            Log::info('House owner assigned tenant to flat', [
                'user_id' => Auth::id(),
                'assignment_id' => $assignment->id,
                'tenant_id' => $assignment->tenant_id,
                'flat_id' => $assignment->flat_id
            ]);

            return redirect()->route('house-owner.tenants.index')
                ->with('success', 'Tenant assigned to flat successfully!');
        } catch (\Exception $e) {
            Log::error('Error assigning tenant to flat for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to assign tenant to flat. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TenantAssignment $tenant)
    {
        try {
            // Ensure the tenant assignment belongs to the authenticated house owner
            if ($tenant->building->owner_id !== Auth::id()) {
                Log::warning('House owner attempted to access unauthorized tenant assignment', [
                    'user_id' => Auth::id(),
                    'assignment_id' => $tenant->id
                ]);

                abort(403, 'Unauthorized access to tenant assignment.');
            }

            $tenant->load(['tenant', 'flat', 'building']);

            Log::info('House owner viewed tenant assignment details', [
                'user_id' => Auth::id(),
                'assignment_id' => $tenant->id,
                'tenant_id' => $tenant->tenant_id
            ]);

            return view('house-owner.tenants.show', compact('tenant'));
        } catch (\Exception $e) {
            Log::error('Error fetching tenant assignment details for house owner', [
                'user_id' => Auth::id(),
                'assignment_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load tenant details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TenantAssignment $tenant)
    {
        // Ensure the tenant assignment belongs to the authenticated house owner
        if ($tenant->building->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to tenant assignment.');
        }

        $buildings = $this->houseOwnerService->getBuildings();
        $flats = $this->houseOwnerService->getFlats();

        return view('house-owner.tenants.edit', compact('tenant', 'buildings', 'flats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TenantAssignmentRequest $request, TenantAssignment $tenant)
    {
        try {
            // Ensure the tenant assignment belongs to the authenticated house owner
            if ($tenant->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to tenant assignment.');
            }

            $validated = $request->validated();

            // Ensure the new flat belongs to the authenticated house owner
            $flat = Flat::where('id', $validated['flat_id'])
                ->whereHas('building', function ($query) {
                    $query->where('owner_id', Auth::id());
                })
                ->first();

            if (!$flat) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected flat does not belong to you.');
            }

            $oldFlatId = $tenant->flat_id;
            $oldStatus = $tenant->status;

            $tenant->update([
                'tenant_id' => $validated['tenant_id'],
                'flat_id' => $validated['flat_id'],
                'building_id' => $validated['building_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'monthly_rent' => $validated['monthly_rent'],
                'status' => $validated['status'],
            ]);

            // Update flat statuses
            if ($oldFlatId !== $validated['flat_id']) {
                // Free up the old flat
                Flat::where('id', $oldFlatId)->update(['status' => 'available']);
                // Occupy the new flat
                $flat->update(['status' => 'occupied']);
            }

            Log::info('House owner updated tenant assignment', [
                'user_id' => Auth::id(),
                'assignment_id' => $tenant->id,
                'tenant_id' => $tenant->tenant_id
            ]);

            return redirect()->route('house-owner.tenants.show', $tenant)
                ->with('success', 'Tenant assignment updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating tenant assignment for house owner', [
                'user_id' => Auth::id(),
                'assignment_id' => $tenant->id,
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update tenant assignment. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TenantAssignment $tenant)
    {
        try {
            // Ensure the tenant assignment belongs to the authenticated house owner
            if ($tenant->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to tenant assignment.');
            }

            // Update flat status to available
            $tenant->flat->update(['status' => 'available']);

            $tenantId = $tenant->tenant_id;
            $flatId = $tenant->flat_id;
            $tenant->delete();

            Log::info('House owner removed tenant assignment', [
                'user_id' => Auth::id(),
                'assignment_id' => $tenant->id,
                'tenant_id' => $tenantId,
                'flat_id' => $flatId
            ]);

            return redirect()->route('house-owner.tenants.index')
                ->with('success', 'Tenant assignment removed successfully!');
        } catch (\Exception $e) {
            Log::error('Error removing tenant assignment for house owner', [
                'user_id' => Auth::id(),
                'assignment_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to remove tenant assignment. Please try again.');
        }
    }

    /**
     * Terminate a tenant assignment
     */
    public function terminate(TenantAssignment $tenant)
    {
        try {
            // Ensure the tenant assignment belongs to the authenticated house owner
            if ($tenant->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to tenant assignment.');
            }

            $tenant->update([
                'status' => 'terminated',
                'end_date' => now()
            ]);

            // Update flat status to available
            $tenant->flat->update(['status' => 'available']);

            Log::info('House owner terminated tenant assignment', [
                'user_id' => Auth::id(),
                'assignment_id' => $tenant->id,
                'tenant_id' => $tenant->tenant_id
            ]);

            return redirect()->route('house-owner.tenants.show', $tenant)
                ->with('success', 'Tenant assignment terminated successfully!');
        } catch (\Exception $e) {
            Log::error('Error terminating tenant assignment for house owner', [
                'user_id' => Auth::id(),
                'assignment_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to terminate tenant assignment. Please try again.');
        }
    }
}
