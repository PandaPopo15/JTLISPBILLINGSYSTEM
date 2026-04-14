@extends('admin.layout')
@section('title', 'Add MikroTik Router')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Add MikroTik Router</div>
        <div class="adm-page-subtitle">Register a new MikroTik router and connect it via ZeroTier VPN.</div>
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
                    <label>Location / Area Served</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Brgy. San Jose, Zone 2">
                </div>
            </div>

            <h3 style="font-size:14px; font-weight:700; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px; margin:20px 0 16px;">ZeroTier & Connection</h3>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>ZeroTier Network ID</label>
                    <input type="text" name="zerotier_network_id" value="{{ old('zerotier_network_id') }}"
                           placeholder="e.g. 8056c2e21c000001" maxlength="16" style="font-family:monospace;">
                    @error('zerotier_network_id')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>
                <div class="adm-form-group">
                    <label>MikroTik ZeroTier IP *</label>
                    <input type="text" name="ip_address" value="{{ old('ip_address') }}"
                           placeholder="e.g. 172.25.0.5" required style="font-family:monospace;">
                    @error('ip_address')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>API Port *</label>
                    <input type="number" name="port" value="{{ old('port', 8728) }}" min="1" max="65535" required>
                    @error('port')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>
                <div class="adm-form-group">
                    <label>Status</label>
                    <select name="is_active">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <h3 style="font-size:14px; font-weight:700; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px; margin:20px 0 16px;">API Credentials</h3>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>API Username *</label>
                    <input type="text" name="username" value="{{ old('username', 'admin') }}" required>
                    @error('username')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>
                <div class="adm-form-group">
                    <label>API Password *</label>
                    <input type="password" name="password" required autocomplete="new-password">
                    @error('password')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>
            </div>

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
            <div style="font-size:13px; font-weight:700; color:#fff; margin-bottom:10px;">💡 Tips</div>
            <ul style="font-size:12px; color:rgba(255,255,255,0.5); line-height:1.9; padding-left:16px;">
                <li>Use a descriptive name like the barangay or tower it serves.</li>
                <li>The IP must be the ZeroTier-assigned IP of the MikroTik, not its LAN IP.</li>
                <li>Default API port is <code style="background:rgba(0,0,0,0.3); padding:1px 5px; border-radius:4px;">8728</code>.</li>
                <li>Create a dedicated API user on MikroTik instead of using <code style="background:rgba(0,0,0,0.3); padding:1px 5px; border-radius:4px;">admin</code>.</li>
            </ul>
        </div>
        <div style="background:rgba(255,193,7,0.07); border:1px solid rgba(255,193,7,0.2); border-radius:12px; padding:14px 16px;">
            <div style="font-size:12px; font-weight:600; color:#ffd54f; margin-bottom:6px;">⚠️ Security</div>
            <div style="font-size:12px; color:rgba(255,255,255,0.5); line-height:1.7;">
                Restrict the MikroTik API service to the ZeroTier subnet only:
                <code style="display:block; margin-top:6px; background:rgba(0,0,0,0.3); padding:5px 8px; border-radius:6px; color:#81d4fa; font-size:11px;">/ip service set api address=172.25.0.0/16</code>
            </div>
        </div>
    </div>

</div>
@endsection
