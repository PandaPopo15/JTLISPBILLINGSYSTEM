<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Handle login request with email or username.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'login.required' => 'Please enter your email address or username.',
            'password.required' => 'Please enter your password.',
        ]);

        // Determine if the login input is an email or username
        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt login with the appropriate field
        if (Auth::attempt([$fieldType => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if email is verified
            if (!$user->isEmailVerified()) {
                Auth::logout();
                return back()
                    ->withInput($request->only('login'))
                    ->with('warning', 'Please verify your email address first. Check your inbox for the verification link.');
            }

            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()
            ->withInput($request->only('login'))
            ->withErrors([
                'login' => 'The provided credentials do not match our records.',
            ]);
    }

    /**
     * Handle logout.
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

