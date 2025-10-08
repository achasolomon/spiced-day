<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class ConsultantController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultant::with('user')
            ->when($request->department, function ($q, $department) {
                return $q->where('department', $department);
            })
            ->when($request->employment_status, function ($q, $status) {
                return $q->where('employment_status', $status);
            })
            ->when($request->availability, function ($q, $availability) {
                if ($availability === 'accepting') {
                    return $q->where('accepts_new_applications', true);
                } elseif ($availability === 'not_accepting') {
                    return $q->where('accepts_new_applications', false);
                }
            })
            ->when($request->search, function ($q, $search) {
                return $q->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            });

        $consultants = $query->latest()->paginate(15);

        return view('admin.consultants.index', compact('consultants'));
    }

    public function show(Consultant $consultant)
    {
        $consultant->load([
            'user',
            'assignedApplications' => function ($query) {
                $query->with('user')->latest()->limit(10);
            },
            'appointments' => function ($query) {
                $query->with('application.user')->latest()->limit(10);
            },
            'inspections' => function ($query) {
                $query->with('application.user')->latest()->limit(5);
            }
        ]);

        return view('admin.consultants.show', compact('consultant'));
    }

    /**
     * Get consultant data as JSON for modal display
     */
    public function getConsultantData(Consultant $consultant)
    {
        $consultant->load(['user', 'assignedApplications', 'appointments', 'inspections']);

        // Get stats
        $stats = [
            'total_applications' => $consultant->assignedApplications()->count(),
            'active_applications' => $consultant->assignedApplications()->whereIn('status', [
                'submitted', 'under_review', 'documents_pending', 'documents_submitted'
            ])->count(),
            'completed_applications' => $consultant->assignedApplications()->whereIn('status', [
                'approved', 'rejected'
            ])->count(),
            'pending_inspections' => $consultant->appointments()
                ->whereIn('type', ['initial_inspection', 'second_inspection'])
                ->where('status', 'scheduled')
                ->count(),
            'completed_inspections' => $consultant->inspections()->count(),
            'appointments_this_month' => $consultant->appointments()
                ->whereMonth('scheduled_at', now()->month)
                ->count(),
        ];

        // Get recent activity
        $recentActivity = \App\Models\AuditLog::where('user_id', $consultant->user_id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'id' => $consultant->id,
            'user_id' => $consultant->user_id,
            'user_name' => $consultant->user->name,
            'user_email' => $consultant->user->email,
            'employee_id' => $consultant->employee_id,
            'department' => $consultant->department,
            'position_title' => $consultant->position_title,
            'hire_date' => $consultant->hire_date->format('Y-m-d'),
            'hire_date_formatted' => $consultant->hire_date->format('M d, Y'),
            'employment_status' => $consultant->employment_status,
            'certifications' => $consultant->certifications,
            'specializations' => $consultant->specializations,
            'qualifications' => $consultant->qualifications,
            'languages' => $consultant->languages,
            'service_areas' => $consultant->service_areas,
            'max_concurrent_applications' => $consultant->max_concurrent_applications,
            'active_applications' => $consultant->active_applications,
            'accepts_new_applications' => $consultant->accepts_new_applications,
            'work_phone' => $consultant->work_phone,
            'emergency_contact_name' => $consultant->emergency_contact_name,
            'emergency_contact_phone' => $consultant->emergency_contact_phone,
            'can_approve_applications' => $consultant->can_approve_applications,
            'can_conduct_inspections' => $consultant->can_conduct_inspections,
            'can_view_all_applications' => $consultant->can_view_all_applications,
            'bio' => $consultant->bio,
            'internal_notes' => $consultant->internal_notes,
            'client_satisfaction_rating' => $consultant->client_satisfaction_rating,
            'total_applications_handled' => $consultant->total_applications_handled,
            'approval_rate' => $consultant->approval_rate,
            'stats' => $stats,
            'recent_activity' => $recentActivity,
        ]);
    }

    public function create()
    {
        $users = User::where('user_type', 'consultant')
            ->doesntHave('consultant')
            ->get();

        return view('admin.consultants.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:consultants,user_id',
            'employee_id' => 'required|string|unique:consultants,employee_id',
            'department' => 'nullable|string|max:255',
            'position_title' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'certifications' => 'nullable|array',
            'specializations' => 'nullable|array',
            'qualifications' => 'nullable|string',
            'languages' => 'nullable|array',
            'service_areas' => 'nullable|array',
            'max_concurrent_applications' => 'required|integer|min:1|max:50',
            'work_phone' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'can_approve_applications' => 'boolean',
            'can_conduct_inspections' => 'boolean',
            'can_view_all_applications' => 'boolean',
            'bio' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $consultant = Consultant::create(array_merge($validated, [
                'employment_status' => 'active',
                'accepts_new_applications' => true,
            ]));

            // Log the creation
            \App\Models\AuditLog::log('consultant_created', $consultant, 'New consultant profile created');

            DB::commit();

            return redirect()->route('admin.consultants.index')
                ->with('success', 'Consultant profile created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Consultant creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to create consultant profile. Please try again.');
        }
    }

    public function edit(Consultant $consultant)
    {
        return view('admin.consultants.edit', compact('consultant'));
    }

    public function update(Request $request, Consultant $consultant)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:consultants,employee_id,' . $consultant->id,
            'department' => 'nullable|string|max:255',
            'position_title' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'employment_status' => 'required|in:active,inactive,on_leave,terminated',
            'certifications' => 'nullable|array',
            'specializations' => 'nullable|array',
            'qualifications' => 'nullable|string',
            'languages' => 'nullable|array',
            'service_areas' => 'nullable|array',
            'max_concurrent_applications' => 'required|integer|min:1|max:50',
            'accepts_new_applications' => 'boolean',
            'work_phone' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'can_approve_applications' => 'boolean',
            'can_conduct_inspections' => 'boolean',
            'can_view_all_applications' => 'boolean',
            'bio' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $oldValues = $consultant->only(array_keys($validated));
        $consultant->update($validated);

        // Log the update
        \App\Models\AuditLog::log('consultant_updated', $consultant, 'Consultant profile updated', [
            'old_values' => $oldValues,
            'new_values' => $validated
        ]);

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Consultant profile updated successfully!');
    }

    public function destroy(Consultant $consultant)
    {
        // Check if consultant has active applications
        if ($consultant->active_applications > 0) {
            return back()->with('error', 'Cannot delete consultant with active applications.');
        }

        $consultant->delete();

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Consultant deleted successfully!');
    }

    public function updateWorkload(Consultant $consultant)
    {
        $consultant->updateWorkloadMetrics();

        return back()->with('success', 'Workload metrics updated!');
    }

    public function toggleAvailability(Consultant $consultant)
    {
        $consultant->update([
            'accepts_new_applications' => !$consultant->accepts_new_applications
        ]);

        $status = $consultant->accepts_new_applications ? 'available' : 'unavailable';
        
        return back()->with('success', "Consultant marked as {$status} for new applications!");
    }

    /**
 * Get available users for consultant creation
 */
public function getAvailableUsers()
{
    $users = User::where('user_type', 'consultant')
        ->doesntHave('consultant')
        ->select('id', 'name', 'email')
        ->get();

    return response()->json($users);
}

}