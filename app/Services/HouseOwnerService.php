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
use Illuminate\Pagination\LengthAwarePaginator;

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
            ->with(['flats'])
            ->latest()
            ->paginate(20);
    }

    /**
     * Get flats for the house owner with optional filters
     */
    public function getFlats(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $houseOwnerId = Auth::id();
        $query = Flat::with(['building', 'tenantAssignments.tenant'])
            ->whereHas('building', function ($query) use ($houseOwnerId) {
                $query->where('owner_id', $houseOwnerId);
            });

        if (isset($filters['building_id']) && $filters['building_id']) {
            $query->where('building_id', $filters['building_id']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('flat_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get flats for a specific building
     */
    public function getFlatsForBuilding(Building $building)
    {
        return $building->flats()->with(['tenantAssignments.tenant'])->paginate(20);
    }

    /**
     * Get tenants for the house owner with optional filters
     */
    public function getTenants(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $houseOwnerId = Auth::id();
        $query = TenantAssignment::with(['tenant', 'building', 'flat'])
            ->whereHas('building', function($query) use ($houseOwnerId) {
                $query->where('owner_id', $houseOwnerId);
            });

        if (isset($filters['building_id']) && $filters['building_id']) {
            $query->where('building_id', $filters['building_id']);
        }

        if (isset($filters['flat_id']) && $filters['flat_id']) {
            $query->where('flat_id', $filters['flat_id']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get bill categories for the house owner
     */
    public function getBillCategories()
    {
        return BillCategory::where('is_active', true)
            ->latest()
            ->paginate(20);
    }

    /**
     * Get bills for the house owner with optional filters
     */
    public function getBills(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $houseOwnerId = Auth::id();
        $query = Bill::with(['flat.building', 'category', 'payments'])
            ->whereHas('flat.building', function($query) use ($houseOwnerId) {
                $query->where('owner_id', $houseOwnerId);
            });

        if (isset($filters['flat_id']) && $filters['flat_id']) {
            $query->where('flat_id', $filters['flat_id']);
        }

        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get payments for the house owner
     */
    public function getPayments(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $houseOwnerId = Auth::id();
        $query = Payment::with(['bill.flat.building', 'tenant'])
            ->whereHas('bill.flat.building', function($query) use ($houseOwnerId) {
                $query->where('owner_id', $houseOwnerId);
            });

        // Filter by status
        if (isset($filters['status']) && in_array($filters['status'], ['completed', 'pending', 'failed'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
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
