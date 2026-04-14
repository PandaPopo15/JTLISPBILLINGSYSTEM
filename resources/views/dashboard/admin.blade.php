@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Dashboard</div>
        <div class="adm-page-subtitle">Welcome back, {{ auth()->user()->first_name }}. Here's what's happening.</div>
    </div>
    <a href="{{ route('admin.clients') }}" class="btn-primary">👥 View Customers</a>
</div>

<div class="adm-kpi-grid">
    <div class="adm-kpi">
        <div class="adm-kpi-label">Total Customers</div>
        <div class="adm-kpi-value">{{ $totalCustomers }}</div>
        <div class="adm-kpi-sub">Registered clients</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">Verified Accounts</div>
        <div class="adm-kpi-value">{{ $verifiedUsers }}</div>
        <div class="adm-kpi-sub">Email confirmed</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">Pending Verification</div>
        <div class="adm-kpi-value">{{ $pendingVerifications }}</div>
        <div class="adm-kpi-sub">Awaiting confirmation</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">Admin Users</div>
        <div class="adm-kpi-value">{{ $totalAdmins }}</div>
        <div class="adm-kpi-sub">System administrators</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <div class="adm-card">
        <h3 style="font-size:16px; font-weight:600; margin-bottom:16px; color:#fff;">Recent Customers</h3>
        @if($recentClients->count())
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentClients as $client)
                    <tr>
                        <td>{{ $client->full_name }}</td>
                        <td style="color:rgba(255,255,255,0.55); font-size:13px;">{{ $client->email }}</td>
                        <td>
                            @if($client->email_verified_at)
                                <span class="badge badge-green">Verified</span>
                            @else
                                <span class="badge badge-yellow">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p style="color:rgba(255,255,255,0.4); font-size:14px;">No customers yet.</p>
        @endif
    </div>

    <div class="adm-card">
        <h3 style="font-size:16px; font-weight:600; margin-bottom:16px; color:#fff;">Quick Actions</h3>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('admin.clients') }}" class="btn-secondary" style="justify-content:flex-start;">
                👥 Manage Customers
            </a>
            <a href="{{ route('admin.landing') }}" class="btn-secondary" style="justify-content:flex-start;">
                🌐 Edit Landing Page
            </a>
            <a href="{{ route('profile.edit') }}" class="btn-secondary" style="justify-content:flex-start;">
                👤 Edit Profile
            </a>
        </div>
    </div>
</div>
@endsection
