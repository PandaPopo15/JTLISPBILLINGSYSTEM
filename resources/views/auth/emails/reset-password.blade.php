<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - JTLFWISP</title>
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

        .reset-button {
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

        .reset-button:hover {
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

        .warning-box {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
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

            <p>We received a request to reset your password. Click the button below to create a new password:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
            </div>

            <div class="warning-box">
                <strong>⚠ Security Notice:</strong> This link will expire in 1 hour. If you did not request a password reset, please ignore this email and your password will remain unchanged.
            </div>

            <p>Or copy and paste this link in your browser:</p>
            <p style="word-break: break-all; background: #f5f5f5; padding: 10px; border-radius: 3px; font-size: 12px;">
                {{ $resetUrl }}
            </p>

            <p><strong>For your security:</strong></p>
            <ul style="color: #333; font-size: 13px;">
                <li>Never share this link with anyone</li>
                <li>Only click this link if you requested a password reset</li>
                <li>This link will expire after 1 hour</li>
            </ul>

            <p>Best regards,<br><strong>JTLFWISP Team</strong></p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JTLFWISP. All rights reserved.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
