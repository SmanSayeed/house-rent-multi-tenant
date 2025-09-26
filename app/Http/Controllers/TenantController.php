<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\TenantAssignment;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display tenant dashboard
     */
    public function dashboard()
    {
        try {
            $stats = $this->tenantService->getDashboardStats();
            $recentBills = $this->tenantService->getRecentBills();
            $currentAssignment = $this->tenantService->getCurrentAssignment();

            Log::info('Tenant viewed dashboard', [
                'user_id' => Auth::id(),
                'stats' => $stats
            ]);

            return view('tenant.dashboard', compact('stats', 'recentBills', 'currentAssignment'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant dashboard', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load dashboard. Please try again.');
        }
    }

    /**
     * Display tenant's bills
     */
    public function bills(Request $request)
    {
        try {
            $filters = $request->only(['status', 'search']);
            $bills = $this->tenantService->getBills($filters);

            Log::info('Tenant viewed bills', [
                'user_id' => Auth::id(),
                'bills_count' => $bills->count(),
                'filters' => $filters
            ]);

            return view('tenant.bills.index', compact('bills', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant bills', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load bills. Please try again.');
        }
    }

    /**
     * Display specific bill details
     */
    public function showBill(Bill $bill)
    {
        try {
            // Ensure the bill belongs to the authenticated tenant
            if (!$this->tenantService->canAccessBill($bill)) {
                Log::warning('Tenant attempted to access unauthorized bill', [
                    'user_id' => Auth::id(),
                    'bill_id' => $bill->id
                ]);
                abort(403, 'Unauthorized access to bill.');
            }

            $bill->load(['flat.building', 'category', 'payments']);

            Log::info('Tenant viewed bill details', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id
            ]);

            return view('tenant.bills.show', compact('bill'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant bill details', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load bill details. Please try again.');
        }
    }

    /**
     * Display tenant's payments
     */
    public function payments(Request $request)
    {
        try {
            $filters = $request->only(['status', 'search']);
            $payments = $this->tenantService->getPayments($filters);

            Log::info('Tenant viewed payments', [
                'user_id' => Auth::id(),
                'payments_count' => $payments->count(),
                'filters' => $filters
            ]);

            return view('tenant.payments.index', compact('payments', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant payments', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load payments. Please try again.');
        }
    }

    /**
     * Display tenant profile
     */
    public function profile()
    {
        try {
            $user = Auth::user();
            $currentAssignment = $this->tenantService->getCurrentAssignment();

            Log::info('Tenant viewed profile', [
                'user_id' => Auth::id()
            ]);

            return view('tenant.profile.index', compact('user', 'currentAssignment'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant profile', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load profile. Please try again.');
        }
    }

    /**
     * Update tenant profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'contact' => 'nullable|string|max:20',
            ]);

            Auth::user()->update($validated);

            Log::info('Tenant updated profile', [
                'user_id' => Auth::id(),
                'changes' => $validated
            ]);

            return redirect()->route('tenant.profile')
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating tenant profile', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Display tenant settings
     */
    public function settings()
    {
        try {
            $user = Auth::user();

            Log::info('Tenant viewed settings', [
                'user_id' => Auth::id()
            ]);

            return view('tenant.settings.index', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load settings. Please try again.');
        }
    }

    /**
     * Display notifications
     */
    public function notifications()
    {
        try {
            $notifications = $this->tenantService->getNotifications();

            Log::info('Tenant viewed notifications', [
                'user_id' => Auth::id(),
                'notifications_count' => $notifications->count()
            ]);

            return view('tenant.notifications.index', compact('notifications'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant notifications', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load notifications. Please try again.');
        }
    }

    /**
     * Display support page
     */
    public function support()
    {
        try {
            $currentAssignment = $this->tenantService->getCurrentAssignment();

            Log::info('Tenant viewed support page', [
                'user_id' => Auth::id()
            ]);

            return view('tenant.support.index', compact('currentAssignment'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant support page', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load support page. Please try again.');
        }
    }

    /**
     * Submit support request
     */
    public function submitSupport(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:1000',
                'priority' => 'required|in:low,medium,high',
            ]);

            // Here you would typically save the support request to a database
            // For now, we'll just log it
            Log::info('Tenant submitted support request', [
                'user_id' => Auth::id(),
                'subject' => $validated['subject'],
                'priority' => $validated['priority']
            ]);

            return redirect()->route('tenant.support')
                ->with('success', 'Support request submitted successfully! We will get back to you soon.');
        } catch (\Exception $e) {
            Log::error('Error submitting tenant support request', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit support request. Please try again.');
        }
    }
}
