<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ISP Billing</title>
    @php
        $settings = \App\Models\LandingSetting::first();
    @endphp
    @if($settings && $settings->favicon)
    <link rel="icon" href="{{ asset('storage/' . $settings->favicon) }}" type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background: #000;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 30px 16px;
            position: relative;
            overflow-x: hidden;
        }
        .animated-bg {
            position: fixed; inset: 0;
            background: radial-gradient(circle at 20% 20%, rgba(255,0,0,0.08), transparent 40%),
                        radial-gradient(circle at 80% 80%, rgba(255,0,0,0.06), transparent 40%);
            z-index: 0;
        }
        .grid-overlay {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 0; pointer-events: none;
        }
        .container {
            width: 100%; max-width: 620px;
            z-index: 10; position: relative;
        }
        .form-container {
            background: rgba(14,14,14,0.92);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,0,0,0.35);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 0 40px rgba(255,0,0,0.12), 0 24px 60px rgba(0,0,0,0.6);
            animation: borderPulse 4s ease-in-out infinite;
        }
        
        /* Disable heavy animations on mobile */
        @media (max-width: 768px) {
            .form-container {
                animation: none;
                backdrop-filter: blur(5px);
                padding: 28px 20px;
            }
        }
        
        @keyframes borderPulse {
            0%,100% { border-color: rgba(255,0,0,0.3); box-shadow: 0 0 20px rgba(255,0,0,0.08), 0 24px 60px rgba(0,0,0,0.6); }
            50%      { border-color: rgba(255,0,0,0.6); box-shadow: 0 0 40px rgba(255,0,0,0.2), 0 24px 60px rgba(0,0,0,0.6); }
        }
        .form-header { text-align:center; margin-bottom:28px; min-height:100px; display:flex; flex-direction:column; justify-content:center; align-items:center; }
        .form-header h1 {
            font-size: 26px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
            background: linear-gradient(90deg, #fff, rgba(255,80,80,0.9));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .form-header p { color: rgba(255,100,100,0.65); font-size: 12px; letter-spacing: 1px; margin-top: 6px; }

        /* Section divider */
        .section-title {
            font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px;
            color: rgba(255,82,82,0.7); margin: 22px 0 14px;
            padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-group { margin-bottom: 14px; }
        label {
            display: block; font-size: 11px; font-weight: 600;
            letter-spacing: 0.8px; text-transform: uppercase;
            color: rgba(255,255,255,0.55); margin-bottom: 6px;
        }
        input, select, textarea {
            width: 100%; padding: 11px 13px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 9px; font-size: 13px; color: #fff;
            font-family: inherit; transition: all 0.25s;
        }
        input::placeholder, textarea::placeholder { color: rgba(255,255,255,0.25); }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: rgba(255,0,0,0.5);
            background: rgba(255,0,0,0.04);
            box-shadow: 0 0 12px rgba(255,0,0,0.12);
        }
        select option { background: #1a1a1a; }
        textarea { resize: vertical; min-height: 70px; }
        .error-msg { color: #ff6b6b; font-size: 11px; margin-top: 4px; }

        /* Plan cards */
        .plan-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px; margin-bottom: 4px;
        }
        .plan-option { display: none; }
        .plan-label {
            display: flex; flex-direction: column; gap: 4px;
            padding: 14px; border-radius: 12px; cursor: pointer;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            transition: all 0.2s;
        }
        .plan-label:hover { border-color: rgba(255,82,82,0.3); background: rgba(255,82,82,0.06); }
        .plan-option:checked + .plan-label {
            border-color: rgba(255,82,82,0.6);
            background: rgba(255,82,82,0.12);
            box-shadow: 0 0 14px rgba(255,82,82,0.15);
        }
        .plan-name { font-size: 13px; font-weight: 700; color: #fff; }
        .plan-price { font-size: 18px; font-weight: 800; color: #ff6b6b; }
        .plan-desc { font-size: 11px; color: rgba(255,255,255,0.45); line-height: 1.4; }

        /* Map */
        #map {
            width: 100%; height: 260px; border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            margin-top: 8px; z-index: 1;
        }
        .map-hint {
            font-size: 11px; color: rgba(255,255,255,0.35);
            margin-top: 6px; text-align: center;
        }
        .coords-row {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;
        }

        /* Submit */
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, rgba(255,0,0,0.25), rgba(180,0,0,0.2));
            color: #fff; border: 1px solid rgba(255,0,0,0.5);
            border-radius: 10px; font-size: 13px; font-weight: 700;
            cursor: pointer; letter-spacing: 1px; text-transform: uppercase;
            transition: all 0.3s; margin-top: 20px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, rgba(255,0,0,0.4), rgba(180,0,0,0.3));
            border-color: rgba(255,0,0,0.8);
            box-shadow: 0 8px 24px rgba(255,0,0,0.2);
            transform: translateY(-1px);
        }
        .form-footer { text-align:center; margin-top:18px; font-size:12px; color:rgba(255,255,255,0.45); }
        .form-footer a { color: rgba(255,120,120,0.8); text-decoration:none; border-bottom:1px solid rgba(255,120,120,0.3); }
        .form-footer a:hover { color: #fff; }

        /* Errors */
        .errors-box {
            background: rgba(255,82,82,0.08); border: 1px solid rgba(255,82,82,0.3);
            border-radius: 10px; padding: 12px 16px; margin-bottom: 18px;
            color: #ff8a80; font-size: 12px; line-height: 1.7;
        }

        /* Toast */
        .toast-wrap { position:fixed; top:20px; right:20px; z-index:999; width:min(360px,calc(100vw - 40px)); }
        .toast {
            background: rgba(15,15,15,0.97); border:1px solid rgba(76,175,80,0.6);
            border-left: 4px solid #4caf50; color:#fff; padding:14px 18px;
            border-radius:12px; font-size:13px; position:relative; overflow:hidden;
        }
        .toast-bar { position:absolute; bottom:0; left:0; height:3px; background:#4caf50; width:100%; animation:tbar 5s linear forwards; }
        @keyframes tbar { from{width:100%} to{width:0} }

        @media(max-width:520px) {
            .form-container { padding: 24px 16px; }
            .form-row { grid-template-columns: 1fr; }
            .plan-grid { grid-template-columns: 1fr; }
            #map { height: 220px; }
        }
        
        /* Additional mobile optimizations */
        @media (max-width: 768px) {
            body { padding: 20px 12px; }
            .form-header h1 { font-size: 22px; }
            .section-title { font-size: 10px; margin: 18px 0 12px; }
            input, select, textarea { padding: 10px 12px; font-size: 13px; }
            .btn-submit { padding: 12px; font-size: 12px; }
        }
    </style>
</head>
<body>
<div class="animated-bg"></div>
<div class="grid-overlay"></div>

@if(session('success'))
<div class="toast-wrap">
    <div class="toast">{{ session('success') }}<div class="toast-bar"></div></div>
</div>
@endif

<div class="container">
    <div class="form-container">
        <div class="form-header">
            @if($settings && $settings->isp_logo)
                <img src="{{ asset('storage/' . $settings->isp_logo) }}" alt="ISP Logo" style="max-width:100%;max-height:90px;object-fit:contain;">
            @else
                <h1>ISP Billing</h1>
            @endif
            <p>Create Your Account</p>
        </div>

        @if($errors->any())
        <div class="errors-box">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            {{-- ── Personal Info ── --}}
            <div class="section-title">Personal Information</div>

            <div class="form-row">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="Juan">
                    @error('first_name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Dela Cruz">
                    @error('last_name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" value="{{ old('age') }}" min="1" max="120" placeholder="Optional">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="09XXXXXXXXX">
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@email.com">
                    @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- ── Account ── --}}
            <div class="section-title">Account Credentials</div>

            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" value="{{ old('username') }}" required placeholder="Choose a username">
                @error('username')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required placeholder="Min. 8 characters">
                    @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat password">
                </div>
            </div>

            {{-- ── Plan Selection ── --}}
            <div class="section-title">Choose a Plan *</div>

            <div class="plan-grid">
                @foreach($plans as $plan)
                <div>
                    <input type="radio" name="plan_interest" id="plan_{{ $loop->index }}"
                           value="{{ $plan->name }}" class="plan-option"
                           {{ old('plan_interest', $selectedPlan ?? '') === $plan->name ? 'checked' : '' }}>
                    <label for="plan_{{ $loop->index }}" class="plan-label">
                        <span class="plan-name">{{ $plan->name }}</span>
                        <span class="plan-price">₱{{ number_format($plan->price, 0) }}<span style="font-size:11px; font-weight:400; color:rgba(255,255,255,0.4);">/mo</span></span>
                        <span class="plan-desc">{{ $plan->speed }}</span>
                    </label>
                </div>
                @endforeach
            </div>
            @error('plan_interest')<div class="error-msg" style="margin-top:4px;">{{ $message }}</div>@enderror

            {{-- ── Address & Location ── --}}
            <div class="section-title">Address & Location</div>

            <div class="form-group">
                <label>Full Address *</label>
                <textarea name="address" rows="2" required
                          placeholder="House No., Street, Barangay, Municipality/City">{{ old('address') }}</textarea>
                @error('address')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>📍 Pin Your Location on the Map</label>
                <div id="map"></div>
                <div class="map-hint">Click on the map to drop a pin at your exact location.</div>
                <div class="coords-row">
                    <div>
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="lat-input"
                               value="{{ old('latitude') }}" placeholder="Auto-filled from map" readonly
                               style="background:rgba(255,255,255,0.02); color:rgba(255,255,255,0.5);">
                    </div>
                    <div>
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="lng-input"
                               value="{{ old('longitude') }}" placeholder="Auto-filled from map" readonly
                               style="background:rgba(255,255,255,0.02); color:rgba(255,255,255,0.5);">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Register Now</button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="{{ route('login.show') }}">Login here</a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Init map centered on Brgy. Nabulao, Sipalay City, Negros Occidental
    const map = L.map('map').setView([9.668866, 122.460734], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;
    const latInput = document.getElementById('lat-input');
    const lngInput = document.getElementById('lng-input');

    // Restore pin if old values exist
    const oldLat = {{ old('latitude') ? old('latitude') : 'null' }};
    const oldLng = {{ old('longitude') ? old('longitude') : 'null' }};
    if (oldLat && oldLng) {
        marker = L.marker([oldLat, oldLng]).addTo(map);
        map.setView([oldLat, oldLng], 15);
    }

    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        if (marker) marker.setLatLng(e.latlng);
        else marker = L.marker(e.latlng).addTo(map);
    });

    // Auto-dismiss toast
    const toast = document.querySelector('.toast');
    if (toast) setTimeout(() => toast.closest('.toast-wrap').remove(), 5000);
</script>
</body>
</html>
