<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — ISP Billing</title>
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

        /* ── HEADER ── */
        .adm-header {
            position: fixed; top: 0; left: 0; right: 0; height: 60px;
            background: rgba(10,10,10,0.97);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 1000;
            box-shadow: 0 2px 12px rgba(0,0,0,0.4);
        }
        .adm-header-left { display: flex; align-items: center; gap: 14px; }
        .adm-header-brand { font-size: 20px; font-weight: 700; color: #fff; letter-spacing: 0.5px; }
        .adm-header-brand span { color: #ff5252; }
        .adm-header-right { display: flex; align-items: center; gap: 12px; position: relative; }
        .adm-theme-toggle {
            width: 38px; height: 38px; border-radius: 50%;
            background: rgba(255,255,255,0.1); color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; cursor: pointer; transition: all 0.2s;
        }
        .adm-theme-toggle:hover { background: rgba(255,255,255,0.2); }
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
        .adm-user-info { text-align: right; line-height: 1.3; }
        .adm-user-name { font-size: 14px; font-weight: 600; color: #fff; }
        .adm-user-role { font-size: 11px; color: rgba(255,255,255,0.5); }

        /* ── SHELL ── */
        .adm-shell {
            display: flex;
            padding-top: 60px;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .adm-sidebar {
            width: 260px; flex-shrink: 0;
            background: rgba(10,10,10,0.95);
            border-right: 1px solid rgba(255,255,255,0.06);
            padding: 24px 16px;
            position: fixed; top: 60px; left: 0; bottom: 0;
            overflow-y: auto; overflow-x: hidden;
            transition: transform 0.3s ease;
            z-index: 900;
        }
        .adm-sidebar::-webkit-scrollbar { width: 4px; }
        .adm-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .adm-sidebar.collapsed { transform: translateX(-260px); }
        .adm-sidebar.mobile-open { transform: translateX(0); }
        .adm-nav-label {
            font-size: 10px; text-transform: uppercase; letter-spacing: 1.8px;
            color: rgba(255,255,255,0.35); padding: 0 10px; margin: 20px 0 8px;
        }
        .adm-nav-label:first-child { margin-top: 0; }
        .adm-nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: 12px; margin-bottom: 4px;
            color: rgba(255,255,255,0.65); text-decoration: none; font-size: 14px;
            font-weight: 500; transition: all 0.2s ease; cursor: pointer;
            border: 1px solid transparent;
        }
        .adm-nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
            border-color: rgba(255,255,255,0.08);
        }
        .adm-nav-item.active {
            background: rgba(255,82,82,0.15);
            color: #ff6b6b;
            border-color: rgba(255,82,82,0.3);
            font-weight: 600;
        }
        .adm-nav-item .nav-icon { font-size: 16px; width: 20px; text-align: center; flex-shrink: 0; }
        .adm-sidebar-toggle {
            position: fixed; left: 268px; top: 70px;
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(255,82,82,0.9); color: #fff;
            border: none; cursor: pointer; font-size: 12px;
            display: flex; align-items: center; justify-content: center;
            z-index: 901; transition: left 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }
        .adm-sidebar-toggle.collapsed { left: 8px; }

        /* ── SIDEBAR BACKDROP ── */
        .adm-sidebar-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 899; display: none;
            transition: opacity 0.3s ease;
        }
        .adm-sidebar.mobile-open + .adm-sidebar-backdrop { display: block; }

        /* ── MAIN ── */
        .adm-main {
            flex: 1; margin-left: 260px;
            padding: 32px; min-width: 0;
            transition: margin-left 0.3s ease;
        }
        .adm-main.expanded { margin-left: 0; }

        /* ── TOAST ── */
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
        .adm-toast-bar {
            position: absolute; bottom: 0; left: 0; height: 3px;
            background: #4caf50; width: 100%;
            animation: toastBar 4s linear forwards;
        }
        .adm-toast.error .adm-toast-bar { background: #ff5252; }
        @keyframes toastBar { from { width: 100%; } to { width: 0; } }

        /* ── PAGE HEADER ── */
        .adm-page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px; flex-wrap: wrap; gap: 14px;
        }
        .adm-page-title { font-size: 26px; font-weight: 700; color: #fff; }
        .adm-page-subtitle { font-size: 13px; color: rgba(255,255,255,0.5); margin-top: 4px; }

        /* ── BUTTONS ── */
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
        .btn-danger { background: rgba(255,82,82,0.15); color: #ff6b6b; border: 1px solid rgba(255,82,82,0.3); }
        .btn-danger:hover { background: rgba(255,82,82,0.25); }
        .btn-success { background: rgba(76,175,80,0.15); color: #66bb6a; border: 1px solid rgba(76,175,80,0.3); }
        .btn-success:hover { background: rgba(76,175,80,0.25); }

        /* ── CARD ── */
        .adm-card {
            background: rgba(18,18,18,0.95);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px; padding: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
        }

        /* ── FORM ── */
        .adm-form-group { margin-bottom: 20px; }
        .adm-form-group label {
            display: block; font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: rgba(255,255,255,0.6); margin-bottom: 8px;
        }
        .adm-form-group input,
        .adm-form-group textarea,
        .adm-form-group select {
            width: 100%; padding: 12px 14px; border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.04); color: #fff;
            font-size: 14px; font-family: inherit; transition: border-color 0.2s;
        }
        .adm-form-group input:focus,
        .adm-form-group textarea:focus,
        .adm-form-group select:focus {
            outline: none; border-color: rgba(255,82,82,0.5);
            background: rgba(255,255,255,0.06);
        }
        .adm-form-group textarea { resize: vertical; min-height: 120px; }
        .adm-form-group select option { background: #1a1a1a; }
        .adm-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .adm-form-error { color: #ff6b6b; font-size: 12px; margin-top: 5px; }
        .adm-form-actions {
            display: flex; justify-content: flex-end; gap: 12px;
            padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.06);
            margin-top: 8px;
        }

        /* ── TABLE ── */
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

        /* ── BADGE ── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;
        }
        .badge-green { background: rgba(76,175,80,0.15); color: #66bb6a; border: 1px solid rgba(76,175,80,0.25); }
        .badge-red { background: rgba(255,82,82,0.15); color: #ff6b6b; border: 1px solid rgba(255,82,82,0.25); }
        .badge-yellow { background: rgba(255,193,7,0.15); color: #ffd54f; border: 1px solid rgba(255,193,7,0.25); }

        /* ── KPI CARDS ── */
        .adm-kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 28px; }
        .adm-kpi {
            background: rgba(18,18,18,0.95); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px; padding: 22px;
        }
        .adm-kpi-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1.2px; color: rgba(255,255,255,0.5); margin-bottom: 10px; }
        .adm-kpi-value { font-size: 36px; font-weight: 700; color: #fff; line-height: 1; margin-bottom: 6px; }
        .adm-kpi-sub { font-size: 12px; color: rgba(255,255,255,0.4); }

        /* ── PAGINATION ── */
        .adm-pagination { display: flex; align-items: center; justify-content: flex-end; gap: 6px; margin-top: 20px; flex-wrap: wrap; }
        .adm-pagination a, .adm-pagination span {
            padding: 7px 13px; border-radius: 8px; font-size: 13px;
            border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
            text-decoration: none; transition: all 0.2s;
        }
        .adm-pagination a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .adm-pagination .active-page { background: rgba(255,82,82,0.2); color: #ff6b6b; border-color: rgba(255,82,82,0.4); }

        /* ── SEARCH BAR ── */
        .adm-search-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .adm-search-bar input, .adm-search-bar select {
            padding: 10px 14px; border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.04); color: #fff; font-size: 14px;
        }
        .adm-search-bar input { flex: 1; min-width: 200px; }
        .adm-search-bar input:focus, .adm-search-bar select:focus { outline: none; border-color: rgba(255,82,82,0.4); }
        .adm-search-bar select option { background: #1a1a1a; }

        /* ── LIGHT MODE ── */
        body.light-mode {
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 50%, #f5f5f5 100%);
            color: #000000;
        }
        body.light-mode * {
            color: #000000 !important;
        }
        body.light-mode ::placeholder {
            color: #666666 !important;
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
        body.light-mode .adm-nav-label { color: #000000; }
        body.light-mode .adm-nav-item {
            color: #000000;
        }
        body.light-mode .adm-nav-item:hover {
            background: rgba(0,0,0,0.06);
            color: #000000;
            border-color: rgba(0,0,0,0.08);
        }
        body.light-mode .adm-nav-item.active {
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
        body.light-mode .adm-dropdown { background: #fff; border: 1px solid rgba(0,0,0,0.1); }
        body.light-mode .adm-dropdown a, body.light-mode .adm-dropdown button { color: #000000; }
        body.light-mode .adm-dropdown a:hover, body.light-mode .adm-dropdown button:hover { background: rgba(0,0,0,0.08); }
        body.light-mode .adm-user-name { color: #000000; }
        body.light-mode .adm-user-role { color: #000000; }
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
        body.light-mode .adm-table th { color: #000000; border-bottom: 1px solid rgba(0,0,0,0.08); }
        body.light-mode .adm-table td { color: #000000; border-bottom: 1px solid rgba(0,0,0,0.05); }
        body.light-mode .adm-table tr:hover td { background: rgba(0,0,0,0.03); }
        body.light-mode .adm-pagination a, body.light-mode .adm-pagination span {
            border: 1px solid rgba(0,0,0,0.1); color: #000000;
        }
        body.light-mode .adm-pagination a:hover { background: rgba(0,0,0,0.08); color: #000000; }
        body.light-mode .adm-pagination .active-page { background: rgba(0,123,255,0.2); color: #007bff; border-color: rgba(0,123,255,0.4); }
        body.light-mode .adm-search-bar input, body.light-mode .adm-search-bar select {
            background: rgba(0,0,0,0.04); color: #000000; border: 1px solid rgba(0,0,0,0.1);
        }
        body.light-mode .adm-search-bar input:focus, body.light-mode .adm-search-bar select:focus { border-color: rgba(0,123,255,0.4); }
        body.light-mode .adm-search-bar select option { background: #fff; }
        body.light-mode .adm-theme-toggle { background: rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.2); color: #000000; }
        body.light-mode .adm-theme-toggle:hover { background: rgba(0,0,0,0.2); }

@media (max-width: 768px) {
            .adm-header {
                padding: 0 16px;
            }
            .adm-header-right {
                gap: 8px;
            }
            .adm-user-info {
                display: none;
            }
            .adm-sidebar { 
                transform: translateX(-100%); 
                width: 280px;
            }
            .adm-sidebar.mobile-open { 
                transform: translateX(0); 
            }
            .adm-main { 
                margin-left: 0; 
                padding: 20px 16px; 
            }
            .adm-page-header {
                flex-direction: column; 
                align-items: stretch; 
                gap: 16px;
            }
            .adm-form-row { 
                grid-template-columns: 1fr; 
            }
            .adm-search-bar {
                flex-direction: column;
            }
            .adm-search-bar input {
                min-width: auto;
            }
            .adm-kpi-grid { 
                grid-template-columns: 1fr; 
                gap: 16px;
            }
            .adm-table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .adm-table th,
            .adm-table td {
                padding: 12px 8px;
                font-size: 13px;
            }
            .btn-primary, .btn-secondary,
            .adm-nav-item {
                min-height: 44px;
                min-width: 44px;
            }
            .badge {
                padding: 6px 12px;
                font-size: 12px;
            }
            .adm-sidebar-toggle { 
                left: 16px; 
                z-index: 902;
            }
            .adm-toast-wrap {
                right: 16px;
                left: 16px;
                max-width: none;
                width: calc(100vw - 32px);
            }
            .adm-card {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .adm-main {
                padding: 16px 12px;
            }
            .adm-page-title {
                font-size: 22px;
            }
            .plan-price {
                font-size: 2.5rem;
            }
            .adm-kpi-value {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<header class="adm-header">
    <div class="adm-header-left">
        <div class="adm-header-brand">ISP <span>Billing</span></div>
    </div>
    <div class="adm-header-right">
        <button class="adm-theme-toggle" id="adm-theme-toggle">🌙</button>
        <div class="adm-user-info">
            <div class="adm-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
            <div class="adm-user-role">Administrator</div>
        </div>
        <div class="adm-profile-btn" id="adm-profile-btn">
            @if(auth()->user()->profile_image)
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile">
            @else
                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
            @endif
        </div>
        <div class="adm-dropdown" id="adm-dropdown">
            <a href="{{ route('profile.edit') }}">✏️ Edit Profile</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">🚪 Sign Out</button>
            </form>
        </div>
    </div>
</header>

{{-- SIDEBAR TOGGLE --}}
<button class="adm-sidebar-toggle" id="adm-sidebar-toggle">◀</button>

<div class="adm-shell">
    {{-- SIDEBAR --}}
    <aside class="adm-sidebar" id="adm-sidebar">
        <div class="adm-nav-label">Main</div>

        <a href="{{ route('admin.dashboard') }}"
           class="adm-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">📊</span> Dashboard
        </a>

        <a href="{{ route('admin.clients') }}"
           class="adm-nav-item {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
            <span class="nav-icon">👥</span> Customers
        </a>

        <a href="{{ route('admin.mikrotik') }}"
           class="adm-nav-item {{ request()->routeIs('admin.mikrotik*') ? 'active' : '' }}">
            <span class="nav-icon">🔌</span> MikroTik Routers
        </a>

        <div class="adm-nav-label">Content</div>

        <a href="{{ route('admin.landing') }}"
           class="adm-nav-item {{ request()->routeIs('admin.landing*') ? 'active' : '' }}">
            <span class="nav-icon">🌐</span> Landing Page
        </a>

        <div class="adm-nav-label">Account</div>

        <a href="{{ route('admin.settings') }}"
           class="adm-nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <span class="nav-icon">⚙️</span> Admin Settings
        </a>

        <form action="{{ route('logout') }}" method="POST" style="margin-top: 8px;">
            @csrf
            <button type="submit" class="adm-nav-item" style="width:100%; background:none; border:none; text-align:left;">
                <span class="nav-icon">🚪</span> Sign Out
            </button>
        </form>
    </aside>

    <div class="adm-sidebar-backdrop" id="adm-sidebar-backdrop"></div>

    {{-- MAIN CONTENT --}}
    <main class="adm-main" id="adm-main">

        {{-- TOASTS --}}
        @if(session('success') || session('error'))
        <div class="adm-toast-wrap">
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
</div>

<script>
    // Profile dropdown
    const profileBtn = document.getElementById('adm-profile-btn');
    const dropdown   = document.getElementById('adm-dropdown');
    profileBtn.addEventListener('click', () => dropdown.classList.toggle('open'));
    document.addEventListener('click', e => {
        if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });

    // Sidebar toggle
    const sidebar      = document.getElementById('adm-sidebar');
    const main         = document.getElementById('adm-main');
    const toggleBtn    = document.getElementById('adm-sidebar-toggle');
    const backdrop     = document.getElementById('adm-sidebar-backdrop');
    let collapsed = false;
    toggleBtn.addEventListener('click', () => {
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
    backdrop.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
    });

    // Theme toggle
    const themeBtn = document.getElementById('adm-theme-toggle');
    let isDark = true;
    themeBtn.addEventListener('click', () => {
        isDark = !isDark;
        document.body.classList.toggle('light-mode', !isDark);
        themeBtn.textContent = isDark ? '🌙' : '☀️';
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        isDark = false;
        document.body.classList.add('light-mode');
        themeBtn.textContent = '☀️';
    }

    // Auto-dismiss toasts
    ['adm-toast', 'adm-toast-err'].forEach(id => {
        const el = document.getElementById(id);
        if (el) setTimeout(() => el.remove(), 4500);
    });

</script>

@stack('scripts')
</body>
</html>
