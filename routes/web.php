<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HouseOwnerAuthController;
use App\Http\Controllers\HouseOwnerController;

// Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Admin Routes
Route::prefix('admin')->group(function () {
    // Admin Authentication
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Admin Protected Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
        Route::get('/buildings', [AdminController::class, 'buildings'])->name('admin.buildings');
        Route::get('/buildings/{building}', [AdminController::class, 'showBuilding'])->name('admin.buildings.show');
        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

        // Admin Profile Routes
        Route::get('/profile', [AdminAuthController::class, 'showProfile'])->name('admin.profile');
        Route::put('/profile/update', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');
        Route::get('/profile/change-password', [AdminAuthController::class, 'showChangePassword'])->name('admin.profile.change-password');
        Route::put('/profile/change-password', [AdminAuthController::class, 'changePassword'])->name('admin.profile.change-password.update');
    });
});

// House Owner Routes
Route::prefix('house-owner')->group(function () {
    // House Owner Authentication
    Route::get('/login', [HouseOwnerAuthController::class, 'showLogin'])->name('house-owner.login');
    Route::post('/login', [HouseOwnerAuthController::class, 'login']);
    Route::post('/logout', [HouseOwnerAuthController::class, 'logout'])->name('house-owner.logout');

    // House Owner Protected Routes
    Route::middleware(['auth', 'house-owner'])->group(function () {
        Route::get('/dashboard', [HouseOwnerController::class, 'dashboard'])->name('house-owner.dashboard');

        // Building Management Routes
        Route::resource('buildings', \App\Http\Controllers\HouseOwner\BuildingController::class)
            ->names([
                'index' => 'house-owner.buildings.index',
                'create' => 'house-owner.buildings.create',
                'store' => 'house-owner.buildings.store',
                'show' => 'house-owner.buildings.show',
                'edit' => 'house-owner.buildings.edit',
                'update' => 'house-owner.buildings.update',
                'destroy' => 'house-owner.buildings.destroy',
            ]);

        // Flat Management Routes
        Route::resource('flats', \App\Http\Controllers\HouseOwner\FlatController::class)
            ->names([
                'index' => 'house-owner.flats.index',
                'create' => 'house-owner.flats.create',
                'store' => 'house-owner.flats.store',
                'show' => 'house-owner.flats.show',
                'edit' => 'house-owner.flats.edit',
                'update' => 'house-owner.flats.update',
                'destroy' => 'house-owner.flats.destroy',
            ]);

        // Tenant Management Routes
        Route::resource('tenants', \App\Http\Controllers\HouseOwner\TenantController::class)
            ->names([
                'index' => 'house-owner.tenants.index',
                'create' => 'house-owner.tenants.create',
                'store' => 'house-owner.tenants.store',
                'show' => 'house-owner.tenants.show',
                'edit' => 'house-owner.tenants.edit',
                'update' => 'house-owner.tenants.update',
                'destroy' => 'house-owner.tenants.destroy',
            ]);
        Route::patch('/tenants/{tenant}/terminate', [\App\Http\Controllers\HouseOwner\TenantController::class, 'terminate'])->name('house-owner.tenants.terminate');

        // Bill Management Routes
        Route::resource('bills', \App\Http\Controllers\HouseOwner\BillController::class)
            ->names([
                'index' => 'house-owner.bills.index',
                'create' => 'house-owner.bills.create',
                'store' => 'house-owner.bills.store',
                'show' => 'house-owner.bills.show',
                'edit' => 'house-owner.bills.edit',
                'update' => 'house-owner.bills.update',
                'destroy' => 'house-owner.bills.destroy',
            ]);
        Route::get('/bills/{bill}/invoice', [\App\Http\Controllers\HouseOwner\BillController::class, 'invoice'])->name('house-owner.bills.invoice');
        Route::patch('/bills/{bill}/mark-as-paid', [\App\Http\Controllers\HouseOwner\BillController::class, 'markAsPaid'])->name('house-owner.bills.mark-as-paid');
        Route::get('/payments', [HouseOwnerController::class, 'payments'])->name('house-owner.payments');

        // House Owner Profile Routes
        Route::get('/profile', [HouseOwnerAuthController::class, 'showProfile'])->name('house-owner.profile');
        Route::put('/profile/update', [HouseOwnerAuthController::class, 'updateProfile'])->name('house-owner.profile.update');
        Route::get('/profile/change-password', [HouseOwnerAuthController::class, 'showChangePassword'])->name('house-owner.profile.change-password');
        Route::put('/profile/change-password', [HouseOwnerAuthController::class, 'changePassword'])->name('house-owner.profile.change-password.update');
    });
});

// House Owner Public Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Tenant Routes
Route::prefix('tenant')->group(function () {
    // Tenant Authentication
    Route::get('/login', [\App\Http\Controllers\TenantAuthController::class, 'showLogin'])->name('tenant.login');
    Route::post('/login', [\App\Http\Controllers\TenantAuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\TenantAuthController::class, 'logout'])->name('tenant.logout');

    // Tenant Protected Routes
    Route::middleware(['auth', 'tenant'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TenantController::class, 'dashboard'])->name('tenant.dashboard');
        Route::get('/bills', [\App\Http\Controllers\TenantController::class, 'bills'])->name('tenant.bills');
        Route::get('/bills/{bill}', [\App\Http\Controllers\TenantController::class, 'showBill'])->name('tenant.bills.show');
        Route::get('/payments', [\App\Http\Controllers\TenantController::class, 'payments'])->name('tenant.payments');
        Route::get('/profile', [\App\Http\Controllers\TenantController::class, 'profile'])->name('tenant.profile');
        Route::put('/profile', [\App\Http\Controllers\TenantController::class, 'updateProfile'])->name('tenant.profile.update');
        Route::get('/settings', [\App\Http\Controllers\TenantController::class, 'settings'])->name('tenant.settings');
        Route::get('/notifications', [\App\Http\Controllers\TenantController::class, 'notifications'])->name('tenant.notifications');
        Route::get('/support', [\App\Http\Controllers\TenantController::class, 'support'])->name('tenant.support');
        Route::post('/support', [\App\Http\Controllers\TenantController::class, 'submitSupport'])->name('tenant.support.submit');
    });
});

// Test route for admin dashboard
Route::get('/test-admin', function () {
    $adminService = new \App\Services\AdminService();
    $stats = $adminService->getDashboardStats();
    return response()->json($stats);
});
