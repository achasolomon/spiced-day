<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inspection Report - {{ $inspection->inspection_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #7c3aed;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px;
            background: #f3f4f6;
            width: 30%;
        }
        .info-value {
            display: table-cell;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #7c3aed;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .checklist-item {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            display: table;
            width: 100%;
        }
        .checklist-label {
            display: table-cell;
            width: 70%;
        }
        .checklist-status {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }
        .status-pass {
            color: #10b981;
        }
        .status-fail {
            color: #ef4444;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .badge-pass {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-fail {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-conditional {
            background: #fef3c7;
            color: #92400e;
        }
        .notes-box {
            background: #f9fafb;
            padding: 15px;
            border-left: 4px solid #7c3aed;
            margin-top: 10px;<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inspection Report - {{ $inspection->inspection_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #7c3aed;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header .subtitle {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }
        .header .report-number {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        
        /* Overall Result Banner */
        .result-banner {
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            color: white;
        }
        .result-banner.pass { background: linear-gradient(135deg, #10b981, #059669); }
        .result-banner.fail { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .result-banner.conditional { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .result-grid {
            display: table;
            width: 100%;
        }
        .result-item {
            display: table-cell;
            padding: 10px;
            text-align: center;
        }
        .result-label {
            font-size: 10px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .result-value {
            font-size: 24px;
            font-weight: bold;
        }
        
        /* Info Grid */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 10px;
            background: #f3f4f6;
            width: 35%;
            border: 1px solid #e5e7eb;
        }
        .info-value {
            display: table-cell;
            padding: 10px;
            border: 1px solid #e5e7eb;
        }
        
        /* Section Styling */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #7c3aed;
            color: white;
            padding: 12px 15px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .category-title {
            background: #e9d5ff;
            color: #6b21a8;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            border-left: 4px solid #7c3aed;
        }
        
        /* Checklist Items */
        .checklist-item {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }
        .checklist-item:last-child {
            border-bottom: none;
        }
        .item-icon {
            display: table-cell;
            width: 30px;
            vertical-align: top;
            padding-right: 10px;
        }
        .item-content {
            display: table-cell;
            vertical-align: top;
        }
        .item-status {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            width: 60px;
        }
        .item-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .item-note {
            font-size: 10px;
            color: #666;
            font-style: italic;
            margin-top: 3px;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
        }
        .badge-pass {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-fail {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-na {
            background: #e5e7eb;
            color: #4b5563;
        }
        .badge-conditional {
            background: #fef3c7;
            color: #92400e;
        }
        
        /* Icons */
        .icon-pass {
            width: 20px;
            height: 20px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            position: relative;
        }
        .icon-pass:after {
            content: '✓';
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14px;
        }
        .icon-fail {
            width: 20px;
            height: 20px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-block;
            position: relative;
        }
        .icon-fail:after {
            content: '✗';
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14px;
        }
        
        /* Summary Box */
        .summary-box {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .summary-item:last-child {
            border-bottom: none;
            padding-top: 12px;
            font-weight: bold;
        }
        
        /* Notes Box */
        .notes-box {
            background: #fef3c7;
            padding: 15px;
            border-left: 4px solid #f59e0b;
            margin: 10px 0;
            page-break-inside: avoid;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #92400e;
        }
        
        /* Failed Items Alert */
        .alert-box {
            background: #fee2e2;
            border: 2px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            page-break-inside: avoid;
        }
        .alert-title {
            font-weight: bold;
            color: #991b1b;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .alert-box ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        .alert-box li {
            margin: 8px 0;
            color: #7f1d1d;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        /* Page Break Control */
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SPICE'd Dayhome Agency</h1>
        <div class="subtitle">Home Inspection Report</div>
        <div class="report-number">{{ $inspection->inspection_number }}</div>
    </div>

    <!-- Overall Result Banner -->
    <div class="result-banner {{ $inspection->overall_result === 'pass' ? 'pass' : '' }}
                                {{ $inspection->overall_result === 'fail' ? 'fail' : '' }}
                                {{ $inspection->overall_result === 'conditional_pass' ? 'conditional' : '' }}">
        <div class="result-grid">
            <div class="result-item">
                <div class="result-label">Overall Result</div>
                <div class="result-value">{{ strtoupper(str_replace('_', ' ', $inspection->overall_result)) }}</div>
            </div>
            <div class="result-item">
                <div class="result-label">Overall Score</div>
                <div class="result-value">{{ number_format($inspection->overall_score, 1) }}%</div>
            </div>
            <div class="result-item">
                <div class="result-label">Items Passed</div>
                <div class="result-value">{{ $inspection->items_passed }} / {{ $inspection->items_checked }}</div>
            </div>
            <div class="result-item">
                <div class="result-label">Items Failed</div>
                <div class="result-value">{{ $inspection->items_failed }}</div>
            </div>
        </div>
    </div>

    <!-- Basic Information -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Inspection Type:</div>
            <div class="info-value">{{ ucwords(str_replace('_', ' ', $inspection->type)) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date Conducted:</div>
            <div class="info-value">{{ $inspection->conducted_at->format('F j, Y \a\t g:i A') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Duration:</div>
            <div class="info-value">{{ $inspection->duration ?? 'N/A' }} minutes</div>
        </div>
        <div class="info-row">
            <div class="info-label">Consultant:</div>
            <div class="info-value">{{ $inspection->consultant->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Applicant:</div>
            <div class="info-value">{{ $inspection->application->educator_first_name }} {{ $inspection->application->educator_last_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Application Number:</div>
            <div class="info-value">{{ $inspection->application->application_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">{{ $inspection->is_final ? 'Finalized' : 'Pending Finalization' }}</div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-box">
        <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">Inspection Summary</div>
        <div class="summary-item">
            <span>Total Items Checked:</span>
            <span><strong>{{ $inspection->items_checked }}</strong></span>
        </div>
        <div class="summary-item">
            <span>Items Passed:</span>
            <span style="color: #10b981;"><strong>{{ $inspection->items_passed }}</strong></span>
        </div>
        <div class="summary-item">
            <span>Items Failed:</span>
            <span style="color: #ef4444;"><strong>{{ $inspection->items_failed }}</strong></span>
        </div>
        <div class="summary-item">
            <span>Not Applicable:</span>
            <span style="color: #6b7280;"><strong>{{ $inspection->items_not_applicable }}</strong></span>
        </div>
        <div class="summary-item">
            <span>Pass Rate:</span>
            <span><strong>{{ $inspection->pass_rate }}%</strong></span>
        </div>
    </div>

    @php
        $checklistResults = is_array($inspection->checklist_results) ? $inspection->checklist_results : json_decode($inspection->checklist_results, true);
        $groupedResults = [];
        if ($checklistResults) {
            foreach($checklistResults as $itemId => $result) {
                $category = $result['category'] ?? 'General';
                if (!isset($groupedResults[$category])) {
                    $groupedResults[$category] = [];
                }
                $groupedResults[$category][$itemId] = $result;
            }
        }
    @endphp

    <!-- Inspection Checklist Results -->
    @if($checklistResults)
    <div class="section">
        <div class="section-title">Detailed Inspection Checklist Results</div>
        
        @foreach($groupedResults as $category => $items)
            <div class="category-title">
                {{ ucwords(str_replace('_', ' ', $category)) }} ({{ count($items) }} items)
            </div>
            
            @foreach($items as $itemId => $result)
                <div class="checklist-item">
                    <div class="item-icon">
                        @if($result['status'] === 'pass')
                            <span class="icon-pass"></span>
                        @elseif($result['status'] === 'fail')
                            <span class="icon-fail"></span>
                        @else
                            <span style="color: #9ca3af;">N/A</span>
                        @endif
                    </div>
                    <div class="item-content">
                        <div class="item-title">{{ $result['title'] ?? "Item $itemId" }}</div>
                        @if(isset($result['notes']) && $result['notes'])
                            <div class="item-note"><strong>Note:</strong> {{ $result['notes'] }}</div>
                        @endif
                    </div>
                    <div class="item-status">
                        <span class="status-badge 
                            {{ $result['status'] === 'pass' ? 'badge-pass' : '' }}
                            {{ $result['status'] === 'fail' ? 'badge-fail' : '' }}
                            {{ $result['status'] === 'n/a' ? 'badge-na' : '' }}">
                            {{ strtoupper($result['status']) }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
    @endif

    <!-- Failed Items Requiring Attention -->
    @if($inspection->items_failed > 0 && $inspection->failed_items)
        <div class="alert-box">
            <div class="alert-title">⚠ Items Requiring Immediate Attention</div>
            <p style="margin: 5px 0; color: #7f1d1d;">The following items must be addressed before the follow-up inspection:</p>
            <ul>
                @php
                    $failedItems = is_array($inspection->failed_items) ? $inspection->failed_items : json_decode($inspection->failed_items, true);
                @endphp
                @foreach($failedItems as $itemId)
                    @if(isset($checklistResults[$itemId]))
                        <li>
                            <strong>{{ $checklistResults[$itemId]['title'] ?? "Item $itemId" }}</strong>
                            @if(isset($checklistResults[$itemId]['notes']) && $checklistResults[$itemId]['notes'])
                                <br><span style="font-size: 10px;">{{ $checklistResults[$itemId]['notes'] }}</span>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Consultant Observations & Recommendations -->
    @if($inspection->observations || $inspection->recommendations_text)
    <div class="section">
        <div class="section-title">Consultant Review</div>
        
        @if($inspection->observations)
            <div class="notes-box">
                <div class="notes-title">General Observations</div>
                <div>{{ $inspection->observations }}</div>
            </div>
        @endif

        @if($inspection->recommendations_text)
            <div class="notes-box">
                <div class="notes-title">Recommendations for Improvement</div>
                <div>{{ $inspection->recommendations_text }}</div>
            </div>
        @endif
    </div>
    @endif

    <!-- Follow-up Information -->
    @if($inspection->requires_reinspection)
    <div class="notes-box" style="background: #fef3c7; border-color: #f59e0b;">
        <div class="notes-title" style="color: #92400e;">Follow-up Inspection Required</div>
        <p style="margin: 5px 0;">A follow-up inspection is required to verify corrections have been made.</p>
        @if($inspection->follow_up_required_by)
            <p style="margin: 5px 0;"><strong>Required by:</strong> {{ $inspection->follow_up_required_by->format('F j, Y') }}</p>
        @endif
        <p style="margin: 10px 0 5px 0; font-weight: bold;">Next Steps:</p>
        <ol style="margin: 5px 0 0 20px; padding: 0;">
            <li>Review all failed items listed above</li>
            <li>Make necessary corrections to your home and procedures</li>
            <li>Contact your consultant when ready for re-inspection</li>
            <li>Schedule follow-up inspection appointment</li>
        </ol>
    </div>
    @endif

    <!-- Consultant Contact Information -->
    <div class="section">
        <div class="section-title">Consultant Contact Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $inspection->consultant->name }}</div>
            </div>
            @if($inspection->consultant->email)
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $inspection->consultant->email }}</div>
            </div>
            @endif
            @if($inspection->consultant->phone)
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $inspection->consultant->phone }}</div>
            </div>
            @endif
        </div>
        <p style="margin-top: 15px; font-size: 10px; color: #666;">
            Please contact your consultant if you have any questions about this inspection report or need clarification on any items.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>This is an official inspection report from SPICE'd Dayhome Agency</strong></p>
        <p>Report Generated: {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>© {{ date('Y') }} SPICE'd Dayhome Agency. All rights reserved.</p>
    </div>
</body>
</html>
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SPICE'd Dayhome Agency</h1>
        <p>Inspection Report</p>
        <p><strong>{{ $inspection->inspection_number }}</strong></p>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Inspection Type:</div>
            <div class="info-value">{{ ucwords(str_replace('_', ' ', $inspection->type)) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date Conducted:</div>
            <div class="info-value">{{ $inspection->conducted_at->format('F j, Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Consultant:</div>
            <div class="info-value">{{ $inspection->consultant->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Applicant:</div>
            <div class="info-value">{{ $inspection->application->educator_first_name }} {{ $inspection->application->educator_last_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Application Number:</div>
            <div class="info-value">{{ $inspection->application->application_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Overall Result:</div>
            <div class="info-value">
                <span class="status-badge 
                    {{ $inspection->overall_result === 'pass' ? 'badge-pass' : '' }}
                    {{ $inspection->overall_result === 'fail' ? 'badge-fail' : '' }}
                    {{ $inspection->overall_result === 'conditional_pass' ? 'badge-conditional' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $inspection->overall_result)) }}
                </span>
            </div>
        </div>
        @if($inspection->overall_score)
        <div class="info-row">
            <div class="info-label">Overall Score:</div>
            <div class="info-value">{{ $inspection->overall_score }}%</div>
        </div>
        @endif
    </div>

    @if($inspection->inspection_data)
    <div class="section">
        <div class="section-title">Inspection Checklist Results</div>
        @foreach($inspection->inspection_data as $category => $items)
            <h3 style="margin-top: 20px; color: #7c3aed;">{{ ucwords(str_replace('_', ' ', $category)) }}</h3>
            @foreach($items as $item => $value)
                <div class="checklist-item">
                    <div class="checklist-label">{{ ucfirst(str_replace('_', ' ', $item)) }}</div>
                    <div class="checklist-status {{ $value === 'pass' || $value === 'yes' || $value === true ? 'status-pass' : 'status-fail' }}">
                        {{ is_bool($value) ? ($value ? 'PASS' : 'FAIL') : strtoupper($value) }}
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
    @endif

    @if($inspection->notes)
    <div class="section">
        <div class="section-title">Consultant Notes</div>
        <div class="notes-box">
            {{ $inspection->notes }}
        </div>
    </div>
    @endif

    @if($inspection->recommendations)
    <div class="section">
        <div class="section-title">Recommendations</div>
        <div class="notes-box">
            {{ $inspection->recommendations }}
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This is an official inspection report from SPICE'd Dayhome Agency</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>© {{ date('Y') }} SPICE'd Dayhome Agency. All rights reserved.</p>
    </div>
</body>
</html>