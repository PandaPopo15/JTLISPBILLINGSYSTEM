<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ISP Billing</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            color: #f5f5f5;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            overflow-x: hidden;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(255, 0, 0, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255, 0, 0, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: rgba(10, 10, 10, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        .header-title {
            color: #f5f5f5;
            font-size: 24px;
            font-weight: 700;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .theme-toggle, .notification-bell {
            background: none;
            border: none;
            color: #f5f5f5;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .theme-toggle:hover, .notification-bell:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .profile-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #ff0000);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            border: none;
            font-size: 18px;
            overflow: hidden;
        }
        .profile-button img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .dropdown-menu {
            position: absolute;
            top: 50px;
            right: 0;
            background: rgba(10, 10, 10, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            min-width: 150px;
            display: none;
            z-index: 1001;
        }
        .dropdown-menu.show { display: block; }
        .dropdown-item {
            padding: 10px 15px;
            color: #f5f5f5;
            text-decoration: none;
            display: block;
            transition: background 0.3s;
        }
        .dropdown-item:hover { background: rgba(255, 255, 255, 0.1); }
        .dashboard-shell {
            display: flex;
            gap: 30px;
            max-width: 1600px;
            margin: 0 auto;
            padding: 80px 20px 40px;
            position: relative;
            min-height: calc(100vh - 100px);
        }
        .sidebar-toggle-btn {
            background: none;
            border: none;
            color: #f5f5f5;
            font-size: 18px;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 6px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar.hidden {
            width: 0;
            min-width: 0;
            padding: 0;
            margin-right: 0;
            border: none;
            opacity: 0;
        }
        .sidebar-open-button {
            position: fixed;
            left: 12px;
            top: 50%;
            transform: translate(-150%, -50%);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff5252, #d50000);
            color: #ffffff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1002;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            opacity: 0;
            pointer-events: none;
            transition: transform 0.35s ease, opacity 0.35s ease;
        }
        .sidebar-open-button.show {
            transform: translate(0, -50%);
            opacity: 1;
            pointer-events: auto;
        }
        .sidebar {
            width: 280px;
            min-width: 280px;
            flex-shrink: 0;
            background: rgba(10, 10, 10, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: all 0.35s ease;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            scrollbar-width: none;
        }
        .sidebar::-webkit-scrollbar {
            display: none;
        }
        .sidebar .brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .sidebar .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.95), rgba(255, 0, 0, 0.9));
            color: #ffffff;
            font-weight: 700;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            flex-shrink: 0;
        }
        .sidebar .brand-text {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
            min-width: 0;
        }
        .sidebar .brand-title {
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .sidebar .brand-subtitle {
            color: rgba(255, 255, 255, 0.65);
            font-size: 12px;
            letter-spacing: 0.3px;
        }
        .sidebar .menu-section {
            margin-top: 24px;
        }
        .sidebar .section-label {
            text-transform: uppercase;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.55);
            letter-spacing: 1.9px;
            margin-bottom: 16px;
        }
        .sidebar .action-card {
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 16px 18px;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .sidebar .action-card:hover {
            transform: translateX(6px);
            background: rgba(255, 0, 0, 0.12);
            border-color: rgba(255, 0, 0, 0.25);
        }
        .sidebar .action-card.active {
            background: rgba(255, 0, 0, 0.18);
            border-color: rgba(255, 0, 0, 0.35);
            box-shadow: 0 8px 24px rgba(255, 0, 0, 0.12);
        }
        .sidebar .action-card h3 {
            color: #ffffff;
            font-size: 15px;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .sidebar .action-card p {
            color: rgba(255, 255, 255, 0.65);
            font-size: 13px;
        }
        .main-content {
            flex: 1;
            margin-top: 0;
            margin-left: 0;
            padding: 40px;
            min-height: calc(100vh - 100px);
            padding-bottom: 80px;
        }
        .page-title {
            font-size: 32px;
            color: #ffffff;
            margin-bottom: 32px;
            letter-spacing: 1px;
        }
        .form-card {
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
        }
        .form-group {
            margin-bottom: 22px;
        }
        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
            letter-spacing: 0.9px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .form-group input[type="text"],
        .form-group input[type="color"],
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: rgba(255, 0, 0, 0.4);
            background: rgba(255, 255, 255, 0.06);
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 22px;
        }
        .form-row .form-group {
            margin-bottom: 0;
        }
        .form-note {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 14px 16px;
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
            margin-bottom: 22px;
        }
        .file-label {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            cursor: pointer;
            color: #fff;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .file-label:hover { background: rgba(255, 255, 255, 0.12); }
        input[type="file"] { display: none; }
        .current-logo {
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .current-logo img {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .form-button {
            background: linear-gradient(135deg, #ff5252, #d50000);
            color: #fff;
            border: none;
            padding: 14px 28px;
            border-radius: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .form-button:hover {
            filter: brightness(1.05);
            box-shadow: 0 0 20px rgba(255, 82, 82, 0.4);
        }
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: min(380px, calc(100vw - 40px));
        }
        .toast {
            background: rgba(15, 15, 15, 0.98);
            border: 1px solid rgba(76, 175, 80, 0.8);
            border-left: 4px solid #4caf50;
            color: #ffffff;
            padding: 16px 18px;
            border-radius: 14px;
            box-shadow: 0 16px 50px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            font-size: 14px;
            line-height: 1.5;
        }
        .toast-warning { border-left-color: #ff9800; border-color: rgba(255, 152, 0, 0.4); }
        .toast-success { border-left-color: #4caf50; }
        @media (max-width: 768px) {
            .dashboard-shell { flex-direction: column; }
            .sidebar { position: relative; width: 100%; max-height: none; top: 0; left: 0; margin-bottom: 30px; }
            .main-content { margin-left: 0; margin-top: 0; padding: 24px; }
            .form-row { grid-template-columns: 1fr; }
            .page-title { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title">Admin Dashboard</div>
        <div class="header-right">
            <button class="theme-toggle" id="theme-toggle">☀️</button>
            <button class="notification-bell" id="notification-bell">🔔</button>
            <div class="profile-button" id="profile-button">
                @if(auth()->user()->profile_image)
                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile">
                @else
                    <span>ISP</span>
                @endif
            </div>
            <div class="dropdown-menu" id="dropdown-menu">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">Edit Profile</a>
                <a href="#" onclick="document.getElementById('logout-form').submit();" class="dropdown-item">Sign Out</a>
            </div>
        </div>
    </div>
    <button class="sidebar-open-button" id="sidebar-open-button">☰</button>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    <div class="dashboard-shell">
        <div class="sidebar" id="sidebar">
            <div class="brand">
                <div>
                    <div class="brand-icon">ISP</div>
                </div>
                <div class="brand-text">
                    <div class="brand-title">ISP Billing</div>
                    <div class="brand-subtitle">Control Center</div>
                </div>
                <button class="sidebar-toggle-btn" id="sidebar-toggle">☰</button>
            </div>

        <div class="menu-section">
            <a href="{{ route('admin.dashboard') }}" class="action-card">
                <h3>📊 Dashboard</h3>
            </a>
            <div class="action-card" onclick="alert('Customers - Coming Soon')">
                <h3>👥 Customers</h3>
            </div>
            <div class="action-card" onclick="alert('Service Plans - Coming Soon')">
                <h3>📋 Service Plans</h3>
            </div>
            <div class="action-card" onclick="alert('Invoices - Coming Soon')">
                <h3>🧾 Invoices</h3>
            </div>
            <div class="action-card" onclick="alert('Payments - Coming Soon')">
                <h3>💳 Payments</h3>
            </div>
            <div class="action-card" onclick="alert('Reports - Coming Soon')">
                <h3>📈 Reports</h3>
            </div>
            <div class="action-card active">
                <h3>🌐 Landing Page</h3>
                <p>Edit public marketing page</p>
            </div>
            <div class="action-card" onclick="alert('Settings - Coming Soon')">
                <h3>⚙️ Settings</h3>
            </div>
        </div>
    </div>

    <div class="main-content">
            @if (session('success'))
            <div class="toast-container">
                <div class="toast toast-success" id="toast-success">
                    {{ session('success') }}
                    <div class="toast-progress"></div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-container">
                <div class="toast toast-warning" id="toast-warning">
                    {{ session('error') }}
                    <div class="toast-progress"></div>
                </div>
            </div>
        @endif

        <div class="landing-section">
            <h2>🌐 Landing Page Settings</h2>
            
            @if ($errors->any())
                <div class="toast-container">
                    <div class="toast toast-warning" id="toast-errors">
                        <strong>Fix the following:</strong>
                        <ul style="margin-top:10px; padding-left:18px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <div class="toast-progress"></div>
                    </div>
                </div>
            @endif

            <div class="landing-form-card">
                <form action="{{ route('admin.landing.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-note">
                        Update your landing page headline, plan cards, logo and theme color. Plans must be valid JSON array with fields: name, price, description, and features.
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="isp_name">ISP Name</label>
                            <input type="text" id="isp_name" name="isp_name" value="{{ old('isp_name', $settings->isp_name ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="theme_color">Theme Color</label>
                            <input type="color" id="theme_color" name="theme_color" value="{{ old('theme_color', $settings->theme_color ?? '#ff5252') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="headline">Headline</label>
                        <input type="text" id="headline" name="headline" value="{{ old('headline', $settings->headline ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="subheadline">Subheadline</label>
                        <textarea id="subheadline" name="subheadline" required>{{ old('subheadline', $settings->subheadline ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="plans">Plans JSON</label>
                        <textarea id="plans" name="plans" required>{{ old('plans', json_encode($settings->plans ?? [], JSON_PRETTY_PRINT)) }}</textarea>
                    </div>

                    <div class="file-input-wrapper">
                        <label class="file-label" for="logo">Choose Logo</label>
                        <input type="file" id="logo" name="logo" accept="image/*">
                        @if(isset($settings) && $settings->logo_path)
                            <div class="current-logo">
                                <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Current logo">
                                <span>Current logo</span>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="form-button">Save Landing Settings</button>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const openButton = document.getElementById('sidebar-open-button');
            const sidebar = document.getElementById('sidebar');
            const themeToggle = document.getElementById('theme-toggle');
            const profileButton = document.getElementById('profile-button');
            const dropdown = document.getElementById('dropdown-menu');

            const toggleSidebar = (hidden) => {
                sidebar.classList.toggle('hidden', hidden);
                openButton.classList.toggle('show', hidden);
            };

            sidebarToggle.addEventListener('click', () => {
                const hidden = !sidebar.classList.contains('hidden');
                toggleSidebar(hidden);
            });

            openButton.addEventListener('click', () => {
                toggleSidebar(false);
            });

            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('light-theme');
                themeToggle.textContent = document.body.classList.contains('light-theme') ? '🌙' : '☀️';
            });

            profileButton.addEventListener('click', () => {
                dropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!profileButton.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
