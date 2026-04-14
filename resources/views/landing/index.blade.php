<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->isp_name }} - Internet Plans</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: #f5f5f5;
            background: linear-gradient(180deg, #090909 0%, #120a0a 40%, #15090a 100%);
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 20%, rgba(255, 82, 82, 0.12), transparent 24%),
                        radial-gradient(circle at 80% 80%, rgba(255, 82, 82, 0.1), transparent 28%);
            pointer-events: none;
            z-index: -1;
        }
        .page-shell {
            max-width: 1400px;
            margin: 0 auto;
            padding: 28px 24px 60px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 32px;
        }
        .brand-block {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand-logo {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: {{ $settings->theme_color }};
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 18px;
            color: #ffffff;
        }
        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 16px;
            background: #111111;
        }
        .brand-title {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .brand-title h1 {
            font-size: 20px;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .brand-title p {
            margin: 0;
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-actions a {
            color: #ffffff;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 999px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .nav-actions a:hover {
            background: rgba(255,255,255,0.08);
        }
        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 32px;
            align-items: center;
            margin-bottom: 40px;
        }
        .hero-copy {
            max-width: 680px;
        }
        .hero-copy h2 {
            font-size: clamp(3rem, 4vw, 4.2rem);
            margin: 0 0 20px;
            line-height: 1.05;
            letter-spacing: -1px;
        }
        .hero-copy p {
            color: rgba(255,255,255,0.75);
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 30px;
            max-width: 640px;
        }
        .hero-copy .cta-group {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }
        .cta-button,
        .secondary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 22px;
            border-radius: 999px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .cta-button {
            background: {{ $settings->theme_color }};
            color: #fff;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }
        .secondary-button {
            background: rgba(255,255,255,0.06);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.12);
        }
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .plan-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 28px;
            padding: 32px;
            min-height: 360px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }
        .plan-card h3 {
            margin: 0 0 12px;
            font-size: 22px;
        }
        .plan-price {
            font-size: 3rem;
            margin: 0 0 16px;
            color: {{ $settings->theme_color }};
        }
        .plan-description {
            color: rgba(255,255,255,0.74);
            margin-bottom: 20px;
        }
        .plan-list {
            list-style: none;
            padding: 0;
            margin: 0 0 26px;
            display: grid;
            gap: 10px;
        }
        .plan-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.82);
        }
        .plan-list li::before {
            content: '✓';
            color: {{ $settings->theme_color }};
            font-weight: bold;
        }
        .plan-card a {
            display: inline-flex;
            padding: 12px 18px;
            background: {{ $settings->theme_color }};
            color: #fff;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 0.3s ease, filter 0.3s ease;
            justify-content: center;
        }
        .plan-card a:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }
        .info-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 24px;
            color: rgba(255,255,255,0.8);
        }
        .info-card strong {
            display: block;
            margin-bottom: 10px;
            color: #fff;
        }
@media (max-width: 1100px) {
            .hero { 
                grid-template-columns: 1fr; 
                gap: 24px;
            }
            .page-shell {
                padding: 20px 16px 40px;
            }
            .topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 20px;
                text-align: center;
            }
            .topbar .brand-block {
                justify-content: center;
            }
            .nav-actions {
                justify-content: center;
            }
            .plans-grid { 
                grid-template-columns: 1fr; 
                gap: 20px;
            }
            .plan-card {
                padding: 28px 24px;
            }
            .info-grid { 
                grid-template-columns: 1fr; 
                gap: 16px;
            }
            .info-card {
                padding: 20px;
            }
            .cta-button, .secondary-button,
            .plan-card a {
                min-height: 48px;
                min-width: 48px;
                padding: 16px 24px;
            }
        }

        @media (max-width: 480px) {
            .page-shell {
                padding: 16px 12px 32px;
            }
            .hero-copy h2 {
                font-size: clamp(2rem, 8vw, 3rem);
            }
            .plan-price {
                font-size: 2.2rem;
            }
            .brand-logo {
                width: 44px;
                height: 44px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="topbar">
            <div class="brand-block">
                <div class="brand-logo">
                    @if($settings->logo_path)
                        <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="{{ $settings->isp_name }} logo">
                    @else
                        {{ strtoupper(substr($settings->isp_name, 0, 3)) }}
                    @endif
                </div>
                <div class="brand-title">
                    <h1>{{ $settings->isp_name }}</h1>
                    <p>Package selection made easy.</p>
                </div>
            </div>

            <div class="nav-actions">
                <a href="{{ route('login.show') }}">Login</a>
                <a href="{{ route('register.show') }}">Register</a>
            </div>
        </div>

        <section class="hero">
            <div class="hero-copy">
                <h2>{{ $settings->headline }}</h2>
                <p>{{ $settings->subheadline }}</p>
                <div class="cta-group">
                    <a href="{{ route('register.show') }}" class="cta-button">Register Now</a>
                    <a href="{{ route('login.show') }}" class="secondary-button">Already a Customer</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="plan-card" style="border-color: rgba(255,255,255,0.12); background: rgba(255,255,255,0.02);">
                    <h3>Verified Process</h3>
                    <p class="plan-description">Register your email, verify it, and wait for admin approval before installation.</p>
                    <div class="plan-list">
                        <li>Secure sign-up</li>
                        <li>Admin review</li>
                        <li>Installation scheduling</li>
                    </div>
                    <a href="{{ route('register.show') }}">Get Started</a>
                </div>
            </div>
        </section>

        <div class="plans-grid">
            @foreach($settings->plans as $plan)
                <div class="plan-card">
                    <div>
                        <h3>{{ $plan['name'] }}</h3>
                        <div class="plan-price">₱{{ $plan['price'] }}</div>
                        <p class="plan-description">{{ $plan['description'] }}</p>
                        <ul class="plan-list">
                            @foreach($plan['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <a href="{{ route('register.show', ['plan' => $plan['name']]) }}">Select Plan</a>
                </div>
            @endforeach
        </div>

        <div class="info-grid">
            <div class="info-card">
                <strong>Why Choose Us</strong>
                Affordable, reliable service with full support and transparent billing.
            </div>
            <div class="info-card">
                <strong>Approval Workflow</strong>
                Customers register with email, verify, and wait for admin review before installation.
            </div>
            <div class="info-card">
                <strong>Flexible Plans</strong>
                Pick the best internet package for your home or business and upgrade later.
            </div>
        </div>
    </div>
</body>
</html>
