<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Flat;
use App\Models\TenantAssignment;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Display a listing of all tenants.
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'status', 'building_id', 'flat_id']);
            $tenants = $this->adminService->getAllTenants($filters);
            $buildings = Building::with('flats')->get();

            Log::info('Admin viewed tenants list', ['user_id' => Auth::id(), 'tenants_count' => $tenants->count(), 'filters' => $filters]);
            return view('admin.tenants.index', compact('tenants', 'buildings', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error loading tenants for admin', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load tenants. Please try again.');
        }
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        try {
            $buildings = Building::with('flats')->get();
            Log::info('Admin accessed create tenant form', ['user_id' => Auth::id()]);
            return view('admin.tenants.create', compact('buildings'));
        } catch (\Exception $e) {
            Log::error('Error accessing create tenant form for admin', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load create form. Please try again.');
        }
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'contact' => 'nullable|string|max:20',
                'flat_id' => 'nullable|exists:flats,id',
                'monthly_rent' => 'nullable|numeric|min:0',
                'start_date' => 'nullable|date',
            ]);

            $tenant = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'tenant',
                'contact' => $validated['contact'],
            ]);

            // If flat_id is provided, assign tenant to flat
            if ($validated['flat_id']) {
                TenantAssignment::create([
                    'tenant_id' => $tenant->id,
                    'flat_id' => $validated['flat_id'],
                    'monthly_rent' => $validated['monthly_rent'] ?? 0,
                    'start_date' => $validated['start_date'] ?? now(),
                    'status' => 'active',
                ]);
            }

            Log::info('Admin created new tenant', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'tenant_email' => $tenant->email,
                'flat_assigned' => $validated['flat_id'] ? true : false
            ]);

            return redirect()->route('admin.tenants.index')
                ->with('success', 'Tenant created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating tenant for admin', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create tenant. Please try again.');
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show(User $tenant)
    {
        try {
            if (!$tenant->isTenant()) {
                Log::warning('Admin attempted to view non-tenant user', [
                    'user_id' => Auth::id(),
                    'target_user_id' => $tenant->id
                ]);
                abort(404, 'Tenant not found.');
            }

            $tenant->load(['tenantAssignments.flat.building', 'tenantAssignments.flat.tenantAssignments.tenant']);
            $buildings = Building::with('flats')->get();

            Log::info('Admin viewed tenant details', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'tenant_email' => $tenant->email
            ]);

            return view('admin.tenants.show', compact('tenant', 'buildings'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant details for admin', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load tenant details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(User $tenant)
    {
        try {
            if (!$tenant->isTenant()) {
                Log::warning('Admin attempted to edit non-tenant user', [
                    'user_id' => Auth::id(),
                    'target_user_id' => $tenant->id
                ]);
                abort(404, 'Tenant not found.');
            }

            $buildings = Building::with('flats')->get();
            Log::info('Admin accessed edit tenant form', ['user_id' => Auth::id(), 'tenant_id' => $tenant->id]);
            return view('admin.tenants.edit', compact('tenant', 'buildings'));
        } catch (\Exception $e) {
            Log::error('Error accessing edit tenant form for admin', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load edit form. Please try again.');
        }
    }

    /**
     * Update the specified tenant.
     */
    public function update(Request $request, User $tenant)
    {
        try {
            if (!$tenant->isTenant()) {
                Log::warning('Admin attempted to update non-tenant user', [
                    'user_id' => Auth::id(),
                    'target_user_id' => $tenant->id
                ]);
                abort(404, 'Tenant not found.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $tenant->id,
                'contact' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'contact' => $validated['contact'],
            ];

            if ($validated['password']) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $tenant->update($updateData);

            Log::info('Admin updated tenant', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'changes' => $tenant->getChanges()
            ]);

            return redirect()->route('admin.tenants.show', $tenant)
                ->with('success', 'Tenant updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating tenant for admin', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update tenant. Please try again.');
        }
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(User $tenant)
    {
        try {
            if (!$tenant->isTenant()) {
                Log::warning('Admin attempted to delete non-tenant user', [
                    'user_id' => Auth::id(),
                    'target_user_id' => $tenant->id
                ]);
                abort(404, 'Tenant not found.');
            }

            // Check if tenant has active assignments
            $activeAssignments = $tenant->tenantAssignments()->where('status', 'active')->count();
            if ($activeAssignments > 0) {
                return redirect()->back()->with('error', 'Cannot delete tenant with active flat assignments. Please remove assignments first.');
            }

            $tenant->delete();

            Log::info('Admin deleted tenant', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'tenant_email' => $tenant->email
            ]);

            return redirect()->route('admin.tenants.index')
                ->with('success', 'Tenant deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting tenant for admin', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to delete tenant. Please try again.');
        }
    }

    /**
     * Assign tenant to a flat.
     */
    public function assignToFlat(Request $request, User $tenant)
    {
        try {
            if (!$tenant->isTenant()) {
                Log::warning('Admin attempted to assign non-tenant user', [
                    'user_id' => Auth::id(),
                    'target_user_id' => $tenant->id
                ]);
                abort(404, 'Tenant not found.');
            }

            $validated = $request->validate([
                'flat_id' => 'required|exists:flats,id',
                'monthly_rent' => 'required|numeric|min:0',
                'start_date' => 'required|date',
            ]);

            // Check if flat is already occupied
            $existingAssignment = TenantAssignment::where('flat_id', $validated['flat_id'])
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                return redirect()->back()->with('error', 'This flat is already occupied by another tenant.');
            }

            TenantAssignment::create([
                'tenant_id' => $tenant->id,
                'flat_id' => $validated['flat_id'],
                'monthly_rent' => $validated['monthly_rent'],
                'start_date' => $validated['start_date'],
                'status' => 'active',
            ]);

            Log::info('Admin assigned tenant to flat', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'flat_id' => $validated['flat_id'],
                'monthly_rent' => $validated['monthly_rent']
            ]);

            return redirect()->route('admin.tenants.show', $tenant)
                ->with('success', 'Tenant assigned to flat successfully!');
        } catch (\Exception $e) {
            Log::error('Error assigning tenant to flat for admin', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Failed to assign tenant to flat. Please try again.');
        }
    }

    /**
     * Remove tenant from flat.
     */
    public function removeFromFlat(Request $request, User $tenant)
    {
        try {
            if (!$tenant->isTenant()) {
                Log::warning('Admin attempted to remove non-tenant user', [
                    'user_id' => Auth::id(),
                    'target_user_id' => $tenant->id
                ]);
                abort(404, 'Tenant not found.');
            }

            $validated = $request->validate([
                'assignment_id' => 'required|exists:tenant_assignments,id',
                'end_date' => 'required|date',
            ]);

            $assignment = TenantAssignment::where('id', $validated['assignment_id'])
                ->where('tenant_id', $tenant->id)
                ->where('status', 'active')
                ->first();

            if (!$assignment) {
                return redirect()->back()->with('error', 'Active assignment not found.');
            }

            $assignment->update([
                'status' => 'terminated',
                'end_date' => $validated['end_date'],
            ]);

            Log::info('Admin removed tenant from flat', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'assignment_id' => $validated['assignment_id'],
                'end_date' => $validated['end_date']
            ]);

            return redirect()->route('admin.tenants.show', $tenant)
                ->with('success', 'Tenant removed from flat successfully!');
        } catch (\Exception $e) {
            Log::error('Error removing tenant from flat for admin', [
                'user_id' => Auth::id(),
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Failed to remove tenant from flat. Please try again.');
        }
    }
}
