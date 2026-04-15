@extends('admin.layout')
@section('title', 'Admin Settings')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Admin Settings</div>
        <div class="adm-page-subtitle">Configure dashboard appearance and system settings.</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">

    {{-- Dashboard Branding --}}
    <div class="adm-card">
        <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.07);">
            🎨 Dashboard Appearance
        </h3>

        @if($errors->any())
        <div style="background:rgba(255,82,82,0.1); border:1px solid rgba(255,82,82,0.3); border-radius:10px; padding:12px 16px; margin-bottom:18px; color:#ff8a80; font-size:13px;">
            @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="adm-form-group">
                <label>Dashboard Title</label>
                <input type="text" name="dashboard_title" value="{{ old('dashboard_title', $settings->dashboard_title ?? 'ISP Billing') }}" placeholder="e.g. ISP Billing">
                @error('dashboard_title')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>Primary Color</label>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color ?? '#ff5252') }}" 
                           style="height:46px; padding:4px 8px; cursor:pointer; border-radius:8px; border:1px solid rgba(255,255,255,0.1); width:80px;">
                    <span style="font-size:12px; color:rgba(255,255,255,0.5);">{{ old('primary_color', $settings->primary_color ?? '#ff5252') }}</span>
                </div>
                @error('primary_color')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>Dashboard Logo</label>
                <input type="file" name="dashboard_logo" accept="image/*"
                       style="padding:10px; border:1px solid rgba(255,255,255,0.1); border-radius:10px; background:rgba(255,255,255,0.04); color:#fff; width:100%; font-size:13px;">
                @error('dashboard_logo')<div class="adm-form-error">{{ $message }}</div>@enderror
                @if($settings && $settings->dashboard_logo)
                <div style="margin-top:12px; display:flex; align-items:center; gap:12px;">
                    <img src="{{ asset('storage/' . $settings->dashboard_logo) }}" alt="Dashboard logo"
                         style="width:50px; height:50px; border-radius:8px; object-fit:contain; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.02);">
                    <span style="font-size:12px; color:rgba(255,255,255,0.5);">Current logo</span>
                </div>
                @endif
            </div>

            <div class="adm-form-group">
                <label>ISP Logo (Login & Register Pages)</label>
                <input type="file" name="isp_logo" accept="image/*"
                       style="padding:10px; border:1px solid rgba(255,255,255,0.1); border-radius:10px; background:rgba(255,255,255,0.04); color:#fff; width:100%; font-size:13px;">
                <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:6px;">This logo will appear on login and register pages</div>
                @error('isp_logo')<div class="adm-form-error">{{ $message }}</div>@enderror
                @if($settings && $settings->isp_logo)
                <div style="margin-top:12px; display:flex; align-items:center; gap:12px;">
                    <img src="{{ asset('storage/' . $settings->isp_logo) }}" alt="ISP logo"
                         style="width:50px; height:50px; border-radius:8px; object-fit:contain; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.02);">
                    <span style="font-size:12px; color:rgba(255,255,255,0.5);">Current ISP logo</span>
                </div>
                @endif
            </div>

            <div class="adm-form-group">
                <label>Favicon (Icon in Browser Tab)</label>
                <input type="file" name="favicon" accept="image/*"
                       style="padding:10px; border:1px solid rgba(255,255,255,0.1); border-radius:10px; background:rgba(255,255,255,0.04); color:#fff; width:100%; font-size:13px;">
                <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:6px;">Recommended: 32x32 PNG or ICO</div>
                @error('favicon')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>Dashboard Tagline</label>
                <textarea name="dashboard_tagline" rows="2" placeholder="e.g. Manage your ISP services"
                          style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); color:#fff; font-size:13px; font-family:inherit; resize:vertical;">{{ old('dashboard_tagline', $settings->dashboard_tagline ?? '') }}</textarea>
                @error('dashboard_tagline')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="btn-primary">💾 Save Appearance</button>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="adm-card">
        <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.07);">
            🔒 Change Password
        </h3>

        <form action="{{ route('admin.settings.password') }}" method="POST">
            @csrf

            <div class="adm-form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required autocomplete="current-password"
                       style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); color:#fff; font-size:13px; font-family:inherit;">
                @error('current_password')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>New Password</label>
                <input type="password" name="password" required autocomplete="new-password"
                       style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); color:#fff; font-size:13px; font-family:inherit;">
                @error('password')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                       style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); color:#fff; font-size:13px; font-family:inherit;">
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="btn-primary">🔑 Update Password</button>
            </div>
        </form>
    </div>

</div>
@endsection
