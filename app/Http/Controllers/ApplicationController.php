<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Enums\ApplicationStatus;
use App\Models\User;
use App\Services\ApplicationStatusService;
use App\Models\Consultant;
use App\Models\PostalCodeRange;
use App\Mail\ConsultantAssigned;
use App\Mail\ApplicationSubmitted;
use App\Models\Notification;
use App\Models\DocumentRequirement;
use App\Mail\RequiredDocumentsSet;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class ApplicationController extends Controller
{
    protected $statusService;

    public function __construct(ApplicationStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    /**
     * Applicant view of their own applications
     */
    public function create()
    {
        if (auth()->user()->hasActiveApplication()) {
            return redirect()
                ->route('applicant.applications.show', auth()->user()->getActiveApplication())
                ->with('warning', 'You already have an active application.');
        }

        return view('applicant.applications.create');
    }

    /**
     * Show anonymous application form (public access)
     */
    public function createAnonymous()
    {
        return view('applications.anonymous-create');
    }


   public function show(Application $application)
{
    $user = auth()->user();
    
    $canView = false;
    
    if ($application->user_id == $user->id) {
        $canView = true;
    }
    
    if ($user->user_type == 'admin') {
        $canView = true;
    }
    
    if ($user->user_type == 'consultant' && $application->consultant_id == $user->id) {
        $canView = true;
    }
    
    if (!$canView) {
        abort(403, 'Unauthorized access to this application.');
    }
    
    // Eager-load related data
    $application->load([
        'user',
        'consultant',
        'documents',
        'stages',
        'appointments' => function ($q) {
            $q->whereIn('status', ['scheduled', 'confirmed'])
              ->where('scheduled_at', '>', now())
              ->orderBy('scheduled_at');
        }
    ]);
    
    // Load the educator profile (only needed for consultant & applicant views)
    $profile = $application->user->educatorProfile ?? null;  // assuming relationship exists

    if ($user->user_type == 'admin') {
        return view('admin.applications.show', compact('application'));
    }
    
    if ($user->user_type == 'consultant') {
        // Validate workflow prerequisites - prevent skipping steps
        $currentStatus = ApplicationStatus::tryFrom($application->status);
        if ($currentStatus) {
            $errorMessage = $this->validateWorkflowPrerequisites($application, $currentStatus);
            if ($errorMessage) {
                return redirect()->route('consultant.applications.show', $application)
                    ->with('error', $errorMessage);
            }
        }

        $documentRequirements = DocumentRequirement::where('is_active', true)
            ->where('stage', 'document_submission')
            ->orderBy('sort_order')
            ->get();
            
        Log::debug('Document Requirements fetched for application', [
            'application_id' => $application->id,
            'count' => $documentRequirements->count(),
        ]);
        
        // Pass $profile to the view
        return view('consultant.applications.show', compact(
            'application',
            'documentRequirements',
            'profile'               // ← added
        ));
    }
    
    // For applicant view
    $application->load(['inspections.consultant']);
    
    return view('applicant.applications.show', compact('application', 'profile'));
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

    // store() method 
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
            'postal_code' => $isDraft ? 'nullable|string|max:10' : ['required', 'string', 'max:10', 'regex:/^[A-Z]\d[A-Z] ?\d[A-Z]\d$/i'],
            'childcare_level' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|string',
            'childcare_education' => 'nullable|string',
            'home_residents_count' => 'nullable|integer|min:0',
            'home_residents_details' => 'nullable|string',
            'smoking_status' => 'nullable|in:no,yes_please_specify',
            'smoking_details' => 'nullable|string|max:255',
            'pets_details' => 'nullable|string|max:255',
            'current_operation_details' => $isDraft ? 'nullable|string' : 'required|string',
            'home_type' => 'nullable|in:apartment,duplex,house,townhouse',
            'home_ownership' => 'nullable|in:rent,own',
            'desired_start_date' => 'nullable|date',
            'motivation' => 'nullable|string',
            'why_spiced' => 'nullable|string',
            'education_philosophy' => 'nullable|string',
            'program_planning_process' => 'nullable|string',
            'legacy_import' => $isLegacy ?? false,
            'workflow_concluded' => $isLegacy ?? false,
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
            
            if (!$isDraft) {
                // Auto-assign consultant based on postal code
                $consultant = $this->assignConsultantByPostalCode($application->postal_code);
                if ($consultant) {
                    $application->update(['consultant_id' => $consultant->user_id]);
                    
                    // Reload the consultant relationship
                    $application->load('consultant');
                    
                    Log::info('Consultant assigned', [
                        'application_id' => $application->id, 
                        'consultant_id' => $consultant->user_id
                    ]);
                    
                    \App\Models\AuditLog::log(
                        'consultant_assigned', 
                        $application, 
                        "Consultant assigned to application: {$consultant->user->name}"
                    );
                    
                    // Send notifications to consultant
                    $this->notifyConsultantAssignment($application);
                } else {
                    Log::warning('No available consultant found for postal code', [
                        'postal_code' => $application->postal_code
                    ]);
                    \App\Models\AuditLog::log(
                        'consultant_assignment_failed', 
                        $application, 
                        "No consultant available for postal code: {$application->postal_code}"
                    );
                }
            }
            
            $application->updateCompletionPercentage();
            if (!$isDraft) {
                // Trigger notification via observer (already in your model)
            }
            DB::commit();
            
            // Send confirmation email to applicant when application is submitted (not draft)
            if (!$isDraft) {
                try {
                    Mail::to($application->user->email)->send(new ApplicationSubmitted($application));
                    Log::info('Application submission confirmation email sent', [
                        'application_id' => $application->id,
                        'email' => $application->user->email
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send application submission confirmation email', [
                        'application_id' => $application->id,
                        'email' => $application->user->email ?? 'N/A',
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the request if email fails
                }
            }
            
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
            return back()->withInput()->with('error', 'Failed to create application: ' . $e->getMessage());
        }
    }

        /**
     * Store anonymous application (public access)
     */
    public function storeAnonymous(Request $request)
    {
        $rules = [
            'educator_first_name' => 'required|string|max:255',
            'educator_last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:applications,email',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => ['required', 'string', 'max:10', 'regex:/^[A-Z]\d[A-Z] ?\d[A-Z]\d$/i'],
            'childcare_level' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|string',
            'childcare_education' => 'nullable|string',
            'home_residents_count' => 'nullable|integer|min:0',
            'home_residents_details' => 'nullable|string',
            'smoking_status' => 'nullable|in:no,yes_please_specify',
            'smoking_details' => 'nullable|string|max:255',
            'pets_details' => 'nullable|string|max:255',
            'current_operation_details' => 'required|string',
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
            
            // Handle checkboxes
            $validated['has_criminal_record_check'] = $request->has('has_criminal_record_check') ? 1 : 0;
            $validated['has_first_aid_cpr'] = $request->has('has_first_aid_cpr') ? 1 : 0;
            $validated['has_pets'] = $request->has('has_pets') ? 1 : 0;
            $validated['comfortable_special_needs'] = $request->has('comfortable_special_needs') ? 1 : 0;
            $validated['fenced_backyard'] = $request->has('fenced_backyard') ? 1 : 0;
            $validated['currently_operating'] = $request->has('currently_operating') ? 1 : 0;
            $validated['evening_overnight_care'] = $request->has('evening_overnight_care') ? 1 : 0;
            
            // Anonymous application is immediately submitted (no draft)
            $validated['status'] = ApplicationStatus::SUBMITTED->value;
            $validated['submitted_at'] = now();
            $validated['anonymous_token'] = Str::random(64);
            $validated['user_id'] = null; // No user yet
            
            // Set workflow flags - anonymous applications are NOT legacy imports
            $validated['legacy_import'] = false;
            $validated['workflow_concluded'] = false;
            
            DB::beginTransaction();
            
            // Create application
            $application = Application::create($validated);
            
            // Auto-assign consultant based on postal code
            $consultant = $this->assignConsultantByPostalCode($application->postal_code);
            if ($consultant) {
                $application->update(['consultant_id' => $consultant->user_id]);
                $application->load('consultant');
                
                Log::info('Consultant assigned to anonymous application', [
                    'application_id' => $application->id, 
                    'consultant_id' => $consultant->user_id
                ]);
                
                \App\Models\AuditLog::log(
                    'consultant_assigned', 
                    $application, 
                    "Consultant assigned to anonymous application: {$consultant->user->name}"
                );
                
                // Send notifications to consultant
                $this->notifyConsultantAssignment($application);
            }
            
            $application->updateCompletionPercentage();
            
            \App\Models\AuditLog::log(
                'application_submitted',
                $application,
                'Anonymous application submitted'
            );
            
            DB::commit();
            
            // Send confirmation email to applicant
            try {
                Mail::to($application->email)->send(new ApplicationSubmitted($application));
                Log::info('Application submission confirmation email sent', [
                    'application_id' => $application->id,
                    'email' => $application->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send application submission confirmation email', [
                    'application_id' => $application->id,
                    'email' => $application->email,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the request if email fails
            }
            
            return view('applications.anonymous-submitted', compact('application'))
                ->with('success', 'Application submitted successfully! We will contact you soon at ' . $application->email);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Anonymous application validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Anonymous application creation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    /**
     * Submit application (convert from draft to submitted)
     */
    public function submit(Application $application)
    {   
        if ($application->user_id != auth()->id()) {
            abort(403);
        }
        
        if (!$application->canBeSubmitted()) {
            return back()->with('error', 'Please complete all required fields before submitting.');
        }
        
        DB::beginTransaction();
        try {
            // Auto-assign consultant if none assigned (e.g., draft to submitted)
            if (!$application->consultant_id) {
                $consultant = $this->assignConsultantByPostalCode($application->postal_code);
                if ($consultant) {
                    $application->update(['consultant_id' => $consultant->user_id]);
                    
                    // Reload the consultant relationship
                    $application->load('consultant');
                    
                    Log::info('Consultant assigned', [
                        'application_id' => $application->id, 
                        'consultant_id' => $consultant->user_id
                    ]);
                    
                    \App\Models\AuditLog::log(
                        'consultant_assigned', 
                        $application, 
                        "Consultant assigned to application: {$consultant->user->name}"
                    );
                    
                    // Send notifications to consultant
                    $this->notifyConsultantAssignment($application);
                } else {
                    Log::warning('No available consultant found for postal code', [
                        'postal_code' => $application->postal_code
                    ]);
                    \App\Models\AuditLog::log(
                        'consultant_assignment_failed', 
                        $application, 
                        "No consultant available for postal code: {$application->postal_code}"
                    );
                }
            }

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
            Log::error('Application submission failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to submit application. Please try again.');
        }
    }

    public function update(Request $request, Application $application)
    {
        if ($application->user_id != auth()->id()) {
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
            'current_operation_details' => $isDraft ? 'nullable|string' : 'required|string',
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

            if (!$isDraft && $oldStatus == ApplicationStatus::DRAFT->value) {
                $validated['status'] = ApplicationStatus::SUBMITTED->value;
                $validated['submitted_at'] = now();
            }

            DB::beginTransaction();

            $application->update($validated);
            $application->updateCompletionPercentage();

            if (!$isDraft && $oldStatus == ApplicationStatus::DRAFT->value) {
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
        if ($application->user_id != auth()->id()) {
            abort(403, 'Unauthorized access to this application.');
        }

        if (!$application->canBeEdited()) {
            return redirect()
                ->route('applicant.applications.show', $application)
                ->with('error', 'This application cannot be edited at this stage.');
        }

        return view('applicant.applications.edit', compact('application'));
    }

    /**
     * Assign a consultant based on the application's postal code.
     *
     * @param string $postalCode
     * @return Consultant|null
     */
    protected function assignConsultantByPostalCode($postalCode)
    {
        // Normalize postal code and extract FSA (first 3 characters)
        $prefix = strtoupper(substr(str_replace(' ', '', $postalCode), 0, 3));
        
        Log::info('Attempting consultant assignment', [
            'postal_code' => $postalCode,
            'prefix' => $prefix
        ]);
        
        // Find region by postal code prefix
        $region = PostalCodeRange::where('prefix', $prefix)->first();
        
        if (!$region) {
            Log::warning('No region found for postal code prefix', ['prefix' => $prefix]);
            return null;
        }
        
        Log::info('Found postal code range', [
            'prefix' => $prefix,
            'region_id' => $region->region_id,
            'region_name' => $region->region->name ?? 'Unknown'
        ]);
        
        // Find available consultants in the region
        $consultant = Consultant::acceptingApplications()
            ->byPostalCode($postalCode)
            ->whereColumn('active_applications', '<', 'max_concurrent_applications')
            ->orderBy('active_applications', 'asc') // Prefer least busy consultant
            ->first();
        
        if ($consultant) {
            Log::info('Found consultant by postal code match', [
                'consultant_id' => $consultant->user_id,
                'postal_code' => $postalCode,
                'prefix' => $prefix
            ]);
            // Update consultant's workload metrics
            $consultant->updateWorkloadMetrics();
            return $consultant;
        }
        
        // Log why no consultant was found
        $allConsultants = Consultant::byPostalCode($postalCode)->get();
        $acceptingConsultants = Consultant::acceptingApplications()->byPostalCode($postalCode)->get();
        $availableConsultants = Consultant::acceptingApplications()
            ->byPostalCode($postalCode)
            ->whereColumn('active_applications', '<', 'max_concurrent_applications')
            ->get();
        
        Log::warning('No consultant found for postal code', [
            'postal_code' => $postalCode,
            'prefix' => $prefix,
            'region_id' => $region->region_id,
            'total_consultants_in_region' => $allConsultants->count(),
            'accepting_consultants' => $acceptingConsultants->count(),
            'available_consultants' => $availableConsultants->count(),
            'consultant_details' => $allConsultants->map(function($c) {
                return [
                    'id' => $c->user_id,
                    'accepts_new_applications' => $c->accepts_new_applications,
                    'employment_status' => $c->employment_status,
                    'active_applications' => $c->active_applications,
                    'max_concurrent_applications' => $c->max_concurrent_applications
                ];
            })->toArray()
        ]);
        
        return null;
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
        if ($application->user_id != auth()->id() && !auth()->user()->isAdmin()) {
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
    if (
        !auth()->user()->isAdmin() &&
        !auth()->user()->isConsultant()
    ) {
        abort(403, 'Only administrators or consultants can approve applications.');
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
            auth()->user()->isConsultant()
                ? 'Application approved by consultant'
                : 'Application approved by administrator'
        );

        // Generate certificate automatically
        try {
            $certificateService = new \App\Services\CertificateService();
            $certificate = $certificateService->generateCertificate($application, auth()->id());

            \App\Models\AuditLog::log(
                'certificate_generated',
                $application,
                "Certificate generated: {$certificate->certificate_number}"
            );

            // Send notification to applicant with certificate
            $this->notifyApplicantOfApproval($application, $certificate);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate certificate during approval', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't fail the approval if certificate generation fails
            // Admin can manually generate it later
        }

        DB::commit();

        return back()->with('success', 'Application approved successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Failed to approve application', [
            'application_id' => $application->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to approve application: ' . $e->getMessage());
    }
    }

    /**
     * Notify applicant of approval with certificate
     */
    protected function notifyApplicantOfApproval($application, $certificate)
    {
        try {
            // In-app notification
            \App\Models\Notification::create([
                'user_id' => $application->user_id,
                'application_id' => $application->id,
                'type' => 'application_approved',
                'title' => 'Application Approved!',
                'message' => "Congratulations! Your application has been approved. Your certificate number is {$certificate->certificate_number}.",
                'priority' => 'high',
                'action_url' => route('applicant.certificates.show', $certificate->id),
                'action_text' => 'View Certificate',
            ]);
    
            // Send email with certificate attachment (uncomment when mailable is created)
            // Mail::to($application->user->email)
            //     ->send(new ApplicationApproved($application, $certificate));
    
        } catch (\Exception $e) {
            Log::error('Failed to notify applicant of approval', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
        }
    }


    // Trigger document collection
   public function enableDocumentUpload(Request $request, Application $application)
{
    $user = auth()->user();

    if ($user->isConsultant() && $application->consultant_id != $user->id) {
        abort(403, 'You are not assigned to this application.');
    } elseif (!$user->isConsultant() && !$user->isAdmin()) {
        abort(403, 'Unauthorized access.');
    }

    $application->refresh();

    \Log::info('Attempting to enable document upload', [
        'application_id' => $application->id,
        'current_status' => $application->status,
        'workflow_concluded' => $application->workflow_concluded,
    ]);

    try {
        // Legacy/concluded workflow: allow uploads but don't update application status
        if ($application->workflow_concluded) {
            \Log::info('Legacy or concluded application – document upload enabled without status change', [
                'application_id' => $application->id,
            ]);

            return back()->with('success', 'Document uploads enabled (legacy/concluded workflow).');
        }

        // Normal workflow – transition status if allowed
        $currentStatusEnum = \App\Enums\ApplicationStatus::from($application->status);
        $targetStatusEnum = \App\Enums\ApplicationStatus::DOCUMENTS_PENDING;

        if (!$currentStatusEnum->canTransitionTo($targetStatusEnum)) {
            \Log::warning('Document upload transition not allowed', [
                'application_id' => $application->id,
                'current_status' => $application->status,
            ]);
            return back()->with('error', "Cannot enable document uploads from current status: {$currentStatusEnum->label()}");
        }

        $this->statusService->transitionTo(
            $application,
            $targetStatusEnum,
            'Document upload enabled by consultant'
        );

        $application->refresh();

        \Log::info('Document upload enabled successfully', [
            'application_id' => $application->id,
            'new_status' => $application->status
        ]);

        return back()->with('success', 'Document uploads enabled for this application.');
    } catch (\Exception $e) {
        \Log::error('Failed to enable document upload', [
            'application_id' => $application->id,
            'error' => $e->getMessage(),
        ]);
        return back()->with('error', 'Failed to enable document uploads. Please try again.');
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
            ->when($request->filter == 'unassigned', function ($q) {
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

    /**
     * Assign consultant to application (Admin only)
     */
    public function assignConsultant(Request $request, Application $application)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can assign consultants.');
        }

        $validated = $request->validate([
            'consultant_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // Get the old consultant ID for logging
            $oldConsultantId = $application->consultant_id;
            
            // Update application with new consultant
            $application->update([
                'consultant_id' => $validated['consultant_id'],
            ]);

            // Reload the relationship to get fresh consultant data
            $application->load('consultant');

            // Create audit log
            if ($oldConsultantId) {
                \App\Models\AuditLog::log(
                    'consultant_reassigned',
                    $application,
                    "Consultant reassigned by admin to: {$application->consultant->name}"
                );
            } else {
                \App\Models\AuditLog::log(
                    'consultant_assigned',
                    $application,
                    "Consultant manually assigned by admin: {$application->consultant->name}"
                );
            }

            // Update consultant's workload metrics
            if ($application->consultant?->consultant) {
                    $application->consultant->consultant->updateWorkloadMetrics();
                }

            // Send notifications to the newly assigned consultant
            $this->notifyConsultantAssignment($application);

            DB::commit();

            return redirect()
                ->route('admin.applications.index')
                ->with('success', 'Consultant assigned successfully! Notification sent.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to assign consultant', [
                'application_id' => $application->id,
                'consultant_id' => $validated['consultant_id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to assign consultant: ' . $e->getMessage());
        }
    }

    /**
     * Send notifications when consultant is assigned to application
     */
    protected function notifyConsultantAssignment(Application $application)
    {
        if (!$application->consultant_id) {
            Log::warning('Cannot send notification: No consultant assigned', [
                'application_id' => $application->id
            ]);
            return;
        }

        try {
            // Reload the consultant relationship (consultant is a User)
            $application->load('consultant');
            
            if (!$application->consultant) {
                Log::error('Consultant relationship not found', [
                    'application_id' => $application->id,
                    'consultant_id' => $application->consultant_id
                ]);
                return;
            }

            // Create in-app notification
            Notification::create([
                'user_id' => $application->consultant_id, // The consultant's user_id
                'application_id' => $application->id,
                'type' => 'consultant_assigned',
                'title' => 'New Application Assigned',
                'message' => "Application #{$application->application_number} from {$application->educator_first_name} {$application->educator_last_name} has been assigned to you.",
                'priority' => 'high',
                'action_url' => route('consultant.applications.show', $application->id),
                'action_text' => 'View Application',
                'requires_action' => true,
            ]);

            // Send email notification
            Mail::to($application->consultant->email)
                ->send(new ConsultantAssigned($application));

            Log::info('Consultant assignment notifications sent successfully', [
                'application_id' => $application->id,
                'consultant_id' => $application->consultant_id,
                'consultant_email' => $application->consultant->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send consultant assignment notifications', [
                'application_id' => $application->id,
                'consultant_id' => $application->consultant_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception - notification failure shouldn't stop the assignment
        }
    }

    /**
     * Admin view of a single application
     */
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
        
        // Get uploaded document requirement IDs
        $uploadedRequirementIds = $application->documents()
            ->where('status', '!=', 'rejected')
            ->whereNotNull('document_requirement_id')
            ->pluck('document_requirement_id')
            ->unique()
            ->toArray();
        
        // Calculate missing documents (requirements not yet uploaded)
        $missingDocuments = $requiredDocuments->filter(function($req) use ($uploadedRequirementIds) {
            return !in_array($req->id, $uploadedRequirementIds);
        });

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
            'uploadedRequirementIds',
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

    /**
     * Set required documents for an application based on current stage
     */


    public function setRequiredDocuments(Request $request, Application $application)
    {
        $user = auth()->user();

        if (
            !$user->isAdmin() &&
            !($user->isConsultant() && $application->consultant_id == $user->id)
        ) {
            abort(403, 'You are not authorized to set required documents for this application.');
        }

        $validated = $request->validate([
            'required_documents' => 'nullable|array',
            'required_documents.*' => 'exists:document_requirements,id',
        ]);

        try {
            // Reset requirements
            $application->documentRequirements()->sync(
                collect($validated['required_documents'] ?? [])
                    ->mapWithKeys(fn ($id) => [$id => ['is_required' => true]])
                    ->toArray()
            );

            //Automatically enable upload when documents are set
            if (
                !$application->workflow_concluded &&
                !empty($validated['required_documents'])
            ) {
                $currentStatus = \App\Enums\ApplicationStatus::from($application->status);
                $targetStatus  = \App\Enums\ApplicationStatus::DOCUMENTS_PENDING;

                if ($currentStatus->canTransitionTo($targetStatus)) {
                    $this->statusService->transitionTo(
                        $application,
                        $targetStatus,
                        'Required documents set by consultant – uploads enabled'
                    );
                }
            }

            // Notify applicant
            if (!$application->workflow_concluded) {
                $this->notifyApplicantDocumentsSet(
                    $application,
                    $validated['required_documents'] ?? []
                );
            }

            return back()->with('success', 'Required documents set. Upload is now enabled.');
        } catch (\Throwable $e) {
            \Log::error('Failed to set required documents', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update required documents. Please try again.');
        }
    }

    protected function notifyApplicantDocumentsSet(Application $application, array $requiredDocuments)
    {
        $requirements = DocumentRequirement::whereIn('id', $requiredDocuments)->pluck('name')->toArray();
        $message = empty($requirements)
            ? 'No specific documents are required at this time.'
            : 'Please upload the following required documents: ' . implode(', ', $requirements) . '.';

        Notification::create([
            'user_id' => $application->user_id,
            'application_id' => $application->id,
            'type' => 'required_documents_set',
            'title' => 'Required Documents Updated',
            'message' => $message,
            'priority' => 'high',
            'action_url' => route('applicant.documents.index', $application),
        ]);

        try {
            Mail::to($application->user->email)->send(
                new RequiredDocumentsSet($application, $message)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send required documents email', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
        }
    }

     public function saveConsultantNotes(Request $request, Application $application)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    
        $request->validate([
            'admin_notes' => 'nullable|string'
        ]);
    
        $application->admin_notes = $request->admin_notes;
        $application->save();
    
        return back()->with('success', 'Notes saved successfully.');
    }

      /**
     * Schedule Final Inspection
     */
    public function scheduleFinalInspection(Request $request, Application $application)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($application->consultant_id != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You are not assigned to this application.');
        }

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::FINAL_INSPECTION_SCHEDULED,
                'Final inspection scheduled by ' . auth()->user()->name
            );

            DB::commit();

            return back()->with('success', 'Final inspection scheduled successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to schedule final inspection', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to schedule final inspection: ' . $e->getMessage());
        }
    }

    /**
     * Complete Final Inspection
     */
    public function completeFinalInspection(Request $request, Application $application)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($application->consultant_id != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You are not assigned to this application.');
        }

        $request->validate([
            'passed' => 'required|boolean',
            'inspection_notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $newStatus = $request->passed 
                ? ApplicationStatus::FINAL_INSPECTION_PASSED
                : ApplicationStatus::FINAL_INSPECTION_FAILED;

            $this->statusService->transitionTo(
                $application,
                $newStatus,
                $request->inspection_notes
            );

            DB::commit();

            $message = $request->passed 
                ? 'Final inspection marked as passed!'
                : 'Final inspection marked as failed.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to complete final inspection', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to complete final inspection: ' . $e->getMessage());
        }
    }

    /**
     * Activate Dayhome (Move from Approved to Active)
     */
    public function activateDayhome(Request $request, Application $application)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isConsultant()) {
            abort(403, 'Only administrators and consultants can activate dayhomes.');
        }

        if ($application->status != ApplicationStatus::APPROVED->value) {
            return back()->with('error', 'Only approved applications can be activated.');
        }

        DB::beginTransaction();
        try {
            // Transition to ACTIVE status
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::ACTIVE,
                "Dayhome activated by " . auth()->user()->name
            );

            // Update application with activation details
            $application->update([
                'activated_at' => now(),
                'next_compliance_inspection_due' => now()->addMonths(6), // Default 6 months
            ]);

            DB::commit();

            return back()->with('success', 'Dayhome activated successfully! It is now operational.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to activate dayhome', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to activate dayhome: ' . $e->getMessage());
        }
    }

    /**
     * Schedule Compliance Inspection
     */
    public function scheduleComplianceInspection(Request $request, Application $application)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isConsultant()) {
            abort(403, 'Only administrators and consultants can schedule compliance inspections.');
        }

        if ($application->status !== ApplicationStatus::ACTIVE->value && 
            $application->status !== ApplicationStatus::COMPLIANCE_INSPECTION_DUE->value) {
            return back()->with('error', 'Only active dayhomes or those with due inspections can have compliance inspections scheduled.');
        }

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::COMPLIANCE_INSPECTION_SCHEDULED,
                'Compliance inspection scheduled by ' . auth()->user()->name
            );

            DB::commit();

            return back()->with('success', 'Compliance inspection scheduled successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to schedule compliance inspection', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to schedule compliance inspection: ' . $e->getMessage());
        }
    }

    /**
     * Complete Compliance Inspection
     */
    public function completeComplianceInspection(Request $request, Application $application)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'passed' => 'required|boolean',
            'inspection_notes' => 'nullable|string|max:1000',
            'next_inspection_months' => 'nullable|integer|min:1|max:24'
        ]);

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::COMPLIANCE_INSPECTION_COMPLETED,
                $request->inspection_notes
            );

            // Determine next status based on inspection result
            if ($request->passed) {
                $nextStatus = ApplicationStatus::ACTIVE;
                $message = 'Compliance inspection passed!';
                
                // Update next compliance due date
                $months = $request->next_inspection_months ?? 6;
                $application->update([
                    'next_compliance_inspection_due' => now()->addMonths($months),
                    'last_compliance_inspection_at' => now(),
                ]);
            } else {
                $nextStatus = ApplicationStatus::SUSPENDED;
                $message = 'Compliance inspection failed. Dayhome suspended.';
            }

            // Transition to next status
            $this->statusService->transitionTo(
                $application,
                $nextStatus,
                $request->inspection_notes
            );

            DB::commit();

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to complete compliance inspection', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to complete compliance inspection: ' . $e->getMessage());
        }
    }

    /**
     * Validate that workflow prerequisites are met before allowing access to certain sections
     * Returns error message if validation fails, null if validation passes
     */
    protected function validateWorkflowPrerequisites(Application $application, ApplicationStatus $currentStatus): ?string
    {
        // After meet and greet is completed, initial inspection must be scheduled/completed before accessing documents
        if ($currentStatus === ApplicationStatus::MEET_AND_GREET_COMPLETED) {
            $hasInitialInspection = $application->appointments()
                ->whereIn('type', ['initial_inspection'])
                ->whereIn('status', ['scheduled', 'confirmed', 'completed'])
                ->exists();

            if (!$hasInitialInspection) {
                return 'Please schedule an Initial Inspection before proceeding to the Documents section. Follow the proper workflow: Meet & Greet → Initial Inspection → Documents.';
            }
        }

        // After initial inspection is completed, documents must be submitted before scheduling second inspection
        if ($currentStatus === ApplicationStatus::INITIAL_INSPECTION_COMPLETED) {
            $hasDocumentsSubmitted = in_array($application->status, [
                ApplicationStatus::DOCUMENTS_SUBMITTED->value,
                ApplicationStatus::DOCUMENTS_APPROVED->value,
            ]);

            if (!$hasDocumentsSubmitted && request()->routeIs('consultant.applications.show')) {
                // Allow access to documents section, but warn about the workflow
                // This is just a warning, not a blocker
            }
        }

        return null;
    }

    /**
     * Suspend Dayhome
     */
    public function suspendDayhome(Request $request, Application $application)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isConsultant()) {
            abort(403, 'Only administrators and consultants can suspend dayhomes.');
        }

        if ($application->status !== ApplicationStatus::COMPLIANCE_INSPECTION_COMPLETED->value) {
            return back()->with('error', 'Only dahyhomes that failed compliance check can be suspended.');
        }

        $request->validate([
            'suspension_reason' => 'required|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::SUSPENDED,
                $request->suspension_reason
            );

            // Update suspension timestamp
            $application->update(['suspended_at' => now()]);

            DB::commit();

            return back()->with('success', 'Dayhome suspended successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to suspend dayhome', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to suspend dayhome: ' . $e->getMessage());
        }
    }

    /**
     * Reinstate Dayhome (from Suspended to Active)
     */
   public function reinstateDayhome(Request $request, Application $application)
    {
    if (!auth()->user()->isAdmin() && !auth()->user()->isConsultant()) {
        abort(403, 'Only administrators and consultants can reinstate dayhomes.');
    }

    // Whitelist statuses eligible for reinstatement
    $reinstatableStatuses = [
        ApplicationStatus::SUSPENDED->value,
        ApplicationStatus::COMPLIANCE_INSPECTION_COMPLETED->value,
        ApplicationStatus::UNDER_REVIEW->value, 
        ApplicationStatus::REMEDIATION_REQUIRED->value,
    ];

    if (!in_array($application->status, $reinstatableStatuses, true)) {
        return back()->with('error', 'Only suspended, compliance-completed, or under-review dayhomes can be reinstated.');
    }

    DB::beginTransaction();

    try {
        $this->statusService->transitionTo(
            $application,
            ApplicationStatus::ACTIVE,
            'Dayhome reinstated by ' . auth()->user()->name
        );

        $application->update([
            'suspended_at' => null,
            'reinstated_at' => now(),
        ]);

        DB::commit();

        return back()->with('success', 'Dayhome reinstated successfully!');

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Failed to reinstate dayhome', [
            'application_id' => $application->id,
            'status' => $application->status,
            'error' => $e->getMessage(),
        ]);

        return back()->with('error', 'Failed to reinstate dayhome.');
    }
}

    /**
     * Require Remediation
     */
    public function requireRemediation(Request $request, Application $application)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isConsultant()) {
            abort(403, 'Only administrators and consultants can require remediation.');
        }

        $request->validate([
            'remediation_reason' => 'required|string|max:1000',
            'deadline' => 'required|date|after:today'
        ]);

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::REMEDIATION_REQUIRED,
                "Remediation required. Reason: {$request->remediation_reason}. Deadline: {$request->deadline}"
            );

            // Store remediation details
            $application->update([
                'remediation_deadline' => $request->deadline,
                'remediation_notes' => $request->remediation_reason,
            ]);

            DB::commit();

            return back()->with('success', 'Remediation required. Deadline set successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to require remediation', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to require remediation: ' . $e->getMessage());
        }
    }

    /**
     * Terminate Dayhome
     */
    public function terminateDayhome(Request $request, Application $application)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can terminate dayhomes.');
        }

        $request->validate([
            'termination_reason' => 'required|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::TERMINATED,
                $request->termination_reason
            );

            // Update termination timestamp
            $application->update(['terminated_at' => now()]);

            DB::commit();

            return back()->with('success', 'Dayhome terminated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to terminate dayhome', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to terminate dayhome: ' . $e->getMessage());
        }
    }

    /**
     * Mark Compliance Inspection Due
     */
    public function markComplianceInspectionDue(Request $request, Application $application)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isConsultant()) {
            abort(403, 'Only administrators and consultants can mark inspections due.');
        }

        if ($application->status !== ApplicationStatus::ACTIVE->value) {
            return back()->with('error', 'Only active dayhomes can have compliance inspections due.');
        }

        $request->validate([
            'due_in_months' => 'nullable|integer|min:1|max:24'
        ]);

        DB::beginTransaction();
        try {
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::COMPLIANCE_INSPECTION_DUE,
                'Compliance inspection marked as due'
            );

            // Update next due date if provided
            if ($request->due_in_months) {
                $application->update([
                    'next_compliance_inspection_due' => now()->addMonths($request->due_in_months)
                ]);
            }

            DB::commit();

            return back()->with('success', 'Compliance inspection marked as due.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to mark compliance inspection due', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to mark compliance inspection due: ' . $e->getMessage());
        }
    }

}
?>