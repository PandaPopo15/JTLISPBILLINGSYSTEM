@extends('admin.layout')
@section('title', 'Account Settings')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Account Settings</div>
        <div class="adm-page-subtitle">Update your admin profile and password.</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">

    {{-- Profile Info --}}
    <div class="adm-card">
        <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.07);">
            👤 Profile Information
        </h3>

        {{-- Avatar --}}
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px;">
            <div style="width:72px; height:72px; border-radius:50%; overflow:hidden; border:2px solid rgba(255,82,82,0.4); flex-shrink:0; background:linear-gradient(135deg,#ff6b6b,#d50000); display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:700; color:#fff;">
                @if(auth()->user()->profile_image)
                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" style="width:100%;height:100%;object-fit:cover;">
                @else
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                @endif
            </div>
            <div>
                <div style="font-weight:600; font-size:15px;">{{ auth()->user()->full_name }}</div>
                <div style="font-size:12px; color:rgba(255,255,255,0.45); margin-top:3px;">{{ auth()->user()->email }}</div>
                <div style="font-size:11px; color:#ff6b6b; margin-top:4px;">Administrator</div>
            </div>
        </div>

        @if($errors->profileBag ?? $errors->any())
        <div style="background:rgba(255,82,82,0.1); border:1px solid rgba(255,82,82,0.3); border-radius:10px; padding:12px 16px; margin-bottom:18px; color:#ff8a80; font-size:13px;">
            @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required>
                </div>
                <div class="adm-form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required>
                </div>
            </div>

            <div class="adm-form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" value="{{ old('middle_name', auth()->user()->middle_name) }}">
            </div>

            <div class="adm-form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}">
                </div>
                <div class="adm-form-group">
                    <label>Age</label>
                    <input type="number" name="age" value="{{ old('age', auth()->user()->age) }}" min="1" max="120">
                </div>
            </div>

            <div class="adm-form-group">
                <label>Address</label>
                <textarea name="address" rows="2">{{ old('address', auth()->user()->address) }}</textarea>
            </div>

            <div class="adm-form-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_image" accept="image/*"
                       style="padding:10px; border:1px solid rgba(255,255,255,0.1); border-radius:10px; background:rgba(255,255,255,0.04); color:#fff; width:100%; font-size:13px;">
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="btn-primary">💾 Save Profile</button>
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
                <input type="password" name="current_password" required autocomplete="current-password">
                @error('current_password')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>New Password</label>
                <input type="password" name="password" required autocomplete="new-password">
                @error('password')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="btn-primary">🔑 Update Password</button>
            </div>
        </form>
    </div>

</div>
@endsection
