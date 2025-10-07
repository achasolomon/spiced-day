<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Application;
use App\Models\DocumentRequirement;
use App\Enums\ApplicationStatus;
use App\Services\ApplicationStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    protected $statusService;

    public function __construct(ApplicationStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function index(Application $application)
    {
        if (auth()->user()->isApplicant()) {
            if ($application->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to this application.');
            }
        } elseif (auth()->user()->isConsultant()) {
            if ($application->consultant_id !== auth()->id()) {
                abort(403, 'You are not assigned to this application.');
            }
        } elseif (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $requiredDocuments = $application->getRequiredDocumentsForStage();
        
        $uploadedDocuments = $application->documents()
            ->with(['uploadedBy', 'reviewedBy'])
            ->latest()
            ->get()
            ->groupBy('category');
        
        $uploadedCategories = $application->documents()
            ->where('status', '!=', 'rejected')
            ->pluck('category')
            ->unique()
            ->toArray();
        
        $pendingDocuments = array_diff($requiredDocuments, $uploadedCategories);
        
        $documentCategories = [
            'criminal_record_check' => 'Criminal Record Check',
            'cpr_first_aid' => 'CPR & First Aid Certificate',
            'educator_certificate' => 'Educator Certificate',
            'home_insurance' => 'Home Insurance',
            'car_insurance' => 'Vehicle Insurance',
            'liability_insurance' => 'Liability Insurance',
            'statement_of_disclosure' => 'Statement of Disclosure',
            'fit_to_work_assessment' => 'Fit to Work Assessment',
            'food_handler_certificate' => 'Food Handler Certificate',
            'pet_vaccination' => 'Pet Vaccination Records',
            'evacuation_plan' => 'Evacuation Plan',
            'emergency_contacts' => 'Emergency Contacts',
            'daily_schedule' => 'Daily Schedule',
            'program_planning' => 'Program Planning',
            'menu_sample' => 'Menu Sample',
            'character_references' => 'Character References',
            'fee_schedule' => 'Fee Schedule',
            'transportation_policy' => 'Transportation Policy',
            'other' => 'Other Documents',
        ];

        return view('applicant.documents.index', compact(
            'application',
            'requiredDocuments',
            'uploadedDocuments',
            'pendingDocuments',
            'documentCategories'
        ));
    }

    public function store(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this application.');
        }

        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            'documents' => 'required|array',
            'documents.*.name' => 'required|string|max:255',
            'documents.*.category' => 'required|string',
            'documents.*.description' => 'nullable|string|max:500',
            'documents.*.issue_date' => 'nullable|date|before_or_equal:today',
            'documents.*.expiry_date' => 'nullable|date|after:documents.*.issue_date',
        ]);

        $uploadedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            $files = $request->file('files');
            $documentsData = $request->input('documents');

            foreach ($files as $index => $file) {
                try {
                    $documentData = $documentsData[$index] ?? [];
                    
                    $fileName = time() . '_' . $index . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                    $filePath = $file->storeAs('documents/' . $application->id, $fileName, 'private');

                    Document::create([
                        'application_id' => $application->id,
                        'uploaded_by' => auth()->id(),
                        'name' => $documentData['name'],
                        'category' => $documentData['category'],
                        'type' => 'required_document',
                        'description' => $documentData['description'] ?? null,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'file_hash' => hash_file('sha256', $file->getRealPath()),
                        'issue_date' => $documentData['issue_date'] ?? null,
                        'expiry_date' => $documentData['expiry_date'] ?? null,
                        'expires' => !empty($documentData['expiry_date']),
                        'status' => 'uploaded',
                    ]);

                    $uploadedCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Failed to upload {$file->getClientOriginalName()}: " . $e->getMessage();
                    \Log::error('Document upload failed', [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update application status if documents were uploaded
            if ($uploadedCount > 0) {
                // Check if this is the first document upload
                $totalDocuments = $application->documents()->count();
                
                if ($totalDocuments === $uploadedCount && $application->status === ApplicationStatus::DOCUMENTS_PENDING->value) {
                    // First documents uploaded
                    $this->statusService->transitionTo(
                        $application,
                        ApplicationStatus::DOCUMENTS_SUBMITTED,
                        "Uploaded {$uploadedCount} document(s)"
                    );
                }
            }

            DB::commit();

            $message = $uploadedCount === count($files) 
                ? "Successfully uploaded {$uploadedCount} document(s)!" 
                : "Uploaded {$uploadedCount} of " . count($files) . " documents. Some failed.";

            return back()->with($errors ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Bulk document upload failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to upload documents. Please try again.');
        }
    }

    public function approve(Document $document)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        
        if (auth()->user()->isConsultant() && $document->application->consultant_id !== auth()->id()) {
            abort(403, 'You are not assigned to this application.');
        }

        DB::beginTransaction();
        try {
            $document->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Check if all documents are approved
            $application = $document->application;
            $allDocumentsApproved = $application->documents()
                ->where('status', '!=', 'approved')
                ->where('type', 'required_document')
                ->doesntExist();

            if ($allDocumentsApproved && $application->status === ApplicationStatus::DOCUMENTS_SUBMITTED->value) {
                $this->statusService->transitionTo(
                    $application,
                    ApplicationStatus::DOCUMENTS_APPROVED,
                    "All documents approved"
                );
            }

            \App\Models\AuditLog::log('document_approved', $document, 'Document approved');

            DB::commit();

            return back()->with('success', 'Document approved successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Document approval failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to approve document.');
        }
    }

    public function reject(Request $request, Document $document)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        
        if (auth()->user()->isConsultant() && $document->application->consultant_id !== auth()->id()) {
            abort(403, 'You are not assigned to this application.');
        }

        $validated = $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $document->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $validated['review_notes'],
            ]);

            // Create notification for applicant
            \App\Models\Notification::create([
                'user_id' => $document->application->user_id,
                'application_id' => $document->application_id,
                'type' => 'document_rejected',
                'title' => 'Document Rejected',
                'message' => "Your document '{$document->name}' has been rejected. Please review the feedback and reupload.",
                'action_url' => route('applicant.documents.index', $document->application),
                'priority' => 'high',
            ]);

            \App\Models\AuditLog::log('document_rejected', $document, 'Document rejected', [
                'review_notes' => $validated['review_notes']
            ]);

            DB::commit();

            return back()->with('success', 'Document rejected. Applicant has been notified.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject document.');
        }
    }

    public function download(Request $request, Application $application = null, Document $document)
    {
        if (auth()->user()->isApplicant()) {
            if (!$application || $application->user_id !== auth()->id() || $document->application_id !== $application->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif (auth()->user()->isConsultant()) {
            if (!$document->application || $document->application->consultant_id !== auth()->id()) {
                abort(403, 'You are not assigned to this application.');
            }
        } elseif (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if (!Storage::disk('private')->exists($document->file_path)) {
            \Log::error('File Not Found', ['file_path' => $document->file_path]);
            return back()->with('error', 'File not found.');
        }

        $document->increment('download_count');
        \App\Models\AuditLog::log('document_downloaded', $document, 'Document downloaded');

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_filename ?: basename($document->file_path)
        );
    }

    public function destroy(Application $application, Document $document)
    {
        if (auth()->user()->isApplicant()) {
            if ($application->user_id !== auth()->id() || $document->application_id !== $application->id) {
                abort(403, 'Unauthorized access.');
            }
            
            if (!in_array($document->status, ['uploaded', 'rejected'])) {
                return back()->with('error', 'Cannot delete a document that is under review or approved.');
            }
        } elseif (auth()->user()->isConsultant()) {
            if ($application->consultant_id !== auth()->id()) {
                abort(403, 'You are not assigned to this application.');
            }
        } elseif (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        DB::beginTransaction();
        try {
            if (Storage::disk('private')->exists($document->file_path)) {
                Storage::disk('private')->delete($document->file_path);
            }

            \App\Models\AuditLog::log('document_deleted', $document, 'Document deleted');
            $document->delete();

            DB::commit();

            return back()->with('success', 'Document deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete document. Please try again.');
        }
    }

    public function pendingReview()
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Document::with(['application.user', 'uploadedBy'])
            ->whereIn('status', ['uploaded', 'under_review']);

        if (auth()->user()->isConsultant()) {
            $query->whereHas('application', function ($q) {
                $q->where('consultant_id', auth()->id());
            });
        }

        $documents = $query->latest()->paginate(20);

        return view('consultant.documents.pending', compact('documents'));
    }

    public function preview(Request $request, Application $application = null, Document $document)
    {
        if (auth()->user()->isApplicant()) {
            if (!$application || $application->user_id !== auth()->id() || $document->application_id !== $application->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif (auth()->user()->isConsultant()) {
            if ($document->application->consultant_id !== auth()->id()) {
                abort(403, 'You are not assigned to this application.');
            }
        } elseif (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $fileType = strtolower($document->file_type);
        
        if (!in_array($fileType, $allowedTypes)) {
            abort(403, 'Preview is only available for image and PDF files.');
        }

        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::disk('private')->path($document->file_path);
        $mimeType = $document->mime_type ?? Storage::disk('private')->mimeType($document->file_path);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    public function bulkDownload(Request $request, Application $application)
    {
        if (auth()->user()->isApplicant()) {
            if ($application->user_id !== auth()->id()) {
                abort(403);
            }
        } elseif (auth()->user()->isConsultant()) {
            if ($application->consultant_id !== auth()->id()) {
                abort(403);
            }
        } elseif (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $documentIds = $request->input('document_ids', []);
        
        if (empty($documentIds)) {
            return back()->with('error', 'No documents selected');
        }

        $documents = Document::whereIn('id', $documentIds)
            ->where('application_id', $application->id)
            ->get();

        if ($documents->isEmpty()) {
            return back()->with('error', 'No documents found');
        }

        $zip = new \ZipArchive();
        $zipFileName = 'documents_' . $application->application_number . '_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($documents as $document) {
                $filePath = storage_path('app/private/' . $document->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $document->original_filename);
                }
            }
            $zip->close();

            \App\Models\AuditLog::log('bulk_document_download', $application, 'Downloaded ' . $documents->count() . ' documents');

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Failed to create ZIP file');
    }
}