<?php

namespace App\Http\Controllers;

use App\Models\LandingSetting;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    public function index()
    {
        $settings = LandingSetting::first();

        if (!$settings) {
            $settings = LandingSetting::create([
                'isp_name'    => 'ISP Billing',
                'theme_color' => '#ff5252',
                'headline'    => 'Fast, Reliable Internet for Your Home or Business',
                'subheadline' => 'Choose the plan that fits your needs and register with your email. Verify your account and wait for admin approval before installation.',
            ]);
        }

        $plans = Plan::active()->get();

        return view('landing.index', compact('settings', 'plans'));
    }

    public function adminLogin()
    {
        return view('auth.login', ['loginTitle' => 'Admin Login']);
    }

    public function edit()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $settings = LandingSetting::first();
        return view('admin.landing.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'isp_name'    => ['required', 'string', 'max:255'],
            'theme_color' => ['required', 'string', 'max:7'],
            'headline'    => ['required', 'string'],
            'subheadline' => ['required', 'string'],
            'logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $settings = LandingSetting::first() ?? new LandingSetting();

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $settings->logo_path = $request->file('logo')->store('landing_logo', 'public');
        }

        $settings->isp_name    = $validated['isp_name'];
        $settings->theme_color = $validated['theme_color'];
        $settings->headline    = $validated['headline'];
        $settings->subheadline = $validated['subheadline'];
        $settings->save();

        return redirect()->back()->with('success', 'Landing page settings updated successfully.');
    }
}
