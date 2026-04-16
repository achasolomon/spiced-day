<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicantInspectionController extends Controller
{
    /**
     * Display the inspection report
     */
    public function show(Inspection $inspection)
    {
        // Ensure applicant can only view their own inspection
        if ($inspection->application->user_id != auth()->id()) {
            abort(403, 'You do not have permission to view this inspection.');
        }

        $inspection->load([
            'application.user',
            'consultant',
            'appointment',
            'approvedBy'
        ]);

        return view('applicant.inspections.show', compact('inspection'));
    }

    /**
     * Download inspection report as PDF
     */
    public function download(Inspection $inspection)
    {
        // Ensure applicant can only download their own inspection
        if ($inspection->application->user_id != auth()->id()) {
            abort(403, 'You do not have permission to download this inspection.');
        }

        $inspection->load([
            'application.user',
            'consultant',
            'appointment'
        ]);

        $pdf = Pdf::loadView('applicant.inspections.pdf', compact('inspection'));
        
        $filename = 'inspection-' . $inspection->inspection_number . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Acknowledge that applicant has viewed the inspection
     */
    public function acknowledge(Inspection $inspection)
    {
        // Ensure applicant can only acknowledge their own inspection
        if ($inspection->application->user_id != auth()->id()) {
            abort(403, 'You do not have permission to acknowledge this inspection.');
        }

        $inspection->update([
            'applicant_acknowledged_at' => now()
        ]);

        \App\Models\AuditLog::log(
            'inspection_acknowledged',
            $inspection,
            'Applicant acknowledged inspection report'
        );

        return response()->json([
            'success' => true,
            'message' => 'Inspection report acknowledged'
        ]);
    }
}