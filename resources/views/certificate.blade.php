<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Approval</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', serif;
            width: 297mm;
            height: 210mm;
            display: flex;
            justify-content: center;
            align-items: center;
            background: white;
        }
        
        .certificate {
            width: 280mm;
            height: 195mm;
            border: 3px solid #333;
            padding: 15mm;
            position: relative;
            background: white;
        }
        
        .border-inner {
            border: 1px solid #999;
            padding: 10mm;
            height: 100%;
            position: relative;
        }
        
        .ornament {
            position: absolute;
            width: 80px;
            height: 80px;
        }
        
        .ornament-tl {
            top: -3px;
            left: -3px;
        }
        
        .ornament-tr {
            top: -3px;
            right: -3px;
            transform: scaleX(-1);
        }
        
        .ornament-bl {
            bottom: -3px;
            left: -3px;
            transform: scaleY(-1);
        }
        
        .ornament-br {
            bottom: -3px;
            right: -3px;
            transform: scale(-1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .title {
            font-size: 56px;
            font-weight: normal;
            letter-spacing: 2px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .subtitle {
            font-size: 16px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #666;
            font-weight: normal;
        }
        
        .content {
            text-align: center;
            margin: 40px 0;
        }
        
        .applicant-name {
            font-size: 48px;
            font-family: 'Dancing Script', 'Brush Script MT', cursive;
            margin: 30px 0;
            color: #333;
            font-style: normal;
            font-weight: 600;
        }
        
        .dotted-line {
            border-bottom: 1px dotted #999;
            width: 500px;
            margin: 0 auto;
        }
        
        .body-text {
            font-size: 16px;
            line-height: 1.8;
            color: #444;
            margin: 20px 0;
        }
        
        .organization {
            font-weight: bold;
            color: #333;
        }
        
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 50px;
            padding: 0 40px;
        }
        
        .signature-section {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
            width: 100%;
        }
        
        .signature-label {
            font-size: 14px;
            color: #666;
        }
        
        .signature-image {
            max-width: 180px;
            max-height: 60px;
            margin-bottom: 5px;
        }
        
        .logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }
        
        .logo {
            max-width: 100px;
            max-height: 80px;
        }
        
        .center-ornament {
            text-align: center;
            margin: 20px 0;
        }
        
        .divider {
            width: 150px;
            height: 2px;
            background: linear-gradient(to right, transparent, #999, transparent);
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="border-inner">
            <!-- Ornamental corners would go here if you have SVG/images -->
            
            <div class="header">
                <h1 class="title">Certificate of Approval</h1>
                <p class="subtitle">The following approval is given to</p>
            </div>
            
            <div class="content">
                <div class="applicant-name">{{ $certificate->applicant_name }}</div>
                <div class="dotted-line"></div>
                
                <p class="body-text">
                    haven met the required standards and is hereby approved to operate a<br>
                    Day Home under the authority of<br>
                    <span class="organization">SPICE'd Childcare Services</span>
                </p>
                
                <p class="body-text" style="font-size: 14px; margin-top: 10px;">
                    This approval confirms compliance with SPICE'd Childcare Services'<br>
                    operational, safety, and childcare standards.
                </p>
            </div>
            
            <div class="center-ornament">
                <div class="divider"></div>
            </div>
            
            <div class="footer">
                <div class="signature-section">
                    <div class="signature-line"></div>
                </div>
                
                <div class="logos">
                    <!-- SPICE'd Logo -->
                    <img src="{{ public_path('assets/images/logo.png') }}" alt="SPICE'd Logo" class="logo">
                    
                    <!-- Alberta Approved Logo -->
                    <img src="{{ public_path('assets/images/alberta-approved-logo.png') }}" alt="Alberta Approved" class="logo">
                </div>
                
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-label">Date</div>
                </div>
            </div>
            
            <!-- CEO Signature and Name (below the signature line) -->
            <div style="position: absolute; bottom: 55px; left: 50px; text-align: center; width: 200px;">
                @if(file_exists(public_path('assets/images/jaye_brown_signature.png')))
                    <img src="{{ asset('assets/images/jaye_brown_signature.png') }}" 
                         alt="CEO Signature" 
                         class="signature-image">
                @elseif($certificate->ceo_signature_path && file_exists(public_path('storage/' . $certificate->ceo_signature_path)))
                    <img src="{{ public_path('storage/' . $certificate->ceo_signature_path) }}" 
                         alt="CEO Signature" 
                         class="signature-image">
                @endif
                <div style="font-family: 'Playfair Display', 'Georgia', serif; font-style: italic; font-weight: 700; font-size: 14px; margin-top: 5px; color: #1a1a1a;">
                    {{ $certificate->ceo_name ?? 'Paola Cortes' }}
                </div>
                <div style="font-size: 12px; color: #666;">CEO</div>
            </div>
            
            <!-- Date (below the date line) -->
            <div style="position: absolute; bottom: 70px; right: 50px; text-align: center; width: 200px;">
                <div style="font-size: 14px; font-weight: bold;">
                    {{ $certificate->issue_date->format('F d, Y') }}
                </div>
            </div>
            
            <!-- Certificate Number (bottom right) -->
            <div style="position: absolute; bottom: 20px; right: 40px; font-size: 10px; color: #999;">
                Certificate No: {{ $certificate->certificate_number }}
            </div>
        </div>
    </div>
</body>
</html>