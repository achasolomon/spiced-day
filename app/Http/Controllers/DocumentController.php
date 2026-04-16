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
        // Refresh to get the latest status
        $application->refresh();
        
        if (auth()->user()->isApplicant()) {
            if ($application->user_id != auth()->id()) {
                abort(403, 'Unauthorized access to this application.');
            }
        } elseif (auth()->user()->isConsultant()) {
            if ($application->consultant_id != auth()->id()) {
                abort(403, 'You are not assigned to this application.');
            }
        } elseif (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get requirements assigned by consultant for this specific application
        $requiredDocuments = $application->documentRequirements()
            ->wherePivot('is_required', true)
            ->where('is_active', true)
            ->get();
        
        // If no specific requirements assigned, use stage defaults
        if ($requiredDocuments->isEmpty()) {
            $requiredDocuments = DocumentRequirement::where('stage', $application->current_stage)
                ->where('is_required', true)
                ->where('is_active', true)
                ->get();
        }
        
        // If still no requirements, get all active requirements for document_submission stage
        // This ensures applicants can always see available document types to upload
        if ($requiredDocuments->isEmpty() && $application->current_stage === 'document_submission') {
            $requiredDocuments = DocumentRequirement::where('stage', 'document_submission')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }
        
        // Get all uploaded documents
        $uploadedDocuments = $application->documents()
            ->with(['uploadedBy', 'reviewedBy', 'documentRequirement'])
            ->latest()
            ->get()
            ->groupBy('document_requirement_id'); // Group by requirement ID instead of category
        
        // Get IDs of requirements that have been uploaded
        $uploadedRequirementIds = $application->documents()
            ->where('status', '!=', 'rejected')
            ->whereNotNull('document_requirement_id')
            ->pluck('document_requirement_id')
            ->unique()
            ->toArray();
        
        // Calculate pending documents (required but not uploaded)
        // If no specific requirements were assigned, show all available requirements
        $pendingDocuments = $requiredDocuments->filter(function($req) use ($uploadedRequirementIds) {
            return !in_array($req->id, $uploadedRequirementIds);
        });
        
        // Document categories for the select dropdown (kept for backward compatibility)
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

        // Check if document upload is allowed
       $canUploadDocuments = false;

if (auth()->user()->isApplicant()) {

    // NORMAL WORKFLOW (status-driven)
    if (!$application->workflow_concluded) {
        $canUploadDocuments = in_array($application->status, [
            ApplicationStatus::DOCUMENTS_PENDING->value,
            ApplicationStatus::DOCUMENTS_SUBMITTED->value,
            ApplicationStatus::DOCUMENTS_APPROVED->value,
        ]);
    }

    // LEGACY / COMPLETED WORKFLOW (requirement-driven)
    if (
        $application->workflow_concluded &&
        $requiredDocuments->isNotEmpty()
    ) {
        $canUploadDocuments = true;
    }
}


        return view('applicant.documents.index', compact(
            'application',
            'requiredDocuments',
            'uploadedDocuments',
            'pendingDocuments',
            'documentCategories',
            'canUploadDocuments'
        ));
    }

   public function store(Request $request, Application $application)
{
    // Authorization
    if ($application->user_id != auth()->id()) {
        abort(403, 'Unauthorized access to this application.');
    }

    $allowedStatuses = [
        ApplicationStatus::DOCUMENTS_PENDING->value,
        ApplicationStatus::DOCUMENTS_SUBMITTED->value,
        ApplicationStatus::DOCUMENTS_APPROVED->value,
    ];

    $canBypassForLegacy = false;
    if ($application->imported_by_consultant) {
        $consultantHasUploaded = $application->documents()
            ->where('uploaded_by', $application->imported_by_consultant)
            ->exists();

        if (!$consultantHasUploaded && auth()->user()->isApplicant() && $application->user_id == auth()->id()) {
            $canBypassForLegacy = true;
        }
    }

    if (!in_array($application->status, $allowedStatuses) && !$canBypassForLegacy) {
        return back()->with('error', 'Document uploads are not allowed at this stage.');
    }

    \Log::info('Document upload attempt', [
        'user_id' => auth()->id(),
        'application_id' => $application->id,
        'file_count' => count($request->file('files') ?? []),
        'documents_count' => count($request->input('documents', [])),
    ]);

    $request->validate([
        'files' => 'required|array|min:1|max:20',
        'files.*' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
    ]);

    $uploadedCount = 0;
    $errors = [];

    DB::beginTransaction();

    try {
        $files = $request->file('files');
        $documentsData = $request->input('documents', []);

        foreach ($files as $index => $file) {
            try {
                $documentData = $documentsData[$index] ?? null;

                if (!$documentData) {
                    throw new \Exception('Missing document metadata.');
                }

                validator($documentData, [
                    'name'        => 'required|string|max:255',
                    'category'    => 'required|string',
                    'description' => 'nullable|string|max:500',
                    'issue_date'  => 'nullable|date|before_or_equal:today',
                    'expiry_date' => 'nullable|date',
                ])->validate();

                if (!empty($documentData['issue_date']) && !empty($documentData['expiry_date'])) {
                    if ($documentData['expiry_date'] < $documentData['issue_date']) {
                        throw new \Exception('Expiry date must be after issue date.');
                    }
                }

                $fileName = time() . '_' . $index . '_' .
                    preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());

                $filePath = $file->storeAs(
                    'documents/' . $application->id,
                    $fileName,
                    'private'
                );

                $requirement = DocumentRequirement::find($documentData['category']);

                Document::create([
                    'application_id' => $application->id,
                    'uploaded_by' => auth()->id(),
                    'document_requirement_id' => $requirement->id ?? null,
                    'document_category_id' => $requirement->document_category_id ?? null,
                    'document_type_id' => $requirement->document_type_id ?? null,
                    'name' => $documentData['name'],
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

            } catch (\Throwable $e) {
                $errors[] = "Failed to upload {$file->getClientOriginalName()}: {$e->getMessage()}";
                \Log::error('Document upload failed', [
                    'index' => $index,
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ✅ Status transition only if workflow is active
        if (!$application->workflow_concluded && $uploadedCount > 0 && $application->status === ApplicationStatus::DOCUMENTS_PENDING->value) {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::DOCUMENTS_SUBMITTED,
                "Uploaded {$uploadedCount} document(s)"
            );
        }

        DB::commit();

        if (count($errors) > 0) {
            return back()
                ->with('warning', "Uploaded {$uploadedCount} document(s). Some failed.")
                ->with('upload_errors', $errors);
        }

        return back()->with('success', "Successfully uploaded {$uploadedCount} document(s)!");

    } catch (\Throwable $e) {
        DB::rollBack();

        \Log::error('Bulk document upload failed', [
            'error' => $e->getMessage(),
        ]);

        return back()->with('error', 'Failed to upload documents. Please try again.');
    }
    }


    /**
     * Consultant bulk upload for imported/legacy applications.
     * Uploaded documents by consultant are auto-approved.
     */
    public function consultantStore(Request $request, Application $application)
{
    $user = auth()->user();

    if (!$user->isConsultant() && !$user->isAdmin()) {
        abort(403);
    }

    if ($user->isConsultant() && $application->consultant_id != $user->id) {
        abort(403, 'You are not assigned to this application.');
    }

    $request->validate([
        'files' => 'required|array|min:1|max:50',
        'files.*' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
    ]);

    $uploadedCount = 0;
    $errors = [];

    DB::beginTransaction();

    try {
        $files = $request->file('files');
        $documentsData = $request->input('documents', []);

        foreach ($files as $index => $file) {
            try {
                $documentData = $documentsData[$index] ?? null;

                if (!$documentData) {
                    throw new \Exception('Missing document metadata.');
                }

                validator($documentData, [
                    'name'        => 'required|string|max:255',
                    'category'    => 'required|string',
                    'description' => 'nullable|string|max:500',
                    'issue_date'  => 'nullable|date|before_or_equal:today',
                    'expiry_date' => 'nullable|date',
                ])->validate();

                if (!empty($documentData['issue_date']) && !empty($documentData['expiry_date'])) {
                    if ($documentData['expiry_date'] < $documentData['issue_date']) {
                        throw new \Exception('Expiry date must be after issue date.');
                    }
                }

                $fileName = time() . '_' . $index . '_' .
                    preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());

                $filePath = $file->storeAs(
                    'documents/' . $application->id,
                    $fileName,
                    'private'
                );

                $requirement = DocumentRequirement::find($documentData['category']);

                $document = Document::create([
                    'application_id' => $application->id,
                    'uploaded_by' => auth()->id(),
                    'document_requirement_id' => $requirement->id ?? null,
                    'document_category_id' => $requirement->document_category_id ?? null,
                    'document_type_id' => $requirement->document_type_id ?? null,
                    'name' => $documentData['name'],
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
                    'status' => 'approved', // auto-approved for consultant uploads
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);

                \App\Models\AuditLog::log('document_uploaded_by_consultant', $document, 'Consultant uploaded document');

                $uploadedCount++;

            } catch (\Throwable $e) {
                $errors[] = "Failed to upload {$file->getClientOriginalName()}: {$e->getMessage()}";
                \Log::error('Consultant document upload failed', [
                    'index' => $index,
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Only transition application status if workflow is active
        if (!$application->workflow_concluded) {
            $allDocumentsApproved = $application->documents()
                ->whereNotNull('document_requirement_id')
                ->where('status', '!=', 'approved')
                ->doesntExist();

            if ($allDocumentsApproved && in_array($application->status, [
                App\Enums\ApplicationStatus::DOCUMENTS_SUBMITTED->value,
                App\Enums\ApplicationStatus::DOCUMENTS_PENDING->value,
            ])) {
                $this->statusService->transitionTo(
                    $application,
                    App\Enums\ApplicationStatus::DOCUMENTS_APPROVED,
                    "All documents uploaded by consultant"
                );
            }
        }

        DB::commit();

        if (count($errors) > 0) {
            return back()->with('warning', "Uploaded {$uploadedCount} document(s). Some failed.")
                         ->with('upload_errors', $errors);
        }

        return back()->with('success', "Successfully uploaded {$uploadedCount} document(s) and auto-approved them.");

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('Consultant bulk upload failed', ['error' => $e->getMessage()]);
        return back()->with('error', 'Failed to upload documents. Please try again.');
    }
    }


   public function approve(Document $document)
{
    $user = auth()->user();

    if (!$user->isConsultant() && !$user->isAdmin()) {
        abort(403);
    }

    if (!$document->relationLoaded('application')) {
        $document->load('application');
    }

    if ($user->isConsultant() && $document->application->consultant_id != $user->id) {
        abort(403, 'You are not assigned to this application.');
    }

    DB::beginTransaction();

    try {
        $document->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        \App\Models\AuditLog::log('document_approved', $document, 'Document approved');

        $application = $document->application;

        // Only check application status if workflow is active
        if (!$application->workflow_concluded) {
            $allDocumentsApproved = $application->documents()
                ->where('status', '!=', 'approved')
                ->whereNotNull('document_requirement_id')
                ->doesntExist();

            if ($allDocumentsApproved && in_array($application->status, [
                \App\Enums\ApplicationStatus::DOCUMENTS_SUBMITTED->value,
                \App\Enums\ApplicationStatus::DOCUMENTS_PENDING->value,
            ])) {
                $this->statusService->transitionTo(
                    $application,
                    \App\Enums\ApplicationStatus::DOCUMENTS_APPROVED,
                    "All documents approved"
                );
            }
        }

        DB::commit();

        $message = $application->workflow_concluded
            ? 'Document approved successfully! (Legacy workflow – application status unchanged)'
            : (isset($allDocumentsApproved) && $allDocumentsApproved
                ? 'All documents have been approved! Applicant has been notified.'
                : 'Document approved successfully!');

        return back()->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Document approval failed', ['error' => $e->getMessage()]);
        return back()->with('error', 'Failed to approve document.');
    }
    }


    public function reject(Request $request, Document $document)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        
        if (auth()->user()->isConsultant() && $document->application->consultant_id != auth()->id()) {
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
            if (!$application || $application->user_id != auth()->id() || $document->application_id != $application->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif (auth()->user()->isConsultant()) {
            if (!$document->application || $document->application->consultant_id != auth()->id()) {
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
            if ($application->user_id != auth()->id() || $document->application_id != $application->id) {
                abort(403, 'Unauthorized access.');
            }
            
            if (!in_array($document->status, ['uploaded', 'rejected'])) {
                return back()->with('error', 'Cannot delete a document that is under review or approved.');
            }
        } elseif (auth()->user()->isConsultant()) {
            if ($application->consultant_id != auth()->id()) {
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
  public function pendingReview(Request $request)
{
    if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
        abort(403);
    }

    // Base query - only documents for this consultant's applications
    $query = Document::with(['application.user', 'uploadedBy', 'reviewedBy', 'documentRequirement'])
        ->whereHas('application', function ($q) {
            if (auth()->user()->isConsultant()) {
                $q->where('consultant_id', auth()->id());
            }
        });

    // Filter by status from tab selection
    $status = $request->input('status', 'uploaded'); // Default to 'uploaded' for pending review
    
    if ($status !== 'all') {
        // Map 'uploaded' to both 'uploaded' and 'under_review' for pending
        if ($status === 'uploaded') {
            $query->whereIn('status', ['uploaded', 'under_review']);
        } else {
            $query->where('status', $status);
        }
    }
    // If status is 'all', no filter applied

    $documents = $query->latest()->paginate(50)->withQueryString();

    // Calculate statistics - for all documents (not filtered)
    $baseStatsQuery = Document::whereHas('application', function ($q) {
        if (auth()->user()->isConsultant()) {
            $q->where('consultant_id', auth()->id());
        }
    });

    $stats = [
        'total' => (clone $baseStatsQuery)->count(),
        'pending' => (clone $baseStatsQuery)->whereIn('status', ['uploaded', 'under_review'])->count(),
        'approved' => (clone $baseStatsQuery)->where('status', 'approved')->count(),
        'rejected' => (clone $baseStatsQuery)->where('status', 'rejected')->count(),
    ];

    return view('consultant.documents.pending', compact('documents', 'stats'));
}
    
    public function documents(Request $request)
    {
    if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
        abort(403);
    }

    // Base query - only documents for this consultant's applications
    $query = Document::with(['application.user', 'uploadedBy', 'reviewedBy', 'documentRequirement'])
        ->whereHas('application', function ($q) {
            if (auth()->user()->isConsultant()) {
                $q->where('consultant_id', auth()->id());
            }
        });

    // Filter by status
    if ($request->has('status') && $request->status !== '') {
        $query->where('status', $request->status);
    }

    // Filter by document type
    if ($request->has('document_type') && $request->document_type !== '') {
        $query->where(function($q) use ($request) {
            $q->whereHas('documentRequirement', function($subQ) use ($request) {
                $subQ->where('slug', $request->document_type);
            });
        });
    }

    // Search by applicant name or document name
    if ($request->has('search') && $request->search !== '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('original_filename', 'like', "%{$search}%")
              ->orWhereHas('application', function($appQuery) use ($search) {
                  $appQuery->where('educator_first_name', 'like', "%{$search}%")
                           ->orWhere('educator_last_name', 'like', "%{$search}%")
                           ->orWhere('application_number', 'like', "%{$search}%");
              });
        });
    }

    $documents = $query->latest()->paginate(50)->withQueryString();

    // Calculate statistics
    $baseStatsQuery = Document::whereHas('application', function ($q) {
        if (auth()->user()->isConsultant()) {
            $q->where('consultant_id', auth()->id());
        }
    });

    $stats = [
        'total' => (clone $baseStatsQuery)->count(),
        'pending' => (clone $baseStatsQuery)->whereIn('status', ['uploaded', 'under_review'])->count(),
        'approved' => (clone $baseStatsQuery)->where('status', 'approved')->count(),
        'rejected' => (clone $baseStatsQuery)->where('status', 'rejected')->count(),
    ];

    return view('consultant.documents.index', compact('documents', 'stats'));
}

    public function preview(Request $request, Application $application = null, Document $document)
    {
        // Ensure application relationship is loaded
        if (!$document->relationLoaded('application')) {
            $document->load('application');
        }
        
        if (auth()->user()->isApplicant()) {
            if (!$application || $application->user_id != auth()->id() || $document->application_id != $application->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif (auth()->user()->isConsultant()) {
            if (!$document->application || $document->application->consultant_id != auth()->id()) {
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
            if ($application->user_id != auth()->id()) {
                abort(403);
            }
        } elseif (auth()->user()->isConsultant()) {
            if ($application->consultant_id != auth()->id()) {
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

    /**
     * Admin documents management page
     */
    public function adminIndex(Request $request)
    {
    $query = Document::with(['application.user', 'uploadedBy', 'reviewedBy'])
        ->latest();

    // Filter by status
    if ($request->has('status') && $request->status !== '') {
        $query->where('status', $request->status);
    }

    // Filter by category
    if ($request->has('category') && $request->category !== '') {
        $query->where('category', $request->category);
    }

    // Search by document name or application number
    if ($request->has('search') && $request->search !== '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('original_filename', 'like', "%{$search}%")
              ->orWhereHas('application', function($appQuery) use ($search) {
                  $appQuery->where('application_number', 'like', "%{$search}%")
                           ->orWhereHas('user', function($userQuery) use ($search) {
                               $userQuery->where('name', 'like', "%{$search}%");
                           });
              });
        });
    }

    // Filter by date range
    if ($request->has('date_from') && $request->date_from !== '') {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->has('date_to') && $request->date_to !== '') {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Filter expired documents
    if ($request->has('expired') && $request->expired === '1') {
        $query->where('expires', true)
              ->where('expiry_date', '<', now());
    }

    $documents = $query->paginate(20)->withQueryString();

    // Statistics
    $stats = [
        'total' => Document::count(),
        'uploaded' => Document::where('status', 'uploaded')->count(),
        'under_review' => Document::where('status', 'under_review')->count(),
        'approved' => Document::where('status', 'approved')->count(),
        'rejected' => Document::where('status', 'rejected')->count(),
        'expired' => Document::where('expires', true)
                            ->where('expiry_date', '<', now())
                            ->count(),
    ];

    $categories = [
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

    return view('admin.documents.index', compact('documents', 'stats', 'categories'));
}

    /**
     * Admin view documents for a specific application
     */
    public function adminApplicationDocuments(Request $request, Application $application)
    {
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized access.');
    }

    // Get all documents for this application with relationships
    $query = Document::where('application_id', $application->id)
        ->with(['uploadedBy', 'reviewedBy', 'documentRequirement'])
        ->latest();

    // Filter by status if provided
    if ($request->has('status') && $request->status !== '') {
        $query->where('status', $request->status);
    }

    // Search by document name
    if ($request->has('search') && $request->search !== '') {
        $query->where('name', 'like', "%{$request->search}%");
    }

    // Filter by date range
    if ($request->has('date_from') && $request->date_from !== '') {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->has('date_to') && $request->date_to !== '') {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $documents = $query->paginate(20)->withQueryString();

    // Statistics for this application
    $stats = [
        'total' => Document::where('application_id', $application->id)->count(),
        'uploaded' => Document::where('application_id', $application->id)->where('status', 'uploaded')->count(),
        'under_review' => Document::where('application_id', $application->id)->where('status', 'under_review')->count(),
        'approved' => Document::where('application_id', $application->id)->where('status', 'approved')->count(),
        'rejected' => Document::where('application_id', $application->id)->where('status', 'rejected')->count(),
        'expired' => Document::where('application_id', $application->id)
                            ->where('expires', true)
                            ->where('expiry_date', '<', now())
                            ->count(),
    ];

    $categories = [
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

    return view('admin.documents.application', compact(
        'application',
        'documents',
        'stats',
        'categories'
    ));
}

}