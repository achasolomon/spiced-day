<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7c0bb9; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .highlight { background: #d1fae5; border: 1px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; padding: 14px 28px; background: #7c0bb9; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .notice { background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0; color: #92400e; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your SPICE'd Account Has Been Created</h1>
        </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <div class="highlight">
                <h2 style="margin-top: 0; color: #065f46;">Welcome to SPICE'd 🎉</h2>
                <p><strong>Your account has been created successfully.</strong></p>
                <p>Your profile is now securely linked to your application.</p>
            </div>

            <p><strong>Account Information:</strong></p>
            <ul>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Application #:</strong> {{ $application->application_number }}</li>
            </ul>

            <p>You can now log in using the email above and the password you created during registration.</p>

            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">
                    Login to Your Account
                </a>
            </div>

            <div class="notice">
                <p>
                    <strong>Email Verification Required:</strong><br>
                    A verification code has been sent to this email address.
                    Please verify your email to complete your account setup and access all features.
                </p>
            </div>

            <p><strong>What you can do next:</strong></p>
            <ul>
                <li>Upload and manage required documents</li>
                <li>Track your application progress</li>
                <li>Communicate with your consultant</li>
                <li>Manage approvals and licensing</li>
            </ul>

            <p>If you did not initiate this registration, please contact us immediately at
                <a href="mailto:executive@spicedchildcare.com">executive@spicedchildcare.com</a>.
            </p>

            <p>Best regards,<br>
                <strong>The SPICE'd Dayhome Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} SPICE'd Dayhome Agency. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
