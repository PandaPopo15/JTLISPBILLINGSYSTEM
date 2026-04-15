<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ISP Billing</title>
    @php
        $settings = \App\Models\LandingSetting::first();
    @endphp
    @if($settings && $settings->favicon)
    <link rel="icon" href="{{ asset('storage/' . $settings->favicon) }}" type="image/x-icon">
    @endif
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: #050505;
            color: #ffffff;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(20px, 8vw, 30px);
        }
        .panel {
            width: min(90vw, 640px);
            max-width: 95vw;
            background: rgba(15, 15, 15, 0.95);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 24px;
            padding: clamp(24px, 8vw, 36px);
            box-shadow: 0 24px 65px rgba(0, 0, 0, 0.35);
            margin: 0 auto;
        }
        .panel h1 {
            font-size: clamp(24px, 8vw, 30px);
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .panel p {
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.8;
            margin-bottom: 18px;
        }
        .panel strong {
            color: #ff7373;
        }
        .action-link {
            display: inline-block;
            margin-top: 16px;
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid transparent;
        }
        .action-link:hover { border-bottom-color: #ff6b6b; }
    </style>
</head>
<body>
    @if($user->isAdmin())
        <script>window.location.href = '{{ route("admin.dashboard") }}';</script>
    @endif
    <div class="panel">
        <h1>Welcome to ISP Billing</h1>
        <p>Hello, {{ $user->first_name }} {{ $user->last_name }}. This is your dashboard landing page while billing features are added.</p>
        <p><strong>Email:</strong> {{ $user->email }}<br>
        <strong>Role:</strong> {{ $user->isAdmin() ? 'Administrator' : 'Customer' }}<br>
        <strong>Verification:</strong> {{ $user->isEmailVerified() ? 'Verified' : 'Pending verification' }}</p>
        <p>Use the navigation and admin area when billing pages are ready. The account center will expand into invoices, payments, and subscription management.</p>
        <a class="action-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>
</body>
</html>
