<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Application Assigned</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #7c0bb9;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f9fafb;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #7c0bb9;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #7c0bb9;
            font-size: 18px;
        }
        .info-row {
            margin: 12px 0;
            display: flex;
            flex-wrap: wrap;
        }
        .label {
            font-weight: bold;
            color: #666;
            min-width: 150px;
            margin-right: 10px;
        }
        .value {
            color: #333;
            flex: 1;
        }
        .highlight {
            background: #fef3c7;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .highlight strong {
            font-size: 20px;
            color: #92400e;
        }
        .next-steps {
            background: #dbeafe;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .next-steps h4 {
            margin-top: 0;
            color: #1e40af;
        }
        .next-steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin: 8px 0;
            color: #1e3a8a;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background: #7c0bb9;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            background: #6a0a9d;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
        }
        .note {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 4px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 20px;
            }
            .info-row {
                flex-direction: column;
            }
            .label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> New Application Assigned</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $consultant->name }}</strong>,</p>
            
            <p>A new daycare application has been assigned to you for review and processing.</p>
            
            <div class="highlight">
                <strong>Application #{{ $application->application_number }}</strong>
            </div>

            <div class="info-box">
                <h3>Applicant Information</h3>
                
                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value">{{ $application->educator_first_name }} {{ $application->educator_last_name }}</span>
                </div>
                
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $application->email }}</span>
                </div>
                
                <div class="info-row">
                    <span class="label">Phone:</span>
                    <span class="value">{{ $application->phone }}</span>
                </div>
                
                <div class="info-row">
                    <span class="label">Location:</span>
                    <span class="value">{{ $application->city }}, {{ $application->province }} {{ $application->postal_code }}</span>
                </div>
                
                <div class="info-row">
                    <span class="label">Childcare Level:</span>
                    <span class="value">{{ $application->childcare_level ?? 'Not specified' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="label">Desired Start Date:</span>
                    <span class="value">
                        @if($application->desired_start_date)
                            {{ \Carbon\Carbon::parse($application->desired_start_date)->format('F j, Y') }}
                        @else
                            Not specified
                        @endif
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="label">Submitted:</span>
                    <span class="value">
                        @if($application->submitted_at)
                            {{ $application->submitted_at->format('F j, Y g:i A') }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>

            <div class="next-steps">
                <h4> Next Steps:</h4>
                <ul>
                    <li>Review the application details thoroughly</li>
                    <li>Verify all required information is complete</li>
                    <li>Schedule an initial consultation with the applicant</li>
                    <li>Begin the inspection process as per guidelines</li>
                    <li>Update the application status accordingly</li>
                </ul>
            </div>
            
            <div class="button-container">
                <a href="{{ route('consultant.applications.show', $application->id) }}" class="button">
                    View Application Details
                </a>
            </div>

            <div class="note">
                <strong>Note:</strong> This application has been automatically assigned to you based on your service area and current workload capacity.
            </div>
        </div>
        
       <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Spiced Dayhome Agency. All rights reserved.</p>
    </div>
    </div>
</body>
</html>