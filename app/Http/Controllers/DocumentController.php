<?php

// app/Http/Controllers/DocumentController.php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Application;
use App\Models\DocumentRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{


   
public function index(Application $application)
{
    // Authorization - only the application owner can view their documents
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
    } if ($application->user_id !== auth()->id()) {
        abort(403, 'Unauthorized access to this application.');
    }

    // Get required documents for current stage
    $requiredDocuments = $application->getRequiredDocumentsForStage();
    
    // Get uploaded documents
    $uploadedDocuments = $application->documents()
        ->with(['uploadedBy', 'reviewedBy'])
        ->latest()
        ->get()
        ->groupBy('category');
    
    // Get pending document categories
    $uploadedCategories = $application->documents()
        ->where('status', '!=', 'rejected')
        ->pluck('category')
        ->unique()
        ->toArray();
    
    $pendingDocuments = array_diff($requiredDocuments, $uploadedCategories);
    
    // Document categories with their display names
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

public function show(Document $document)
    {
        Gate::authorize('view', $document);

        $document->load(['application.user', 'uploadedBy', 'reviewedBy', 'documentRequirement']);

        return view('documents.show', compact('document'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Document::class);

        $application = null;
        if ($request->application_id) {
            $application = Application::findOrFail($request->application_id);
            Gate::authorize('view', $application);
        }

        $documentRequirements = DocumentRequirement::active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('stage');

        return view('documents.create', compact('application', 'documentRequirements'));
    }

public function store(Request $request, Application $application)
{
    // Authorization
    if ($application->user_id !== auth()->id()) {
        abort(403, 'Unauthorized access to this application.');
    }
 \Log::info('Document Upload Request', [
        'files' => $request->hasFile('files') ? 'YES' : 'NO',
        'files_count' => $request->hasFile('files') ? count($request->file('files')) : 0,
        'documents' => $request->has('documents') ? 'YES' : 'NO',
        'all_input' => $request->except('files'),
        'file_names' => $request->hasFile('files') ? array_map(fn($f) => $f->getClientOriginalName(), $request->file('files')) : []
    ]);

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

        // Notify consultant if assigned
        if ($uploadedCount > 0 && $application->consultant_id) {
            \App\Models\Notification::create([
                'user_id' => $application->consultant_id,
                'type' => 'document_uploaded',
                'title' => 'New Documents Uploaded',
                'message' => "{$uploadedCount} new document(s) uploaded for application #{$application->application_number}",
                'action_url' => route('consultant.applications.show', $application),
                'priority' => 'normal',
            ]);
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

public function download(Request $request, Application $application = null, Document $document)
{
    \Log::info('Download Attempt', [
        'document_id' => $document->id,
        'application_id' => $document->application_id,
        'file_path' => $document->file_path,
        'exists' => Storage::disk('private')->exists($document->file_path),
        'user_id' => auth()->id(),
        'user_type' => auth()->user()->type,
        'application_consultant_id' => $document->application ? $document->application->consultant_id : null
    ]);

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

    public function bulkDownload(Request $request, Application $application)
    {
        // Authorization check
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

        // Create ZIP file
        $zip = new \ZipArchive();
        $zipFileName = 'documents_' . $application->application_number . '_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Create temp directory if it doesn't exist
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

            // Log the bulk download
            \App\Models\AuditLog::log('bulk_document_download', $application, 'Downloaded ' . $documents->count() . ' documents');

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Failed to create ZIP file');
    }
    public function consultantDownload(Document $document)
    {
        // Check consultant has access
        if (auth()->user()->isConsultant() && $document->application->consultant_id !== auth()->id()) {
            abort(403);
        }

        // Rest of download logic...
        if (!Storage::disk('private')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        $document->increment('download_count');

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_filename
        );
    }
    public function review(Request $request, Document $document)
        {
            Gate::authorize('review', $document);

            $validated = $request->validate([
                'status' => 'required|in:approved,rejected',
                'review_notes' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();
            try {
                $document->update(array_merge($validated, [
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]));

                // Create notification for applicant
                $document->application->notifications()->create([
                    'user_id' => $document->application->user_id,
                    'type' => 'document_reviewed',
                    'title' => 'Document Review Completed',
                    'message' => "Your document '{$document->name}' has been {$validated['status']}.",
                    'priority' => $validated['status'] === 'rejected' ? 'high' : 'normal',
                    'action_url' => route('documents.show', $document),
                ]);

                // Log the review
                \App\Models\AuditLog::log('document_reviewed', $document, "Document {$validated['status']}", [
                    'status' => $validated['status'],
                    'review_notes' => $validated['review_notes']
                ]);

                DB::commit();

                return back()->with('success', 'Document review completed!');

            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', 'Failed to complete review. Please try again.');
            }
        }

    public function destroy(Application $application, Document $document)
    {
        // Authorization based on role
        if (auth()->user()->isApplicant()) {
            // Applicants can only delete their own docs if uploaded or rejected
            if ($application->user_id !== auth()->id() || $document->application_id !== $application->id) {
                abort(403, 'Unauthorized access.');
            }
            
            if (!in_array($document->status, ['uploaded', 'rejected'])) {
                return back()->with('error', 'Cannot delete a document that is under review or approved.');
            }
        } elseif (auth()->user()->isConsultant()) {
            // Consultants can delete if they're assigned
            if ($application->consultant_id !== auth()->id()) {
                abort(403, 'You are not assigned to this application.');
            }
        } elseif (!auth()->user()->isAdmin()) {
            // Only admin, consultant, or applicant can delete
            abort(403, 'Unauthorized access.');
        }


            DB::beginTransaction();
            try {
                // Delete file from storage
                if (Storage::disk('private')->exists($document->file_path)) {
                    Storage::disk('private')->delete($document->file_path);
                }

                // Log the deletion before deleting the record
                \App\Models\AuditLog::log('document_deleted', $document, 'Document deleted');

                $document->delete();

                DB::commit();

                return back()->with('success', 'Document deleted successfully.');

            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', 'Failed to delete document. Please try again.');
            }
        }

        public function approve(Document $document)
    {
        // Only consultant or admin
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

            // Notify applicant
            \App\Models\Notification::create([
                'user_id' => $document->application->user_id,
                'type' => 'document_approved',
                'title' => 'Document Approved',
                'message' => "Your document '{$document->name}' has been approved.",
                'action_url' => route('applicant.documents.index', $document->application),
                'priority' => 'normal',
            ]);

            DB::commit();

            return back()->with('success', 'Document approved successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve document.');
        }
    }

    public function reject(Request $request, Document $document)
    {
        // Only consultant or admin
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

            // Notify applicant
            \App\Models\Notification::create([
                'user_id' => $document->application->user_id,
                'type' => 'document_rejected',
                'title' => 'Document Rejected',
                'message' => "Your document '{$document->name}' has been rejected. Please review the feedback and reupload.",
                'action_url' => route('applicant.documents.index', $document->application),
                'priority' => 'high',
            ]);

            DB::commit();

            return back()->with('success', 'Document rejected. Applicant has been notified.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject document.');
        }
    }

    public function pendingReview()
    {
        // Only consultant or admin
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

    if ($fileType === 'pdf') {
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"',
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control' => 'private, max-age=3600',
    ]);
}

}