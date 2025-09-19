<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .verification-code {
            background-color: #099F9A;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            letter-spacing: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Email Verification</h2>
    </div>
    
    <p>Hello {{ $userName }},</p>
    
    <p>Thank you for registering with us! To complete your registration, please use the verification code below:</p>
    
    <div class="verification-code">
        {{ $verificationCode }}
    </div>
    
    <p>This code will expire in 10 minutes. If you didn't request this verification, please ignore this email.</p>
    
    <p>Best regards,<br>
    {{ config('app.name') }} Team</p>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html> 