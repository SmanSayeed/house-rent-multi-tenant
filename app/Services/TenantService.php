<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\TenantAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantService
{
    /**
     * Get dashboard statistics for tenant
     */
    public function getDashboardStats(): array
    {
        $tenantId = Auth::id();

        $totalBills = Bill::whereHas('flat.tenantAssignments', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId)->where('status', 'active');
        })->count();

        $pendingBills = Bill::whereHas('flat.tenantAssignments', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId)->where('status', 'active');
        })->where('status', 'pending')->count();

        $paidBills = Bill::whereHas('flat.tenantAssignments', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId)->where('status', 'active');
        })->where('status', 'paid')->count();

        $totalAmount = Bill::whereHas('flat.tenantAssignments', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId)->where('status', 'active');
        })->sum('amount');

        return [
            'total_bills' => $totalBills,
            'pending_bills' => $pendingBills,
            'paid_bills' => $paidBills,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Get recent bills for tenant
     */
    public function getRecentBills(int $limit = 5): LengthAwarePaginator
    {
        $tenantId = Auth::id();

        return Bill::with(['flat.building', 'category'])
            ->whereHas('flat.tenantAssignments', function($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId)->where('status', 'active');
            })
            ->latest()
            ->paginate($limit);
    }

    /**
     * Get all bills for tenant with optional filters
     */
    public function getBills(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $tenantId = Auth::id();

        $query = Bill::with(['flat.building', 'category', 'payments'])
            ->whereHas('flat.tenantAssignments', function($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId)->where('status', 'active');
            });

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
     * Get payments for tenant with optional filters
     */
    public function getPayments(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $tenantId = Auth::id();

        $query = Payment::with(['bill.flat.building'])
            ->where('tenant_id', $tenantId);

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->whereHas('bill', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get current tenant assignment
     */
    public function getCurrentAssignment(): ?TenantAssignment
    {
        $tenantId = Auth::id();

        return TenantAssignment::with(['flat.building', 'tenant'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Check if tenant can access a specific bill
     */
    public function canAccessBill(Bill $bill): bool
    {
        $tenantId = Auth::id();

        return $bill->flat->tenantAssignments()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get notifications for tenant
     */
    public function getNotifications(int $perPage = 20): LengthAwarePaginator
    {
        // For now, return empty collection
        // In a real application, you would have a notifications table
        return new LengthAwarePaginator([], 0, $perPage);
    }

    /**
     * Get tenant profile data
     */
    public function getProfile(): User
    {
        return Auth::user();
    }

    /**
     * Update tenant profile
     */
    public function updateProfile(array $data): bool
    {
        $user = Auth::user();
        return $user->update($data);
    }

    /**
     * Get tenant settings
     */
    public function getSettings(): array
    {
        $user = Auth::user();

        return [
            'email_notifications' => true, // Default settings
            'sms_notifications' => false,
            'language' => 'en',
            'timezone' => 'UTC',
        ];
    }

    /**
     * Update tenant settings
     */
    public function updateSettings(array $data): bool
    {
        // For now, just return true
        // In a real application, you would save settings to a user_settings table
        return true;
    }
}
