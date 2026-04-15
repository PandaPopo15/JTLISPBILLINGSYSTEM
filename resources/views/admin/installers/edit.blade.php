@extends('admin.layout')
@section('title', 'Edit Installer')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Edit Installer</div>
        <div class="adm-page-subtitle">{{ $installer->full_name }}</div>
    </div>
    <a href="{{ route('admin.installers') }}" class="btn-secondary">← Back to Installers</a>
</div>

<div class="adm-card" style="max-width: 860px;">
    @if($errors->any())
    <div style="background:rgba(255,82,82,0.1); border:1px solid rgba(255,82,82,0.3); border-radius:10px; padding:14px 18px; margin-bottom:24px; color:#ff8a80; font-size:14px;">
        <strong>Please fix the following:</strong>
        <ul style="margin-top:8px; padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.installers.update', $installer) }}" method="POST">
        @csrf

        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $installer->first_name) }}" required>
                @error('first_name')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $installer->last_name) }}" required>
                @error('last_name')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="adm-form-group">
            <label>Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $installer->middle_name) }}">
        </div>

        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $installer->email) }}" required>
                @error('email')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $installer->phone_number) }}">
            </div>
        </div>

        <div class="adm-form-group">
            <label>Password (Leave blank to keep current)</label>
            <input type="password" name="password" autocomplete="new-password">
            @error('password')<div class="adm-form-error">{{ $message }}</div>@enderror
        </div>

        <div class="adm-form-actions">
            <a href="{{ route('admin.installers') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">💾 Save Changes</button>
        </div>
    </form>
</div>
@endsection
