{{-- resources/views/emails/appointment-reminder-24hours.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .appointment-card {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .appointment-card h2 {
            margin-top: 0;
            color: #667eea;
            font-size: 20px;
        }
        .detail-row {
            display: flex;
            margin: 10px 0;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            width: 140px;
            color: #666;
        }
        .detail-value {
            flex: 1;
            color: #333;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #5568d3;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="icon">📅</div>
        <h1>Appointment Reminder</h1>
        <p style="margin: 10px 0 0 0; font-size: 16px;">Your appointment is tomorrow!</p>
    </div>

    <div class="content">
        <p>Hi {{ $recipient === 'applicant' ? $appointment->applicant->name : $appointment->consultant->name }},</p>

        <p>This is a friendly reminder about your upcoming appointment scheduled for <strong>tomorrow</strong>.</p>

        <div class="appointment-card">
            <h2>{{ $appointment->title }}</h2>
            
            <div class="detail-row">
                <div class="detail-label">📅 Date:</div>
                <div class="detail-value">{{ $appointment->scheduled_at->format('l, F j, Y') }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">🕐 Time:</div>
                <div class="detail-value">{{ $appointment->scheduled_at->format('g:i A') }} - {{ $appointment->ends_at->format('g:i A') }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">⏱️ Duration:</div>
                <div class="detail-value">{{ $appointment->duration }} minutes</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">📍 Location:</div>
                <div class="detail-value">
                    {{ $appointment->location_address }}<br>
                    <small style="color: #666;">{{ ucfirst($appointment->location_type) }}</small>
                </div>
            </div>

            @if($recipient === 'applicant')
                <div class="detail-row">
                    <div class="detail-label">👤 Consultant:</div>
                    <div class="detail-value">{{ $appointment->consultant->name }}</div>
                </div>
            @else
                <div class="detail-row">
                    <div class="detail-label">👤 Applicant:</div>
                    <div class="detail-value">
                        {{ $appointment->applicant->name }}<br>
                        <small style="color: #666;">Application #{{ $appointment->application->application_number }}</small>
                    </div>
                </div>
            @endif
        </div>

        @if($appointment->description)
            <div class="alert-box">
                <strong>📝 Notes:</strong>
                <p style="margin: 5px 0 0 0;">{{ $appointment->description }}</p>
            </div>
        @endif

        @if($recipient === 'applicant' && $appointment->preparation_notes)
            <div class="alert-box">
                <strong>✅ Please Prepare:</strong>
                <p style="margin: 5px 0 0 0;">{{ $appointment->preparation_notes }}</p>
            </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            @if($recipient === 'applicant')
                <a href="{{ route('applicant.appointments.show', $appointment) }}" class="button">
                    View Appointment Details
                </a>
            @else
                <a href="{{ route('consultant.appointments.show', $appointment) }}" class="button">
                    View Appointment Details
                </a>
            @endif
        </div>

        <p style="color: #666; font-size: 14px;">
            <strong>Need to reschedule?</strong><br>
            Please contact us as soon as possible if you need to make changes to this appointment.
        </p>
    </div>

    <div class="footer">
        <p style="margin: 0;">
            <strong>Spiced Dayhome Agency</strong><br>
            Calgary, Alberta<br>
            <a href="mailto:info@spiceddayhome.ca">info@spiceddayhome.ca</a>
        </p>
        <p style="margin: 10px 0 0 0; font-size: 12px; color: #999;">
            You're receiving this email because you have an appointment scheduled with us.
        </p>
    </div>
</body>
</html>