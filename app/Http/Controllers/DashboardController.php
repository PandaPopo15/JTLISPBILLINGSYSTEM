<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\LandingSetting;
use App\Models\Payment;
use App\Models\Mikrotik;
use App\Models\Sale;
use App\Models\Expense;
use App\Mail\ClientAccepted;

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
        
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        $clients = User::where('is_admin', false)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->with(['payments' => function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('due_date', [$startOfMonth, $endOfMonth])
                      ->where('status', 'paid');
            }])
            ->get()
            ->map(function($user) {
                $plan = \App\Models\Plan::where('name', $user->plan_interest)->first();
                $user->plan_amount = $plan ? $plan->price : 0;
                return $user;
            })
            ->groupBy(function($user) {
                return $user->due_date->day;
            });

        return view('dashboard.admin', compact(
            'totalAdmins',
            'totalCustomers',
            'verifiedUsers',
            'pendingVerifications',
            'recentClients',
            'clients',
            'now'
        ));
    }

    public function sales()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Total revenue from payments
        $paymentRevenue = Payment::where('status', 'paid')->sum('amount');
        
        // Total revenue from hotspot sales
        $hotspotRevenue = Sale::sum('amount');
        
        // Combined total revenue
        $totalRevenue = $paymentRevenue + $hotspotRevenue;
        
        // Total expenses
        $totalExpenses = Expense::sum('amount');
        
        // Net profit
        $netProfit = $totalRevenue - $totalExpenses;
        
        // This month revenue
        $monthPaymentRevenue = Payment::where('status', 'paid')
            ->whereBetween('paid_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $monthHotspotRevenue = Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $monthRevenue = $monthPaymentRevenue + $monthHotspotRevenue;
        
        // This month expenses
        $monthExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        // This month profit
        $monthProfit = $monthRevenue - $monthExpenses;
        
        // Total paid payments
        $totalPayments = Payment::where('status', 'paid')->count();
        
        // Pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        
        // Prospected income (from active clients' plans)
        $activeClients = User::where('is_admin', false)
            ->where('status', 'active')
            ->whereNotNull('plan_interest')
            ->get();
        
        $prospectedIncome = 0;
        foreach ($activeClients as $client) {
            $plan = \App\Models\Plan::where('name', $client->plan_interest)->first();
            if ($plan) {
                $prospectedIncome += $plan->price;
            }
        }
        
        // Recent payments
        $recentPayments = Payment::with('user')
            ->where('status', 'paid')
            ->latest('paid_date')
            ->take(10)
            ->get();
        
        // Hotspot sales
        $hotspotSales = Sale::latest('sale_date')->get();
        
        // Expenses
        $expenses = Expense::latest('expense_date')->get();
        
        // Monthly revenue chart data (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $paymentRev = Payment::where('status', 'paid')
                ->whereYear('paid_date', $month->year)
                ->whereMonth('paid_date', $month->month)
                ->sum('amount');
            $hotspotRev = Sale::whereYear('sale_date', $month->year)
                ->whereMonth('sale_date', $month->month)
                ->sum('amount');
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $paymentRev + $hotspotRev
            ];
        }

        return view('admin.sales', compact(
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'monthRevenue',
            'monthExpenses',
            'monthProfit',
            'totalPayments',
            'pendingPayments',
            'prospectedIncome',
            'recentPayments',
            'hotspotSales',
            'expenses',
            'monthlyRevenue',
            'now'
        ));
    }

    public function storeExpense(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'purpose' => 'required|string|max:255',
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Expense::create($validated);
        return redirect()->route('admin.sales')->with('success', 'Expense added successfully.');
    }

    public function deleteExpense(Expense $expense)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $expense->delete();
        return redirect()->route('admin.sales')->with('success', 'Expense deleted successfully.');
    }

    public function storeSale(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['type'] = 'hotspot';

        Sale::create($validated);
        return redirect()->route('admin.sales')->with('success', 'Hotspot sale added successfully.');
    }

    public function deleteSale(Sale $sale)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        $sale->delete();
        return redirect()->route('admin.sales')->with('success', 'Sale deleted successfully.');
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

        // Auto-generate unique username from first + last name
        $base = strtolower(preg_replace('/\s+/', '', $validated['first_name'] . $validated['last_name']));
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }
        $validated['username'] = $username;

        $generatedLoginPassword = \Illuminate\Support\Str::random(8);
        $generatedPPPoEPassword = \Illuminate\Support\Str::random(8);
        $generatedPPPoEUsername = $this->generateUniquePPPoEUsername();

        $validated['password']          = \Hash::make($generatedLoginPassword);
        $validated['pppoe_username']    = $generatedPPPoEUsername;
        $validated['pppoe_password']    = $generatedPPPoEPassword;
        $validated['is_admin']          = false;
        $validated['status']            = 'pending';
        $validated['email_verified_at'] = null;

        User::create($validated);
        return redirect()->route('admin.clients')->with('success', "Client created successfully. Login Password: {$generatedLoginPassword}, PPPoE Username: {$generatedPPPoEUsername}, PPPoE Password: {$generatedPPPoEPassword}");
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
        
        Mail::to($client->email)->send(new ClientAccepted($client));
        
        return redirect()->back()->with('success', $client->full_name . ' has been accepted and notified via email.');
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
            'pppoe_password'=> 'nullable|string|max:64',
            'password'      => 'nullable|string|min:8|confirmed',
            'installation_date' => 'nullable|date',
            'due_date'          => 'nullable|date|after_or_equal:installation_date',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = \Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $client->update($validated);
        return redirect()->route('admin.clients')->with('success', 'Client updated successfully.');
    }

    public function markPaymentPaid(User $client)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        
        $plan = \App\Models\Plan::where('name', $client->plan_interest)->first();
        $amount = $plan ? $plan->price : 0;
        
        $payment = Payment::where('user_id', $client->id)
            ->whereMonth('due_date', $currentMonth)
            ->whereYear('due_date', $currentYear)
            ->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'paid',
                'paid_date' => now(),
                'amount' => $amount,
            ]);
        } else {
            Payment::create([
                'user_id' => $client->id,
                'due_date' => $client->due_date,
                'status' => 'paid',
                'paid_date' => now(),
                'amount' => $amount,
            ]);
        }
        
        return redirect()->back()->with('success', 'Payment marked as paid.');
    }

    public function markPaymentPaidById(Payment $payment)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
        
        if ($payment->status !== 'paid') {
            $payment->update([
                'status' => 'paid',
                'paid_date' => now(),
            ]);
            
            $nextDueDate = $payment->due_date->copy()->addMonth();
            Payment::create([
                'user_id' => $payment->user_id,
                'due_date' => $nextDueDate,
                'status' => 'pending',
            ]);
        }
        
        return redirect()->back()->with('success', 'Payment marked as paid. Next due date created.');
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
                // Create new PPPoE secret with pppoe_password
                $api->query('/ppp/secret/add', [
                    '=name'     => $client->pppoe_username,
                    '=password' => $client->pppoe_password,
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
        $settings = LandingSetting::first();
        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);

        $validated = $request->validate([
            'dashboard_title'   => 'nullable|string|max:255',
            'dashboard_tagline' => 'nullable|string|max:500',
            'primary_color'     => 'nullable|string|max:7',
            'dashboard_logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'isp_logo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon'           => 'nullable|mimes:jpeg,png,jpg,gif,ico,svg|max:512',
        ]);

        $settings = LandingSetting::first() ?? new LandingSetting();

        if ($request->hasFile('dashboard_logo')) {
            if ($settings->dashboard_logo) {
                \Storage::disk('public')->delete($settings->dashboard_logo);
            }
            $settings->dashboard_logo = $request->file('dashboard_logo')->store('dashboard', 'public');
        }

        if ($request->hasFile('isp_logo')) {
            if ($settings->isp_logo) {
                \Storage::disk('public')->delete($settings->isp_logo);
            }
            $settings->isp_logo = $request->file('isp_logo')->store('dashboard', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($settings->favicon) {
                \Storage::disk('public')->delete($settings->favicon);
            }
            $settings->favicon = $request->file('favicon')->store('dashboard', 'public');
        }

        $settings->dashboard_title   = $validated['dashboard_title'] ?? 'ISP Billing';
        $settings->dashboard_tagline = $validated['dashboard_tagline'] ?? '';
        $settings->primary_color     = $validated['primary_color'] ?? '#ff5252';
        $settings->save();

        return redirect()->route('admin.settings')->with('success', 'Dashboard appearance updated successfully.');
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


