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
            <h1>Welcome to SPICE'd – Create Your Profile</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $application->educator_first_name }},</p>

            <div class="highlight">
                <h2 style="margin-top: 0; color: #065f46;">Your Application is Ready!</h2>
                <p><strong>Your legacy application has been successfully imported.</strong></p>
                <p>You can now create your SPICE'd profile to take full control of your application and account.</p>
            </div>

            <h3>Next Step: Create Your Official Profile</h3>
            <p>By creating your profile, you'll get access to:</p>
            <ul>
                <li>Submit and manage documents online</li>
                <li>Track your application progress</li>
                <li>Communicate directly with your consultant</li>
                <li>Manage your license and approvals</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ $registrationUrl }}" class="button">
                    Create Your Profile Now
                </a>
            </div>

            <div class="notice">
                <p><strong>Important:</strong> This registration link is unique to your application and will expire in 7 days. Please complete your profile setup promptly.</p>
            </div>

            <p><strong>Application Summary:</strong></p>
            <ul>
                <li><strong>Application #:</strong> {{ $application->application_number }}</li>
                <li><strong>Applicant:</strong> {{ $application->educator_first_name }} {{ $application->educator_last_name }}</li>
                <li><strong>Consultant:</strong> {{ $application->consultant->name ?? 'To be assigned' }}</li>
            </ul>

            <p>If you have any questions, contact your consultant or email us at <a href="mailto:executive@spicedchildcare.com">executive@spicedchildcare.com</a>.</p>

            <p>Best regards,<br>
            <strong>The SPICE'd Dayhome Team</strong></p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} SPICE'd Dayhome Agency. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
