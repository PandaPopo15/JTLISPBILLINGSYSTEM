<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $resetToken = Str::random(64);

        // Store reset token
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($resetToken),
                'created_at' => now(),
            ]
        );

        // Send email with reset link
        $resetUrl = route('password.reset.form', [
            'token' => $resetToken,
            'email' => $user->email,
        ]);

        Mail::send('auth.emails.reset-password', [
            'user' => $user,
            'resetUrl' => $resetUrl,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Reset Your Password - ISP Billing');
        });

        return back()->with('success', 'Password reset link sent! Check your email.');
    }

    /**
     * Show reset password form
     */
    public function showResetForm(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return redirect('/forgot-password')->with('error', 'Invalid reset token.');
        }

        // Check if token is not expired (1 hour)
        if (now()->diffInHours($reset->created_at) > 1) {
            return redirect('/forgot-password')->with('error', 'Reset token expired. Please request a new one.');
        }

        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify token
        $reset = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$reset || !Hash::check($validated['token'], $reset->token)) {
            return back()->with('error', 'Invalid reset token.');
        }

        // Check if token is not expired
        if (now()->diffInHours($reset->created_at) > 1) {
            return back()->with('error', 'Reset token expired.');
        }

        // Update password
        $user = User::where('email', $validated['email'])->first();
        $user->update(['password' => Hash::make($validated['password'])]);

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect('/login')->with('success', 'Password reset successful! You can now log in.');
    }
}
