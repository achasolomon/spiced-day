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
        /* DomPDF Compatible Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Times-Roman', 'Georgia', serif;
            margin: 0;
            padding: 15px;
            background: white;
        }
        
        .certificate-wrapper {
            width: 100%;
            margin: 0 auto;
        }
        
        .certificate {
            border: 3px solid #2d2d2d;
            padding: 15px;
            background: white;
        }
        
        .border-inner {
            border: 1px solid #888;
            padding: 15px 25px;
            min-height: 500px;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .title {
            font-size: 42px;
            font-weight: normal;
            letter-spacing: 1px;
            margin: 0 0 10px 0;
            color: #1a1a1a;
        }
        
        .subtitle {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #666;
            font-weight: normal;
            margin: 0;
        }
        
        /* Content */
        .content {
            text-align: center;
            margin: 30px 0;
        }
        
        .applicant-name {
            font-size: 36px;
            font-family: 'Dancing Script', 'Brush Script MT', 'Edwardian Script ITC', 'Lucida Handwriting', cursive;
            font-style: normal;
            margin: 20px 0;
            color: #1a1a1a;
            font-weight: 600;
        }
        
        .dotted-line {
            border-bottom: 1px dotted #999;
            width: 420px;
            margin: 0 auto 25px auto;
            height: 1px;
        }
        
        .body-text {
            font-size: 13px;
            line-height: 1.7;
            color: #333;
            margin: 15px 0;
        }
        
        .organization {
            font-weight: bold;
            color: #1a1a1a;
        }
        
        .divider-section {
            text-align: center;
            margin: 25px 0;
        }
        
        .divider {
            width: 120px;
            height: 1px;
            background: #999;
            margin: 0 auto;
        }
        
        /* Footer - Table-based layout for DomPDF */
        .footer-container {
            margin-top: 40px;
        }
        
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .footer-table td {
            vertical-align: bottom;
            text-align: center;
            padding: 10px;
        }
        
        .footer-left {
            width: 25%;
        }
        
        .footer-center {
            width: 50%;
        }
        
        .footer-right {
            width: 25%;
        }
        
        /* Signature blocks */
        .signature-block {
            text-align: center;
            padding-top: 10px;
        }
        
        .signature-image {
            max-width: 150px;
            max-height: 50px;
            width: auto;
            height: auto;
            margin: 0 auto 5px;
            display: block;
        }
        
        .signature-line {
            border-top: 1px solid #2d2d2d;
            width: 150px;
            margin: 30px auto 5px auto;
        }
        
        .signature-label {
            font-size: 11px;
            color: #666;
            margin: 5px 0 0 0;
        }
        
        .ceo-name {
            font-family: 'Playfair Display', 'Georgia', serif;
            font-style: italic;
            font-weight: 700;
            font-size: 12px;
            color: #1a1a1a;
            margin-top: 5px;
        }
        
        /* Date block */
        .date-block {
            text-align: center;
            padding-top: 5px;
        }
        
        .date-text {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        
        .date-line {
            border-top: 1px solid #2d2d2d;
            width: 150px;
            margin: 10px auto 5px auto;
        }
        
        /* Logos */
        .logos {
            text-align: center;
            padding: 10px 0;
        }
        
        .logo {
            max-width: 70px;
            max-height: 60px;
            width: auto;
            height: auto;
            margin: 0 10px;
            vertical-align: middle;
            display: inline-block;
        }
        
        /* Certificate number */
        .cert-number {
            text-align: right;
            font-size: 8px;
            color: #999;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="certificate">
            <div class="border-inner">
                <!-- Header -->
                <div class="header">
                    <h1 class="title">Certificate of Approval</h1>
                    <p class="subtitle">The following approval is given to</p>
                </div>
                
                <!-- Content -->
                <div class="content">
                    <div class="applicant-name">{{ $certificate->applicant_name }}</div>
                    <div class="dotted-line"></div>
                    
                    <p class="body-text">
                        has met the required standards and is hereby approved to operate a<br>
                        Day Home under the authority of<br>
                        <span class="organization">SPICE'd Childcare Services</span>
                    </p>
                    
                    <p class="body-text" style="font-size: 12px; margin-top: 10px;">
                        This approval confirms compliance with SPICE'd Childcare Services'<br>
                        operational, safety, and childcare standards.
                    </p>
                    
                    <div class="divider-section">
                        <div class="divider"></div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="footer-container">
                    <table class="footer-table">
                        <tr>
                            <!-- CEO Signature -->
                            <td class="footer-left">
                                <div class="signature-block">
                                    @if(isset($isPdf) && $isPdf && isset($signatureBase64))
                                        <img src="{{ $signatureBase64 }}" 
                                             alt="Signature" 
                                             class="signature-image">
                                    @elseif(file_exists(public_path('assets/images/jaye_brown_signature.png')))
                                        <img src="{{ asset('assets/images/jaye_brown_signature.png') }}" 
                                             alt="Signature" 
                                             class="signature-image">
                                    @endif
                                    <p class="ceo-name">
                                        {{ $certificate->ceo_name ?? 'Paola Cortes' }}
                                    </p>
                                </div>
                            </td>
                            
                            <!-- Logos -->
                            <td class="footer-center">
                                <div class="logos">
                                    @if(isset($isPdf) && $isPdf && isset($logoBase64))
                                        <img src="{{ $logoBase64 }}" 
                                             alt="SPICE'd Logo" 
                                             class="logo">
                                    @elseif(file_exists(public_path('assets/images/logo.png')))
                                        <img src="{{ asset('assets/images/logo.png') }}" 
                                             alt="SPICE'd Logo" 
                                             class="logo">
                                    @endif
                                    
                                    @if(isset($isPdf) && $isPdf && isset($albertaLogoBase64))
                                        <img src="{{ $albertaLogoBase64 }}" 
                                             alt="Alberta Approved" 
                                             class="logo">
                                    @elseif(file_exists(public_path('assets/images/alberta-approved-logo.png')))
                                        <img src="{{ asset('assets/images/alberta-approved-logo.png') }}" 
                                             alt="Alberta Approved" 
                                             class="logo">
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Date -->
                            <td class="footer-right">
                                <div class="date-block">
                                    <p class="date-text">{{ $certificate->issue_date->format('F d, Y') }}</p>
                                    <div class="date-line"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Certificate Number -->
                <div class="cert-number">
                    Certificate No: {{ $certificate->certificate_number }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>