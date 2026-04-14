<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    private function guard()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) abort(403);
    }

    public function index()
    {
        $this->guard();
        $plans = Plan::orderBy('price')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $this->guard();
        
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'speed'            => 'required|string|max:100',
            'price'            => 'required|numeric|min:0',
            'description'      => 'nullable|string',
            'installation_fee' => 'nullable|numeric|min:0',
            'is_active'        => 'nullable|boolean',
            'is_popular'       => 'nullable|boolean',
        ]);

        $data['is_active']        = $request->boolean('is_active', true);
        $data['is_popular']       = $request->boolean('is_popular', false);
        $data['installation_fee'] = $data['installation_fee'] ?? 0;
        
        Plan::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully.'
            ]);
        }

        return redirect()->route('admin.plans')->with('success', 'Plan created successfully.');
    }

    public function update(Request $request, Plan $plan)
    {
        $this->guard();
        
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'speed'            => 'required|string|max:100',
            'price'            => 'required|numeric|min:0',
            'description'      => 'nullable|string',
            'installation_fee' => 'nullable|numeric|min:0',
            'is_active'        => 'nullable|boolean',
            'is_popular'       => 'nullable|boolean',
        ]);

        $data['is_active']        = $request->boolean('is_active', true);
        $data['is_popular']       = $request->boolean('is_popular', false);
        $data['installation_fee'] = $data['installation_fee'] ?? 0;
        
        $plan->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Plan updated successfully.'
            ]);
        }

        return redirect()->route('admin.plans')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $this->guard();
        $plan->delete();
        return redirect()->route('admin.plans')->with('success', 'Plan deleted.');
    }
}
