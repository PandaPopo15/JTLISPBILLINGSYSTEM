<?php

namespace App\Http\Controllers;

use App\Models\Napbox;
use App\Models\Mikrotik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NapboxController extends Controller
{
    private function guard()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
    }

    public function index()
    {
        $this->guard();
        $napboxes = Napbox::with('mikrotik')->latest()->get();
        return view('admin.napboxes.index', compact('napboxes'));
    }

    public function create()
    {
        $this->guard();
        $mikrotiks = Mikrotik::orderBy('name')->get();
        return view('admin.napboxes.create', compact('mikrotiks'));
    }

    public function store(Request $request)
    {
        $this->guard();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'mikrotik_id' => 'nullable|exists:mikrotiks,id',
            'notes'       => 'nullable|string',
        ]);

        Napbox::create($validated);
        return redirect()->route('admin.napboxes')->with('success', 'NapBox created successfully.');
    }

    public function edit(Napbox $napbox)
    {
        $this->guard();
        $mikrotiks = Mikrotik::orderBy('name')->get();
        return view('admin.napboxes.edit', compact('napbox', 'mikrotiks'));
    }

    public function update(Request $request, Napbox $napbox)
    {
        $this->guard();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'mikrotik_id' => 'nullable|exists:mikrotiks,id',
            'notes'       => 'nullable|string',
        ]);

        $napbox->update($validated);
        return redirect()->route('admin.napboxes')->with('success', 'NapBox updated successfully.');
    }

    public function destroy(Napbox $napbox)
    {
        $this->guard();
        $napbox->delete();
        return redirect()->route('admin.napboxes')->with('success', 'NapBox deleted.');
    }
}
