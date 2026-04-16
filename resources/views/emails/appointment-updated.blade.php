<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Updated</title>
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
            background-color: #7c0bb9;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #43049bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .details-box {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 14px;
        }
        .detail-value {
            color: #111827;
            margin-top: 4px;
        }
        .button {
            display: inline-block;
            background-color: #43049bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #610bd8ff;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Appointment Updated</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $appointment->applicant->name ?? $appointment->application->educator_first_name ?? 'there' }},</p>
        
        <div class="alert-box">
            <strong>⚠️ Important:</strong> Your appointment has been updated and requires your confirmation.
        </div>
        
        <p>Your <strong>{{ $appointment->title }}</strong> has been modified by {{ $appointment->consultant->name }}. Please review the updated details below and confirm your availability.</p>
        
        <div class="details-box">
            <h3 style="margin-top: 0; color: #ea580c;">Updated Appointment Details</h3>
            
            <div class="detail-row">
                <div class="detail-label">Appointment Type</div>
                <div class="detail-value">{{ ucwords(str_replace('_', ' ', $appointment->type)) }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Date & Time</div>
                <div class="detail-value">{{ \App\Helpers\TimezoneHelper::formatForUser($appointment->scheduled_at, $appointment->applicant ?? $appointment->consultant, 'l, F j, Y \a\t g:i A') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Duration</div>
                <div class="detail-value">{{ $appointment->duration }} minutes</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Location</div>
                <div class="detail-value">
                    {{ $appointment->location_address }}<br>
                    <small style="color: #6b7280;">({{ ucfirst($appointment->location_type) }})</small>
                </div>
            </div>
            
            @if($appointment->location_notes)
            <div class="detail-row">
                <div class="detail-label">Location Notes</div>
                <div class="detail-value">{{ $appointment->location_notes }}</div>
            </div>
            @endif
            
            @if($appointment->preparation_notes)
            <div class="detail-row">
                <div class="detail-label">Preparation Notes</div>
                <div class="detail-value">{{ $appointment->preparation_notes }}</div>
            </div>
            @endif
            
            @if($appointment->reschedule_reason)
            <div class="detail-row">
                <div class="detail-label">Reason for Change</div>
                <div class="detail-value">{{ $appointment->reschedule_reason }}</div>
            </div>
            @endif
            
            <div class="detail-row">
                <div class="detail-label">Consultant</div>
                <div class="detail-value">
                    {{ $appointment->consultant->name }}<br>
                    <small style="color: #6b7280;">{{ $appointment->consultant->email }}</small>
                </div>
            </div>
        </div>
        
        @if($appointment->confirmation_token)
        <div style="text-align: center; margin: 30px 0; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <h3 style="color: #374151; margin-bottom: 15px;">Action Required: Confirm Your Appointment</h3>
            <p style="color: #6b7280; margin-bottom: 20px;">Please confirm your availability by clicking the button below:</p>
            
            <a href="{{ route('appointments.confirm-by-email', ['appointment' => $appointment->id, 'token' => $appointment->confirmation_token]) }}" 
               style="background: #10b981; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; margin-bottom: 15px;">
                Confirm Appointment
            </a>
            
            <p style="color: #6b7280; font-size: 14px;">
                Or <a href="{{ route('appointments.confirm-by-email', ['appointment' => $appointment->id, 'token' => $appointment->confirmation_token, 'action' => 'reschedule']) }}" 
                      style="color: #7c0bb9; text-decoration: underline;">request to reschedule</a> if this time doesn't work for you.
            </p>
        </div>
        @else
        <div style="text-align: center; margin: 30px 0; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <p style="color: #6b7280;">Please log in to your account to confirm this appointment.</p>
        </div>
        @endif
                
        <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
            <strong>Note:</strong> This appointment will remain in "Scheduled" status until both you and the consultant confirm the updated details.
        </p>
        
        <p>If you have any questions or concerns about this update, please contact your consultant directly or reach out to our support team.</p>
        
        <p>Best regards,<br>
        <strong>Spiced Dayhome Agency Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Spiced Dayhome Agency. All rights reserved.</p>
    </div>
</body>
</html>