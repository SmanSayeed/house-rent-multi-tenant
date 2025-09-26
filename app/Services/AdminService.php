<?php

namespace App\Services;

use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;

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
}
