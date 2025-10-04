<?php


// app/Http/Controllers/AppointmentController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AppointmentController extends Controller
{
   public function index(Request $request)
{
    $user = auth()->user();
    
    // Debug logging
    \Log::info('Appointment Index Access', [
        'user_id' => $user->id,
        'user_type' => $user->user_type,
        'can_view_any' => $user->can('viewAny', Appointment::class)
    ]);
    
    Gate::authorize('viewAny', Appointment::class);

    $query = Appointment::with(['application.user', 'consultant', 'applicant'])
        ->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        })
        ->when($request->type, function ($q, $type) {
            return $q->where('type', $type);
        })
        ->when($request->date, function ($q, $date) {
            return $q->whereDate('scheduled_at', $date);
        });

    if (auth()->user()->isConsultant()) {
        $query->where('consultant_id', auth()->id());
    } elseif (auth()->user()->isApplicant()) {
        $query->where('applicant_id', auth()->id());
    }

    $appointments = $query->latest('scheduled_at')->paginate(15);

    return view('appointments.index', compact('appointments'));
}

   public function show(Appointment $appointment)
{
    $user = auth()->user();
    
    // Authorization check
    if ($user->user_type !== 'admin' && $appointment->consultant_id !== $user->id && $appointment->applicant_id !== $user->id) {
        abort(403, 'Unauthorized access to this appointment.');
    }

    $appointment->load([
        'application',
        'consultant',
        'applicant',
        'inspection'
    ]);

    // Return appropriate view based on user type
    if ($user->user_type === 'consultant') {
        return view('consultant.appointments.show', compact('appointment'));
    }
    
    if ($user->user_type === 'applicant') {
        return view('applicant.appointments.show', compact('appointment'));
    }
    
    return view('admin.appointments.show', compact('appointment'));
}

    public function create(Request $request)
    {
        Gate::authorize('create', Appointment::class);

        $application = null;
        if ($request->application_id) {
            $application = Application::findOrFail($request->application_id);
            Gate::authorize('view', $application);
        }

        $consultants = User::consultants()
            ->whereHas('consultant', function ($query) {
                $query->where('employment_status', 'active');
            })
            ->get();

        return view('appointments.create', compact('application', 'consultants'));
    }

public function store(Request $request)
{
    // Since this is consultant-only route, we can skip the policy or make it simple
    if (auth()->user()->user_type !== 'consultant') {
        abort(403, 'Only consultants can schedule appointments.');
    }

    $validated = $request->validate([
        'application_id' => 'required|exists:applications,id',
        'consultant_id' => 'required|exists:users,id',
        'type' => 'required|in:meet_and_greet,initial_inspection,second_inspection,final_inspection,contract_signing,follow_up',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'scheduled_at' => 'required|date|after:now',
        'duration' => 'required|integer|min:30|max:480',
        'location_address' => 'required|string',
        'location_type' => 'required|string|in:home,office,other',
        'location_notes' => 'nullable|string',
        'preparation_notes' => 'nullable|string',
    ]);

    $application = Application::findOrFail($validated['application_id']);
    
    // Verify consultant has access to this application
    if ($application->consultant_id !== auth()->id() && auth()->user()->user_type !== 'admin') {
        abort(403, 'You do not have access to this application.');
    }

    // Calculate end time - CAST TO INT to fix the Carbon error
    $scheduledAt = Carbon::parse($validated['scheduled_at']);
    $endsAt = $scheduledAt->copy()->addMinutes((int) $validated['duration']);

    DB::beginTransaction();
    try {
        $appointment = Appointment::create(array_merge($validated, [
            'applicant_id' => $application->user_id,
            'ends_at' => $endsAt,
            'status' => 'scheduled',
        ]));

        DB::commit();

        return redirect()->route('consultant.calendar')
            ->with('success', 'Appointment scheduled successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Appointment creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->withInput()->with('error', 'Failed to schedule appointment. Please try again.');
    }
}

  public function edit(Appointment $appointment)
{
    $user = auth()->user();
    
    // Authorization check
    if ($user->user_type !== 'admin' && $appointment->consultant_id !== $user->id) {
        abort(403, 'Unauthorized to edit this appointment.');
    }

    if (!in_array($appointment->status, ['scheduled', 'confirmed'])) {
        return back()->with('error', 'This appointment cannot be edited.');
    }

    $consultants = User::where('user_type', 'consultant')
        ->whereHas('consultant', function ($query) {
            $query->where('employment_status', 'active');
        })
        ->get();

    return view('consultant.appointments.edit', compact('appointment', 'consultants'));
}

    public function update(Request $request, Appointment $appointment)
    {
        Gate::authorize('update', $appointment);

        if (!in_array($appointment->status, ['scheduled', 'confirmed'])) {
            return back()->with('error', 'This appointment cannot be edited.');
        }

        $validated = $request->validate([
            'consultant_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:30|max:480',
            'location_address' => 'required|string',
            'location_type' => 'required|string|in:home,office,other',
            'location_notes' => 'nullable|string',
            'preparation_notes' => 'nullable|string',
        ]);

        $scheduledAt = Carbon::parse($validated['scheduled_at']);
        $endsAt = $scheduledAt->copy()->addMinutes($validated['duration']);

        $oldValues = $appointment->only(array_keys($validated));
        
        $appointment->update(array_merge($validated, [
            'ends_at' => $endsAt,
            'applicant_confirmed' => false,
            'consultant_confirmed' => false,
        ]));

        // Log the update
        \App\Models\AuditLog::log('appointment_updated', $appointment, 'Appointment details updated', [
            'old_values' => $oldValues,
            'new_values' => $validated
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully!');
    }

    public function confirm(Appointment $appointment)
    {
        Gate::authorize('confirm', $appointment);

        $user = auth()->user();
        $updateData = ['confirmed_at' => now(), 'confirmation_method' => 'web'];

        if ($user->id === $appointment->consultant_id) {
            $updateData['consultant_confirmed'] = true;
        } elseif ($user->id === $appointment->applicant_id) {
            $updateData['applicant_confirmed'] = true;
        }

        $appointment->update($updateData);

        // Update status if both parties confirmed
        if ($appointment->fresh()->isConfirmed()) {
            $appointment->update(['status' => 'confirmed']);
        }

        return back()->with('success', 'Appointment confirmed!');
    }

    public function complete(Request $request, Appointment $appointment)
    {
        Gate::authorize('complete', $appointment);

        $validated = $request->validate([
            'outcome' => 'nullable|string',
            'result' => 'nullable|in:pass,fail,conditional,pending',
            'completion_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $appointment->update(array_merge($validated, [
                'status' => 'completed',
                'completed_at' => now(),
            ]));

            // Create completion notification
            $appointment->application->notifications()->create([
                'user_id' => $appointment->applicant_id,
                'type' => 'appointment_completed',
                'title' => 'Appointment Completed',
                'message' => "Your {$appointment->type} appointment has been completed.",
                'priority' => 'normal',
            ]);

            // Log the completion
            \App\Models\AuditLog::log('appointment_completed', $appointment, 'Appointment marked as completed');

            DB::commit();

            return back()->with('success', 'Appointment marked as completed!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create application. Please try again.');
        }
    }
public function calendar()
{
    $weekAppointments = auth()->user()->consultantAppointments()
        ->whereBetween('scheduled_at', [
            now()->startOfMonth()->startOfWeek(), 
            now()->endOfMonth()->endOfWeek()
        ])
        ->with(['applicant', 'application'])
        ->get();

    return view('consultant.calendar', compact('weekAppointments'));
}
    public function applicantIndex(Request $request)
{
    $user = auth()->user();
    
    // Get appointments for this applicant
    $query = Appointment::with(['application', 'consultant'])
        ->where('applicant_id', $user->id);
    
    // Filter by status if provided
    if ($request->status) {
        $query->where('status', $request->status);
    }
    
    // Get upcoming appointments
    $upcomingAppointments = (clone $query)
        ->where('scheduled_at', '>', now())
        ->whereIn('status', ['scheduled', 'confirmed'])
        ->orderBy('scheduled_at')
        ->get();
    
    // Get past appointments
    $pastAppointments = (clone $query)
        ->where(function($q) {
            $q->where('scheduled_at', '<', now())
              ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
        })
        ->orderBy('scheduled_at', 'desc')
        ->paginate(10);
    
    // Stats
    $stats = [
        'total' => Appointment::where('applicant_id', $user->id)->count(),
        'upcoming' => Appointment::where('applicant_id', $user->id)
            ->where('scheduled_at', '>', now())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->count(),
        'completed' => Appointment::where('applicant_id', $user->id)
            ->where('status', 'completed')
            ->count(),
        'pending_confirmation' => Appointment::where('applicant_id', $user->id)
            ->where('applicant_confirmed', false)
            ->whereIn('status', ['scheduled'])
            ->count(),
    ];
    
    return view('applicant.appointments.index', compact(
        'upcomingAppointments',
        'pastAppointments',
        'stats'
    ));
}
}


