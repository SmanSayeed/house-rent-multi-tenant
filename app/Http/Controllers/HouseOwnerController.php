<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\HouseOwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HouseOwnerController extends Controller
{
    protected HouseOwnerService $houseOwnerService;

    public function __construct(HouseOwnerService $houseOwnerService)
    {
        $this->houseOwnerService = $houseOwnerService;
    }

    /**
     * Show house owner dashboard
     */
    public function dashboard()
    {
        $stats = $this->houseOwnerService->getDashboardStats();
        $recentBuildings = $this->houseOwnerService->getBuildings();

        return view('house-owner.dashboard', compact('stats', 'recentBuildings'));
    }

    /**
     * Show buildings management page
     */
    public function buildings()
    {
        $buildings = $this->houseOwnerService->getBuildings();

        return view('house-owner.buildings.index', compact('buildings'));
    }

    /**
     * Show flats management page
     */
    public function flats()
    {
        $flats = $this->houseOwnerService->getFlats();

        return view('house-owner.flats.index', compact('flats'));
    }

    /**
     * Show tenants management page
     */
    public function tenants()
    {
        $tenants = $this->houseOwnerService->getTenants();

        return view('house-owner.tenants.index', compact('tenants'));
    }

    /**
     * Show bills management page
     */
    public function bills()
    {
        $bills = $this->houseOwnerService->getBills();

        return view('house-owner.bills.index', compact('bills'));
    }

    /**
     * Show payments management page
     */
    public function payments()
    {
        $payments = $this->houseOwnerService->getPayments();

        return view('house-owner.payments.index', compact('payments'));
    }
}
