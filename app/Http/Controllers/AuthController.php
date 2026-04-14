<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister(Request $request)
    {
        $plans = Plan::active()->get();
        $selectedPlan = $request->query('plan');
        return view('auth.register', compact('plans', 'selectedPlan'));
    }

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

        $this->sendVerificationEmail($user);

        return redirect('/login')
            ->with('success', 'Registration successful! Verification sent — check your inbox for the email link before logging in.');
    }

    private function sendVerificationEmail(User $user)
    {
        $verificationToken = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $verificationToken,
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

    public function verifyEmail(Request $request)
    {
        if (!$request->token || !$request->email) {
            return redirect('/login')->with('error', 'Invalid verification link.');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'User not found.');
        }

        if ($user->isEmailVerified()) {
            return redirect('/login')->with('success', 'Your email is already verified.');
        }

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset || $reset->token !== $request->token) {
            return redirect('/login')->with('error', 'Invalid verification token.');
        }

        if (now()->diffInHours(\Carbon\Carbon::parse($reset->created_at)) > 24) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect('/login')->with('error', 'Verification link expired. Please register again.');
        }

        $user->update(['email_verified_at' => now()]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Email verified successfully! You can now log in.');
    }

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
