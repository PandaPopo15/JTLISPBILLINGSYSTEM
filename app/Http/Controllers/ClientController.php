<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    private function adminGuard()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    /**
     * Generate a random 5-character password
     */
    private function generatePassword(): string
    {
        return Str::random(5);
    }

    /**
     * Generate a unique PPPoE username
     */
    private function generatePPPoEUsername(): string
    {
        $prefix = 'client_';
        $suffix = Str::random(8);
        $username = $prefix . $suffix;

        // Ensure uniqueness
        while (User::where('pppoe_username', $username)->exists()) {
            $suffix = Str::random(8);
            $username = $prefix . $suffix;
        }

        return $username;
    }

    public function index(Request $request)
    {
        $this->adminGuard();

        $query = User::where('is_admin', false);

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
            }
        }

        $clients = $query->latest()->paginate(10)->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        $this->adminGuard();
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $this->adminGuard();

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users',
            'email'        => 'required|email|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'address'      => 'nullable|string',
            'age'          => 'nullable|integer|min:1|max:120',
            'plan_interest'=> 'nullable|string|max:255',
            'mikrotik_id'  => 'nullable|exists:mikrotiks,id',
            'installation_date' => 'nullable|date',
            'due_date'          => 'nullable|date|after_or_equal:installation_date',
            'password'     => 'nullable|string',
        ]);

        // Auto-generate password and PPPoE username
        $generatedPassword = $this->generatePassword();
        $generatedPPPoEUsername = $this->generatePPPoEUsername();

        // Store password in plain text (for MikroTik activation)
        $validated['password'] = $generatedPassword;
        $validated['pppoe_username'] = $generatedPPPoEUsername;
        $validated['is_admin'] = false;
        $validated['status'] = 'pending';

        $client = User::create($validated);

        return redirect()->route('admin.clients')->with('success', "Client created successfully. PPPoE Username: {$generatedPPPoEUsername}, Password: {$generatedPassword}");
    }

    public function edit(User $client)
    {
        $this->adminGuard();
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        $this->adminGuard();

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $client->id,
            'email'        => 'required|email|unique:users,email,' . $client->id,
            'phone_number' => 'nullable|string|max:20',
            'address'      => 'nullable|string',
            'age'          => 'nullable|integer|min:1|max:120',
            'plan_interest'=> 'nullable|string|max:255',
            'mikrotik_id'  => 'nullable|exists:mikrotiks,id',
            'installation_date' => 'nullable|date',
            'due_date'          => 'nullable|date|after_or_equal:installation_date',
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = $validated['password'];
        } else {
            unset($validated['password']);
        }

        $client->update($validated);

        return redirect()->route('admin.clients')->with('success', 'Client updated successfully.');
    }

    public function destroy(User $client)
    {
        $this->adminGuard();
        $client->delete();
        return redirect()->route('admin.clients')->with('success', 'Client deleted successfully.');
    }

    public function verify(User $client)
    {
        $this->adminGuard();
        $client->update(['email_verified_at' => now()]);
        return redirect()->back()->with('success', 'Client verified successfully.');
    }
}
