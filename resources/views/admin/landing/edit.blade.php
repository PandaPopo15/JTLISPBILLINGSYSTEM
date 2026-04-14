@extends('admin.layout')
@section('title', 'Landing Page Editor')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Landing Page Editor</div>
        <div class="adm-page-subtitle">Changes are reflected immediately on the public landing page.</div>
    </div>
    <a href="{{ url('/') }}" target="_blank" class="btn-secondary">🔗 View Live Page</a>
</div>

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

<form action="{{ route('admin.landing.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Branding --}}
    <div class="adm-card" style="margin-bottom: 20px;">
        <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.07);">
            🏢 Branding
        </h3>
        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>ISP Name</label>
                <input type="text" name="isp_name" value="{{ old('isp_name', $settings->isp_name) }}" required>
                @error('isp_name')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Theme Color</label>
                <input type="color" name="theme_color" value="{{ old('theme_color', $settings->theme_color) }}"
                       style="height:46px; padding:4px 8px; cursor:pointer;" required>
                @error('theme_color')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="adm-form-group">
            <label>Logo Image</label>
            <input type="file" name="logo" accept="image/*"
                   style="padding:10px; border:1px solid rgba(255,255,255,0.1); border-radius:10px; background:rgba(255,255,255,0.04); color:#fff; width:100%;">
            @error('logo')<div class="adm-form-error">{{ $message }}</div>@enderror
            @if($settings->logo_path)
            <div style="margin-top:12px; display:flex; align-items:center; gap:12px;">
                <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Current logo"
                     style="width:60px; height:60px; border-radius:12px; object-fit:cover; border:1px solid rgba(255,255,255,0.1);">
                <span style="font-size:13px; color:rgba(255,255,255,0.5);">Current logo</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Hero Section --}}
    <div class="adm-card" style="margin-bottom: 20px;">
        <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.07);">
            🎯 Hero Section
        </h3>
        <div class="adm-form-group">
            <label>Headline</label>
            <input type="text" name="headline" value="{{ old('headline', $settings->headline) }}" required
                   placeholder="e.g. Fast, Reliable Internet for Your Home">
            @error('headline')<div class="adm-form-error">{{ $message }}</div>@enderror
        </div>
        <div class="adm-form-group">
            <label>Subheadline</label>
            <textarea name="subheadline" rows="3" required
                      placeholder="Supporting text below the headline...">{{ old('subheadline', $settings->subheadline) }}</textarea>
            @error('subheadline')<div class="adm-form-error">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Plans --}}
    <div class="adm-card" style="margin-bottom: 24px;">
        <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:8px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.07);">
            📋 Plans (JSON)
        </h3>
        <p style="font-size:13px; color:rgba(255,255,255,0.45); margin-bottom:16px;">
            Each plan needs: <code style="background:rgba(255,255,255,0.08); padding:2px 6px; border-radius:4px;">name</code>,
            <code style="background:rgba(255,255,255,0.08); padding:2px 6px; border-radius:4px;">price</code>,
            <code style="background:rgba(255,255,255,0.08); padding:2px 6px; border-radius:4px;">description</code>,
            <code style="background:rgba(255,255,255,0.08); padding:2px 6px; border-radius:4px;">features</code> (array of strings).
        </p>
        <div class="adm-form-group">
            <textarea name="plans" rows="18" required
                      style="font-family: 'Courier New', monospace; font-size: 13px; line-height:1.6;">{{ old('plans', json_encode($settings->plans, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
            @error('plans')<div class="adm-form-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="adm-form-actions" style="border-top:none; padding-top:0;">
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">💾 Save Landing Page</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Validate JSON before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const textarea = document.querySelector('textarea[name="plans"]');
        try {
            JSON.parse(textarea.value);
        } catch (err) {
            e.preventDefault();
            textarea.style.borderColor = 'rgba(255,82,82,0.7)';
            alert('Plans JSON is invalid. Please check the format.\n\n' + err.message);
        }
    });
</script>
@endpush
