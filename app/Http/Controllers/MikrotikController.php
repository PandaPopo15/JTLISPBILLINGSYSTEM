<?php

namespace App\Http\Controllers;

use App\Models\Mikrotik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MikrotikController extends Controller
{
    private function guard()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
    }

    public function index()
    {
        $this->guard();
        $mikrotiks = Mikrotik::withCount('clients')->latest()->get();
        return view('admin.mikrotik.index', compact('mikrotiks'));
    }

    public function create()
    {
        $this->guard();
        return view('admin.mikrotik.create');
    }

    public function store(Request $request)
    {
        $this->guard();

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'zerotier_network_id' => 'nullable|string|max:16',
            'ip_address'          => 'required|string|max:45',
            'port'                => 'required|integer|min:1|max:65535',
            'username'            => 'required|string|max:64',
            'password'            => 'required|string|max:255',
            'location'            => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'is_active'           => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Mikrotik::create($validated);
        return redirect()->route('admin.mikrotik')->with('success', 'MikroTik router added successfully.');
    }

    public function edit(Mikrotik $mikrotik)
    {
        $this->guard();
        $clients = User::where('is_admin', false)
                       ->where(function($q) use ($mikrotik) {
                           $q->where('mikrotik_id', $mikrotik->id)
                             ->orWhereNull('mikrotik_id');
                       })
                       ->orderBy('first_name')
                       ->get();
        return view('admin.mikrotik.edit', compact('mikrotik', 'clients'));
    }

    public function update(Request $request, Mikrotik $mikrotik)
    {
        $this->guard();

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'zerotier_network_id' => 'nullable|string|max:16',
            'ip_address'          => 'required|string|max:45',
            'port'                => 'required|integer|min:1|max:65535',
            'username'            => 'required|string|max:64',
            'password'            => 'nullable|string|max:255',
            'location'            => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'is_active'           => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $mikrotik->update($validated);
        return redirect()->route('admin.mikrotik')->with('success', 'MikroTik router updated successfully.');
    }

    public function destroy(Mikrotik $mikrotik)
    {
        $this->guard();
        // Unlink clients before deleting
        User::where('mikrotik_id', $mikrotik->id)->update(['mikrotik_id' => null]);
        $mikrotik->delete();
        return redirect()->route('admin.mikrotik')->with('success', 'MikroTik router deleted.');
    }

    public function test(Mikrotik $mikrotik)
    {
        $this->guard();

        $ip   = $mikrotik->ip_address;
        $port = $mikrotik->port;

        // TCP reachability check
        $socket = @fsockopen($ip, $port, $errno, $errstr, 5);
        if (!$socket) {
            return response()->json([
                'success' => false,
                'message' => "Cannot reach {$ip}:{$port} — {$errstr}. Check ZeroTier is active and MikroTik API is enabled.",
            ]);
        }
        fclose($socket);

        // RouterOS API login (requires routeros/api package)
        try {
            $client = new \RouterOS\Client([
                'host' => $ip,
                'user' => $mikrotik->username,
                'pass' => $mikrotik->password,
                'port' => $port,
            ]);
            $result   = $client->query('/system/identity/print')->read();
            $identity = $result[0]['name'] ?? 'Unknown';

            $mikrotik->update(['last_connected_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => "Connected — Router identity: {$identity}",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Port reachable but API login failed: " . $e->getMessage(),
            ]);
        }
    }

    public function assignClients(Request $request, Mikrotik $mikrotik)
    {
        $this->guard();

        $request->validate([
            'client_ids'   => 'nullable|array',
            'client_ids.*' => 'exists:users,id',
        ]);

        // Remove this mikrotik from all previously assigned clients
        User::where('mikrotik_id', $mikrotik->id)->update(['mikrotik_id' => null]);

        // Assign selected clients
        if ($request->filled('client_ids')) {
            User::whereIn('id', $request->client_ids)
                ->where('is_admin', false)
                ->update(['mikrotik_id' => $mikrotik->id]);
        }

        return redirect()->route('admin.mikrotik.edit', $mikrotik)
                         ->with('success', 'Clients assigned successfully.');
    }
}
