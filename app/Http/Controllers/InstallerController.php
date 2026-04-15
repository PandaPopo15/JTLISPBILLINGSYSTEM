<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InstallerController extends Controller
{
    public function index()
    {
        $installers = User::where('is_admin', 2)->paginate(15);
        return view('admin.installers.index', compact('installers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|min:8',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'username' => 'installer_' . strtolower($request->first_name) . rand(100, 999),
            'password' => Hash::make($request->password),
            'is_admin' => 2,
            'status' => 'active',
        ]);

        return redirect()->route('admin.installers')->with('success', 'Installer added successfully!');
    }

    public function edit(User $installer)
    {
        if ($installer->is_admin !== 2) {
            abort(404);
        }
        return view('admin.installers.edit', compact('installer'));
    }

    public function update(Request $request, User $installer)
    {
        if ($installer->is_admin !== 2) {
            abort(404);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $installer->id,
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|min:8',
        ]);

        $installer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        if ($request->filled('password')) {
            $installer->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.installers')->with('success', 'Installer updated successfully!');
    }

    public function destroy(User $installer)
    {
        if ($installer->is_admin !== 2) {
            abort(404);
        }

        $installer->delete();
        return redirect()->route('admin.installers')->with('success', 'Installer deleted successfully!');
    }

    public function jobOrders()
    {
        $jobOrders = JobOrder::with(['client', 'installer'])->latest()->paginate(15);
        $clients = User::where('is_admin', 0)->where('status', 'active')->get();
        $installers = User::where('is_admin', 2)->get();
        return view('admin.job-orders.index', compact('jobOrders', 'clients', 'installers'));
    }

    public function storeJobOrder(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'assigned_to' => 'nullable|exists:users,id',
            'installation_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        JobOrder::create([
            'client_id' => $request->client_id,
            'assigned_to' => $request->assigned_to,
            'status' => 'pending',
            'installation_date' => $request->installation_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.job-orders')->with('success', 'Job order created successfully!');
    }

    public function updateJobOrder(Request $request, JobOrder $jobOrder)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,ongoing,completed',
            'installation_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $jobOrder->update($request->only(['assigned_to', 'status', 'installation_date', 'notes']));

        return redirect()->route('admin.job-orders')->with('success', 'Job order updated successfully!');
    }

    public function deleteJobOrder(JobOrder $jobOrder)
    {
        $jobOrder->delete();
        return redirect()->route('admin.job-orders')->with('success', 'Job order deleted successfully!');
    }

    public function installerDashboard()
    {
        $jobOrders = JobOrder::where('assigned_to', auth()->id())
            ->with('client')
            ->latest()
            ->paginate(15);

        $stats = [
            'pending' => JobOrder::where('assigned_to', auth()->id())->where('status', 'pending')->count(),
            'ongoing' => JobOrder::where('assigned_to', auth()->id())->where('status', 'ongoing')->count(),
            'completed' => JobOrder::where('assigned_to', auth()->id())->where('status', 'completed')->count(),
        ];

        return view('installer.dashboard', compact('jobOrders', 'stats'));
    }

    public function updateJobStatus(Request $request, JobOrder $jobOrder)
    {
        if ($jobOrder->assigned_to !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,ongoing,completed',
            'notes' => 'nullable|string',
        ]);

        $jobOrder->update($request->only(['status', 'notes']));

        return redirect()->route('installer.dashboard')->with('success', 'Job status updated successfully!');
    }

    public function profile()
    {
        return view('installer.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
        }

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->update(['profile_image' => $path]);
        }

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('installer.profile')->with('success', 'Profile updated successfully!');
    }
}
