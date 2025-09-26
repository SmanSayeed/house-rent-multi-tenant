<?php

namespace App\Services;

use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\BillCategory;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\TenantAssignment;
use Illuminate\Support\Facades\Auth;

class HouseOwnerService
{
    /**
     * Get dashboard statistics for house owner
     */
    public function getDashboardStats(): array
    {
        $user = Auth::user();

        return [
            'total_buildings' => Building::where('owner_id', $user->id)->count(),
            'total_flats' => Flat::whereHas('building', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->count(),
            'total_tenants' => TenantAssignment::whereHas('building', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->count(),
            'total_bills' => Bill::whereHas('flat.building', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->count(),
            'total_payments' => Payment::whereHas('bill.flat.building', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->count(),
            'pending_payments' => Payment::whereHas('bill.flat.building', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->where('status', 'pending')->count(),
            'paid_payments' => Payment::whereHas('bill.flat.building', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->where('status', 'paid')->count(),
        ];
    }

    /**
     * Get buildings owned by the house owner
     */
    public function getBuildings()
    {
        return Building::where('owner_id', Auth::id())
            ->with(['flats', 'billCategories'])
            ->latest()
            ->paginate(20);
    }

    /**
     * Get flats for a specific building
     */
    public function getFlats(Building $building)
    {
        return $building->flats()->with(['tenantAssignments.tenant'])->paginate(20);
    }

    /**
     * Get tenants for the house owner
     */
    public function getTenants()
    {
        return TenantAssignment::whereHas('building', function($query) {
            $query->where('owner_id', Auth::id());
        })
        ->with(['tenant', 'building', 'flat'])
        ->latest()
        ->paginate(20);
    }

    /**
     * Get bill categories for the house owner
     */
    public function getBillCategories()
    {
        return BillCategory::whereHas('building', function($query) {
            $query->where('owner_id', Auth::id());
        })
        ->with(['building'])
        ->latest()
        ->paginate(20);
    }

    /**
     * Get bills for the house owner
     */
    public function getBills(array $filters = [])
    {
        $query = Bill::whereHas('flat.building', function($query) {
            $query->where('owner_id', Auth::id());
        })
        ->with(['flat.building', 'category']);

        // Filter by status
        if (isset($filters['status']) && in_array($filters['status'], ['paid', 'unpaid'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by building
        if (isset($filters['building_id'])) {
            $query->whereHas('flat', function($q) use ($filters) {
                $q->where('building_id', $filters['building_id']);
            });
        }

        return $query->latest()->paginate(20);
    }

    /**
     * Get payments for the house owner
     */
    public function getPayments(array $filters = [])
    {
        $query = Payment::whereHas('bill.flat.building', function($query) {
            $query->where('owner_id', Auth::id());
        })
        ->with(['bill.flat.building']);

        // Filter by status
        if (isset($filters['status']) && in_array($filters['status'], ['paid', 'pending'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(20);
    }

    /**
     * Create a new building
     */
    public function createBuilding(array $data): Building
    {
        $data['owner_id'] = Auth::id();
        $data['tenant_id'] = Auth::id(); // Multi-tenant isolation

        return Building::create($data);
    }

    /**
     * Update building
     */
    public function updateBuilding(Building $building, array $data): Building
    {
        $building->update($data);
        return $building->fresh();
    }

    /**
     * Create a new flat
     */
    public function createFlat(Building $building, array $data): Flat
    {
        $data['building_id'] = $building->id;
        $data['tenant_id'] = Auth::id(); // Multi-tenant isolation

        return Flat::create($data);
    }

    /**
     * Update flat
     */
    public function updateFlat(Flat $flat, array $data): Flat
    {
        $flat->update($data);
        return $flat->fresh();
    }

    /**
     * Assign tenant to building/flat
     */
    public function assignTenant(array $data): TenantAssignment
    {
        return TenantAssignment::create($data);
    }
}
