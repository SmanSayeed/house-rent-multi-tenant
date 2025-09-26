<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect('/admin/dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->validated();

        // Find user by email
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and is admin
        if (!$user || !$user->isAdmin()) {
            return back()->withErrors([
                'email' => 'Invalid admin credentials.',
            ])->withInput(['email' => $credentials['email']]);
        }

        // Check password
        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid admin credentials.',
            ])->withInput(['email' => $credentials['email']]);
        }

        // Login the user
        Auth::login($user, !empty($request->remember));

        // Create Sanctum token for API access
        $token = $user->createApiToken('admin-session');

        // Store token in session for API calls
        session(['admin_token' => $token]);

        return redirect()->intended('/admin/dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Revoke Sanctum tokens
            $user->revokeAllTokens();

            // Clear session token
            session()->forget('admin_token');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show admin profile edit form
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update admin profile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact' => $validated['contact'],
        ]);

        return redirect('/admin/profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('admin.profile.change-password');
    }

    /**
     * Change admin password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect('/admin/profile')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show admin settings
     */
    public function showSettings()
    {
        return view('admin.settings');
    }

    /**
     * Get current admin user data for API
     */
    public function me()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return ResponseHelper::unauthorized();
        }

        return ResponseHelper::success([
            'user' => $user,
            'token' => session('admin_token'),
        ]);
    }
}
