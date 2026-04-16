<?php

namespace App\Http\Controllers;

use App;
use App\Models\Inspection;
use App\Models\Application;
use App\Models\Appointment;
use App\Models\InspectionChecklist;
use App\Enums\ApplicationStatus;
use App\Services\ApplicationStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{
    protected $statusService;

    public function __construct(ApplicationStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    /**
     * Consultant view of their inspections
     */
    public function index(Request $request)
    {
        $query = Inspection::with(['application.user', 'consultant', 'appointment'])
            ->where('consultant_id', auth()->id())
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->whereHas('application', function ($subQ) use ($search) {
                    $subQ->where('educator_first_name', 'like', "%{$search}%")
                        ->orWhere('educator_last_name', 'like', "%{$search}%")
                        ->orWhere('application_number', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->filled('result'), function ($q) use ($request) {
                return $q->where('overall_result', $request->result);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('conducted_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('conducted_at', '<=', $request->date_to);
            });

        $inspections = $query->latest('conducted_at')->paginate(15);

        $consultants = \App\Models\User::where('id', auth()->id())->get();

        $stats = [
            'total' => Inspection::where('consultant_id', auth()->id())->count(),
            'passed' => Inspection::where('consultant_id', auth()->id())->where('overall_result', 'pass')->count(),
            'failed' => Inspection::where('consultant_id', auth()->id())->where('overall_result', 'fail')->count(),
            'conditional' => Inspection::where('consultant_id', auth()->id())->where('overall_result', 'conditional_pass')->count(),
            'pending_finalization' => Inspection::where('consultant_id', auth()->id())->where('is_final', false)->count(),
            'this_month' => Inspection::where('consultant_id', auth()->id())->whereMonth('conducted_at', now()->month)->count(),
        ];

        return view('consultant.inspections.index', compact('inspections', 'consultants', 'stats'));
    }

    /**
     * Admin view of all inspections
     */
    public function adminIndex(Request $request)
    {
        $query = Inspection::with(['application.user', 'consultant', 'appointment'])
            ->when($request->search, function ($q, $search) {
                return $q->whereHas('application', function($query) use ($search) {
                    $query->where('educator_first_name', 'like', "%{$search}%")
                          ->orWhere('educator_last_name', 'like', "%{$search}%")
                          ->orWhere('application_number', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($q, $type) {
                return $q->where('type', $type);
            })
            ->when($request->result, function ($q, $result) {
                return $q->where('overall_result', $result);
            })
            ->when($request->consultant_id, function ($q, $consultantId) {
                return $q->where('consultant_id', $consultantId);
            })
            ->when($request->status, function ($q, $status) {
                if ($status === 'pending') {
                    return $q->where('is_final', false);
                } elseif ($status === 'finalized') {
                    return $q->where('is_final', true);
                }
            })
            ->when($request->date_from, function ($q, $dateFrom) {
                return $q->whereDate('conducted_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($q, $dateTo) {
                return $q->whereDate('conducted_at', '<=', $dateTo);
            });

        $sortBy = $request->get('sort_by', 'conducted_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $inspections = $query->orderBy($sortBy, $sortOrder)->paginate(15);

        $consultants = \App\Models\User::consultants()
            ->active()
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => Inspection::count(),
            'passed' => Inspection::where('overall_result', 'pass')->count(),
            'failed' => Inspection::where('overall_result', 'fail')->count(),
            'conditional' => Inspection::where('overall_result', 'conditional_pass')->count(),
            'pending_finalization' => Inspection::where('is_final', false)->count(),
            'this_month' => Inspection::whereMonth('conducted_at', now()->month)->count(),
        ];

        return view('admin.inspections.index', compact('inspections', 'consultants', 'stats'));
    }

    public function show(Inspection $inspection)
    {
        if (auth()->user()->isConsultant() && $inspection->consultant_id != auth()->id()) {
            abort(403, 'You do not have permission to view this inspection.');
        }
    
        if (auth()->user()->isApplicant() && $inspection->application->user_id != auth()->id()) {
            abort(403, 'You do not have permission to view this inspection.');
        }
    
        $inspection->load([
            'application.user',
            'consultant',
            'appointment',
            'approvedBy'
        ]);
    
        $user = auth()->user();
    
        if ($user->isAdmin()) {
            return view('admin.inspections.show', compact('inspection'));
        }
    
        if ($user->isConsultant()) {
            return view('consultant.inspections.show', compact('inspection'));
        }
    
        if ($user->isApplicant()) {
            return view('applicant.inspections.show', compact('inspection'));
        }
    
        abort(403, 'Unknown role.');
    }

    public function create(Request $request)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to create inspections.');
        }

        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $isConsultant = $user->isConsultant();

        $appointment = null;
        $applications = collect();
        $inspectionType = $request->type ?? 'initial_inspection';
        $application = null; // Initialize to avoid undefined variable errors

        if ($request->appointment_id) {
            $appointment = Appointment::with('application')->findOrFail($request->appointment_id);
            
            if (auth()->user()->isConsultant() && $appointment->consultant_id != auth()->id()) {
                abort(403, 'You do not have permission to access this appointment.');
            }
            
            // Ensure application is set from appointment
            if ($appointment->application) {
                $application = $appointment->application;
                
                // Check if there's already an inspection in progress for this application of the SAME TYPE
                $existingInspection = Inspection::where('application_id', $application->id)
                    ->where('type', $inspectionType)
                    ->where(function($query) {
                        $query->where('is_final', false)
                              ->orWhereNull('is_final');
                    })
                    ->first();
                
                if ($existingInspection) {
                    $route = $isAdmin ? 'admin.applications.show' : 'consultant.applications.show';
                    return redirect()->route($route, $application->id)
                        ->with('error', ucwords(str_replace('_', ' ', $inspectionType)) . ' already commenced for this application.');
                }
            }
        } elseif ($request->application_id) {
            $application = Application::findOrFail($request->application_id);
            
            if (auth()->user()->isConsultant() && $application->consultant_id != auth()->id()) {
                abort(403, 'You do not have permission to access this application.');
            }
            
            // Check if there's already an inspection in progress for this application of the SAME TYPE
            $existingInspection = Inspection::where('application_id', $application->id)
                ->where('type', $inspectionType)
                ->where(function($query) {
                    $query->where('is_final', false)
                          ->orWhereNull('is_final');
                })
                ->first();
            
            if ($existingInspection) {
                $route = $isAdmin ? 'admin.applications.show' : 'consultant.applications.show';
                return redirect()->route($route, $application->id)
                    ->with('error', ucwords(str_replace('_', ' ', $inspectionType)) . ' already commenced for this application.');
            }
            
            // Check if an appointment has been scheduled for this inspection type
            // Map inspection types to appointment types
            $appointmentTypeMap = [
                'initial_inspection' => 'initial_inspection',
                'second_inspection' => 'second_inspection',
                'final_inspection' => 'final_inspection',
                'compliance_inspection' => null, // Compliance inspections might not require appointments
            ];
            
            $appointmentType = $appointmentTypeMap[$inspectionType] ?? null;
            
            // For inspection types that require appointments, check if one is scheduled
            if ($appointmentType !== null) {
                // Build the query
                $query = Appointment::with('application')
                    ->where('application_id', $application->id)
                    ->where('type', $appointmentType)
                    ->whereIn('status', ['scheduled', 'confirmed']);
                
                // For consultants, they can see appointments if:
                // 1. They're assigned to the appointment (consultant_id matches), OR
                // 2. They're assigned to the application (even if appointment consultant_id is null)
                if (auth()->user()->isConsultant()) {
                    $query->where(function($q) use ($application) {
                        $q->where('consultant_id', auth()->id());
                        // If consultant is assigned to application, allow appointments without consultant_id
                        if ($application->consultant_id == auth()->id()) {
                            $q->orWhereNull('consultant_id');
                        }
                    });
                }
                
                $scheduledAppointment = $query->first();
                
                if (!$scheduledAppointment) {
                    $inspectionTypeName = ucwords(str_replace('_', ' ', $inspectionType));
                    // Redirect to application show page where they can schedule the appointment
                    $route = $isAdmin ? 'admin.applications.show' : 'consultant.applications.show';
                    return redirect()->route($route, $application)
                        ->with('error', "No {$inspectionTypeName} appointment has been scheduled for this application. Please schedule an inspection appointment first.");
                }
                
                // Ensure the application relationship is loaded
                if (!$scheduledAppointment->relationLoaded('application')) {
                    $scheduledAppointment->load('application');
                }
                
                $appointment = $scheduledAppointment;
            } else {
                // For inspection types that don't require appointments (like compliance_inspection),
                // create a placeholder object
                $appointment = (object)[
                    'application_id' => $request->application_id,
                    'application' => $application
                ];
            }
        } else {
            // Show applications that are ready for any inspection stage
            // This includes: scheduled inspections, completed inspections (ready for next stage), and documents approved (ready for second inspection)
            $query = Application::with('user')
                ->whereIn('status', [
                    // Ready for initial inspection
                    ApplicationStatus::MEET_AND_GREET_COMPLETED->value,
                    ApplicationStatus::INITIAL_INSPECTION_SCHEDULED->value,
                    // Ready for second inspection (after initial completed and documents approved)
                    ApplicationStatus::INITIAL_INSPECTION_COMPLETED->value,
                    ApplicationStatus::DOCUMENTS_APPROVED->value,
                    ApplicationStatus::SECOND_INSPECTION_SCHEDULED->value,
                    // Ready for final inspection (after second completed)
                    ApplicationStatus::SECOND_INSPECTION_COMPLETED->value,
                    ApplicationStatus::FINAL_INSPECTION_SCHEDULED->value,
                    //
                     // Ready for compliance inspection (after activation)
                    ApplicationStatus::COMPLIANCE_INSPECTION_SCHEDULED->value,
                ]);
            
            if (auth()->user()->isConsultant()) {
                $query->where('consultant_id', auth()->id());
            }
            
            $applications = $query->latest()->get();
            
            if ($applications->isNotEmpty()) {
                return view('consultant.inspections.select-application', compact('applications'));
            }
            
            $route = $isAdmin ? 'admin.inspections.index' : 'consultant.inspections.index';
            return redirect()->route($route)
                ->with('error', 'No applications available for inspection.');
        }

        // Get the appropriate checklist based on inspection type
        try {
            $checklist = $this->getChecklistForType($inspectionType);
        } catch (\Exception $e) {
            \Log::error('Error getting checklist for inspection type', [
                'inspection_type' => $inspectionType,
                'application_id' => $request->application_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $redirectApplicationId = $application?->id ?? $request->application_id;
            if ($redirectApplicationId) {
                $route = $isAdmin ? 'admin.applications.show' : 'consultant.applications.show';
                return redirect()->route($route, $redirectApplicationId)
                    ->with('error', 'Error loading inspection checklist. Please try again or contact support.');
            }
            $route = $isAdmin ? 'admin.inspections.index' : 'consultant.inspections.index';
            return redirect()->route($route)
                ->with('error', 'Error loading inspection checklist. Please try again or contact support.');
        }

        if (!$checklist) {
            $redirectApplicationId = $application?->id ?? $request->application_id;
            if ($redirectApplicationId) {
                $route = $isAdmin ? 'admin.applications.show' : 'consultant.applications.show';
                return redirect()->route($route, $redirectApplicationId)
                    ->with('error', 'No checklist found for this inspection type. Please contact support.');
            }
            $route = $isAdmin ? 'admin.inspections.index' : 'consultant.inspections.index';
            return redirect()->route($route)
                ->with('error', 'No checklist found for this inspection type. Please contact support.');
        }

        // Load checklist items grouped by category
        try {
            $checklistItems = $checklist->items()
                ->active()
                ->forInspectionType($inspectionType) // Filter by inspection type
                ->orderBy('sort_order')
                ->get()
                ->map(function($item) use ($inspectionType) {
                    // Add dynamic is_critical attribute based on inspection type
                    $item->is_critical = $item->isCriticalForInspectionType($inspectionType);
                    return $item;
                })
                ->groupBy('category');        
            } catch (\Exception $e) {
            \Log::error('Error loading checklist items', [
                'checklist_id' => $checklist->id,
                'application_id' => $request->application_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $redirectApplicationId = $application?->id ?? $request->application_id;
            if ($redirectApplicationId) {
                $route = $isAdmin ? 'admin.applications.show' : 'consultant.applications.show';
                return redirect()->route($route, $redirectApplicationId)
                    ->with('error', 'Error loading inspection checklist items. Please try again or contact support.');
            }
            $route = $isAdmin ? 'admin.inspections.index' : 'consultant.inspections.index';
            return redirect()->route($route)
                ->with('error', 'Error loading inspection checklist items. Please try again or contact support.');
        }

        // Ensure application is available for the view
        if (!$application && $appointment) {
            $application = $appointment->application ?? null;
        }
        
        return view('consultant.inspections.create', compact(
            'appointment', 
            'checklist', 
            'checklistItems',
            'inspectionType',
            'application',
            'isAdmin',
            'isConsultant'
        ));
    }

    /**
     * Store inspection (with draft support)
     */
   public function store(Request $request)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to create inspections.');
        }

        $isDraft = $request->boolean('save_as_draft');

        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'type' => 'required|in:initial_inspection,second_inspection,final_inspection,compliance_inspection_scheduled,compliance_inspection_unscheduled',
            'conducted_at' => $isDraft ? 'nullable|date' : 'nullable|date',
            'duration' => 'nullable|integer|min:30',
            'checklist_results' => $isDraft ? 'nullable|array' : 'required|array',
            'weather_conditions' => 'nullable|string|max:255',
            'temperature' => 'nullable|numeric',
            'environmental_factors' => 'nullable|string',
            'observations' => $isDraft ? 'nullable|string' : 'nullable|string',
            'consultant_notes' => $isDraft ? 'nullable|string' : 'nullable|string',
            'save_as_draft' => 'nullable|boolean',
            'consultant_decision' => 'nullable|in:proceed_to_next_stage,schedule_follow_up,reject_application',
            'decision_notes' => 'nullable|string',
        ]);

        $application = Application::findOrFail($validated['application_id']);
        
        if (auth()->user()->isConsultant() && $application->consultant_id != auth()->id()) {
            abort(403, 'You do not have permission to create inspections for this application.');
        }

        DB::beginTransaction();
        try {
            // Process checklist results
            $enrichedChecklistResults = [];
            if (!empty($validated['checklist_results'])) {
                $itemIds = collect($validated['checklist_results'])
                    ->pluck('item_id')
                    ->filter()
                    ->unique()
                    ->toArray();
                            
                $inspectionType = $validated['type'];
                $checklistItems = \App\Models\InspectionItem::whereIn('id', $itemIds)
                    ->get()
                    ->keyBy('id');
                
                foreach ($validated['checklist_results'] as $itemCode => $result) {
                    $itemId = $result['item_id'] ?? null;
                    $item = $itemId ? $checklistItems->get($itemId) : null;
                    
                    $enrichedChecklistResults[$itemCode] = array_merge($result, [
                        'title' => $item ? $item->title : $itemCode,
                        'code' => $itemCode,
                        'is_critical' => $item ? $item->isCriticalForInspectionType($inspectionType) : false,
                        'included_in_second_final' => $item ? ($item->included_in_second || $item->included_in_final) : false,
                    ]);
                }
            }
            
            $validated['checklist_results'] = $enrichedChecklistResults;
            
            // Calculate results
            $results = null;
            $canProceed = false;
            
            if (!$isDraft && !empty($enrichedChecklistResults)) {
                $results = $this->calculateInspectionResults($validated['checklist_results'], $validated['type']);
                $canProceed = $this->canInspectionProceed($results, $validated['type']);
            }

            $inspection = Inspection::create(array_merge($validated, [
                'consultant_id' => auth()->id(),
                'is_draft' => $isDraft,
                'draft_saved_at' => $isDraft ? now() : null,
                'conducted_at' => $validated['conducted_at'] ?? ($isDraft ? null : now()),
                'overall_result' => $isDraft ? 'incomplete' : ($results ? $results['overall_result'] : 'incomplete'),
                'overall_score' => $results ? $results['overall_score'] : 0,
                'items_checked' => $results ? $results['items_checked'] : 0,
                'items_passed' => $results ? $results['items_passed'] : 0,
                'items_failed' => $results ? $results['items_failed'] : 0,
                'items_not_applicable' => $results ? $results['items_not_applicable'] : 0,
                'failed_items' => $results ? $results['failed_items'] : [],
                'critical_failed_items' => $results ? $results['critical_failed_items'] : [],
                'requires_reinspection' => $results ? $results['requires_reinspection'] : false,
                'consultant_decision' => $validated['consultant_decision'] ?? null,
                'decision_notes' => $validated['decision_notes'] ?? null,
            ]));

            if ($isDraft) {
                DB::commit();
                $user = auth()->user();
                $route = $user->isAdmin() ? 'admin.inspections.show' : 'consultant.inspections.show';
                return redirect()->route($route, $inspection)
                    ->with('success', 'Inspection saved as draft. You can continue editing later.');
            }

            // Complete appointment if it exists
            if ($inspection->appointment_id && $validated['type'] !== 'compliance_inspection_unscheduled') {
                try {
                    $appointment = Appointment::find($inspection->appointment_id);
                    if ($appointment) {
                        $appointment->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'result' => $inspection->overall_result,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to update appointment after inspection', [
                        'inspection_id' => $inspection->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Update application status based on inspection result and consultant decision
            $newStatus = $this->determineNewStatus($inspection, $canProceed);
            if ($newStatus) {
                $this->statusService->transitionTo(
                    $application,
                    $newStatus,
                    $this->getStatusTransitionMessage($inspection)
                );
            }

            \App\Models\AuditLog::log('inspection_completed', $inspection, 'Inspection completed', [
                'overall_result' => $inspection->overall_result,
                'can_proceed' => $canProceed,
                'consultant_decision' => $inspection->consultant_decision,
            ]);

            // Send notification to applicant
            $this->notifyApplicant($inspection);

            DB::commit();

            $user = auth()->user();
            $route = $user->isAdmin() ? 'admin.inspections.show' : 'consultant.inspections.show';
            return redirect()->route($route, $inspection)
                ->with('success', 'Inspection completed successfully! Applicant has been notified.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Inspection creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Failed to complete inspection: ' . $e->getMessage());
        }
    }


    public function update(Request $request, Inspection $inspection)
{
    if ($inspection->is_final) {
        return back()->with('error', 'This inspection has been finalized and cannot be edited.');
    }

    if (auth()->user()->isConsultant() && $inspection->consultant_id != auth()->id()) {
        abort(403, 'You do not have permission to update this inspection.');
    }

    if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
        abort(403, 'You do not have permission to update inspections.');
    }

    $validated = $request->validate([
        'conducted_at' => 'required|date',
        'duration' => 'nullable|integer|min:30',
        'checklist_results' => 'required|array',
        'weather_conditions' => 'nullable|string|max:255',
        'temperature' => 'nullable|numeric',
        'environmental_factors' => 'nullable|string',
        'observations' => 'nullable|string',
        'consultant_notes' => 'nullable|string',
    ]);

    DB::beginTransaction();
    try {
        // Fetch the checklist items using the item_id from each result
        $itemIds = collect($validated['checklist_results'])
            ->pluck('item_id')
            ->filter()
            ->unique()
            ->toArray();
        
        $checklistItems = \App\Models\InspectionItem::whereIn('id', $itemIds)
            ->get()
            ->keyBy('id');
        
        // Enrich checklist results with titles
        $enrichedChecklistResults = [];
        foreach ($validated['checklist_results'] as $itemCode => $result) {
            $itemId = $result['item_id'] ?? null;
            $item = $itemId ? $checklistItems->get($itemId) : null;
            
            $enrichedChecklistResults[$itemCode] = array_merge($result, [
                'title' => $item ? $item->title : $itemCode,
                'code' => $itemCode
            ]);
        }
        
        // Replace the checklist_results with enriched version
        $validated['checklist_results'] = $enrichedChecklistResults;
        
        $results = $this->calculateInspectionResults($validated['checklist_results']);
        $oldValues = $inspection->only(['overall_result', 'overall_score']);

        $inspection->update(array_merge($validated, [
            'overall_result' => $results['overall_result'],
            'overall_score' => $results['overall_score'],
            'items_checked' => $results['items_checked'],
            'items_passed' => $results['items_passed'],
            'items_failed' => $results['items_failed'],
            'items_not_applicable' => $results['items_not_applicable'],
            'failed_items' => $results['failed_items'],
            'requires_reinspection' => $results['requires_reinspection'],
        ]));

        \App\Models\AuditLog::log('inspection_updated', $inspection, 'Inspection updated', [
            'old_values' => $oldValues,
            'new_result' => $inspection->overall_result,
            'new_score' => $inspection->overall_score,
        ]);

        DB::commit();

        $route = auth()->user()->isAdmin() ? 'admin.inspections.show' : 'consultant.inspections.show';
        return redirect()->route($route, $inspection)
            ->with('success', 'Inspection updated successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Inspection update failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to update inspection. Please try again.');
    }
}


    public function finalize(Inspection $inspection)
    {
        if ($inspection->is_final) {
            return back()->with('error', 'This inspection has already been finalized.');
        }

        if (auth()->user()->isConsultant() && $inspection->consultant_id != auth()->id()) {
            abort(403, 'You do not have permission to finalize this inspection.');
        }

        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to finalize inspections.');
        }

        DB::beginTransaction();
        try {
            $inspection->update([
                'is_final' => true,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            \App\Models\AuditLog::log('inspection_finalized', $inspection, 'Inspection finalized');

            DB::commit();

            return back()->with('success', 'Inspection finalized successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to finalize inspection. Please try again.');
        }
    }
    
    public function edit(Inspection $inspection)
    {
    if (auth()->user()->isConsultant() && $inspection->consultant_id != auth()->id()) {
        abort(403, 'You do not have permission to edit this inspection.');
    }

    // Block normal users from editing finalized inspections
    if ($inspection->is_final && !auth()->user()->isAdmin()) {
        return back()->with('error', 'This inspection has been finalized and cannot be edited.');
    }

    $inspection->load([
        'application',
        'appointment'
    ]);

    $checklist = $this->getChecklistForType($inspection->type);

    // Match create() logic: filter items by inspection type and compute dynamic is_critical
    $checklistItems = $checklist->items()
        ->active()
        ->forInspectionType($inspection->type)
        ->orderBy('sort_order')
        ->get()
        ->map(function ($item) use ($inspection) {
            $item->is_critical = $item->isCriticalForInspectionType($inspection->type);
            return $item;
        })
        ->groupBy('category');

    return view('consultant.inspections.edit', compact(
        'inspection',
        'checklist',
        'checklistItems'
    ));
}
    
    public function reinspectForm(Inspection $inspection)
{
    // Only allow consultants assigned to the application or admins
    if (auth()->user()->isConsultant() && $inspection->consultant_id != auth()->id()) {
        abort(403, 'You do not have permission to reinspect this application.');
    }

    $checklistResults = is_array($inspection->checklist_results)
        ? $inspection->checklist_results
        : json_decode($inspection->checklist_results, true);

    $failedItems = is_array($inspection->failed_items)
        ? $inspection->failed_items
        : json_decode($inspection->failed_items, true);

    // Get the checklist to retrieve item titles and details
    $checklist = $this->getChecklistForType($inspection->type);
    
    // Build a map of checklist item details indexed by item ID
    $checklistItemsMap = [];
    if ($checklist) {
        foreach ($checklist->items as $item) {
            $checklistItemsMap[$item->id] = $item;
        }
    }

    return view('consultant.inspections.reinspect', compact(
        'inspection', 
        'checklistResults', 
        'failedItems',
        'checklistItemsMap'
    ));
}

public function storeReinspection(Request $request, Inspection $inspection)
{
    $validated = $request->validate([
        'items' => 'required|array',
        'items.*.status' => 'required|in:pass,fail,n/a',
        'items.*.notes' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();
    try {
        $checklistResults = is_array($inspection->checklist_results)
            ? $inspection->checklist_results
            : json_decode($inspection->checklist_results, true);

        $failedItems = is_array($inspection->failed_items)
            ? $inspection->failed_items
            : json_decode($inspection->failed_items, true);

        // Update only previously failed items
        foreach ($validated['items'] as $itemId => $data) {
            if (!in_array($itemId, $failedItems)) continue;

            $checklistResults[$itemId]['status'] = $data['status'];
            $checklistResults[$itemId]['notes'] = $data['notes'] ?? null;
        }

        // Recalculate totals
        $items = collect($checklistResults);
        $inspection->checklist_results = $checklistResults;
        $inspection->items_checked = $items->count();
        $inspection->items_passed = $items->where('status', 'pass')->count();
        $inspection->items_failed = $items->where('status', 'fail')->count();
        $inspection->items_not_applicable = $items->where('status', 'n/a')->count();
        $inspection->overall_score = $inspection->items_checked > 0
            ? ($inspection->items_passed / $inspection->items_checked) * 100
            : 0;
        $inspection->overall_result = $inspection->items_failed > 0 ? 'fail' : 'pass';
        $inspection->requires_reinspection = $inspection->items_failed > 0;
        $inspection->save();

        // Update application status using your existing method
        $canProceed = $inspection->items_failed === 0;
        $newStatus = $this->determineNewStatus($inspection, $canProceed);
        if ($newStatus) {
            $this->statusService->transitionTo(
                $inspection->application,
                $newStatus,
                "Reinspection completed with result: {$inspection->overall_result}"
            );
        }

        \App\Models\AuditLog::log('inspection_reinspected', $inspection, 'Reinspection completed', [
            'overall_result' => $inspection->overall_result,
            'overall_score' => $inspection->overall_score,
        ]);

        $this->notifyApplicant($inspection);

        DB::commit();

        $route = auth()->user()->isAdmin() ? 'admin.inspections.show' : 'consultant.inspections.show';
        return redirect()->route($route, $inspection)
            ->with('success', 'Reinspection completed successfully! Applicant has been notified.');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Reinspection failed', [
            'inspection_id' => $inspection->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to complete reinspection: ' . $e->getMessage());
    }
}

    /**
     * Get checklist for specific inspection type
     */
    private function getChecklistForType(string $inspectionType): ?InspectionChecklist
    {
        return InspectionChecklist::active()
            ->forInspectionType($inspectionType)
            ->default()
            ->with(['items' => function ($query) {
                $query->active()->orderBy('sort_order');
            }])
            ->first();
    }

    
/**
     * Determine new application status based on inspection
     */
   
  private function determineNewStatus(Inspection $inspection, bool $canProceed): ?ApplicationStatus
    {
        $type = $inspection->type;
        $passed = $inspection->isPassed();
        $decision = $inspection->consultant_decision;

        // Initial Inspection Logic
        if ($type === Inspection::TYPE_INITIAL) {
            if ($passed) {
                return ApplicationStatus::INITIAL_INSPECTION_COMPLETED;
            }
            
            // Failed - check consultant decision
            if ($decision === 'proceed_to_next_stage') {
                return ApplicationStatus::INITIAL_INSPECTION_COMPLETED;
            }
            if ($decision === 'schedule_follow_up') {
                return ApplicationStatus::INITIAL_INSPECTION_SCHEDULED;
            }
            if ($decision === 'reject_application') {
                return null; // Will be handled to reject
            }
            
            // If no decision but can proceed (only repeating critical items failed)
            if ($canProceed) {
                return ApplicationStatus::INITIAL_INSPECTION_COMPLETED;
            }
            
            return null;
        }

        // Second Inspection Logic
        if ($type === Inspection::TYPE_SECOND) {
            if ($passed) {
                return ApplicationStatus::SECOND_INSPECTION_COMPLETED;
            }
            
            // Failed - consultant decision required
            if ($decision === 'proceed_to_next_stage') {
                return ApplicationStatus::SECOND_INSPECTION_COMPLETED;
            }
            if ($decision === 'schedule_follow_up') {
                return ApplicationStatus::SECOND_INSPECTION_SCHEDULED;
            }
            if ($decision === 'reject_application') {
                return null; // Will be rejected
            }
            
            return null;
        }

        // Final Inspection Logic
        if ($type === Inspection::TYPE_FINAL) {
            if ($passed) {
                return ApplicationStatus::FINAL_INSPECTION_COMPLETED;
            }
            
            // Failed - consultant decision required
            if ($decision === 'proceed_to_next_stage') {
                return ApplicationStatus::FINAL_INSPECTION_COMPLETED;
            }
            if ($decision === 'schedule_follow_up') {
                return ApplicationStatus::FINAL_INSPECTION_SCHEDULED;
            }
            if ($decision === 'reject_application') {
                return ApplicationStatus::FINAL_INSPECTION_FAILED;
            }
            
            return null;
        }

        // Compliance Inspection Logic
        if (in_array($type, [Inspection::TYPE_COMPLIANCE_SCHEDULED, Inspection::TYPE_COMPLIANCE_UNSCHEDULED])) {
            if ($passed) {
                return ApplicationStatus::ACTIVE;
            }
            
            // Failed - consultant decision
            if ($decision === 'proceed_to_next_stage') {
                return ApplicationStatus::ACTIVE;
            }
            if ($decision === 'schedule_follow_up') {
                return ApplicationStatus::SUSPENDED;
            }
            if ($decision === 'reject_application') {
                return ApplicationStatus::SUSPENDED;
            }
            
            return ApplicationStatus::SUSPENDED;
        }

        return null;
    }

    
    /**
     * Calculate inspection results with STRICT critical item logic
     */
     private function calculateInspectionResults(array $checklistResults, string $inspectionType)
    {
        $itemsChecked = 0;
        $itemsPassed = 0;
        $itemsFailed = 0;
        $itemsNotApplicable = 0;
        $failedItems = [];
        $criticalFailedItems = [];
        $totalScore = 0;
        $maxScore = 0;

        foreach ($checklistResults as $itemCode => $result) {
            $itemsChecked++;
            $maxScore += $result['points_possible'] ?? 1;

            switch ($result['status']) {
                case 'pass':
                    $itemsPassed++;
                    $totalScore += $result['points_possible'] ?? 1;
                    break;
                case 'fail':
                    $itemsFailed++;
                    $failedItems[] = $itemCode;
                    
                    // Determine if this item is critical for this inspection type
                    $isCritical = $this->isItemCriticalForType($result, $inspectionType);
                    
                    if ($isCritical) {
                        $criticalFailedItems[] = [
                            'id' => $itemCode,
                            'title' => $result['title'] ?? $itemCode,
                            'notes' => $result['notes'] ?? '',
                            'included_in_second_final' => $result['included_in_second_final'] ?? false,
                        ];
                    }
                    break;
                case 'n/a':
                    $itemsNotApplicable++;
                    $maxScore -= $result['points_possible'] ?? 1;
                    break;
            }
        }

        $overallScore = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        // STRICT RULE: ANY failure = fail result
        $overallResult = $itemsFailed > 0 ? 'fail' : 'pass';

        return [
            'overall_result' => $overallResult,
            'overall_score' => round($overallScore, 2),
            'items_checked' => $itemsChecked,
            'items_passed' => $itemsPassed,
            'items_failed' => $itemsFailed,
            'items_not_applicable' => $itemsNotApplicable,
            'failed_items' => $failedItems,
            'critical_failed_items' => $criticalFailedItems,
            'requires_reinspection' => count($criticalFailedItems) > 0,
        ];
    }

     /**
     * Determine if an item is critical for the given inspection type
     */
   /**
 * Determine if an item is critical for the given inspection type
 */
private function isItemCriticalForType(array $result, string $inspectionType): bool
{
    // Check if is_critical was already set in the result array
    if (isset($result['is_critical'])) {
        return (bool) $result['is_critical'];
    }

    // Fallback: Get the item from database to check critical status
    $itemId = $result['item_id'] ?? null;
    if (!$itemId) {
        return false;
    }

    $item = \App\Models\InspectionItem::find($itemId);
    if (!$item) {
        return false;
    }

    return $item->isCriticalForInspectionType($inspectionType);
}

    /**
     * Determine overall result based on inspection type and failures
     */
    private function determineOverallResult(string $inspectionType, int $itemsFailed, array $criticalFailedItems, float $overallScore)
    {
        // For second, final, and compliance: ANY failure = fail
        if (in_array($inspectionType, ['second_inspection', 'final_inspection', 'compliance_inspection_scheduled', 'compliance_inspection_unscheduled'])) {
            return $itemsFailed > 0 ? 'fail' : 'pass';
        }

        // For initial inspection
        if ($inspectionType === 'initial_inspection') {
            // Any critical failure = fail
            if (count($criticalFailedItems) > 0) {
                return 'fail';
            }
            
            // No failures = pass
            if ($itemsFailed === 0) {
                return 'pass';
            }
            
            // Non-critical failures only = conditional pass
            return 'conditional_pass';
        }

        return 'incomplete';
    }

    /**
     * Check if inspection can proceed to next stage
     */
     private function canInspectionProceed(array $results, string $inspectionType): bool
    {
        // Second, Final, Compliance inspections: always can proceed (consultant discretion)
        if (in_array($inspectionType, ['second_inspection', 'final_inspection', 'compliance_inspection_scheduled', 'compliance_inspection_unscheduled'])) {
            return true; 
        }

        // Initial inspection: can proceed ONLY if no critical items failed
        // that are NOT in second/final inspection (those items don't repeat)
        if ($inspectionType === 'initial_inspection') {
            foreach ($results['critical_failed_items'] as $criticalItem) {
                // If a critical item that's NOT in second/final inspection failed, cannot proceed
                if (!($criticalItem['included_in_second_final'] ?? false)) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

     /**
     * Get status transition message
     */
    private function getStatusTransitionMessage(Inspection $inspection): string
    {
        $type = str_replace('_', ' ', $inspection->type);
        $result = $inspection->overall_result;
        
        if ($inspection->consultant_decision) {
            $decision = str_replace('_', ' ', $inspection->consultant_decision);
            return ucfirst($type) . " completed with result: {$result}. Consultant decision: {$decision}";
        }
        
        return ucfirst($type) . " completed with result: {$result}";
    }

    /**
     * Load draft inspection for editing
     */
    public function editDraft(Inspection $inspection)
    {
        if (!$inspection->is_draft) {
            return redirect()->route('consultant.inspections.edit', $inspection)
                ->with('error', 'This inspection is not a draft.');
        }

        if (auth()->user()->isConsultant() && $inspection->consultant_id != auth()->id()) {
            abort(403);
        }

        $inspection->load(['application', 'appointment']);
        $checklist = $this->getChecklistForType($inspection->type);

        // Match create() logic so draft editing uses the same critical/required rules
        $checklistItems = $checklist->items()
            ->active()
            ->forInspectionType($inspection->type)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($item) use ($inspection) {
                $item->is_critical = $item->isCriticalForInspectionType($inspection->type);
                return $item;
            })
            ->groupBy('category');

        return view('consultant.inspections.edit-draft', compact(
            'inspection',
            'checklist',
            'checklistItems'
        ));
    }


  /**
     * Notify applicant about inspection (skip for unscheduled compliance)
     */
    private function notifyApplicant(Inspection $inspection)
    {
        // Don't notify for unscheduled compliance inspections
        if ($inspection->type === Inspection::TYPE_COMPLIANCE_UNSCHEDULED) {
            \Log::info('Skipping notification for unscheduled compliance inspection', [
                'inspection_id' => $inspection->id
            ]);
            return;
        }

        try {
            $application = $inspection->application;
            
            $user = $application->user;
            $recipientEmail = $user ? $user->email : $application->email;
            
            if (!$recipientEmail) {
                \Log::warning('No email found for notification', [
                    'inspection_id' => $inspection->id,
                ]);
                return;
            }
            
            \Mail::to($recipientEmail)->send(new \App\Mail\InspectionCompleted($inspection));
            
            if ($user) {
                try {
                    $user->notify(new \App\Notifications\InspectionCompletedNotification($inspection));
                } catch (\Exception $e) {
                    \Log::warning('Failed to create in-app notification', [
                        'inspection_id' => $inspection->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to send inspection notification', [
                'inspection_id' => $inspection->id,
                'error' => $e->getMessage(),
            ]);
        }
    }


    /**
     * Update draft inspection (can save partial progress)
     */
    public function updateDraft(Request $request, Inspection $inspection)
        {
            if (!$inspection->is_draft) {
                return redirect()->route('consultant.inspections.edit', $inspection)
                    ->with('error', 'This inspection is not a draft.');
            }

            if (auth()->user()->isConsultant() && $inspection->consultant_id != auth()->id()) {
                abort(403, 'You do not have permission to update this inspection.');
            }

            $isDraft = $request->boolean('save_as_draft');

            $rules = [
                'conducted_at' => $isDraft ? 'nullable|date' : 'required|date',
                'duration' => 'nullable|integer|min:30',
                'checklist_results' => 'nullable|array',
                'weather_conditions' => 'nullable|string|max:255',
                'temperature' => 'nullable|numeric',
                'environmental_factors' => 'nullable|string',
                'observations' => $isDraft ? 'nullable|string' : 'required|string',
                'consultant_notes' => $isDraft ? 'nullable|string' : 'required|string',
                'consultant_decision' => 'nullable|in:proceed_to_next_stage,schedule_follow_up,reject_application',
                'decision_notes' => 'nullable|string',
            ];

            $validated = $request->validate($rules);

            DB::beginTransaction();
            try {
                // Process checklist results
                if (isset($validated['checklist_results']) && !empty($validated['checklist_results'])) {
                    $itemIds = collect($validated['checklist_results'])
                        ->pluck('item_id')
                        ->filter()
                        ->unique()
                        ->toArray();
                    
                    $checklistItems = \App\Models\InspectionItem::whereIn('id', $itemIds)
                        ->get()
                        ->keyBy('id');
                    
                    $enrichedChecklistResults = [];
                    foreach ($validated['checklist_results'] as $itemCode => $result) {
                        $itemId = $result['item_id'] ?? null;
                        $item = $itemId ? $checklistItems->get($itemId) : null;
                        
                        $enrichedChecklistResults[$itemCode] = array_merge($result, [
                            'title' => $item ? $item->title : $itemCode,
                            'code' => $itemCode,
                            'is_critical' => $item ? $item->isCriticalForInspectionType($inspection->type) : false,
                            'included_in_second_final' => $item ? ($item->included_in_second || $item->included_in_final) : false,
                        ]);
                    }
                    
                    $validated['checklist_results'] = $enrichedChecklistResults;
                }

                // Calculate results if completing
                if (!$isDraft && isset($validated['checklist_results'])) {
                    $results = $this->calculateInspectionResults($validated['checklist_results'], $inspection->type);
                    $canProceed = $this->canInspectionProceed($results, $inspection->type);
                    
                    $updateData = array_merge($validated, [
                        'is_draft' => false,
                        'draft_saved_at' => null,
                        'conducted_at' => $validated['conducted_at'] ?? now(),
                        'overall_result' => $results['overall_result'],
                        'overall_score' => $results['overall_score'],
                        'items_checked' => $results['items_checked'],
                        'items_passed' => $results['items_passed'],
                        'items_failed' => $results['items_failed'],
                        'items_not_applicable' => $results['items_not_applicable'],
                        'failed_items' => $results['failed_items'],
                        'critical_failed_items' => $results['critical_failed_items'],
                        'requires_reinspection' => $results['requires_reinspection'],
                    ]);
                } else {
                    $updateData = array_merge($validated, [
                        'is_draft' => true,
                        'draft_saved_at' => now(),
                    ]);
                }

                $inspection->update($updateData);

                // If completing inspection
                if (!$isDraft) {
                    // Complete appointment
                    if ($inspection->appointment_id && $inspection->type !== 'compliance_inspection_unscheduled') {
                        try {
                            $appointment = Appointment::find($inspection->appointment_id);
                            if ($appointment) {
                                $appointment->update([
                                    'status' => 'completed',
                                    'completed_at' => now(),
                                    'result' => $inspection->overall_result,
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Failed to update appointment', [
                                'inspection_id' => $inspection->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    
                    // Update application status
                    $newStatus = $this->determineNewStatus($inspection, $canProceed ?? false);
                    if ($newStatus) {
                        $this->statusService->transitionTo(
                            $inspection->application,
                            $newStatus,
                            $this->getStatusTransitionMessage($inspection)
                        );
                    }

                    \App\Models\AuditLog::log('inspection_completed_from_draft', $inspection, 'Draft inspection completed', [
                        'overall_result' => $inspection->overall_result,
                        'consultant_decision' => $inspection->consultant_decision,
                    ]);

                    $this->notifyApplicant($inspection);

                    DB::commit();

                    $route = auth()->user()->isAdmin() ? 'admin.inspections.show' : 'consultant.inspections.show';
                    return redirect()->route($route, $inspection)
                        ->with('success', 'Inspection completed successfully! Applicant has been notified.');
                } else {
                    \App\Models\AuditLog::log('inspection_draft_updated', $inspection, 'Draft inspection progress saved');
                    
                    DB::commit();

                    return back()->with('success', 'Draft saved successfully! You can continue editing later.');
                }

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Draft inspection update failed', [
                    'inspection_id' => $inspection->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                return back()
                    ->withInput()
                    ->with('error', 'Failed to update inspection: ' . $e->getMessage());
            }
        }


/**
 * Delete draft inspection
 */
    public function destroyDraft(Inspection $inspection)
    {
        if (!$inspection->is_draft) {
            return back()->with('error', 'Only draft inspections can be deleted.');
        }

        if (auth()->user()->isConsultant() && $inspection->consultant_id != auth()->id()) {
            abort(403, 'You do not have permission to delete this inspection.');
        }

        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to delete inspections.');
        }

        DB::beginTransaction();
        try {
            \App\Models\AuditLog::log('inspection_draft_deleted', $inspection, 'Draft inspection deleted');
            
            $applicationId = $inspection->application_id;
            $inspection->delete();

            DB::commit();

            $route = auth()->user()->isAdmin() ? 'admin.applications.show' : 'consultant.applications.show';
            return redirect()->route($route, $applicationId)
                ->with('success', 'Draft inspection deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to delete draft inspection', [
                'inspection_id' => $inspection->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to delete draft inspection. Please try again.');
        }
    }
}
