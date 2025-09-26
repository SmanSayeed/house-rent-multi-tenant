<?php

namespace App\Http\Controllers;

use App\Http\Requests\HouseOwnerLoginRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Services\HouseOwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HouseOwnerAuthController extends Controller
{
    protected HouseOwnerService $houseOwnerService;

    public function __construct(HouseOwnerService $houseOwnerService)
    {
        $this->houseOwnerService = $houseOwnerService;
    }

    /**
     * Show house owner login form
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isHouseOwner()) {
            return redirect('/house-owner/dashboard');
        }

        return view('house-owner.auth.login');
    }

    /**
     * Handle house owner login
     */
    public function login(HouseOwnerLoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Find user by email
        $user = User::where('email', $email)->first();

        // Check if user exists and is house owner
        if (!$user || !$user->isHouseOwner()) {
            return back()->withErrors([
                'email' => 'Invalid house owner credentials.',
            ])->withInput(['email' => $email]);
        }

        // Check password
        if (!Hash::check($password, $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid house owner credentials.',
            ])->withInput(['email' => $email]);
        }

        // Login the user
        Auth::login($user, $request->has('remember'));

        // Create Sanctum token for API access
        $token = $user->createApiToken('house-owner-session');

        // Store token in session for API calls
        session(['house_owner_token' => $token]);

        return redirect()->intended('/house-owner/dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Handle house owner logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Revoke Sanctum tokens
            $user->revokeAllTokens();

            // Clear session token
            session()->forget('house_owner_token');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/house-owner/login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show house owner profile edit form
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('house-owner.profile.edit', compact('user'));
    }

    /**
     * Update house owner profile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'contact' => $request->input('contact'),
        ]);

        return redirect('/house-owner/profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('house-owner.profile.change-password');
    }

    /**
     * Change house owner password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect('/house-owner/profile')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Get current house owner user data for API
     */
    public function me()
    {
        $user = Auth::user();

        if (!$user || !$user->isHouseOwner()) {
            return ResponseHelper::unauthorized();
        }

        return ResponseHelper::success([
            'user' => $user,
            'token' => session('house_owner_token'),
        ]);
    }
}
