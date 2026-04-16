<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; margin: 0; }
        .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #553e96; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; padding: 20px; background: #f9fafb; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%); color: white; text-decoration: none; border-radius: 6px; margin: 15px 0; }
        .success-icon { font-size: 48px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✓</div>
            <h1>Application Submitted Successfully!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $application->educator_first_name }} {{ $application->educator_last_name }},</p>
            
            <p>Thank you for submitting your dayhome application to SPICE'd Dayhome Agency. We have successfully received your application and our team will review it shortly.</p>
            
            <div class="info-box">
                <h3 style="margin-top: 0; color: #553e96;">Application Details</h3>
                <p><strong>Application Number:</strong> {{ $application->application_number ?? 'Pending' }}</p>
                <p><strong>Submitted Date:</strong> {{ $application->submitted_at ? $application->submitted_at->format('F j, Y \a\t g:i A') : 'N/A' }}</p>
                <p><strong>Status:</strong> <span style="color: #7c3aed; font-weight: bold;">Submitted</span></p>
            </div>
            
            <h3 style="color: #553e96;">What Happens Next?</h3>
            <ol style="line-height: 2;">
                <li><strong>Consultant Assignment:</strong> A dedicated consultant will be assigned to your application within 2-3 business days.</li>
                <li><strong>Initial Contact:</strong> Your consultant will contact you to discuss the next steps and schedule a meet & greet.</li>
                <li><strong>Home Inspection:</strong> We'll schedule an inspection to ensure your home meets safety standards for childcare.</li>
                <li><strong>Account Creation:</strong> After inspection, you'll create your online account to continue with document submission.</li>
            </ol>
            
            <p><strong>Important:</strong> Please keep this email for your records. You will receive further communications at <strong>{{ $application->email }}</strong>.</p>
            
            @if($application->consultant)
            <div class="info-box" style="background: #e3d4fc; border-left-color: #7c3aed;">
                <p><strong>Your Assigned Consultant:</strong> {{ $application->consultant->user->name ?? 'TBD' }}</p>
                <p>Your consultant will contact you soon to discuss your application and guide you through the next steps.</p>
            </div>
            @endif
            
            <p>If you have any questions or need assistance, please don't hesitate to contact us at <a href="mailto:executive@spicedchildcare.com" style="color: #553e96;">executive@spicedchildcare.com</a>.</p>
            
            <p>We look forward to working with you!</p>
            
            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>The SPICE'd Dayhome Team</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} SPICE'd Dayhome Agency. All rights reserved.</p>
            <p>This is an automated confirmation email. Please do not reply to this email.</p>
            <p>If you have questions, contact us at <a href="mailto:executive@spicedchildcare.com" style="color: #553e96;">executive@spicedchildcare.com</a></p>
        </div>
    </div>
</body>
</html>

