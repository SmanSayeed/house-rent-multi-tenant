<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Bill;
use App\Models\Payment;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = $this->adminService->getDashboardStats();
        $recentUsers = $this->adminService->getRecentUsers();
        $recentBuildings = $this->adminService->getRecentBuildings();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentBuildings'));
    }


    /**
     * Show users management page
     */
    public function users()
    {
        $users = $this->adminService->getUsers();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load(['buildings', 'tenantAssignments']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show buildings management page
     */
    public function buildings()
    {
        $buildings = $this->adminService->getBuildings();

        return view('admin.buildings.index', compact('buildings'));
    }

    /**
     * Show building details
     */
    public function showBuilding(Building $building)
    {
        $building->load(['owner', 'flats.tenantAssignments.tenant']);

        return view('admin.buildings.show', compact('building'));
    }

    /**
     * Get flats for a building
     */
    public function getBuildingFlats(Building $building)
    {
        $flats = $building->flats()->with(['bills.category', 'tenantAssignments.tenant'])->get();
        return view('admin.buildings.flats', compact('building', 'flats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function createUser()
    {
        try {
            Log::info('Admin accessed create user form', ['user_id' => Auth::id()]);
            return view('admin.users.create');
        } catch (\Exception $e) {
            Log::error('Error accessing create user form for admin', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load create form. Please try again.');
        }
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|in:admin,house_owner,tenant',
                'contact' => 'nullable|string|max:20',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'contact' => $validated['contact'],
            ]);

            Log::info('Admin created new user', [
                'user_id' => Auth::id(),
                'created_user_id' => $user->id,
                'created_user_email' => $user->email,
                'created_user_role' => $user->role
            ]);

            return redirect()->route('admin.users')
                ->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating user for admin', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function editUser(User $user)
    {
        try {
            Log::info('Admin accessed edit user form', ['user_id' => Auth::id(), 'target_user_id' => $user->id]);
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error accessing edit user form for admin', [
                'user_id' => Auth::id(),
                'target_user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load edit form. Please try again.');
        }
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'role' => 'required|in:admin,house_owner,tenant',
                'contact' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'contact' => $validated['contact'],
            ];

            if ($validated['password']) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            Log::info('Admin updated user', [
                'user_id' => Auth::id(),
                'target_user_id' => $user->id,
                'changes' => $user->getChanges()
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating user for admin', [
                'user_id' => Auth::id(),
                'target_user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroyUser(User $user)
    {
        try {
            // Prevent admin from deleting themselves
            if ($user->id === Auth::id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            // Check if user has associated data
            $hasData = false;
            $dataTypes = [];

            if ($user->role === 'house_owner' && $user->buildings()->count() > 0) {
                $hasData = true;
                $dataTypes[] = 'buildings';
            }

            if ($user->role === 'tenant' && $user->tenantAssignments()->count() > 0) {
                $hasData = true;
                $dataTypes[] = 'tenant assignments';
            }

            if ($hasData) {
                return redirect()->back()->with('error',
                    'Cannot delete user with associated ' . implode(', ', $dataTypes) . '. Please remove the associated data first.');
            }

            $user->delete();

            Log::info('Admin deleted user', [
                'user_id' => Auth::id(),
                'deleted_user_id' => $user->id,
                'deleted_user_email' => $user->email
            ]);

            return redirect()->route('admin.users')
                ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting user for admin', [
                'user_id' => Auth::id(),
                'target_user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Show system settings
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Get dashboard data for API
     */
    public function getDashboardData()
    {
        $stats = $this->adminService->getDashboardStats();
        $recentUsers = $this->adminService->getRecentUsers();
        $recentBuildings = $this->adminService->getRecentBuildings();

        return ResponseHelper::success([
            'stats' => $stats,
            'recent_users' => $recentUsers,
            'recent_buildings' => $recentBuildings,
        ]);
    }

    /**
     * Get users data for API
     */
    public function getUsers(Request $request)
    {
        $filters = $request->only(['role', 'search']);
        $users = $this->adminService->getUsers($filters);

        return ResponseHelper::paginated($users);
    }

    /**
     * Get buildings data for API
     */
    public function getBuildings(Request $request)
    {
        $buildings = $this->adminService->getBuildings();

        return ResponseHelper::paginated($buildings);
    }

    /**
     * Show all bills for admin
     */
    public function bills(Request $request)
    {
        $bills = $this->adminService->getAllBills();

        return view('admin.bills.index', compact('bills'));
    }

    /**
     * Show specific bill for admin
     */
    public function showBill($billId)
    {
        $bill = $this->adminService->getBillById($billId);

        if (!$bill) {
            return redirect()->route('admin.bills')->with('error', 'Bill not found.');
        }

        return view('admin.bills.show', compact('bill'));
    }

    /**
     * Show all payments for admin
     */
    public function payments(Request $request)
    {
        $payments = $this->adminService->getAllPayments();

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show specific payment for admin
     */
    public function showPayment($paymentId)
    {
        $payment = $this->adminService->getPaymentById($paymentId);

        if (!$payment) {
            return redirect()->route('admin.payments')->with('error', 'Payment not found.');
        }

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Assign tenant to flat
     */
    public function assignTenantToFlat(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'flat_id' => 'required|exists:flats,id',
        ]);

        try {
            // Check if tenant is already assigned to this flat
            $existingAssignment = \App\Models\TenantAssignment::where('tenant_id', $request->tenant_id)
                ->where('flat_id', $request->flat_id)
                ->first();

            if ($existingAssignment) {
                return redirect()->back()->with('error', 'Tenant is already assigned to this flat.');
            }

            // Create new assignment
            \App\Models\TenantAssignment::create([
                'tenant_id' => $request->tenant_id,
                'flat_id' => $request->flat_id,
                'assigned_at' => now(),
            ]);

            Log::info('Admin assigned tenant to flat', [
                'admin_id' => Auth::id(),
                'tenant_id' => $request->tenant_id,
                'flat_id' => $request->flat_id,
            ]);

            return redirect()->back()->with('success', 'Tenant assigned to flat successfully!');
        } catch (\Exception $e) {
            Log::error('Error assigning tenant to flat', [
                'admin_id' => Auth::id(),
                'tenant_id' => $request->tenant_id,
                'flat_id' => $request->flat_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to assign tenant. Please try again.');
        }
    }
}
