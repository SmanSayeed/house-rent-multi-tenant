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
     * Get flats for a building (API endpoint)
     */
    public function getBuildingFlats(Building $building)
    {
        $flats = $building->flats()->select('id', 'flat_number', 'rent_amount')->get();
        return response()->json($flats);
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
}
