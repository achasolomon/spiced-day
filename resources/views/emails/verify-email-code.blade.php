<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #7C3AED;
            margin: 0;
        }
        .code-box {
            background-color: #ffffff;
            border: 2px solid #7C3AED;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #7C3AED;
            margin: 10px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Email Address</h1>
        </div>
        
        <p>Thank you for registering with Spiced Dayhome Agency!</p>
        
        <p>To complete your registration, please enter the verification code below:</p>
        
        <div class="code-box">
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Your Verification Code</p>
            <div class="code">{{ $token }}</div>
        </div>
        
        <p>This code will expire in 24 hours for security reasons.</p>
        
        <p>If you didn't create an account with us, please ignore this email.</p>
        
        <div class="footer">
            <p>© {{ date('Y') }} Spiced Dayhome Agency. All rights reserved.</p>
        </div>
    </div>
</body>
</html>