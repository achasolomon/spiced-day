<?php
// app/Http/Controllers/ConsultantController.php

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
        Gate::authorize('viewAny', Consultant::class);

        $query = Consultant::with('user')
            ->when($request->department, function ($q, $department) {
                return $q->where('department', $department);
            })
            ->when($request->employment_status, function ($q, $status) {
                return $q->where('employment_status', $status);
            })
            ->when($request->search, function ($q, $search) {
                return $q->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            });

        $consultants = $query->latest()->paginate(15);

        return view('consultants.index', compact('consultants'));
    }

    public function show(Consultant $consultant)
    {
        Gate::authorize('view', $consultant);

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

        return view('consultants.show', compact('consultant'));
    }

    public function create()
    {
        Gate::authorize('create', Consultant::class);

        $users = User::where('user_type', 'consultant')
            ->doesntHave('consultant')
            ->get();

        return view('consultants.create', compact('users'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Consultant::class);

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

            return redirect()->route('consultants.show', $consultant)
                ->with('success', 'Consultant profile created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create consultant profile. Please try again.');
        }
    }

    public function edit(Consultant $consultant)
    {
        Gate::authorize('update', $consultant);

        return view('consultants.edit', compact('consultant'));
    }

    public function update(Request $request, Consultant $consultant)
    {
        Gate::authorize('update', $consultant);

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

        return redirect()->route('consultants.show', $consultant)
            ->with('success', 'Consultant profile updated successfully!');
    }

    public function updateWorkload(Consultant $consultant)
    {
        Gate::authorize('update', $consultant);

        $consultant->updateWorkloadMetrics();

        return back()->with('success', 'Workload metrics updated!');
    }

    public function toggleAvailability(Consultant $consultant)
    {
        Gate::authorize('update', $consultant);

        $consultant->update([
            'accepts_new_applications' => !$consultant->accepts_new_applications
        ]);

        $status = $consultant->accepts_new_applications ? 'available' : 'unavailable';
        
        return back()->with('success', "Consultant marked as {$status} for new applications!");
    }
}
