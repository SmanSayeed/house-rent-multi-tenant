<?php

namespace App\Services;

use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminService
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_admins' => User::admins()->count(),
            'total_house_owners' => User::houseOwners()->count(),
            'total_tenants' => User::tenants()->count(),
            'total_buildings' => Building::count(),
            'total_flats' => Flat::count(),
            'total_bills' => Bill::count(),
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'paid_payments' => Payment::where('status', 'paid')->count(),
        ];
    }

    /**
     * Get recent users
     */
    public function getRecentUsers(int $limit = 5)
    {
        return User::latest()->take($limit)->get();
    }

    /**
     * Get recent buildings
     */
    public function getRecentBuildings(int $limit = 5)
    {
        return Building::with(['owner'])->latest()->take($limit)->get();
    }

    /**
     * Get all users with pagination
     */
    public function getUsers(array $filters = [])
    {
        $query = User::query();

        // Filter by role
        if (isset($filters['role']) && in_array($filters['role'], ['admin', 'house_owner', 'tenant'])) {
            $query->where('role', $filters['role']);
        }

        // Search by name or email
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(20);
    }

    /**
     * Get all buildings with pagination
     */
    public function getBuildings()
    {
        return Building::with(['owner', 'flats'])->latest()->paginate(20);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user->fresh();
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Get all tenants with optional filters
     */
    public function getAllTenants(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = User::tenants()->with(['tenantAssignments.flat.building']);

        // Filter by search term
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%");
            });
        }

        // Filter by status (active/inactive based on assignments)
        if (isset($filters['status']) && $filters['status']) {
            if ($filters['status'] === 'active') {
                $query->whereHas('tenantAssignments', function ($q) {
                    $q->where('status', 'active');
                });
            } elseif ($filters['status'] === 'inactive') {
                $query->whereDoesntHave('tenantAssignments', function ($q) {
                    $q->where('status', 'active');
                });
            }
        }

        // Filter by building
        if (isset($filters['building_id']) && $filters['building_id']) {
            $query->whereHas('tenantAssignments.flat', function ($q) use ($filters) {
                $q->where('building_id', $filters['building_id']);
            });
        }

        // Filter by flat
        if (isset($filters['flat_id']) && $filters['flat_id']) {
            $query->whereHas('tenantAssignments', function ($q) use ($filters) {
                $q->where('flat_id', $filters['flat_id']);
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get all bills across all tenants
     */
    public function getAllBills()
    {
        return Bill::with(['flat.building.owner', 'category', 'payments'])
            ->latest()
            ->paginate(15);
    }

    /**
     * Get specific bill by ID
     */
    public function getBillById($billId)
    {
        return Bill::with(['flat.building.owner', 'category', 'payments'])
            ->find($billId);
    }

    /**
     * Get all payments across all tenants
     */
    public function getAllPayments()
    {
        return Payment::with(['bill.flat.building.owner', 'bill.category'])
            ->latest()
            ->paginate(15);
    }

    /**
     * Get specific payment by ID
     */
    public function getPaymentById($paymentId)
    {
        return Payment::with(['bill.flat.building.owner', 'bill.category'])
            ->find($paymentId);
    }
}
