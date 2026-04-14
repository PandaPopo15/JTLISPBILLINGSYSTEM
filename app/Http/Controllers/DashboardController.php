<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LandingSetting;

use App\Models\Mikrotik;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('dashboard.index', compact('user'));
    }

    public function admin()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $totalAdmins = User::where('is_admin', true)->count();
        $totalCustomers = User::where('is_admin', false)->count();
        $verifiedUsers = User::where('is_admin', false)->whereNotNull('email_verified_at')->count();
        $pendingVerifications = User::where('is_admin', false)->whereNull('email_verified_at')->count();
        $recentClients = User::where('is_admin', false)->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalAdmins',
            'totalCustomers',
            'verifiedUsers',
            'pendingVerifications',
            'recentClients'
        ));
    }

    public function clients(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $query = User::with('mikrotik')->where('is_admin', false);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('username', 'like', "%$s%")
                  ->orWhere('phone_number', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            } elseif (in_array($request->status, ['pending', 'active', 'rejected'])) {
                $query->where('status', $request->status);
            }
        }

        $clients      = $query->latest()->paginate(15)->withQueryString();
        $pendingCount = User::where('is_admin', false)->where('status', 'pending')->count();
        $mikrotiks    = Mikrotik::where('is_active', true)->orderBy('name')->get();

        return view('admin.clients.index', compact('clients', 'pendingCount', 'mikrotiks'));
    }

    public function storeClient(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:users',
            'email'         => 'required|email|unique:users',
            'phone_number'  => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'age'           => 'nullable|integer|min:1|max:120',
            'plan_interest' => 'nullable|string|max:255',
            'mikrotik_id'   => 'nullable|exists:mikrotiks,id',
            'installation_date' => 'nullable|date',
            'due_date'          => 'nullable|date|after_or_equal:installation_date',
        ]);

        $generatedPassword = \Illuminate\Support\Str::random(5);
        $generatedPPPoEUsername = $this->generateUniquePPPoEUsername();

        $validated['password']          = $generatedPassword;
        $validated['pppoe_username']    = $generatedPPPoEUsername;
        $validated['is_admin']          = false;
        $validated['status']            = 'pending';
        $validated['email_verified_at'] = null;

        User::create($validated);
        return redirect()->route('admin.clients')->with('success', "Client created successfully. PPPoE Username: {$generatedPPPoEUsername}, Password: {$generatedPassword}");
    }

    private function generateUniquePPPoEUsername(): string
    {
        $prefix = 'client_';
        $suffix = \Illuminate\Support\Str::random(8);
        $username = $prefix . $suffix;

        while (User::where('pppoe_username', $username)->exists()) {
            $suffix = \Illuminate\Support\Str::random(8);
            $username = $prefix . $suffix;
        }

        return $username;
    }

    public function acceptClient(User $client)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $client->update(['status' => 'active', 'email_verified_at' => $client->email_verified_at ?? now()]);
        return redirect()->back()->with('success', $client->full_name . ' has been accepted.');
    }

    public function rejectClient(User $client)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $client->update(['status' => 'rejected']);
        return redirect()->back()->with('success', $client->full_name . ' has been rejected.');
    }

    public function editClient(User $client)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $mikrotiks = Mikrotik::where('is_active', true)->orderBy('name')->get();
        return view('admin.clients.edit', compact('client', 'mikrotiks'));
    }

    public function updateClient(Request $request, User $client)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:users,username,' . $client->id,
            'email'         => 'required|email|unique:users,email,' . $client->id,
            'phone_number'  => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'age'           => 'nullable|integer|min:1|max:120',
            'plan_interest' => 'nullable|string|max:255',
            'mikrotik_id'   => 'nullable|exists:mikrotiks,id',
            'pppoe_username'=> 'nullable|string|max:64|unique:users,pppoe_username,' . $client->id,
        ]);

        $client->update($validated);
        return redirect()->route('admin.clients')->with('success', 'Client updated successfully.');
    }

    public function activateClient(User $client)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        if (empty($client->pppoe_username)) {
            return redirect()->back()->with('error', 'Cannot activate: PPPoE username is not set for this client.');
        }
        if (empty($client->mikrotik_id)) {
            return redirect()->back()->with('error', 'Cannot activate: No MikroTik router assigned to this client.');
        }

        $mikrotik = $client->mikrotik;
        $ip       = $mikrotik->ip_address;
        $port     = $mikrotik->port;

        // TCP reachability check
        $socket = @fsockopen($ip, $port, $errno, $errstr, 5);
        if (!$socket) {
            return redirect()->back()->with('error', "Cannot reach MikroTik at {$ip}:{$port} — {$errstr}. Check ZeroTier connection.");
        }
        fclose($socket);

        try {
            $api = new \RouterOS\Client([
                'host' => $ip,
                'user' => $mikrotik->username,
                'pass' => $mikrotik->password,
                'port' => $port,
            ]);

            // Check if PPPoE secret already exists
            $existing = $api->query('/ppp/secret/print', [
                '?name' => $client->pppoe_username,
            ])->read();

            if (!empty($existing)) {
                // Enable existing secret
                $api->query('/ppp/secret/set', [
                    '=.id'      => $existing[0]['.id'],
                    '=disabled' => 'no',
                ])->read();
            } else {
                // Create new PPPoE secret
                $api->query('/ppp/secret/add', [
                    '=name'     => $client->pppoe_username,
                    '=password' => $client->username,
                    '=service'  => 'pppoe',
                    '=profile'  => $client->plan_interest ?? 'default',
                    '=comment'  => $client->full_name,
                ])->read();
            }

            $client->update(['status' => 'active']);
            return redirect()->back()->with('success', "PPPoE account for {$client->full_name} activated on {$mikrotik->name}.");

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'MikroTik API error: ' . $e->getMessage());
        }
    }

    public function verifyClient(User $client)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $client->update(['email_verified_at' => now()]);
        return redirect()->back()->with('success', 'Client verified successfully.');
    }

    public function deleteClient(User $client)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $client->delete();
        return redirect()->route('admin.clients')->with('success', 'Client deleted successfully.');
    }

    public function settings()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        return view('admin.settings.index');
    }

    public function updateSettings(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . Auth::id(),
            'phone_number'  => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'age'           => 'nullable|integer|min:1|max:120',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->update($validated);
        return redirect()->route('admin.settings')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $request->validate([
            'current_password' => ['required', function ($attr, $val, $fail) {
                if (!\Hash::check($val, Auth::user()->password)) $fail('Current password is incorrect.');
            }],
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update(['password' => \Hash::make($request->password)]);
        return redirect()->route('admin.settings')->with('success', 'Password updated successfully.');
    }

    public function mikrotikSettings()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $config = [
            'zerotier_network_id' => config('mikrotik.zerotier_network_id', env('MIKROTIK_ZEROTIER_NETWORK', '')),
            'mikrotik_ip'         => config('mikrotik.ip',                  env('MIKROTIK_IP', '')),
            'mikrotik_port'       => config('mikrotik.port',                 env('MIKROTIK_PORT', '8728')),
            'mikrotik_user'       => config('mikrotik.user',                 env('MIKROTIK_USER', 'admin')),
            'mikrotik_password'   => config('mikrotik.password',             env('MIKROTIK_PASSWORD', '')),
        ];
        return view('admin.settings.mikrotik', compact('config'));
    }

    public function saveMikrotik(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'zerotier_network_id' => 'nullable|string|max:16',
            'mikrotik_ip'         => 'nullable|ip',
            'mikrotik_port'       => 'nullable|integer|min:1|max:65535',
            'mikrotik_user'       => 'nullable|string|max:64',
            'mikrotik_password'   => 'nullable|string|max:255',
        ]);

        $envPath = base_path('.env');
        $env     = file_get_contents($envPath);

        $map = [
            'MIKROTIK_ZEROTIER_NETWORK' => $validated['zerotier_network_id'] ?? '',
            'MIKROTIK_IP'               => $validated['mikrotik_ip']         ?? '',
            'MIKROTIK_PORT'             => $validated['mikrotik_port']       ?? '8728',
            'MIKROTIK_USER'             => $validated['mikrotik_user']       ?? 'admin',
            'MIKROTIK_PASSWORD'         => $validated['mikrotik_password']   ?? '',
        ];

        foreach ($map as $key => $value) {
            if (preg_match("/^{$key}=/m", $env)) {
                $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
            } else {
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $env);
        return redirect()->route('admin.settings.mikrotik')->with('success', 'MikroTik settings saved.');
    }

    public function testMikrotik()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $ip       = env('MIKROTIK_IP', '');
        $port     = (int) env('MIKROTIK_PORT', 8728);
        $user     = env('MIKROTIK_USER', 'admin');
        $password = env('MIKROTIK_PASSWORD', '');

        if (empty($ip)) {
            return response()->json(['success' => false, 'message' => 'MikroTik IP is not configured.']);
        }

        // TCP socket test first (checks ZeroTier reachability + API port)
        $socket = @fsockopen($ip, $port, $errno, $errstr, 5);
        if (!$socket) {
            return response()->json([
                'success' => false,
                'message' => "Cannot reach {$ip}:{$port} — {$errstr} (errno {$errno}). Check ZeroTier is running and MikroTik API is enabled."
            ]);
        }
        fclose($socket);

        // RouterOS API login attempt
        try {
            $api    = new \RouterOS\Client(['host' => $ip, 'user' => $user, 'pass' => $password, 'port' => $port]);
            $result = $api->query('/system/identity/print')->read();
            $name   = $result[0]['name'] ?? 'Unknown';
            return response()->json(['success' => true, 'message' => "Connected to MikroTik: {$name}"]);
        } catch (\Throwable $e) {
            // Socket reachable but API login failed — still useful info
            return response()->json([
                'success' => false,
                'message' => "Port {$port} is reachable but API login failed: " . $e->getMessage()
            ]);
        }
    }

    public function landingEditor()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $settings = LandingSetting::first();

        return view('dashboard.landing-editor', compact('settings'));
    }

    public function editProfile()
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }

        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'age' => 'nullable|integer|min:1|max:120',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $path;
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}


