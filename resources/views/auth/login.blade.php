<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ISP Billing</title>
    @php
        $settings = \App\Models\LandingSetting::first();
    @endphp
    @if($settings && $settings->favicon)
    <link rel="icon" href="{{ asset('storage/' . $settings->favicon) }}" type="image/x-icon">
    @endif
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
            padding: 20px;
        }

        /* Animated background - mobile optimized */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000000;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: #ffffff;
            border-radius: 50%;
            animation: float 20s infinite;
            opacity: 0.1;
        }

        /* Reduce particles on mobile */
        @media (max-width: 768px) {
            .particle:nth-child(n+4) {
                display: none;
            }
        }

        .particle:nth-child(1) { width: 80px; height: 80px; left: 10%; top: 10%; animation-duration: 25s; animation-delay: 0s; }
        .particle:nth-child(2) { width: 100px; height: 100px; right: 5%; top: 30%; animation-duration: 30s; animation-delay: 5s; }
        .particle:nth-child(3) { width: 60px; height: 60px; left: 20%; bottom: 20%; animation-duration: 28s; animation-delay: 10s; }
        .particle:nth-child(4) { width: 80px; height: 80px; right: 15%; bottom: 10%; animation-duration: 32s; animation-delay: 15s; }
        .particle:nth-child(5) { width: 50px; height: 50px; left: 50%; top: 5%; animation-duration: 26s; animation-delay: 8s; }
        .particle:nth-child(6) { width: 70px; height: 70px; right: 30%; top: 60%; animation-duration: 29s; animation-delay: 12s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.1; }
            50% { transform: translate(30px, 30px) scale(1.1); opacity: 0.2; }
        }

        /* Grid overlay - smaller on mobile */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 1;
            pointer-events: none;
        }

        /* Disable glow lines on mobile for performance */
        @media (max-width: 768px) {
            .glow-line {
                display: none;
            }
        }

        /* Glow lines */
        .glow-line {
            position: absolute;
            background: linear-gradient(90deg, transparent, rgba(255, 0, 0, 0.4), transparent);
            z-index: 1;
            pointer-events: none;
        }

        .glow-line-1 {
            width: 200%;
            height: 1px;
            top: 30%;
            left: -50%;
            animation: moveRight 15s linear infinite;
        }

        .glow-line-2 {
            width: 200%;
            height: 1px;
            top: 70%;
            left: -50%;
            animation: moveLeft 20s linear infinite;
        }

        @keyframes moveRight {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @keyframes moveLeft {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        .login-container {
            background: rgba(20, 20, 20, 0.8);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 0, 0, 0.6);
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            padding: 40px 24px;
            margin: 0 auto;
            z-index: 10;
            position: relative;
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.3), 0 8px 32px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1), inset 0 0 20px rgba(255, 0, 0, 0.05);
            animation: borderGlow 3s ease-in-out infinite;
        }

        /* Disable heavy animations on mobile */
        @media (max-width: 768px) {
            .login-container {
                animation: none;
                backdrop-filter: blur(5px);
                padding: 32px 20px;
            }
        }

        @keyframes borderGlow {
            0%, 100% { 
                box-shadow: 0 0 20px rgba(255, 0, 0, 0.2), 0 8px 32px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1), inset 0 0 20px rgba(255, 0, 0, 0.05);
                border-color: rgba(255, 0, 0, 0.4);
            }
            50% { 
                box-shadow: 0 0 40px rgba(255, 0, 0, 0.5), 0 8px 32px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1), inset 0 0 30px rgba(255, 0, 0, 0.1);
                border-color: rgba(255, 0, 0, 0.8);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .login-header h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            background: linear-gradient(90deg, #ffffff, rgba(255, 0, 0, 0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-header p {
            color: rgba(255, 100, 100, 0.7);
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(255, 0, 0, 0.5), transparent);
            transition: width 0.3s ease;
        }

        .form-group:last-of-type::after {
            display: none;
        }

        label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.8), rgba(255, 100, 100, 0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            font-size: 14px;
            color: #ffffff;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            background: rgba(255, 0, 0, 0.05);
            border-color: rgba(255, 0, 0, 0.6);
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.2), inset 0 0 10px rgba(255, 0, 0, 0.05);
        }

        .error-message {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 6px;
            letter-spacing: 0.5px;
            text-shadow: 0 0 10px rgba(255, 107, 107, 0.3);
        }

        .errors {
            background-color: rgba(255, 107, 107, 0.08);
            border: 1px solid rgba(255, 107, 107, 0.4);
            color: #ff9999;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 13px;
            backdrop-filter: blur(5px);
        }

        .errors ul {
            list-style: none;
        }

        .errors li {
            margin-bottom: 6px;
        }

        .errors li:last-child {
            margin-bottom: 0;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, rgba(255, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.3) 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 0, 0, 0.5);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 0, 0, 0.4), transparent);
            transition: left 0.5s;
        }

        button:hover {
            background: linear-gradient(135deg, rgba(255, 0, 0, 0.35) 0%, rgba(255, 0, 0, 0.1) 100%);
            border-color: rgba(255, 0, 0, 0.8);
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 0 20px rgba(255, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        button:hover::before {
            left: 100%;
        }

        button:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
        }

        .login-footer p {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 10px;
        }

        .login-footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .login-footer a:hover {
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.8);
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: min(380px, calc(100vw - 40px));
        }

        .toast {
            background: rgba(20, 20, 20, 0.95);
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

        .toast-success {
            border-left-color: #4caf50;
        }

        .toast-progress {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4caf50, #81c784);
            width: 100%;
            animation: progressBar 5s linear forwards;
        }

        @keyframes progressBar {
            from { width: 100%; }
            to { width: 0%; }
        }

        .corner-decoration {
            position: fixed;
            z-index: 2;
        }

        /* Hide corner decorations on mobile */
        @media (max-width: 768px) {
            .corner-decoration {
                display: none;
            }
        }

        .corner-tl {
            top: 0;
            left: 0;
            width: 100px;
            height: 100px;
            border-top: 2px solid rgba(255, 0, 0, 0.3);
            border-left: 2px solid rgba(255, 0, 0, 0.3);
            border-radius: 0 0 50px 0;
        }

        .corner-tr {
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            border-top: 2px solid rgba(255, 0, 0, 0.3);
            border-right: 2px solid rgba(255, 0, 0, 0.3);
            border-radius: 0 0 0 50px;
        }

        .corner-bl {
            bottom: 0;
            left: 0;
            width: 100px;
            height: 100px;
            border-bottom: 2px solid rgba(255, 0, 0, 0.3);
            border-left: 2px solid rgba(255, 0, 0, 0.3);
            border-radius: 50px 0 0 0;
        }

        .corner-br {
            bottom: 0;
            right: 0;
            width: 100px;
            height: 100px;
            border-bottom: 2px solid rgba(255, 0, 0, 0.3);
            border-right: 2px solid rgba(255, 0, 0, 0.3);
            border-radius: 0 50px 0 0;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="glow-line glow-line-1"></div>
        <div class="glow-line glow-line-2"></div>
        <div class="grid-overlay"></div>
    </div>

    <!-- Corner Decorations -->
    <div class="corner-decoration corner-tl"></div>
    <div class="corner-decoration corner-tr"></div>
    <div class="corner-decoration corner-bl"></div>
    <div class="corner-decoration corner-br"></div>

    <div class="login-container">
        <div class="login-header">
            @if($settings && $settings->isp_logo)
                <img src="{{ asset('storage/' . $settings->isp_logo) }}" alt="ISP Logo" style="max-width:100%;max-height:90px;object-fit:contain;">
            @else
                <h1>ISP Billing</h1>
            @endif
            <p>Secure Access Portal</p>
        </div>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="form-group">
                <label for="login">Email or Username</label>
                <input 
                    type="text" 
                    id="login" 
                    name="login" 
                    placeholder="your@email.com or username"
                    value="{{ old('login') }}"
                    required 
                    autofocus
                >
                @error('login')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">LOGIN</button>
        </form>

        <div class="login-footer">
            <p>Don't have an account? <a href="{{ route('register.show') }}">Register here</a></p>
            <p style="margin-top: 8px;"><a href="{{ route('password.forgot') }}">Forgot your password?</a></p>
        </div>
    </div>

    <div class="toast-container">
        @if (session('error'))
            <div class="toast" id="toast-error" style="border-left-color:#f44336; border-color:rgba(244,67,54,0.8);">
                {{ session('error') }}
                <div class="toast-progress" style="background:linear-gradient(90deg,#f44336,#e57373);"></div>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast toast-warning" id="toast-warning">
                {{ session('warning') }}
                <div class="toast-progress"></div>
            </div>
        @endif

        @if (session('success'))
            <div class="toast toast-success" id="toast-success">
                {{ session('success') }}
                <div class="toast-progress"></div>
            </div>
        @endif
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', function () {
            var toastSuccess = document.getElementById('toast-success');
            var toastWarning = document.getElementById('toast-warning');

            function hideToast(toast) {
                if (!toast) return;
                toast.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(function () {
                    toast.remove();
                }, 700);
            }

            if (toastSuccess) {
                setTimeout(function () { hideToast(toastSuccess); }, 5000);
            }

            if (toastWarning) {
                setTimeout(function () { hideToast(toastWarning); }, 5000);
            }

            var toastError = document.getElementById('toast-error');
            if (toastError) {
                setTimeout(function () { hideToast(toastError); }, 5000);
            }
        });
    </script>
</body>
</html>
