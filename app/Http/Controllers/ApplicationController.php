<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Enums\ApplicationStatus;
use App\Models\User;
use App\Services\ApplicationStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    protected $statusService;

    public function __construct(ApplicationStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function create()
    {
        if (auth()->user()->hasActiveApplication()) {
            return redirect()
                ->route('applicant.applications.show', auth()->user()->getActiveApplication())
                ->with('warning', 'You already have an active application.');
        }

        return view('applicant.applications.create');
    }

    public function show(Application $application)
    {
        $user = auth()->user();
        
        $canView = false;
        
        if ($application->user_id === $user->id) {
            $canView = true;
        }
        
        if ($user->user_type === 'admin') {
            $canView = true;
        }
        
        if ($user->user_type === 'consultant' && $application->consultant_id === $user->id) {
            $canView = true;
        }
        
        if (!$canView) {
            abort(403, 'Unauthorized access to this application.');
        }
        
        $application->load(['user', 'consultant', 'documents', 'appointments']);
        
        if ($user->user_type === 'admin') {
            return view('admin.applications.show', compact('application'));
        }
        
        if ($user->user_type === 'consultant') {
            return view('consultant.applications.show', compact('application'));
        }
        
        return view('applicant.applications.show', compact('application'));
    }

    public function consultantIndex(Request $request)
    {
        $query = Application::with(['user', 'consultant'])
            ->where('consultant_id', auth()->id())
            ->when($request->search, function ($q, $search) {
                return $q->where(function($query) use ($search) {
                    $query->where('educator_first_name', 'like', "%{$search}%")
                          ->orWhere('educator_last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('application_number', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->stage, function ($q, $stage) {
                return $q->where('current_stage', $stage);
            });

        $applications = $query->latest()->paginate(15);

        return view('consultant.applications.index', compact('applications'));
    }

    public function store(Request $request)
    {
        Log::info('=== APPLICATION STORE STARTED ===');
        
        $isDraft = $request->boolean('is_draft');
        
        $rules = [
            'educator_first_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'educator_last_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'email' => $isDraft ? 'nullable|email|max:255' : 'required|email|max:255',
            'phone' => $isDraft ? 'nullable|string|max:20' : 'required|string|max:20',
            'address_line_1' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'city' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'province' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'postal_code' => $isDraft ? 'nullable|string|max:10' : 'required|string|max:10',
            'childcare_level' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|string',
            'childcare_education' => 'nullable|string',
            'home_residents_count' => 'nullable|integer|min:0',
            'home_residents_details' => 'nullable|string',
            'smoking_status' => 'nullable|in:no,yes_please_specify',
            'smoking_details' => 'nullable|string|max:255',
            'pets_details' => 'nullable|string|max:255',
            'current_operation_details' => 'nullable|string',
            'home_type' => 'nullable|in:apartment,duplex,house,townhouse',
            'home_ownership' => 'nullable|in:rent,own',
            'desired_start_date' => 'nullable|date',
            'motivation' => 'nullable|string',
            'why_spiced' => 'nullable|string',
            'education_philosophy' => 'nullable|string',
            'program_planning_process' => 'nullable|string',
        ];

        try {
            $validated = $request->validate($rules);
            
            $validated['has_criminal_record_check'] = $request->has('has_criminal_record_check') ? 1 : 0;
            $validated['has_first_aid_cpr'] = $request->has('has_first_aid_cpr') ? 1 : 0;
            $validated['has_pets'] = $request->has('has_pets') ? 1 : 0;
            $validated['comfortable_special_needs'] = $request->has('comfortable_special_needs') ? 1 : 0;
            $validated['fenced_backyard'] = $request->has('fenced_backyard') ? 1 : 0;
            $validated['currently_operating'] = $request->has('currently_operating') ? 1 : 0;
            $validated['evening_overnight_care'] = $request->has('evening_overnight_care') ? 1 : 0;
            
            $validated['status'] = $isDraft ? ApplicationStatus::DRAFT->value : ApplicationStatus::SUBMITTED->value;
            
            if (!$isDraft) {
                $validated['submitted_at'] = now();
            }

            DB::beginTransaction();

            $application = auth()->user()->applications()->create($validated);
            
            Log::info('Application created', ['id' => $application->id]);
            
            $application->updateCompletionPercentage();

            if (!$isDraft) {
                // Don't use statusService for new submissions to avoid duplicate notifications
                // The notification will be sent automatically via the Application model observer
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isDraft ? 'Draft saved successfully!' : 'Application submitted successfully!',
                    'application_id' => $application->id,
                    'redirect' => route('applicant.applications.show', $application)
                ], 200);
            }

            return redirect()
                ->route('applicant.applications.show', $application)
                ->with('success', $isDraft ? 'Draft saved successfully!' : 'Application created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Application creation failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save: ' . $e->getMessage()
                ], 500);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create application: ' . $e->getMessage());
        }
    }
    public function update(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$application->canBeEdited()) {
            return back()->with('error', 'This application cannot be edited at this stage.');
        }

        $isDraft = $request->boolean('is_draft');

        $rules = [
            'educator_first_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'educator_last_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'email' => $isDraft ? 'nullable|email|max:255' : 'required|email|max:255',
            'phone' => $isDraft ? 'nullable|string|max:20' : 'required|string|max:20',
            'address_line_1' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'city' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'province' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'postal_code' => $isDraft ? 'nullable|string|max:10' : 'required|string|max:10',
            'childcare_level' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|string',
            'childcare_education' => 'nullable|string',
            'home_residents_count' => 'nullable|integer|min:0',
            'home_residents_details' => 'nullable|string',
            'smoking_status' => 'nullable|in:no,yes_please_specify',
            'smoking_details' => 'nullable|string|max:255',
            'pets_details' => 'nullable|string|max:255',
            'current_operation_details' => 'nullable|string',
            'home_type' => 'nullable|in:apartment,duplex,house,townhouse',
            'home_ownership' => 'nullable|in:rent,own',
            'desired_start_date' => 'nullable|date',
            'motivation' => 'nullable|string',
            'why_spiced' => 'nullable|string',
            'education_philosophy' => 'nullable|string',
            'program_planning_process' => 'nullable|string',
        ];

        try {
            $validated = $request->validate($rules);

            $validated['has_criminal_record_check'] = $request->has('has_criminal_record_check') ? 1 : 0;
            $validated['has_first_aid_cpr'] = $request->has('has_first_aid_cpr') ? 1 : 0;
            $validated['has_pets'] = $request->has('has_pets') ? 1 : 0;
            $validated['comfortable_special_needs'] = $request->has('comfortable_special_needs') ? 1 : 0;
            $validated['fenced_backyard'] = $request->has('fenced_backyard') ? 1 : 0;
            $validated['currently_operating'] = $request->has('currently_operating') ? 1 : 0;
            $validated['evening_overnight_care'] = $request->has('evening_overnight_care') ? 1 : 0;

            $oldStatus = $application->status;

            if (!$isDraft && $oldStatus === ApplicationStatus::DRAFT->value) {
                $validated['status'] = ApplicationStatus::SUBMITTED->value;
                $validated['submitted_at'] = now();
            }

            DB::beginTransaction();

            $application->update($validated);
            $application->updateCompletionPercentage();

            if (!$isDraft && $oldStatus === ApplicationStatus::DRAFT->value) {
                $this->statusService->transitionTo(
                    $application,
                    ApplicationStatus::SUBMITTED,
                    "Application submitted by applicant"
                );
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isDraft ? 'Draft saved successfully!' : 'Application submitted successfully!',
                    'redirect' => route('applicant.applications.show', $application)
                ]);
            }

            return redirect()
                ->route('applicant.applications.show', $application)
                ->with('success', $isDraft ? 'Draft saved successfully!' : 'Application submitted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update failed', ['error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    public function edit(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this application.');
        }

        if (!$application->canBeEdited()) {
            return redirect()
                ->route('applicant.applications.show', $application)
                ->with('error', 'This application cannot be edited at this stage.');
        }

        return view('applicant.applications.edit', compact('application'));
    }

    public function submit(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$application->canBeSubmitted()) {
            return back()->with('error', 'Please complete all required fields before submitting.');
        }

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::SUBMITTED,
                "Application submitted by applicant"
            );

            DB::commit();

            return redirect()
                ->route('applicant.dashboard')
                ->with('success', 'Application submitted successfully! We will contact you soon.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to submit application. Please try again.');
        }
    }

    // Consultant actions
    public function reject(Request $request, Application $application)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $application->update([
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::REJECTED,
                $validated['rejection_reason']
            );

            DB::commit();

            return back()->with('success', 'Application rejected successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject application.');
        }
    }

    public function cancel(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::CANCELLED,
                $validated['cancellation_reason']
            );

            DB::commit();

            return redirect()
                ->route('applicant.dashboard')
                ->with('success', 'Application cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel application.');
        }
    }

    public function approve(Application $application)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can approve applications.');
        }

        DB::beginTransaction();
        try {
            $application->update([
                'approved_at' => now(),
                'license_expires_at' => now()->addYear(),
            ]);

            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::APPROVED,
                "Application approved by administrator"
            );

            DB::commit();

            return back()->with('success', 'Application approved successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve application.');
        }
    }

    // Trigger document collection
    public function enableDocumentUpload(Application $application)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($application->status !== ApplicationStatus::INITIAL_INSPECTION_COMPLETED->value) {
            return back()->with('error', 'Initial inspection must be completed first.');
        }

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::DOCUMENTS_PENDING,
                "Document upload enabled by consultant"
            );

            DB::commit();

            return back()->with('success', 'Document upload enabled for applicant.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to enable document upload.');
        }
    }

    /**
     * Admin view of all applications
     */
    public function adminIndex(Request $request)
    {
        $query = Application::with(['user', 'consultant'])
            ->when($request->search, function ($q, $search) {
                return $q->where(function($query) use ($search) {
                    $query->where('educator_first_name', 'like', "%{$search}%")
                        ->orWhere('educator_last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('application_number', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->stage, function ($q, $stage) {
                return $q->where('current_stage', $stage);
            })
            ->when($request->consultant, function ($q, $consultantId) {
                return $q->where('consultant_id', $consultantId);
            })
            ->when($request->filter === 'unassigned', function ($q) {
                return $q->whereNull('consultant_id')
                        ->whereIn('status', ['submitted', 'under_review']);
            })
            ->when($request->date_from, function ($q, $dateFrom) {
                return $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($q, $dateTo) {
                return $q->whereDate('created_at', '<=', $dateTo);
            });

        // Get sorting parameters
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $applications = $query->orderBy($sortBy, $sortOrder)->paginate(15);

        // Get consultants for filter dropdown
        $consultants = \App\Models\User::consultants()
            ->active()
            ->orderBy('name')
            ->get();

        // Get statistics for the current filter
        $stats = [
            'total' => (clone $query)->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'under_review' => (clone $query)->where('status', 'under_review')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'consultants', 'stats'));
    }
     public function assignConsultant(Request $request, Application $application)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'consultant_id' => 'required|exists:users,id',
        ]);

        $application->update([
            'consultant_id' => $validated['consultant_id'],
        ]);

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Consultant assigned successfully!');
    }
    public function adminShow(Application $application)
    {
        // Load all necessary relationships
        $application->load([
            'user',
            'consultant',
            'documents.uploadedBy',
            'documents.reviewedBy',
            'appointments.consultant',
            'appointments.applicant',
            'inspections.consultant',
            'inspections.appointment',
            'stages',
            'notifications',
            'auditLogs.user'
        ]);

        // Get statistics for this application
        $stats = [
            'total_documents' => $application->documents()->count(),
            'approved_documents' => $application->documents()->where('status', 'approved')->count(),
            'pending_documents' => $application->documents()->whereIn('status', ['uploaded', 'under_review'])->count(),
            'rejected_documents' => $application->documents()->where('status', 'rejected')->count(),
            
            'total_appointments' => $application->appointments()->count(),
            'upcoming_appointments' => $application->appointments()
                ->where('scheduled_at', '>', now())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->count(),
            'completed_appointments' => $application->appointments()->where('status', 'completed')->count(),
            
            'total_inspections' => $application->inspections()->count(),
            'passed_inspections' => $application->inspections()->where('overall_result', 'pass')->count(),
            'failed_inspections' => $application->inspections()->where('overall_result', 'fail')->count(),
        ];

        // Get all consultants for potential reassignment
        $consultants = User::consultants()
            ->whereHas('consultant', function ($query) {
                $query->where('employment_status', 'active');
            })
            ->get();

        // Get timeline/activity (audit logs + status changes)
        $timeline = $application->auditLogs()
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        // Get required documents for current stage
        $requiredDocuments = $application->getRequiredDocumentsForStage();
        
        // Get uploaded document categories
        $uploadedCategories = $application->documents()
            ->where('status', '!=', 'rejected')
            ->pluck('category')
            ->unique()
            ->toArray();
        
        // Calculate missing documents
        $missingDocuments = array_diff($requiredDocuments, $uploadedCategories);

        // Get next scheduled appointment
        $nextAppointment = $application->appointments()
            ->where('scheduled_at', '>', now())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('scheduled_at')
            ->first();

        // Get latest inspection
        $latestInspection = $application->inspections()
            ->latest('conducted_at')
            ->first();

        return view('admin.applications.show', compact(
            'application',
            'stats',
            'consultants',
            'timeline',
            'requiredDocuments',
            'uploadedCategories',
            'missingDocuments',
            'nextAppointment',
            'latestInspection'
        ));
    }

  /**
 * Show audit log for an application
 */
    public function auditLog(Application $application)
    {
        // Authorization check
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can view audit logs.');
        }

        // Load the application with necessary relationships
        $application->load(['user', 'consultant']);

        // Apply filters from request
        $query = $application->auditLogs()->with('user');

        if (request('action')) {
            $query->where('action', request('action'));
        }

        if (request('category')) {
            $query->where('category', request('category'));
        }

        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        // Get paginated audit logs
        $auditLogs = $query->latest()->paginate(50);

        // Get statistics - Fixed to use correct column names
        $stats = [
            'total_logs' => $application->auditLogs()->count(),
            'status_changes' => $application->auditLogs()
                ->where('action', 'like', '%status%')
                ->count(),
            'document_actions' => $application->auditLogs()
                ->where('category', 'document_management')
                ->count(),
            'appointment_actions' => $application->auditLogs()
                ->where('category', 'appointment')
                ->count(),
            'inspection_actions' => $application->auditLogs()
                ->where('category', 'inspection')
                ->count(),
        ];

        // Get unique actions for filtering
        $actions = $application->auditLogs()
            ->select('action')
            ->distinct()
            ->pluck('action')
            ->sort();

        // Get unique categories for filtering
        $categories = $application->auditLogs()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort();

        return view('admin.applications.audit-log', compact(
            'application',
            'auditLogs',
            'stats',
            'actions',
            'categories'
        ));
    }

}