<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #ff5252, #d50000);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-header .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.8;
        }
        .email-body h2 {
            color: #ff5252;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .email-body p {
            margin: 16px 0;
            font-size: 15px;
        }
        .info-box {
            background: #f9f9f9;
            border-left: 4px solid #ff5252;
            padding: 20px;
            margin: 24px 0;
            border-radius: 8px;
        }
        .info-box strong {
            color: #ff5252;
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #ff5252, #d50000);
            color: #ffffff;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 700;
            margin: 24px 0;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .email-footer {
            background: #f9f9f9;
            padding: 30px;
            text-align: center;
            color: #666666;
            font-size: 13px;
            border-top: 1px solid #eeeeee;
        }
        .email-footer p {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="icon">🎉</div>
            <h1>Congratulations!</h1>
        </div>
        
        <div class="email-body">
            <h2>Hello, {{ $user->first_name }}!</h2>
            
            <p>Great news! Your account has been <strong>accepted</strong> by our team.</p>
            
            <div class="info-box">
                <strong>What's Next?</strong>
                <p style="margin: 8px 0 0 0;">
                    Our team will contact you shortly to schedule your internet installation. 
                    You can now log in to your account and view your service details.
                </p>
            </div>
            
            <p>Your account details:</p>
            <ul style="margin: 16px 0; padding-left: 20px;">
                <li><strong>Name:</strong> {{ $user->full_name }}</li>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Plan:</strong> {{ $user->plan_interest ?? 'Not selected' }}</li>
            </ul>
            
            <center>
                <a href="{{ route('login.show') }}" class="cta-button">Login to Your Account</a>
            </center>
            
            <p>If you have any questions, feel free to contact our support team.</p>
            
            <p style="margin-top: 32px;">
                Best regards,<br>
                <strong>{{ config('app.name', 'ISP Billing') }} Team</strong>
            </p>
        </div>
        
        <div class="email-footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'ISP Billing') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
