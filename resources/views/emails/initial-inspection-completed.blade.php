<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7c0bb9; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .success-box { background: #d1fae5; border: 1px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; padding: 14px 28px; background: #7c0bb9; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Initial Inspection Completed!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $application->educator_first_name }},</p>
            
            <div class="success-box">
                <h2 style="margin-top: 0; color: #065f46;">Congratulations!</h2>
                <p><strong>Your initial home inspection has been completed successfully!</strong></p>
                <p>You've passed the first major step in having your dayhome approved by SPICE'd.</p>
            </div>

            <h3>Next Step: Create Your Profile</h3>
            <p>To continue with your application process, you need to create your official SPICE'd profile. This will give you access to:</p>
            
            <ul>
                <li>Document submission portal</li>
                <li>Application progress tracking</li>
                <li>Communication with your consultant</li>
                <li>License management system</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ $registrationUrl }}" class="button">
                    Create Your Profile Now
                </a>
            </div>

            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; color: #92400e;">
                    <strong>Important:</strong> This registration link is unique to your application and will expire in 7 days.
                    Please complete your profile setup to continue with the licensing process.
                </p>
            </div>

            <p><strong>Application Details:</strong></p>
            <ul>
                <li><strong>Application #:</strong> {{ $application->application_number }}</li>
                <li><strong>Applicant:</strong> {{ $application->educator_first_name }} {{ $application->educator_last_name }}</li>
                <li><strong>Consultant:</strong> {{ $application->consultant->name ?? 'To be assigned' }}</li>
            </ul>

            <p>If you have any questions, please contact your consultant or email us at <a href="mailto:executive@spicedchildcare.com">executive@spicedchildcare.com</a>.</p>

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
