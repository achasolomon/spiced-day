<?php

namespace App\Http\Controllers;

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

    public function index(Request $request)
    {
        $query = Inspection::with(['application.user', 'consultant', 'appointment'])
            ->when($request->type, function ($q, $type) {
                return $q->where('type', $type);
            })
            ->when($request->result, function ($q, $result) {
                return $q->where('overall_result', $result);
            })
            ->when($request->consultant_id, function ($q, $consultantId) {
                return $q->where('consultant_id', $consultantId);
            });

        if (auth()->user()->isConsultant()) {
            $query->where('consultant_id', auth()->id());
        } elseif (auth()->user()->isApplicant()) {
            $query->whereHas('application', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $inspections = $query->latest('conducted_at')->paginate(15);

        return view('consultant.inspections.index', compact('inspections'));
    }

    public function show(Inspection $inspection)
    {
        if (auth()->user()->isConsultant() && $inspection->consultant_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this inspection.');
        }

        if (auth()->user()->isApplicant() && $inspection->application->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this inspection.');
        }

        $inspection->load([
            'application.user',
            'consultant',
            'appointment',
            'approvedBy'
        ]);

        return view('inspections.show', compact('inspection'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to create inspections.');
        }

        $appointment = null;
        $applications = collect();

        if ($request->appointment_id) {
            $appointment = Appointment::with('application')->findOrFail($request->appointment_id);
            
            if (auth()->user()->isConsultant() && $appointment->consultant_id !== auth()->id()) {
                abort(403, 'You do not have permission to access this appointment.');
            }
        } elseif ($request->application_id) {
            $application = Application::findOrFail($request->application_id);
            
            if (auth()->user()->isConsultant() && $application->consultant_id !== auth()->id()) {
                abort(403, 'You do not have permission to access this application.');
            }
            
            $appointment = (object)[
                'application_id' => $request->application_id,
                'application' => $application
            ];
        } else {
            // Show applications ready for inspection
            $query = Application::with('user')
                ->whereIn('status', [
                    ApplicationStatus::MEET_AND_GREET_COMPLETED->value,
                    ApplicationStatus::INITIAL_INSPECTION_SCHEDULED->value,
                    ApplicationStatus::DOCUMENTS_APPROVED->value,
                    ApplicationStatus::SECOND_INSPECTION_SCHEDULED->value,
                ]);
            
            if (auth()->user()->isConsultant()) {
                $query->where('consultant_id', auth()->id());
            }
            
            $applications = $query->latest()->get();
            
            if ($applications->isNotEmpty()) {
                return view('consultant.inspections.select-application', compact('applications'));
            }
            
            return redirect()->route('consultant.inspections.index')
                ->with('error', 'No applications available for inspection.');
        }

        return view('consultant.inspections.create', compact('appointment'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isConsultant() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to create inspections.');
        }

        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'type' => 'required|in:initial_inspection,second_inspection,final_inspection,follow_up_inspection,complaint_inspection,renewal_inspection',
            'conducted_at' => 'required|date',
            'duration' => 'nullable|integer|min:30',
            'checklist_results' => 'required|array',
            'weather_conditions' => 'nullable|string|max:255',
            'temperature' => 'nullable|numeric',
            'environmental_factors' => 'nullable|string',
            'observations' => 'nullable|string',
            'consultant_notes' => 'nullable|string',
        ]);

        $application = Application::findOrFail($validated['application_id']);
        
        if (auth()->user()->isConsultant() && $application->consultant_id !== auth()->id()) {
            abort(403, 'You do not have permission to create inspections for this application.');
        }

        DB::beginTransaction();
        try {
            $results = $this->calculateInspectionResults($validated['checklist_results']);

            $inspection = Inspection::create(array_merge($validated, [
                'consultant_id' => auth()->id(),
                'overall_result' => $results['overall_result'],
                'overall_score' => $results['overall_score'],
                'items_checked' => $results['items_checked'],
                'items_passed' => $results['items_passed'],
                'items_failed' => $results['items_failed'],
                'items_not_applicable' => $results['items_not_applicable'],
                'failed_items' => $results['failed_items'],
                'requires_reinspection' => $results['requires_reinspection'],
            ]));

            // Update appointment if linked
            if ($inspection->appointment_id) {
                $inspection->appointment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'result' => $inspection->overall_result,
                ]);
            }

            // Update application status based on inspection type and result
            $newStatus = $this->determineNewStatus($inspection);
            if ($newStatus) {
                $this->statusService->transitionTo(
                    $application,
                    $newStatus,
                    "Inspection completed with result: {$inspection->overall_result}"
                );
            }

            \App\Models\AuditLog::log('inspection_completed', $inspection, 'Inspection completed', [
                'overall_result' => $inspection->overall_result,
                'overall_score' => $inspection->overall_score,
            ]);

            DB::commit();

            return redirect()->route('inspections.show', $inspection)
                ->with('success', 'Inspection completed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Inspection creation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to complete inspection. Please try again.');
        }
    }

    public function update(Request $request, Inspection $inspection)
    {
        if ($inspection->is_final) {
            return back()->with('error', 'This inspection has been finalized and cannot be edited.');
        }

        if (auth()->user()->isConsultant() && $inspection->consultant_id !== auth()->id()) {
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

            return redirect()->route('inspections.show', $inspection)
                ->with('success', 'Inspection updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update inspection. Please try again.');
        }
    }

    public function finalize(Inspection $inspection)
    {
        if ($inspection->is_final) {
            return back()->with('error', 'This inspection has already been finalized.');
        }

        if (auth()->user()->isConsultant() && $inspection->consultant_id !== auth()->id()) {
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

    private function determineNewStatus(Inspection $inspection): ?ApplicationStatus
    {
        // Only update status if inspection passed
        if (!$inspection->isPassed()) {
            return null;
        }

        return match($inspection->type) {
            'initial_inspection' => ApplicationStatus::INITIAL_INSPECTION_COMPLETED,
            'second_inspection' => ApplicationStatus::SECOND_INSPECTION_COMPLETED,
            default => null,
        };
    }

    private function calculateInspectionResults(array $checklistResults)
    {
        $itemsChecked = 0;
        $itemsPassed = 0;
        $itemsFailed = 0;
        $itemsNotApplicable = 0;
        $failedItems = [];
        $totalScore = 0;
        $maxScore = 0;
        $criticalFailures = 0;

        foreach ($checklistResults as $itemId => $result) {
            $itemsChecked++;
            $maxScore += $result['points_possible'] ?? 1;

            switch ($result['status']) {
                case 'pass':
                    $itemsPassed++;
                    $totalScore += $result['points_possible'] ?? 1;
                    break;
                case 'fail':
                    $itemsFailed++;
                    $failedItems[] = $itemId;
                    if ($result['is_critical'] ?? false) {
                        $criticalFailures++;
                    }
                    break;
                case 'n/a':
                    $itemsNotApplicable++;
                    $maxScore -= $result['points_possible'] ?? 1;
                    break;
            }
        }

        $overallScore = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        $overallResult = 'incomplete';
        if ($criticalFailures > 0) {
            $overallResult = 'fail';
        } elseif ($overallScore >= 80 && $itemsFailed == 0) {
            $overallResult = 'pass';
        } elseif ($overallScore >= 70) {
            $overallResult = 'conditional_pass';
        } elseif ($itemsChecked > 0) {
            $overallResult = 'fail';
        }

        return [
            'overall_result' => $overallResult,
            'overall_score' => round($overallScore, 2),
            'items_checked' => $itemsChecked,
            'items_passed' => $itemsPassed,
            'items_failed' => $itemsFailed,
            'items_not_applicable' => $itemsNotApplicable,
            'failed_items' => $failedItems,
            'requires_reinspection' => in_array($overallResult, ['fail', 'conditional_pass']),
        ];
    }
}