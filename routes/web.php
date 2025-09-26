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
        Route::get('/buildings', [HouseOwnerController::class, 'buildings'])->name('house-owner.buildings');
        Route::get('/flats', [HouseOwnerController::class, 'flats'])->name('house-owner.flats');
        Route::get('/tenants', [HouseOwnerController::class, 'tenants'])->name('house-owner.tenants');
        Route::get('/bills', [HouseOwnerController::class, 'bills'])->name('house-owner.bills');
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

// Test route for admin dashboard
Route::get('/test-admin', function () {
    $adminService = new \App\Services\AdminService();
    $stats = $adminService->getDashboardStats();
    return response()->json($stats);
});
