<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7c0bb9; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; margin: 20px 0; }
        .button { display: inline-block; padding: 12px 24px; background: #7c0bb9; color: white; text-decoration: none; border-radius: 6px; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Meet & Greet Completed</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $application->educator_first_name }},</p>
            
            <p>Congratulations! Your Meet & Greet session has been completed successfully.</p>
            
            <p><strong>What's Next:</strong></p>
            <ul>
                <li>Your consultant will schedule your Initial Home Inspection</li>
                <li>Prepare your home for the inspection</li>
                <li>Gather any required documents</li>
            </ul>
            
            <p>Your consultant, {{ $application->consultant->name }}, will contact you soon to schedule the next steps.</p>
            
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