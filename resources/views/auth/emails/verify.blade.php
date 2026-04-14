<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - ISP Billing</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #000000, #1a1a1a);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 2px;
        }

        .content {
            padding: 40px 30px;
        }

        .content p {
            color: #333;
            line-height: 1.6;
            margin: 15px 0;
        }

        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #ff0000, #cc0000);
            color: white;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .verify-button:hover {
            background: linear-gradient(135deg, #cc0000, #990000);
        }

        .footer {
            background: #f9f9f9;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #eee;
        }

        .info-box {
            background: #f0f0f0;
            padding: 15px;
            border-left: 4px solid #ff0000;
            margin: 20px 0;
            border-radius: 3px;
            font-size: 13px;
        }

        strong {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>JTLFWISP</h1>
        </div>

        <div class="content">
            <p>Hello <strong>{{ $user->first_name }}</strong>,</p>

            <p>Thank you for registering with JTLFWISP! To complete your registration and access your account, please verify your email address by clicking the button below:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="verify-button">Verify Email</a>
            </div>

            <div class="info-box">
                <strong>Important:</strong> This verification link will expire in 24 hours. If you did not create this account, please ignore this email.
            </div>

            <p>Or copy and paste this link in your browser:</p>
            <p style="word-break: break-all; background: #f5f5f5; padding: 10px; border-radius: 3px; font-size: 12px;">
                {{ $verificationUrl }}
            </p>

            <p>Best regards,<br><strong>ISP Billing Team</strong></p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ISP Billing. All rights reserved.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
