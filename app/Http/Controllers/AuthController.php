<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegister(Request $request)
    {
        $settings    = \App\Models\LandingSetting::first();
        $plans       = $settings ? ($settings->plans ?? []) : [];
        $selectedPlan = $request->query('plan');
        return view('auth.register', compact('plans', 'selectedPlan'));
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:255'],
            'middle_name'   => ['nullable', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'username'      => ['required', 'string', 'max:255', 'unique:users'],
            'email'         => ['required', 'email', 'unique:users'],
            'phone_number'  => ['nullable', 'string', 'max:20'],
            'address'       => ['required', 'string'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
            'age'           => ['nullable', 'integer', 'min:1', 'max:120'],
            'plan_interest' => ['required', 'string', 'max:255'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name'    => $validated['first_name'],
            'middle_name'   => $validated['middle_name'] ?? null,
            'last_name'     => $validated['last_name'],
            'username'      => $validated['username'],
            'email'         => $validated['email'],
            'phone_number'  => $validated['phone_number'] ?? null,
            'address'       => $validated['address'],
            'latitude'      => $validated['latitude'] ?? null,
            'longitude'     => $validated['longitude'] ?? null,
            'age'           => $validated['age'] ?? null,
            'plan_interest' => $validated['plan_interest'],
            'password'      => Hash::make($validated['password']),
            'is_admin'      => 0,
            'status'        => 'pending',
        ]);

        // Send verification email
        $this->sendVerificationEmail($user);

        return redirect('/login')
            ->with('success', 'Registration successful! Verification sent — check your inbox for the email link before logging in.');
    }

    /**
     * Send verification email to user
     */
    private function sendVerificationEmail(User $user)
    {
        $verificationToken = Str::random(64);
        
        // Store token in password_reset_tokens table temporarily for verification
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($verificationToken),
                'created_at' => now(),
            ]
        );

        $verificationUrl = route('verify.email', [
            'token' => $verificationToken,
            'email' => $user->email,
        ]);

        Mail::send('auth.emails.verify', [
            'user' => $user,
            'verificationUrl' => $verificationUrl,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email Address - ISP Billing');
        });
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'User not found.');
        }

        if ($user->isEmailVerified()) {
            return redirect('/login')->with('success', 'Your email is already verified.');
        }

        $reset = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return redirect('/login')->with('error', 'Invalid verification token.');
        }

        // Check if token is not expired (24 hours)
        if (now()->diffInHours($reset->created_at) > 24) {
            return redirect('/login')->with('error', 'Verification token expired. Please register again.');
        }

        // Mark email as verified
        $user->update(['email_verified_at' => now()]);

        // Delete the token
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Email verified successfully! You can now log in.');
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->isEmailVerified()) {
            return back()->with('warning', 'Your email is already verified.');
        }

        $this->sendVerificationEmail($user);

        return back()->with('success', 'Verification email sent! Please check your inbox.');
    }
}
