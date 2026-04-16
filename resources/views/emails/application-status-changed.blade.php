<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7c0bb9ff; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; margin: 20px 0; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
        .button { display: inline-block; padding: 12px 24px; background: #7c0bb9ff; color: white; text-decoration: none; border-radius: 6px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Application Status Update</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $isConsultant ? $application->consultant->name : $application->full_name }},</p>
            <p><strong>Application #{{ $application->application_number }}</strong></p>

                        
            <p>Your application status has been updated to:</p>
            <p style="text-align: center;">
                <span class="status-badge" style="background: {{ $newStatus->color() === 'white' ? '#f9fafb' : ($newStatus->color() === '7c0bb9ff' ? '#7c0bb9ff' : '#f59e0b') }}; color: white;">
                    {{ $newStatus->label() }}
                </span>
            </p>
            
            <p>{!! nl2br(e($statusMessage)) !!}</p>
            
            <div style="text-align: center;">
                <a href="{{ $isConsultant ? route('consultant.applications.show', $application->id) : route('applicant.applications.show', $application->id) }}" class="button">
                    View Application
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Daycare Application System. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>