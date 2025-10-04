<?php
// app/Http/Controllers/ApplicationController.php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ApplicationController extends Controller
{
  

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
    
    // Check if user has permission to view this application
    $canView = false;
    
    // Owner can view their own application
    if ($application->user_id === $user->id) {
        $canView = true;
    }
    
    // Admin can view all applications
    if ($user->user_type === 'admin') {
        $canView = true;
    }
    
    // Consultant can view if assigned to this application
    if ($user->user_type === 'consultant' && $application->consultant_id === $user->id) {
        $canView = true;
    }
    
    // Debug: Log the values to check what's happening
    \Log::info('Application Access Check', [
        'user_id' => $user->id,
        'user_role' => $user->user_type,
        'application_user_id' => $application->user_id,
        'application_consultant_id' => $application->consultant_id,
        'can_view' => $canView
    ]);
    
    // If none of the conditions are met, deny access
    if (!$canView) {
        abort(403, 'Unauthorized access to this application.');
    }
    
    // Load relationships that might be needed in the view
    $application->load(['user', 'consultant', 'documents', 'appointments']);
    
    // Return appropriate view based on user role
    if ($user->user_type === 'admin') {
        return view('admin.applications.show', compact('application'));
    }
    
    if ($user->user_type === 'consultant') {
        return view('consultant.applications.show', compact('application'));
    }
    
    // Default to applicant view
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
        
        // Convert checkboxes
        $validated['has_criminal_record_check'] = $request->has('has_criminal_record_check') ? 1 : 0;
        $validated['has_first_aid_cpr'] = $request->has('has_first_aid_cpr') ? 1 : 0;
        $validated['has_pets'] = $request->has('has_pets') ? 1 : 0;
        $validated['comfortable_special_needs'] = $request->has('comfortable_special_needs') ? 1 : 0;
        $validated['fenced_backyard'] = $request->has('fenced_backyard') ? 1 : 0;
        $validated['currently_operating'] = $request->has('currently_operating') ? 1 : 0;
        $validated['evening_overnight_care'] = $request->has('evening_overnight_care') ? 1 : 0;
        
        $validated['status'] = $isDraft ? 'draft' : 'submitted';
        if (!$isDraft) {
            $validated['submitted_at'] = now();
        }

        DB::beginTransaction();

        $application = auth()->user()->applications()->create($validated);
        
        Log::info('Application created', ['id' => $application->id]);
        
        $application->updateCompletionPercentage();

        DB::commit();

        // For AJAX requests (Save Draft button)
        if ($request->expectsJson()) {
            Log::info('Returning JSON response');
            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Draft saved successfully!' : 'Application submitted successfully!',
                'application_id' => $application->id,
                'redirect' => route('applicant.applications.show', $application)
            ], 200);
        }

        // For regular form submission (Submit button)
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
            ->with('error' , 'Failed to create application: ' . $e->getMessage());
    }
}

public function update(Request $request, Application $application)
{
    Log::info('=== APPLICATION UPDATE STARTED ===');

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

        // Convert checkboxes
        $validated['has_criminal_record_check'] = $request->has('has_criminal_record_check') ? 1 : 0;
        $validated['has_first_aid_cpr'] = $request->has('has_first_aid_cpr') ? 1 : 0;
        $validated['has_pets'] = $request->has('has_pets') ? 1 : 0;
        $validated['comfortable_special_needs'] = $request->has('comfortable_special_needs') ? 1 : 0;
        $validated['fenced_backyard'] = $request->has('fenced_backyard') ? 1 : 0;
        $validated['currently_operating'] = $request->has('currently_operating') ? 1 : 0;
        $validated['evening_overnight_care'] = $request->has('evening_overnight_care') ? 1 : 0;

        // Update status based on whether it's a draft or submission
        if (!$isDraft) {
            $validated['status'] = 'submitted';
            $validated['submitted_at'] = now();
        }
        // If it's a draft, keep status as 'draft'

        DB::beginTransaction();

        $application->update($validated);
        $application->updateCompletionPercentage();

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

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed', ['errors' => $e->errors()]);
        return back()->withErrors($e->errors())->withInput();
        
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
    // Authorization check
    if ($application->user_id !== auth()->id()) {
        abort(403, 'Unauthorized access to this application.');
    }

    // Check if application can be edited
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
            $application->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('applicant.dashboard')
                ->with('success', 'Application submitted successfully! We will contact you soon.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to submit application. Please try again.');
        }
    }

}