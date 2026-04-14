@extends('admin.layout')
@section('title', 'Create Plan')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Create New Plan</div>
        <div class="adm-page-subtitle">Add a new internet service plan</div>
    </div>
</div>

<div class="adm-card" style="max-width:600px;">
    @if($errors->any())
    <div style="background:rgba(255,82,82,0.1); border:1px solid rgba(255,82,82,0.3); border-radius:12px; padding:16px 20px; margin-bottom:24px; color:#ff8a80; font-size:14px;">
        <strong>Please fix the following errors:</strong>
        <ul style="margin-top:8px; padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf

        <div class="adm-form-group">
            <label>Plan Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Basic Plan">
            @error('name')<div class="adm-form-error">{{ $message }}</div>@enderror
        </div>

        <div class="adm-form-group">
            <label>Speed *</label>
            <input type="text" name="speed" value="{{ old('speed') }}" required placeholder="e.g. 50 Mbps">
            @error('speed')<div class="adm-form-error">{{ $message }}</div>@enderror
        </div>

        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>Monthly Price (₱) *</label>
                <input type="number" name="price" value="{{ old('price') }}" required step="0.01" min="0" placeholder="999.00">
                @error('price')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Installation Fee (₱)</label>
                <input type="number" name="installation_fee" value="{{ old('installation_fee', 0) }}" step="0.01" min="0" placeholder="0.00">
                @error('installation_fee')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="adm-form-group">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Brief description of this plan...">{{ old('description') }}</textarea>
            @error('description')<div class="adm-form-error">{{ $message }}</div>@enderror
        </div>

        <div class="adm-form-row">
            <div class="adm-form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width:auto;">
                    <span>Active</span>
                </label>
            </div>
            <div class="adm-form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }} style="width:auto;">
                    <span>Mark as Popular</span>
                </label>
            </div>
        </div>

        <div class="adm-form-actions">
            <a href="{{ route('admin.plans') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">💾 Create Plan</button>
        </div>
    </form>
</div>
@endsection
