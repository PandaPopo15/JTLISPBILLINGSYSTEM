@extends('admin.layout')
@section('title', 'Add MikroTik Router')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Add MikroTik Router</div>
        <div class="adm-page-subtitle">Register a new MikroTik router and assign NapBoxes.</div>
    </div>
    <a href="{{ route('admin.mikrotik') }}" class="btn-secondary">← Back</a>
</div>

<div style="display:grid; grid-template-columns:1.4fr 0.6fr; gap:20px; align-items:start;">

    <div class="adm-card">
        @if($errors->any())
        <div style="background:rgba(255,82,82,0.1); border:1px solid rgba(255,82,82,0.3); border-radius:10px; padding:12px 16px; margin-bottom:20px; color:#ff8a80; font-size:13px;">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.mikrotik.store') }}" method="POST">
            @csrf

            <h3 style="font-size:14px; font-weight:700; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Router Info</h3>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Router Name / Label *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Tower 1 — Brgy. San Jose" required>
                    @error('name')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>
                <div class="adm-form-group">
                    <label>ZeroTier IP Address *</label>
                    <input type="text" name="ip_address" value="{{ old('ip_address') }}"
                           placeholder="e.g. 172.25.0.5" required style="font-family:monospace;">
                    @error('ip_address')<div class="adm-form-error">{{ $message }}</div>@enderror
                    <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px;">
                        Use ZeroTier IP or any IP that can reach the MikroTik API
                    </div>
                </div>
            </div>

            <div class="adm-form-group">
                <label>Deployment Location (Area Identifier)</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Brgy. San Jose, Zone 2, Poblacion">
                <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px;">
                    Optional identifier for where this router is physically deployed
                </div>
            </div>

            <h3 style="font-size:14px; font-weight:700; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px; margin:20px 0 16px;">Connection & Credentials</h3>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>MikroTik Username *</label>
                    <input type="text" name="username" value="{{ old('username', 'admin') }}" required>
                    @error('username')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>
                <div class="adm-form-group">
                    <label>MikroTik Password *</label>
                    <input type="password" name="password" required autocomplete="new-password">
                    @error('password')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <input type="hidden" name="port" value="8728">
            <input type="hidden" name="is_active" value="1">

            <div class="adm-form-group">
                <label>Notes</label>
                <textarea name="notes" rows="2" placeholder="Optional notes about this router...">{{ old('notes') }}</textarea>
            </div>

            <div class="adm-form-actions">
                <a href="{{ route('admin.mikrotik') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">💾 Save Router</button>
            </div>
        </form>
    </div>

    {{-- Quick tips --}}
    <div style="display:flex; flex-direction:column; gap:14px;">
        <div class="adm-card" style="padding:18px;">
            <div style="font-size:13px; font-weight:700; color:#fff; margin-bottom:10px;">🔌 How to Connect MikroTik</div>
            <ol style="font-size:12px; color:rgba(255,255,255,0.6); line-height:1.9; padding-left:18px;">
                <li><strong>Via ZeroTier (Recommended):</strong>
                    <ul style="margin-top:4px; padding-left:16px;">
                        <li>Install ZeroTier on MikroTik</li>
                        <li>Join your ZeroTier network</li>
                        <li>Use the assigned ZeroTier IP</li>
                    </ul>
                </li>
                <li style="margin-top:8px;"><strong>Other Options:</strong>
                    <ul style="margin-top:4px; padding-left:16px;">
                        <li>Direct LAN IP (if on same network)</li>
                        <li>Public IP with port forwarding</li>
                        <li>VPN tunnel (WireGuard, OpenVPN)</li>
                    </ul>
                </li>
                <li style="margin-top:8px;">Enable MikroTik API:
                    <code style="display:block; margin:4px 0; background:rgba(0,0,0,0.3); padding:4px 8px; border-radius:4px; color:#81d4fa; font-size:11px;">/ip service enable api</code>
                </li>
                <li>Use admin credentials or create API user</li>
                <li>Test connection after saving</li>
            </ol>
        </div>
        
        <div class="adm-card" style="padding:18px;">
            <div style="font-size:13px; font-weight:700; color:#fff; margin-bottom:10px;">💡 Tips</div>
            <ul style="font-size:12px; color:rgba(255,255,255,0.5); line-height:1.9; padding-left:16px;">
                <li>Use a descriptive name to identify the router (e.g. tower, barangay, area).</li>
                <li>Deployment location is optional but helps identify where the router is installed.</li>
                <li>Default API port is 8728 (automatically set).</li>
            </ul>
        </div>
    </div>

</div>
@endsection
