<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantAuthController extends Controller
{
    /**
     * Show tenant login form
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isTenant()) {
            return redirect()->route('tenant.dashboard');
        }

        return view('tenant.auth.login');
    }

    /**
     * Handle tenant login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->isTenant()) {
                Auth::logout();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Access denied. This account is not authorized for tenant portal.');
            }

            $request->session()->regenerate();

            Log::info('Tenant logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return redirect()->intended(route('tenant.dashboard'))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        Log::warning('Failed tenant login attempt', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'Invalid credentials. Please try again.');
    }

    /**
     * Handle tenant logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        Log::info('Tenant logged out', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login')
            ->with('success', 'You have been logged out successfully.');
    }
}
