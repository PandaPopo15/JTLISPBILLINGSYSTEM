<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Installer') — ISP Billing</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            color: #f5f5f5;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(255,0,0,0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255,0,0,0.08) 0%, transparent 50%);
            pointer-events: none; z-index: -1;
        }

        .adm-header {
            position: fixed; top: 0; left: 0; right: 0; height: 60px;
            background: rgba(10,10,10,0.97);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 1000;
            box-shadow: 0 2px 12px rgba(0,0,0,0.4);
        }
        .adm-header-brand { font-size: 20px; font-weight: 700; color: #fff; letter-spacing: 0.5px; }
        .adm-header-brand span { color: #ff5252; }
        .adm-header-right { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1002; }
        .adm-theme-toggle {
            width: 38px; height: 38px; border-radius: 50%;
            background: transparent; color: #fff;
            border: none;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; cursor: pointer; transition: all 0.2s;
        }
        .adm-theme-toggle:hover { opacity: 0.8; }
        .adm-notif-btn {
            width: 38px; height: 38px; border-radius: 50%;
            background: transparent; color: #fff;
            border: none;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; cursor: pointer; transition: all 0.2s;
            position: relative;
        }
        .adm-notif-btn:hover { opacity: 0.8; }
        .adm-notif-badge {
            position: absolute; top: -4px; right: -4px;
            background: #ff5252; color: #fff; font-size: 10px;
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; border: 2px solid #0a0a0a;
        }
        .adm-notif-dropdown {
            position: absolute; top: 46px; right: 100px;
            background: #111; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; min-width: 320px; display: none; z-index: 1001;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5); max-height: 400px;
            overflow-y: auto;
        }
        .adm-notif-dropdown.open { display: block; }
        .adm-notif-header {
            padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.1);
            font-weight: 600; font-size: 14px; color: #fff;
        }
        .adm-notif-item {
            padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.85); font-size: 13px; cursor: pointer;
            transition: background 0.2s;
        }
        .adm-notif-item:hover { background: rgba(255,255,255,0.08); }
        .adm-notif-item:last-child { border-bottom: none; }
        .adm-notif-empty {
            padding: 24px 16px; text-align: center;
            color: rgba(255,255,255,0.4); font-size: 13px;
        }
        .adm-notif-time {
            font-size: 11px; color: rgba(255,255,255,0.4); margin-top: 4px;
        }
        .adm-user-info { text-align: right; line-height: 1.3; }
        .adm-user-name { font-size: 14px; font-weight: 600; color: #fff; }
        .adm-user-role { font-size: 11px; color: rgba(255,255,255,0.5); }
        .adm-profile-btn {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #d50000);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; cursor: pointer;
            border: none; overflow: hidden; flex-shrink: 0;
        }
        .adm-profile-btn img { width: 100%; height: 100%; object-fit: cover; }
        .adm-dropdown {
            position: absolute; top: 46px; right: 0;
            background: #111; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; min-width: 160px; display: none; z-index: 1001;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
        }
        .adm-dropdown.open { display: block; }
        .adm-dropdown a, .adm-dropdown button {
            display: block; width: 100%; text-align: left;
            padding: 10px 16px; color: #f5f5f5; text-decoration: none;
            font-size: 14px; background: none; border: none; cursor: pointer;
            transition: background 0.2s;
        }
        .adm-dropdown a:hover, .adm-dropdown button:hover { background: rgba(255,255,255,0.08); }

        .adm-sidebar {
            position: fixed; top: 60px; left: 0; bottom: 0;
            width: 240px; background: rgba(10,10,10,0.97);
            border-right: 1px solid rgba(255,255,255,0.08);
            padding: 24px 0; z-index: 999;
            box-shadow: 2px 0 12px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        .adm-sidebar.collapsed { transform: translateX(-240px); }
        .adm-sidebar.mobile-open { transform: translateX(0); }
        
        @media (max-width: 768px) {
            .adm-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .adm-sidebar.mobile-open {
                transform: translateX(0);
            }
        }
        .adm-sidebar-nav { display: flex; flex-direction: column; gap: 4px; padding: 0 12px; }
        .adm-sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 12px;
            color: rgba(255,255,255,0.6); text-decoration: none;
            font-size: 14px; font-weight: 500; transition: all 0.2s;
        }
        .adm-sidebar-link:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .adm-sidebar-link.active {
            background: linear-gradient(135deg, rgba(255,82,82,0.15), rgba(213,0,0,0.15));
            color: #ff6b6b; border: 1px solid rgba(255,82,82,0.3);
        }
        .adm-sidebar-icon { font-size: 18px; width: 20px; text-align: center; }

        .adm-sidebar-toggle {
            position: fixed; left: 248px; top: 70px;
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(255,82,82,0.9); color: #fff;
            border: none; cursor: pointer; font-size: 12px;
            display: flex; align-items: center; justify-content: center;
            z-index: 1001; transition: left 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }
        .adm-sidebar-toggle.collapsed { left: 8px; }
        
        @media (max-width: 768px) {
            .adm-sidebar-toggle {
                left: 8px;
                top: 70px;
                z-index: 1002;
            }
        }

        .adm-sidebar-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 899; display: none;
            transition: opacity 0.3s ease;
        }
        .adm-sidebar.mobile-open + .adm-sidebar-backdrop { display: block; }

        /* ── LIGHT MODE ── */
        body.light-mode {
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 50%, #f5f5f5 100%);
            color: #000000;
        }
        body.light-mode::before {
            background: radial-gradient(circle at 20% 80%, rgba(0,123,255,0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(0,123,255,0.08) 0%, transparent 50%);
        }
        body.light-mode .adm-header {
            background: rgba(255,255,255,0.97);
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        body.light-mode .adm-header-brand { color: #000000; }
        body.light-mode .adm-header-brand span { color: #007bff; }
        body.light-mode .adm-sidebar {
            background: rgba(255,255,255,0.95);
            border-right: 1px solid rgba(0,0,0,0.06);
        }
        body.light-mode .adm-sidebar-link {
            color: #000000;
        }
        body.light-mode .adm-sidebar-link:hover {
            background: rgba(0,0,0,0.06);
            color: #000000;
        }
        body.light-mode .adm-sidebar-link.active {
            background: rgba(0,123,255,0.15);
            color: #007bff;
            border-color: rgba(0,123,255,0.3);
        }
        body.light-mode .adm-main { background: #f8f9fa; }
        body.light-mode .adm-card {
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(0,0,0,0.08);
        }
        body.light-mode .adm-page-title { color: #000000; }
        body.light-mode .adm-page-subtitle { color: #000000; }
        body.light-mode .adm-toast { background: rgba(255,255,255,0.98); color: #000000; }
        body.light-mode .adm-toast.welcome {
            background: linear-gradient(135deg, rgba(0,123,255,0.95) 0%, rgba(0,86,179,0.95) 100%);
            border: 1px solid rgba(0,123,255,0.6);
            border-left: 4px solid #007bff;
            color: #fff;
        }
        body.light-mode .adm-dropdown { background: #fff; border: 1px solid rgba(0,0,0,0.1); }
        body.light-mode .adm-dropdown a, body.light-mode .adm-dropdown button { color: #000000; }
        body.light-mode .adm-dropdown a:hover, body.light-mode .adm-dropdown button:hover { background: rgba(0,0,0,0.08); }
        body.light-mode .adm-notif-dropdown { background: #fff; border: 1px solid rgba(0,0,0,0.1); }
        body.light-mode .adm-notif-header { color: #000000; border-bottom: 1px solid rgba(0,0,0,0.1); }
        body.light-mode .adm-notif-item { color: #000000; border-bottom: 1px solid rgba(0,0,0,0.05); }
        body.light-mode .adm-notif-item:hover { background: rgba(0,0,0,0.08); }
        body.light-mode .adm-notif-empty { color: #666666; }
        body.light-mode .adm-user-name { color: #000000; }
        body.light-mode .adm-user-role { color: #666666; }
        body.light-mode .adm-kpi {
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
        }
        body.light-mode .adm-kpi-label { color: #666666; }
        body.light-mode .adm-kpi-value { color: #000000; }
        body.light-mode .adm-table th { color: #000000; border-bottom: 1px solid rgba(0,0,0,0.08); }
        body.light-mode .adm-table td { color: #000000; border-bottom: 1px solid rgba(0,0,0,0.05); }
        body.light-mode .adm-table tr:hover td { background: rgba(0,0,0,0.03); }
        body.light-mode .adm-pagination a, body.light-mode .adm-pagination span {
            border: 1px solid rgba(0,0,0,0.1); color: #000000;
        }
        body.light-mode .adm-pagination a:hover { background: rgba(0,0,0,0.08); color: #000000; }
        body.light-mode .adm-pagination .active-page { background: rgba(0,123,255,0.2); color: #007bff; border-color: rgba(0,123,255,0.4); }
        body.light-mode .modal-backdrop { background: rgba(0,0,0,0.5); }
        body.light-mode .modal-box { background: #fff; border: 1px solid rgba(0,0,0,0.1); }
        body.light-mode .modal-title { color: #000000; }
        body.light-mode .modal-header { border-bottom: 1px solid rgba(0,0,0,0.1); }
        body.light-mode .modal-footer { border-top: 1px solid rgba(0,0,0,0.1); }
        body.light-mode .adm-form-group label { color: #000000; }
        body.light-mode .adm-form-group input,
        body.light-mode .adm-form-group textarea,
        body.light-mode .adm-form-group select {
            background: rgba(0,0,0,0.04); color: #000000; border: 1px solid rgba(0,0,0,0.1);
        }
        body.light-mode .adm-form-group input:focus,
        body.light-mode .adm-form-group textarea:focus,
        body.light-mode .adm-form-group select:focus {
            border-color: rgba(0,123,255,0.5);
            background: rgba(0,0,0,0.06);
        }
        body.light-mode .modal-close {
            background: rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.1);
            color: #666666;
        }
        body.light-mode .modal-close:hover {
            background: rgba(255,82,82,0.2); color: #ff6b6b; border-color: rgba(255,82,82,0.3);
        }
        body.light-mode .adm-theme-toggle {
            color: #000000;
        }
        body.light-mode .adm-notif-btn {
            color: #000000;
        }
        body.light-mode .adm-profile-btn {
            color: #fff;
        }
        body.light-mode .adm-sidebar-toggle {
            background: rgba(0,123,255,0.9);
        }
        
        @media (max-width: 768px) {
            .adm-header {
                padding: 0 12px;
                height: 56px;
            }
            .adm-header-brand {
                font-size: 18px;
            }
            .adm-header-right {
                gap: 8px;
            }
            .adm-user-info {
                display: none;
            }
            .adm-theme-toggle,
            .adm-notif-btn,
            .adm-profile-btn {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
            .adm-notif-dropdown {
                right: 0;
                min-width: 280px;
                max-width: calc(100vw - 24px);
            }
        }

        .adm-main {
            padding-top: 60px;
            min-height: 100vh;
            padding: 92px 32px 32px 272px;
            transition: margin-left 0.3s ease;
        }
        .adm-main.expanded { margin-left: 0; padding-left: 32px; }
        
        @media (max-width: 768px) {
            .adm-main {
                margin-left: 0;
                padding: 80px 16px 32px 16px;
            }
        }

        .adm-page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px; flex-wrap: wrap; gap: 14px;
        }
        .adm-page-title { font-size: 26px; font-weight: 700; color: #fff; }
        .adm-page-subtitle { font-size: 13px; color: rgba(255,255,255,0.5); margin-top: 4px; }

        .btn-primary {
            background: linear-gradient(135deg, #ff5252, #d50000);
            color: #fff; border: none; padding: 10px 20px;
            border-radius: 10px; font-weight: 600; font-size: 14px;
            cursor: pointer; transition: all 0.2s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-primary:hover { filter: brightness(1.1); box-shadow: 0 0 16px rgba(255,82,82,0.4); }
        .btn-secondary {
            background: rgba(255,255,255,0.06); color: #fff;
            border: 1px solid rgba(255,255,255,0.12);
            padding: 10px 20px; border-radius: 10px; font-weight: 600;
            font-size: 14px; cursor: pointer; transition: all 0.2s;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); }
        .btn-sm { padding: 6px 14px; font-size: 13px; border-radius: 8px; }

        .adm-card {
            background: rgba(18,18,18,0.95);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px; padding: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
        }

        .adm-kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 28px; }
        .adm-kpi {
            background: rgba(18,18,18,0.95);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
        }
        .adm-kpi-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1.2px; color: rgba(255,255,255,0.5); margin-bottom: 10px; font-weight: 700; }
        .adm-kpi-value { font-size: 36px; font-weight: 700; color: #fff; line-height: 1; margin-bottom: 6px; }

        .adm-table-wrap { overflow-x: auto; border-radius: 14px; }
        .adm-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .adm-table th {
            padding: 12px 16px; text-align: left; font-size: 11px;
            text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,0.45); border-bottom: 1px solid rgba(255,255,255,0.08);
            white-space: nowrap;
        }
        .adm-table td {
            padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.85); vertical-align: middle;
        }
        .adm-table tr:last-child td { border-bottom: none; }
        .adm-table tr:hover td { background: rgba(255,255,255,0.03); }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;
        }
        .badge-green { background: rgba(76,175,80,0.15); color: #66bb6a; border: 1px solid rgba(76,175,80,0.25); }
        .badge-red { background: rgba(255,82,82,0.15); color: #ff6b6b; border: 1px solid rgba(255,82,82,0.25); }
        .badge-yellow { background: rgba(255,193,7,0.15); color: #ffd54f; border: 1px solid rgba(255,193,7,0.25); }

        .adm-pagination { display: flex; align-items: center; justify-content: flex-end; gap: 6px; margin-top: 20px; flex-wrap: wrap; }
        .adm-pagination a, .adm-pagination span {
            padding: 7px 13px; border-radius: 8px; font-size: 13px;
            border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
            text-decoration: none; transition: all 0.2s;
        }
        .adm-pagination a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .adm-pagination .active-page { background: rgba(255,82,82,0.2); color: #ff6b6b; border-color: rgba(255,82,82,0.4); }

        .adm-toast-wrap {
            position: fixed; top: 72px; right: 20px;
            z-index: 1100; display: flex; flex-direction: column; gap: 10px;
            width: min(360px, calc(100vw - 40px));
        }
        .adm-toast {
            padding: 14px 18px; border-radius: 12px;
            font-size: 14px; line-height: 1.5; color: #fff;
            border-left: 4px solid #4caf50;
            background: rgba(15,15,15,0.98);
            border: 1px solid rgba(76,175,80,0.5);
            border-left: 4px solid #4caf50;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            position: relative; overflow: hidden;
        }
        .adm-toast.error { border-left-color: #ff5252; border-color: rgba(255,82,82,0.4); }
        .adm-toast.welcome {
            background: linear-gradient(135deg, rgba(255,82,82,0.95) 0%, rgba(213,0,0,0.95) 100%);
            border: 1px solid rgba(255,82,82,0.6);
            border-left: 4px solid #ff6b6b;
            padding: 18px 20px;
        }
        .adm-toast-bar {
            position: absolute; bottom: 0; left: 0; height: 3px;
            background: #4caf50; width: 100%;
            animation: toastBar 4s linear forwards;
        }
        .adm-toast.error .adm-toast-bar { background: #ff5252; }
        .adm-toast.welcome .adm-toast-bar { background: #ff6b6b; }
        @keyframes toastBar { from { width: 100%; } to { width: 0; } }
    </style>
</head>
<body>

<header class="adm-header">
    <div class="adm-header-brand">ISP <span>Billing</span></div>
    <div class="adm-header-right">
        <button class="adm-theme-toggle" id="adm-theme-toggle">🌙</button>
        <button class="adm-notif-btn" id="adm-notif-btn">
            🔔
            <span class="adm-notif-badge" id="adm-notif-badge" style="display: none;">0</span>
        </button>
        <div class="adm-notif-dropdown" id="adm-notif-dropdown">
            <div class="adm-notif-header">Notifications</div>
            <div id="adm-notif-list">
                <div class="adm-notif-empty">No notifications yet</div>
            </div>
        </div>
        <div class="adm-user-info">
            <div class="adm-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
            <div class="adm-user-role">Installer / Technician</div>
        </div>
        <div class="adm-profile-btn" id="adm-profile-btn">
            @if(auth()->user()->profile_image)
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile">
            @else
                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
            @endif
        </div>
        <div class="adm-dropdown" id="adm-dropdown">
            <a href="{{ route('installer.profile') }}">👤 My Profile</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">🚪 Logout</button>
            </form>
        </div>
    </div>
</header>

{{-- SIDEBAR TOGGLE --}}
<button class="adm-sidebar-toggle" id="adm-sidebar-toggle">◀</button>

<aside class="adm-sidebar" id="adm-sidebar">
    <nav class="adm-sidebar-nav">
        <a href="{{ route('installer.dashboard') }}" class="adm-sidebar-link {{ request()->routeIs('installer.dashboard') ? 'active' : '' }}">
            <span class="adm-sidebar-icon">📋</span>
            <span>My Job Orders</span>
        </a>
    </nav>
</aside>

<div class="adm-sidebar-backdrop" id="adm-sidebar-backdrop"></div>

<main class="adm-main" id="adm-main">
    @if(session('success') || session('error') || session('welcome'))
    <div class="adm-toast-wrap">
        @if(session('welcome'))
        <div class="adm-toast welcome" id="adm-toast-welcome">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="font-size:28px;">👋</div>
                <div>
                    <div style="font-weight:700;font-size:16px;margin-bottom:2px;">Welcome back!</div>
                    <div style="font-size:13px;opacity:0.9;">Hello, {{ session('welcome') }}! Ready to complete your tasks?</div>
                </div>
            </div>
            <div class="adm-toast-bar"></div>
        </div>
        @endif
        @if(session('success'))
        <div class="adm-toast" id="adm-toast">
            ✅ {{ session('success') }}
            <div class="adm-toast-bar"></div>
        </div>
        @endif
        @if(session('error'))
        <div class="adm-toast error" id="adm-toast-err">
            ❌ {{ session('error') }}
            <div class="adm-toast-bar"></div>
        </div>
        @endif
    </div>
    @endif

    @yield('content')
</main>

<script>
    // Notification dropdown
    const notifBtn = document.getElementById('adm-notif-btn');
    const notifDropdown = document.getElementById('adm-notif-dropdown');
    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('open');
        });
        document.addEventListener('click', e => {
            if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove('open');
            }
        });
    }

    // Profile dropdown
    const profileBtn = document.getElementById('adm-profile-btn');
    const dropdown   = document.getElementById('adm-dropdown');
    if (profileBtn && dropdown) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });
        document.addEventListener('click', e => {
            if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    }

    // Sidebar toggle
    const sidebar      = document.getElementById('adm-sidebar');
    const main         = document.getElementById('adm-main');
    const toggleBtn    = document.getElementById('adm-sidebar-toggle');
    const backdrop     = document.getElementById('adm-sidebar-backdrop');
    let collapsed = false;
    
    if (toggleBtn && sidebar && main) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
            } else {
                collapsed = !collapsed;
                sidebar.classList.toggle('collapsed', collapsed);
                main.classList.toggle('expanded', collapsed);
                toggleBtn.classList.toggle('collapsed', collapsed);
                toggleBtn.textContent = collapsed ? '▶' : '◀';
            }
        });
    }
    
    if (backdrop) {
        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
        });
    }
    
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && sidebar) {
            sidebar.classList.remove('mobile-open');
        }
    });

    // Theme toggle
    const themeBtn = document.getElementById('adm-theme-toggle');
    let isDark = true;
    if (themeBtn) {
        themeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            isDark = !isDark;
            document.body.classList.toggle('light-mode', !isDark);
            themeBtn.textContent = isDark ? '🌙' : '☀️';
            localStorage.setItem('installer-theme', isDark ? 'dark' : 'light');
        });
        // Load saved theme
        const savedTheme = localStorage.getItem('installer-theme');
        if (savedTheme === 'light') {
            isDark = false;
            document.body.classList.add('light-mode');
            themeBtn.textContent = '☀️';
        }
    }

    // Auto-dismiss toasts
    ['adm-toast', 'adm-toast-err', 'adm-toast-welcome'].forEach(id => {
        const el = document.getElementById(id);
        if (el) setTimeout(() => el.remove(), 4500);
    });
</script>

@stack('scripts')
</body>
</html>
