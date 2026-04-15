@extends('installer.layout')
@section('title', 'My Profile')

@section('content')

<div class="adm-page-header">
    <div>
        <div class="adm-page-title">My Profile</div>
        <div class="adm-page-subtitle">Manage your personal information</div>
    </div>
</div>

<div class="adm-card" style="max-width:800px;">
    <form action="{{ route('installer.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-bottom:24px;">
            <div class="adm-form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:#fff;font-size:14px;">
                @error('first_name')<div style="color:#ff6b6b;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:#fff;font-size:14px;">
                @error('last_name')<div style="color:#ff6b6b;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-bottom:24px;">
            <div class="adm-form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:#fff;font-size:14px;">
                @error('email')<div style="color:#ff6b6b;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:#fff;font-size:14px;">
                @error('phone_number')<div style="color:#ff6b6b;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="adm-form-group" style="margin-bottom:24px;">
            <label>Profile Image</label>
            @if(auth()->user()->profile_image)
            <div style="margin-bottom:12px;">
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Current Profile" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.1);">
                <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:8px;">Current profile image</div>
            </div>
            @endif
            <input type="file" name="profile_image" accept="image/*" style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:#fff;font-size:14px;">
            <div style="font-size:11px;color:rgba(255,255,255,0.4);margin-top:6px;">Upload a new image to replace the current one (max 2MB)</div>
            @error('profile_image')<div style="color:#ff6b6b;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div style="border-top:1px solid rgba(255,255,255,0.08);padding-top:20px;margin-top:28px;">
            <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.7);margin-bottom:16px;">Change Password (optional)</div>
            
            <div class="adm-form-group" style="margin-bottom:16px;">
                <label>Current Password</label>
                <input type="password" name="current_password" style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:#fff;font-size:14px;">
                @error('current_password')<div style="color:#ff6b6b;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
                <div class="adm-form-group">
                    <label>New Password</label>
                    <input type="password" name="password" style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:#fff;font-size:14px;">
                    @error('password')<div style="color:#ff6b6b;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="adm-form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.04);color:#fff;font-size:14px;">
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:28px;">
            <a href="{{ route('installer.dashboard') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">💾 Save Changes</button>
        </div>
    </form>
</div>

<style>
.adm-form-group { margin-bottom: 0; }
.adm-form-group label {
    display: block; font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.8px;
    color: rgba(255,255,255,0.6); margin-bottom: 8px;
}
</style>

@endsection
