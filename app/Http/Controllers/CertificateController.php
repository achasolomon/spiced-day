<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Application;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * View certificate
     */
    public function show(Certificate $certificate)
    {
        $user = auth()->user();
        
        // Authorization
        if ($certificate->application->user_id != $user->id && 
            !$user->isAdmin() && 
            !($user->isConsultant() && $certificate->application->consultant_id == $user->id)) {
            abort(403, 'Unauthorized access to this certificate.');
        }

        $certificate->load('application', 'issuedBy');

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Download certificate PDF
     */
    public function download(Certificate $certificate)
    {
        $user = auth()->user();
        
        // Authorization
        if ($certificate->application->user_id != $user->id && 
            !$user->isAdmin() && 
            !($user->isConsultant() && $certificate->application->consultant_id == $user->id)) {
            abort(403, 'Unauthorized access to this certificate.');
        }

        if (!$certificate->pdf_path || !Storage::exists($certificate->pdf_path)) {
            // Regenerate PDF if missing
            $this->certificateService->generatePDF($certificate);
        }

        $filename = "Certificate_{$certificate->certificate_number}.pdf";
        
        // Use private storage (not public)
        return Storage::download($certificate->pdf_path, $filename);
    }

    /**
     * Preview certificate (HTML view)
     */
    public function preview(Certificate $certificate)
    {
        $user = auth()->user();
        
        // Authorization
        if ($certificate->application->user_id != $user->id && 
            !$user->isAdmin() && 
            !($user->isConsultant() && $certificate->application->consultant_id == $user->id)) {
            abort(403, 'Unauthorized access to this certificate.');
        }

        $certificate->load('application');

        return view('certificates.template', compact('certificate'));
    }

    /**
     * Admin: List all certificates
     */
    public function adminIndex(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Certificate::with(['application.user', 'issuedBy'])
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->search, function ($q, $search) {
                return $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhere('applicant_name', 'like', "%{$search}%");
            })
            ->when($request->expiring_soon, function ($q) {
                return $q->where('expiry_date', '<=', now()->addDays(30))
                    ->where('status', 'active');
            });

        $certificates = $query->latest()->paginate(20);

        $stats = [
            'total' => Certificate::count(),
            'active' => Certificate::where('status', 'active')->count(),
            'expired' => Certificate::where('status', 'expired')->count(),
            'revoked' => Certificate::where('status', 'revoked')->count(),
            'expiring_soon' => Certificate::where('status', 'active')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->count(),
        ];

        return view('admin.certificates.adminIndex', compact('certificates', 'stats'));
    }

    /**
     * Admin: Show certificate by Id
     */
    
     public function adminShow(Certificate $certificate)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Admins only.');
    
        $certificate->load('application', 'issuedBy');
    
        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Admin: Revoke certificate
     */
    public function revoke(Request $request, Certificate $certificate)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'revocation_reason' => 'required|string|max:500',
        ]);

        $this->certificateService->revokeCertificate(
            $certificate, 
            $request->revocation_reason
        );

        \App\Models\AuditLog::log(
            'certificate_revoked',
            $certificate->application,
            "Certificate {$certificate->certificate_number} revoked: {$request->revocation_reason}"
        );

        return back()->with('success', 'Certificate revoked successfully.');
    }

    /**
     * Admin: Regenerate certificate
     */
    public function regenerate(Certificate $certificate)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        try {
            $this->certificateService->regeneratePDF($certificate);

            return back()->with('success', 'Certificate PDF regenerated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to regenerate certificate', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to regenerate certificate.');
        }
    }

    /**
     * Admin: Manual certificate generation
     */
    public function generate(Application $application)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isConsultant()) {
            abort(403);
        }

        if ($application->status !== \App\Enums\ApplicationStatus::APPROVED->value) {
            return back()->with('error', 'Only approved applications can have certificates generated.');
        }

        try {
            $certificate = $this->certificateService->generateCertificate($application, auth()->id());

            \App\Models\AuditLog::log(
                'certificate_generated',
                $application,
                "Certificate manually generated: {$certificate->certificate_number}"
            );

            return redirect()
                ->route('certificates.show', $certificate)
                ->with('success', 'Certificate generated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to generate certificate', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }
    
    /**
 * Export certificates to CSV
 * Add this method to your CertificateController class
 */
public function export(Request $request)
{
    if (!auth()->user()->isAdmin()) {
        abort(403);
    }

    $query = Certificate::with(['application.user', 'issuedBy'])
        ->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        })
        ->when($request->search, function ($q, $search) {
            return $q->where('certificate_number', 'like', "%{$search}%")
                ->orWhere('applicant_name', 'like', "%{$search}%");
        })
        ->when($request->expiring_soon, function ($q) {
            return $q->where('expiry_date', '<=', now()->addDays(30))
                ->where('status', 'active');
        });

    $certificates = $query->get();

    $filename = 'certificates_export_' . now()->format('Y-m-d_His') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function() use ($certificates) {
        $file = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($file, [
            'Certificate Number',
            'Applicant Name',
            'Issue Date',
            'Expiry Date',
            'Status',
            'Application Tracking Number',
            'Applicant Email',
            'Issued By',
            'Revocation Reason',
            'Revoked At'
        ]);

        // Add data rows
        foreach ($certificates as $certificate) {
            fputcsv($file, [
                $certificate->certificate_number,
                $certificate->applicant_name,
                $certificate->issue_date->format('Y-m-d'),
                $certificate->expiry_date->format('Y-m-d'),
                $certificate->status,
                $certificate->application->tracking_number,
                $certificate->application->user->email,
                $certificate->issuedBy->name ?? 'System',
                $certificate->revocation_reason ?? '',
                $certificate->revoked_at ? $certificate->revoked_at->format('Y-m-d H:i:s') : ''
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}