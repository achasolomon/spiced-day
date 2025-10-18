{{-- resources/views/emails/appointment-reminder-1hour.blade.php --}}

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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .urgent-badge {
            background: #ff4757;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .countdown-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .countdown-box h2 {
            margin: 0;
            font-size: 36px;
            font-weight: bold;
        }
        .countdown-box p {
            margin: 5px 0 0 0;
            font-size: 18px;
        }
        .appointment-card {
            background: #f8f9fa;
            border-left: 4px solid #f5576c;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
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
            background: #f5576c;
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
        }
        .button:hover {
            background: #d4455a;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="icon">⏰</div>
        <h1>Your Appointment is Starting Soon!</h1>
        <div class="urgent-badge">STARTING IN 1 HOUR</div>
    </div>

    <div class="content">
        <p>Hi {{ $recipient === 'applicant' ? $appointment->applicant->name : $appointment->consultant->name }},</p>

        <div class="countdown-box">
            <h2>🕐 1 HOUR</h2>
            <p>until your appointment</p>
        </div>

        <p style="font-size: 16px; font-weight: bold; color: #f5576c;">
            Your appointment is scheduled to begin at {{ $appointment->scheduled_at->format('g:i A') }}
        </p>

        <div class="appointment-card">
            <h2 style="margin-top: 0; color: #f5576c;">{{ $appointment->title }}</h2>
            
            <div class="detail-row">
                <div class="detail-label">🕐 Time:</div>
                <div class="detail-value">
                    <strong>{{ $appointment->scheduled_at->format('g:i A') }}</strong>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">⏱️ Duration:</div>
                <div class="detail-value">{{ $appointment->duration }} minutes</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">📍 Location:</div>
                <div class="detail-value">
                    <strong>{{ $appointment->location_address }}</strong>
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
                    <div class="detail-value">{{ $appointment->applicant->name }}</div>
                </div>
            @endif
        </div>

        @if($appointment->location_notes)
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <strong>📝 Location Notes:</strong>
                <p style="margin: 5px 0 0 0;">{{ $appointment->location_notes }}</p>
            </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            @if($recipient === 'applicant')
                <a href="{{ route('applicant.appointments.show', $appointment) }}" class="button">
                    📱 View Appointment Details
                </a>
            @else
                <a href="{{ route('consultant.appointments.show', $appointment) }}" class="button">
                    📱 View Appointment Details
                </a>
            @endif
        </div>

        <div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0; color: #1976d2;">
                <strong>💡 Pro Tip:</strong> Allow extra time for travel and parking. We look forward to seeing you soon!
            </p>
        </div>
    </div>

    <div class="footer">
        <p style="margin: 0;">
            <strong>Spiced Dayhome Agency</strong><br>
            Calgary, Alberta<br>
            <a href="mailto:info@spiceddayhome.ca">info@spiceddayhome.ca</a>
        </p>
    </div>
</body>
</html>