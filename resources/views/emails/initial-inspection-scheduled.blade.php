<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7c0bb9; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .appointment-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border: 1px solid #e5e7eb; }
        .detail-row { padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: bold; color: #6b7280; font-size: 14px; }
        .detail-value { color: #111827; margin-top: 4px; }
        .button { display: inline-block; padding: 12px 24px; background: #7c0bb9; color: #ffff; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .confirmation-section { background: #f0f9ff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981; }
        .checklist { background: #ecfdf5; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Initial Inspection Scheduled!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $application->educator_first_name }},</p>
            
            <p>Great progress! Your Initial Home Inspection has been scheduled.</p>
            
            <div class="appointment-box">
                <h3 style="margin-top: 0; color: #7c0bb9;">Appointment Details</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Appointment Type</div>
                    <div class="detail-value">Initial Home Inspection</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Date & Time</div>
                    <div class="detail-value">{{ \App\Helpers\TimezoneHelper::formatForUser($appointment->scheduled_at, $appointment->applicant, 'l, F j, Y \a\t g:i A') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Duration</div>
                    <div class="detail-value">{{ $appointment->duration }} minutes</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Location</div>
                    <div class="detail-value">
                        {{ $appointment->location_address ?? 'Your home address' }}
                        <br><small style="color: #6b7280;">({{ ucfirst($appointment->location_type) }})</small>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Consultant</div>
                    <div class="detail-value">
                        {{ $appointment->consultant->name }}<br>
                        <small style="color: #6b7280;">{{ $appointment->consultant->email }}</small>
                    </div>
                </div>
            </div>

            <!-- Confirmation Section -->
            <div class="confirmation-section">
                <h4 style="margin-top: 0; color: #10b981;">Action Required: Confirm Your Appointment</h4>
                <p>Please confirm your availability for this inspection or request to reschedule if needed.</p>
                
                <div style="text-align: center; margin: 20px 0;">
                    @if($application->user_id)
                        <!-- Registered users: link to portal -->
                        <a href="{{ route('applicant.appointments.show', $appointment) }}" 
                           style="background: #7c0bb9; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; margin-bottom: 10px;">
                           View & Confirm Appointment
                        </a>
                    @else
                        <!-- Anonymous users: email confirmation link -->
                        <a href="{{ $appointment->getApplicantConfirmationUrl() }}" 
                           style="background: #7c0bb9; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; margin-bottom: 10px;">
                           Confirm Inspection
                        </a>
                        <br>
                        <a href="{{ $appointment->getRescheduleUrl() }}" 
                           style="color: #7c0bb9; text-decoration: underline; font-size: 14px;">
                           Need to reschedule?
                        </a>
                    @endif
                </div>
                
                @if(!$application->user_id)
                <p style="color: #6b7280; font-size: 14px; text-align: center;">
                    This confirmation link expires in 3 days.
                </p>
                @endif
            </div>

            <div class="checklist">
                <h4 style="margin-top: 0; color: #065f46;">Preparation Checklist</h4>
                <ul>
                    <li>Ensure your home is clean and organized</li>
                    <li>Remove any safety hazards</li>
                    <li>Have emergency contact information available</li>
                    <li>Prepare any questions you have for the consultant</li>
                    <li>Ensure smoke detectors and carbon monoxide detectors are working</li>
                    <li>Secure cleaning supplies and medications out of children's reach</li>
                </ul>
            </div>

            <p><strong>Application Reference:</strong> {{ $application->application_number }}</p>

            <p>If you have any questions about the inspection process, please contact your consultant directly or email us at <a href="mailto:executive@spicedchildcare.com">executive@spicedchildcare.com</a>.</p>

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