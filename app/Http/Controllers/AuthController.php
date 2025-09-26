<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Requests\HouseOwnerRegistrationRequest;

class AuthController extends Controller
{
    /**
     * Show house owner registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Register a new house owner
     */
    public function register(HouseOwnerRegistrationRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'house_owner',
            'contact' => $validated['contact'],
        ]);

        Auth::login($user);

        return redirect('/house-owner/dashboard')
            ->with('success', 'Registration successful! Welcome to the platform.');
    }

    /**
     * Show house owner login form
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isHouseOwner()) {
            return redirect('/house-owner/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Login house owner
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->isHouseOwner()) {
            return back()->withErrors([
                'email' => 'Invalid house owner credentials.',
            ])->withInput(['email' => $request->email]);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid house owner credentials.',
            ])->withInput(['email' => $request->email]);
        }

        Auth::login($user, $request->has('remember'));

        return redirect()->intended('/house-owner/dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Logout house owner
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'You have been logged out successfully.');
    }
}
