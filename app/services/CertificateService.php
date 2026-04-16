<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateService
{
    /**
     * Generate certificate for approved application
     */
    public function generateCertificate(Application $application, $issuedBy = null)
    {
        try {
            // Check if certificate already exists
            $existingCert = Certificate::where('application_id', $application->id)
                ->where('status', 'active')
                ->first();

            if ($existingCert) {
                Log::info('Certificate already exists for application', [
                    'application_id' => $application->id,
                    'certificate_id' => $existingCert->id
                ]);
                return $existingCert;
            }

            // Create certificate record
            $certificate = Certificate::create([
                'application_id' => $application->id,
                'certificate_number' => Certificate::generateCertificateNumber(),
                'issue_date' => now(),
                'expiry_date' => now()->addYear(),
                'applicant_name' => $application->educator_first_name . ' ' . $application->educator_last_name,
                'ceo_name' => 'Paola Cortes',
                'ceo_signature_path' => 'assets/images/jaye_brown_signature.png',
                'status' => 'active',
                'issued_by' => $issuedBy ?? auth()->id(),
            ]);

            // Generate PDF
            $this->generatePDF($certificate);

            Log::info('Certificate generated successfully', [
                'certificate_id' => $certificate->id,
                'application_id' => $application->id
            ]);

            return $certificate;

        } catch (\Exception $e) {
            Log::error('Failed to generate certificate', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate PDF certificate
     */
    public function generatePDF(Certificate $certificate)
    {
        $certificate->load('application');

        // Prepare image paths for PDF
        $signaturePath = public_path('assets/images/jaye_brown_signature.png');
        $logoPath = public_path('assets/images/logo.png');
        $albertaLogoPath = public_path('assets/images/alberta-approved-logo.png');

        // Convert images to base64 for reliable PDF rendering
        $signatureBase64 = file_exists($signaturePath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath))
            : null;
        
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;
        
        $albertaLogoBase64 = file_exists($albertaLogoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($albertaLogoPath))
            : null;

        $pdf = PDF::loadView('certificates.template', [
            'certificate' => $certificate,
            'application' => $certificate->application,
            'signatureBase64' => $signatureBase64,
            'logoBase64' => $logoBase64,
            'albertaLogoBase64' => $albertaLogoBase64,
            'isPdf' => true,
        ]);

        $pdf->setPaper('a4', 'landscape');
        
        // Optimized DomPDF options
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true, // Enable to load Google Fonts
            'defaultFont' => 'serif',
            'dpi' => 96, // Match browser DPI
            'enable_php' => false,
            'chroot' => public_path(),
        ]);

        $filename = 'certificate_' . $certificate->certificate_number . '.pdf';
        $path = 'certificates/' . $filename;

        Storage::put($path, $pdf->output());
        $certificate->update(['pdf_path' => $path]);

        return $path;
    }

    /**
     * Regenerate certificate PDF
     */
    public function regeneratePDF(Certificate $certificate)
    {
        if ($certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
            Storage::delete($certificate->pdf_path);
        }

        return $this->generatePDF($certificate);
    }

    /**
     * Revoke certificate
     */
    public function revokeCertificate(Certificate $certificate, $reason)
    {
        $certificate->revoke($reason);

        Log::info('Certificate revoked', [
            'certificate_id' => $certificate->id,
            'reason' => $reason
        ]);

        return $certificate;
    }

    /**
     * Check and update expired certificates
     */
    public function checkExpiredCertificates()
    {
        $expiredCount = 0;

        Certificate::where('status', 'active')
            ->where('expiry_date', '<', now())
            ->chunk(100, function ($certificates) use (&$expiredCount) {
                foreach ($certificates as $certificate) {
                    $certificate->update(['status' => 'expired']);
                    $expiredCount++;
                }
            });

        Log::info('Expired certificates updated', ['count' => $expiredCount]);

        return $expiredCount;
    }
}