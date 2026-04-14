<?php

namespace App\Http\Controllers;

use App\Models\LandingSetting;
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
                'isp_name' => 'ISP Billing',
                'theme_color' => '#ff5252',
                'headline' => 'Fast, Reliable Internet for Your Home or Business',
                'subheadline' => 'Choose the plan that fits your needs and register with your email. Verify your account and wait for admin approval before installation.',
                'plans' => [
                    [
                        'name' => 'Starter',
                        'price' => '499',
                        'description' => 'Basic connection for light browsing and messaging.',
                        'features' => ['10 Mbps speed', 'Unlimited bandwidth', 'Free setup'],
                    ],
                    [
                        'name' => 'Pro',
                        'price' => '999',
                        'description' => 'Perfect for small homes and remote work.',
                        'features' => ['50 Mbps speed', 'Unlimited bandwidth', 'Priority support'],
                    ],
                    [
                        'name' => 'Ultra',
                        'price' => '1599',
                        'description' => 'High-speed plan for streaming, gaming, and multiple users.',
                        'features' => ['100 Mbps speed', 'Unlimited bandwidth', 'Dedicated line'],
                    ],
                ],
            ]);
        }

        return view('landing.index', compact('settings'));
    }

    public function adminLogin()
    {
        return view('auth.login', ['loginTitle' => 'Admin Login']);
    }

    public function edit()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $settings = LandingSetting::first();

        return view('admin.landing.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'isp_name' => ['required', 'string', 'max:255'],
            'theme_color' => ['required', 'string', 'max:7'],
            'headline' => ['required', 'string'],
            'subheadline' => ['required', 'string'],
            'plans' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $settings = LandingSetting::first();

        if (!$settings) {
            $settings = new LandingSetting();
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $path = $request->file('logo')->store('landing_logo', 'public');
            $settings->logo_path = $path;
        }

        $settings->isp_name = $validated['isp_name'];
        $settings->theme_color = $validated['theme_color'];
        $settings->headline = $validated['headline'];
        $settings->subheadline = $validated['subheadline'];
        $settings->plans = json_decode($validated['plans'], true) ?: [];
        $settings->save();

        return redirect()->back()->with('success', 'Landing page settings updated successfully.');
    }
}
