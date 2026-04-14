@extends('admin.layout')
@section('title', 'Edit Customer')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Edit Customer</div>
        <div class="adm-page-subtitle">{{ $client->full_name }}</div>
    </div>
    <a href="{{ route('admin.clients') }}" class="btn-secondary">← Back to Customers</a>
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

    <form action="{{ route('admin.clients.update', $client) }}" method="POST">
        @csrf

        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $client->first_name) }}" required>
                @error('first_name')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $client->last_name) }}" required>
                @error('last_name')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" value="{{ old('middle_name', $client->middle_name) }}">
            </div>
            <div class="adm-form-group">
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username', $client->username) }}" required>
                @error('username')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" required>
                @error('email')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $client->phone_number) }}">
            </div>
        </div>

        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>Age</label>
                <input type="number" name="age" value="{{ old('age', $client->age) }}" min="1" max="120">
            </div>
            <div class="adm-form-group">
                <label>Plan Interest</label>
                <input type="text" name="plan_interest" value="{{ old('plan_interest', $client->plan_interest) }}" placeholder="e.g. Pro, Starter">
            </div>
        </div>

        <div class="adm-form-group">
            <label>Address</label>
            <textarea name="address" rows="3">{{ old('address', $client->address) }}</textarea>
        </div>

        <div style="font-size:11px;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,82,82,0.7);margin:4px 0 14px;">Service & Connection</div>
        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>PPPoE Username</label>
                <input type="text" name="pppoe_username" value="{{ old('pppoe_username', $client->pppoe_username) }}"
                       placeholder="e.g. client001" style="font-family:monospace;">
                @error('pppoe_username')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Plan / Subscription</label>
                <select name="plan_interest">
                    <option value="">— None —</option>
                    @foreach(\App\Models\LandingSetting::first()?->plans ?? [] as $plan)
                    <option value="{{ $plan['name'] }}" {{ old('plan_interest', $client->plan_interest) === $plan['name'] ? 'selected' : '' }}>
                        {{ $plan['name'] }} — ₱{{ $plan['price'] }}/mo
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="adm-form-group">
            <label>Assigned MikroTik Router</label>
            <select name="mikrotik_id">
                <option value="">— Not assigned —</option>
                @foreach($mikrotiks as $mt)
                <option value="{{ $mt->id }}"
                    {{ old('mikrotik_id', $client->mikrotik_id) == $mt->id ? 'selected' : '' }}>
                    {{ $mt->name }}@if($mt->location) — {{ $mt->location }}@endif ({{ $mt->ip_address }})
                </option>
                @endforeach
            </select>
            @if($client->mikrotik)
            <div style="margin-top:6px;font-size:12px;color:rgba(255,255,255,0.4);">
                Currently: <span style="color:#81d4fa;">{{ $client->mikrotik->name }}</span>
            </div>
            @endif
        </div>

        <div style="font-size:11px;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,82,82,0.7);margin:4px 0 14px;">Billing & Dates</div>
        <div class="adm-form-row">
            <div class="adm-form-group">
                <label>Installation Date</label>
                <input type="date" name="installation_date" value="{{ old('installation_date', $client->installation_date?->format('Y-m-d')) }}">
                @error('installation_date')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
            <div class="adm-form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date', $client->due_date?->format('Y-m-d')) }}">
                @error('due_date')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="font-size:11px;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,82,82,0.7);margin:4px 0 14px;">Location</div>
        <div class="adm-form-group">
            <label>📍 Pin Location on Map</label>
            <div id="edit-map" style="height:240px;border-radius:12px;border:1px solid rgba(255,255,255,0.1);margin-top:4px;"></div>
            <div style="font-size:11px;color:rgba(255,255,255,0.3);text-align:center;margin-top:5px;">Click map to update pin</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:10px;">
                <div class="adm-form-group" style="margin-bottom:0;">
                    <label>Latitude</label>
                    <input type="text" name="latitude" id="edit-lat" value="{{ old('latitude', $client->latitude) }}" readonly
                           style="background:rgba(255,255,255,0.02);color:rgba(255,255,255,0.5);">
                </div>
                <div class="adm-form-group" style="margin-bottom:0;">
                    <label>Longitude</label>
                    <input type="text" name="longitude" id="edit-lng" value="{{ old('longitude', $client->longitude) }}" readonly
                           style="background:rgba(255,255,255,0.02);color:rgba(255,255,255,0.5);">
                </div>
            </div>
        </div>

        <div class="adm-form-group">
            <label>Password (Leave blank to keep current)</label>
            <input type="password" name="password" autocomplete="new-password">
            @error('password')<div class="adm-form-error">{{ $message }}</div>@enderror
        </div>

        <div class="adm-form-group">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" autocomplete="new-password">
        </div>

        <div class="adm-form-actions">
            <a href="{{ route('admin.clients') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">💾 Save Changes</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $client->latitude ?? 'null' }};
    const lng = {{ $client->longitude ?? 'null' }};
    const map = L.map('edit-map').setView(lat && lng ? [lat, lng] : [9.7517, 122.4003], lat && lng ? 15 : 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19
    }).addTo(map);
    let marker = lat && lng ? L.marker([lat, lng]).addTo(map) : null;
    map.on('click', e => {
        document.getElementById('edit-lat').value = e.latlng.lat.toFixed(7);
        document.getElementById('edit-lng').value = e.latlng.lng.toFixed(7);
        if (marker) marker.setLatLng(e.latlng);
        else marker = L.marker(e.latlng).addTo(map);
    });
</script>
@endpush
