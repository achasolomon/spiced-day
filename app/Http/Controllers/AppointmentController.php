<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Application;
use App\Models\User;
use App\Enums\ApplicationStatus;
use App\Services\ApplicationStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    protected $statusService;

    public function __construct(ApplicationStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
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
        
        if ($user->user_type !== 'admin' && $appointment->consultant_id !== $user->id && $appointment->applicant_id !== $user->id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        $appointment->load([
            'application',
            'consultant',
            'applicant',
            'inspection'
        ]);

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
        'location_type' => 'required|string|in:home,office,virtual,other',
        'location_address' => 'required_unless:location_type,virtual|nullable|string',
        'virtual_meeting_link' => 'required_if:location_type,virtual|nullable|url',
        'location_notes' => 'nullable|string',
        'preparation_notes' => 'nullable|string',
        'consultant_confirmed' => 'nullable|boolean',
    ]);

    $application = Application::findOrFail($validated['application_id']);
    
    if ($application->consultant_id !== auth()->id() && auth()->user()->user_type !== 'admin') {
        abort(403, 'You do not have access to this application.');
    }

    $scheduledAt = Carbon::parse($validated['scheduled_at']);
    $endsAt = $scheduledAt->copy()->addMinutes((int) $validated['duration']);

    // Handle location address based on type
    $locationAddress = $validated['location_type'] === 'virtual' 
        ? ($validated['virtual_meeting_link'] ?? null)
        : ($validated['location_address'] ?? null);

    DB::beginTransaction();
    try {
        $appointment = Appointment::create([
            'application_id' => $validated['application_id'],
            'consultant_id' => $validated['consultant_id'],
            'applicant_id' => $application->user_id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'scheduled_at' => $scheduledAt,
            'ends_at' => $endsAt,
            'duration' => $validated['duration'],
            'location_type' => $validated['location_type'],
            'location_address' => $locationAddress,
            'location_notes' => $validated['location_notes'] ?? null,
            'preparation_notes' => $validated['preparation_notes'] ?? null,
            'status' => 'scheduled',
            'consultant_confirmed' => $request->has('consultant_confirmed'),
            'applicant_confirmed' => false,
        ]);

        // Update application status based on appointment type
        $newStatus = $this->getStatusForAppointmentType($validated['type']);
        if ($newStatus) {
            $this->statusService->transitionTo(
                $application, 
                $newStatus,
                "Appointment scheduled for {$validated['type']}"
            );
        }

        // Create notification for applicant
        \App\Models\Notification::create([
            'user_id' => $application->user_id,
            'application_id' => $application->id,
            'type' => 'appointment_scheduled',
            'title' => 'New Appointment Scheduled',
            'message' => "A new appointment has been scheduled: {$appointment->title} on {$scheduledAt->format('F j, Y \a\t g:i A')}",
            'priority' => 'high',
            'action_url' => route('applicant.appointments.show', $appointment),
            'data' => [
                'appointment_id' => $appointment->id,
                'type' => $appointment->type,
                'scheduled_at' => $scheduledAt->toIso8601String(),
            ],
        ]);

        // Log the appointment creation
        \App\Models\AuditLog::log(
            'appointment_created',
            $appointment,
            "Appointment created: {$appointment->title}",
            ['type' => $appointment->type]
        );

        DB::commit();

        return redirect()->route('consultant.calendar')
            ->with('success', 'Appointment scheduled successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Appointment creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'validated_data' => $validated
        ]);
        
        return back()
            ->withInput()
            ->withErrors(['error' => 'Failed to schedule appointment: ' . $e->getMessage()]);
    }
}

    public function edit(Appointment $appointment)
    {
        $user = auth()->user();
        
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
        // Cast duration to integer to avoid type error
        $endsAt = $scheduledAt->copy()->addMinutes((int) $validated['duration']);

        $oldValues = $appointment->only(array_keys($validated));
        
        DB::beginTransaction();
        try {
            $appointment->update(array_merge($validated, [
                'ends_at' => $endsAt,
                'applicant_confirmed' => false,
                'consultant_confirmed' => false,
                'status' => 'scheduled', // Reset to scheduled since confirmations are reset
            ]));

            // Log the update
            \App\Models\AuditLog::log('appointment_updated', $appointment, 'Appointment details updated', [
                'old_values' => $oldValues,
                'new_values' => $validated
            ]);

            // Create notification for the applicant
            \App\Models\Notification::create([
                'user_id' => $appointment->applicant_id,
                'application_id' => $appointment->application_id,
                'type' => 'appointment_updated',
                'title' => 'Appointment Updated - Confirmation Required',
                'message' => "Your {$appointment->title} has been updated. Please review and confirm the new appointment details. Scheduled for {$scheduledAt->format('F j, Y \a\t g:i A')}.",
                'priority' => 'high',
                'data' => [
                    'appointment_id' => $appointment->id,
                    'scheduled_at' => $scheduledAt->toIso8601String(),
                    'changes_made' => array_keys(array_diff_assoc($validated, $oldValues))
                ],
            ]);

            // Send email notification to the applicant
            try {
                \Mail::to($appointment->applicant->email)->send(
                    new \App\Mail\AppointmentUpdated($appointment)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send appointment update email', [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the entire operation if email fails
            }

            DB::commit();

            return redirect()->route('consultant.appointments.show', $appointment)
                ->with('success', 'Appointment updated successfully! The applicant has been notified and will need to confirm the changes.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Appointment update failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Failed to update appointment. Please try again.');
        }
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

            // Update application status based on appointment type
            $newStatus = $this->getStatusForCompletedAppointment($appointment->type);
            if ($newStatus) {
                $this->statusService->transitionTo(
                    $appointment->application,
                    $newStatus,
                    "Completed {$appointment->type} appointment"
                );
            }

            \App\Models\AuditLog::log('appointment_completed', $appointment, 'Appointment marked as completed');

            DB::commit();

            return back()->with('success', 'Appointment marked as completed!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to complete appointment. Please try again.');
        }
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        Gate::authorize('cancel', $appointment);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
            ]);

            // Notify both parties
            \App\Models\Notification::create([
                'user_id' => $appointment->applicant_id,
                'application_id' => $appointment->application_id,
                'type' => 'appointment_cancelled',
                'title' => 'Appointment Cancelled',
                'message' => "Your {$appointment->type} appointment has been cancelled. Reason: {$validated['cancellation_reason']}",
                'priority' => 'high',
            ]);

            \App\Models\AuditLog::log('appointment_cancelled', $appointment, 'Appointment cancelled', $validated);

            DB::commit();

            return back()->with('success', 'Appointment cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel appointment.');
        }
    }

    // public function calendar()
    // {
    //     $weekAppointments = auth()->user()->consultantAppointments()
    //         ->whereBetween('scheduled_at', [
    //             now()->startOfMonth()->startOfWeek(), 
    //             now()->endOfMonth()->endOfWeek()
    //         ])
    //         ->with(['applicant', 'application'])
    //         ->get();

    //     return view('consultant.calendar', compact('weekAppointments'));
    // }

    // In your AppointmentController.php

public function calendar()
{
    $weekAppointments = auth()->user()->consultantAppointments()
        ->whereBetween('scheduled_at', [
            now()->startOfMonth()->startOfWeek(), 
            now()->endOfMonth()->endOfWeek()
        ])
        ->with(['applicant', 'application'])
        ->get();
    
    // Get all active applications for the consultant
    $applications = \App\Models\Application::with('user')
        ->where('consultant_id', auth()->id())
        ->whereIn('status', ['submitted', 'meet_and_greet_completed', 'document_submitted', 'document_approved', 'second_inspection_completed' ])
        ->select('id', 'user_id', 'educator_first_name', 'application_number', 'address_line_1',)
        ->get()
        ->map(function($app) {
            return [
                'id' => $app->id,
                'user_id' => $app->user_id,
                'full_name' => $app->full_name,
                'application_number' => $app->application_number,
                'full_address' => $app->full_address,
            ];
        });

    return view('consultant.calendar', compact('weekAppointments', 'applications'));
}
    public function applicantIndex(Request $request)
    {
        $user = auth()->user();
        
        $query = Appointment::with(['application', 'consultant'])
            ->where('applicant_id', $user->id);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $upcomingAppointments = (clone $query)
            ->where('scheduled_at', '>', now())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('scheduled_at')
            ->get();
        
        $pastAppointments = (clone $query)
            ->where(function($q) {
                $q->where('scheduled_at', '<', now())
                  ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
            })
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);
        
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

    private function getStatusForAppointmentType(string $type): ?ApplicationStatus
    {
        return match($type) {
            'meet_and_greet' => ApplicationStatus::MEET_AND_GREET_SCHEDULED,
            'initial_inspection' => ApplicationStatus::INITIAL_INSPECTION_SCHEDULED,
            'second_inspection' => ApplicationStatus::SECOND_INSPECTION_SCHEDULED,
            'contract_signing' => ApplicationStatus::CONTRACT_SIGNING_SCHEDULED,
            default => null,
        };
    }

    private function getStatusForCompletedAppointment(string $type): ?ApplicationStatus
    {
        return match($type) {
            'meet_and_greet' => ApplicationStatus::MEET_AND_GREET_COMPLETED,
            'initial_inspection' => ApplicationStatus::INITIAL_INSPECTION_COMPLETED,
            'second_inspection' => ApplicationStatus::SECOND_INSPECTION_COMPLETED,
            'contract_signing' => ApplicationStatus::CONTRACT_SIGNED,
            default => null,
        };
    }
}