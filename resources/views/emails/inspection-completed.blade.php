<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspection Report</title>
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
            background: #7c0bb9;
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #7c0bb9;
            border-top: none;
        }
        .result-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            margin: 20px 0;
            font-size: 18px;
        }
        .result-pass {
            background: #10b981;
            color: white;
        }
        .result-fail {
            background: #ef4444;
            color: white;
        }
        .result-conditional {
            background: #f59e0b;
            color: white;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .alert {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-danger {
            background: #fee2e2;
            border-left-color: #ef4444;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #7c0bb9;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
        }
        .btn:hover {
            background: #a22ee0ff;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .failed-items {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            margin: 15px 0;
        }
        .failed-items li {
            margin: 8px 0;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">SPICE'd Dayhome Agency</h1>
        <p style="margin: 10px 0 0 0;">Inspection Report</p>
    </div>

    <div class="content">
        <p>Dear {{ $inspection->application->educator_first_name }} {{ $inspection->application->educator_last_name }},</p>

        <p>Your <strong>{{ ucwords(str_replace('_', ' ', $inspection->type)) }}</strong> has been completed and the results are now available.</p>

        <!-- Result Badge -->
        <div style="text-align: center;">
            <div class="result-badge result-{{ $inspection->overall_result === 'pass' ? 'pass' : ($inspection->overall_result === 'fail' ? 'fail' : 'conditional') }}">
                @if($inspection->overall_result === 'pass')
                    PASSED
                @elseif($inspection->overall_result === 'fail')
                    FAILED
                @elseif($inspection->overall_result === 'conditional_pass')
                    CONDITIONAL PASS
                @else
                    INCOMPLETE
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="stats">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($inspection->overall_score, 1) }}%</div>
                <div class="stat-label">Overall Score</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $inspection->items_passed }}/{{ $inspection->items_checked }}</div>
                <div class="stat-label">Items Passed</div>
            </div>
            <div class="stat-box" style="color: #ef4444;">
                <div class="stat-value">{{ $inspection->items_failed }}</div>
                <div class="stat-label">Items Failed</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $inspection->items_not_applicable }}</div>
                <div class="stat-label">Not Applicable</div>
            </div>
        </div>

        <!-- Alert for Failed/Conditional -->
        @if($inspection->overall_result === 'fail' || $inspection->overall_result === 'conditional_pass')
        <div class="alert {{ $inspection->overall_result === 'fail' ? 'alert-danger' : '' }}">
            <strong>Action Required</strong>
            <p style="margin: 10px 0 0 0;">
                @if($inspection->overall_result === 'fail')
                    Your inspection did not pass. Please review the failed items and make necessary corrections before scheduling a follow-up inspection.
                @else
                    Your inspection passed conditionally. Some improvements are needed. Please address the items noted in your report.
                @endif
            </p>
            @if($inspection->follow_up_required_by)
            <p style="margin: 10px 0 0 0;">
                <strong>Follow-up required by:</strong> {{ $inspection->follow_up_required_by->format('F j, Y') }}
            </p>
            @endif
        </div>
        @endif

        <!-- Failed Items -->
        @if($inspection->items_failed > 0 && $inspection->failed_items)
        <div class="failed-items">
            <strong style="color: #991b1b;">Items Requiring Attention:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                @php
                    $failedItems = is_array($inspection->failed_items) ? $inspection->failed_items : json_decode($inspection->failed_items, true);
                    $checklistResults = is_array($inspection->checklist_results) ? $inspection->checklist_results : json_decode($inspection->checklist_results, true);
                @endphp
                @foreach($failedItems as $itemId)
                    @if(isset($checklistResults[$itemId]))
                        <li>{{ $checklistResults[$itemId]['title'] ?? "Item $itemId" }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Inspection Details -->
        <div style="background: white; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Inspection Number:</strong> {{ $inspection->inspection_number }}</p>
            <p style="margin: 5px 0;"><strong>Conducted On:</strong> {{ $inspection->conducted_at->format('F j, Y \a\t g:i A') }}</p>
            <p style="margin: 5px 0;"><strong>Consultant:</strong> {{ $inspection->consultant->name }}</p>
            <p style="margin: 5px 0;"><strong>Duration:</strong> {{ $inspection->duration ?? 'N/A' }} minutes</p>
        </div>

        @if($inspection->observations)
        <div style="background: white; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #9805bdff;">
            <strong>Inspector's Observations:</strong>
            <p style="margin: 10px 0 0 0; color: #4b5563;">{{ $inspection->observations }}</p>
        </div>
        @endif

        <!-- Call to Action -->
        <div style="text-align: center; margin: 30px 0;">
            @if($inspection->application->user)
            <a href="{{ route('applicant.inspections.show', $inspection) }}" class="btn">
                View Full Inspection Report
            </a>
            @else
            <p style="color: #6b7280; font-size: 14px;">
                Your full inspection report will be available in your portal once you complete your user registration. 
                Please contact your consultant if you need immediate access to the detailed report.
            </p>
            @endif
        </div>

        <!-- Next Steps -->
        @if($inspection->requires_reinspection)
        <div style="background: white; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>Next Steps:</strong>
            <ol style="margin: 10px 0; padding-left: 20px; color: #4b5563;">
                <li>Review the full inspection report in your portal</li>
                <li>Address all failed items listed above</li>
                <li>Contact your consultant when corrections are complete</li>
                <li>Schedule a follow-up inspection</li>
            </ol>
        </div>
        @endif

        <!-- Contact Info -->
        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>Need Help?</strong>
            <p style="margin: 10px 0 0 0;">Contact your consultant:</p>
            <p style="margin: 5px 0;"><strong>{{ $inspection->consultant->name }}</strong></p>
            @if($inspection->consultant->email)
            <p style="margin: 5px 0;">📧 <a href="mailto:{{ $inspection->consultant->email }}" style="color: #7c0bb9;">{{ $inspection->consultant->email }}</a></p>
            @endif
            @if($inspection->consultant->phone)
            <p style="margin: 5px 0;">📞 <a href="tel:{{ $inspection->consultant->phone }}" style="color: #7c0bb9;">{{ $inspection->consultant->phone }}</a></p>
            @endif
        </div>

        <p>Thank you for your cooperation with the inspection process.</p>

        <p style="margin-top: 20px;">
            Best regards,<br>
            <strong>SPICE'd Dayhome Agency Team</strong>
        </p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply directly to this email.</p>
        <p>&copy; {{ date('Y') }} SPICE'd Dayhome Agency. All rights reserved.</p>
    </div>
</body>
</html>