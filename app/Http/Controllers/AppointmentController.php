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
use Illuminate\Support\Str; 
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
        
        $isAdmin = $user->user_type == 'admin';
        $isConsultant = $appointment->consultant_id == $user->id;
        
        // FIXED: Handle both registered applicants and anonymous applications
        $isApplicant = false;
        if ($appointment->applicant_id) {
            // Registered applicant
            $isApplicant = $appointment->applicant_id == $user->id;
        } else {
            // Anonymous application - check if user owns the application
            $isApplicant = $appointment->application && $appointment->application->user_id == $user->id;
        }
        
        if (!$isAdmin && !$isConsultant && !$isApplicant) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        $appointment->load([
            'application',
            'consultant',
            'applicant',
            'inspection'
        ]);

        if ($user->user_type == 'consultant') {
            return view('consultant.appointments.show', compact('appointment'));
        }
        
        if ($user->user_type == 'applicant') {
            return view('applicant.appointments.show', compact('appointment'));
        }
        
        return view('admin.appointments.show', compact('appointment'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Appointment::class);

        $user = auth()->user();
        $isAdmin = $user->user_type === 'admin';
        $isConsultant = $user->user_type === 'consultant';

        // If application_id is provided, redirect to application show page where they can use the schedule modal
        if ($request->application_id) {
            $application = Application::findOrFail($request->application_id);
            
            // Check authorization using the same logic as ApplicationController
            $canView = false;
            if ($application->user_id == $user->id) {
                $canView = true;
            }
            if ($isAdmin) {
                $canView = true;
            }
            if ($isConsultant && $application->consultant_id == $user->id) {
                $canView = true;
            }
            
            if (!$canView) {
                abort(403, 'Unauthorized access to this application.');
            }
            
            // Redirect to the appropriate application show page based on user type
            if ($isAdmin) {
                return redirect()->route('admin.applications.show', $application)
                    ->with('info', 'Use the "Schedule Appointment" button to create a new appointment.');
            } elseif ($isConsultant) {
                return redirect()->route('consultant.applications.show', $application)
                    ->with('info', 'Use the "Schedule Appointment" button to create a new appointment.');
            }
        }

        // If no application_id, redirect to calendar where they can schedule appointments
        if ($isAdmin) {
            return redirect()->route('admin.appointments.index')
                ->with('info', 'Please select an application first to schedule an appointment.');
        } elseif ($isConsultant) {
            return redirect()->route('consultant.calendar')
                ->with('info', 'Please select an application first to schedule an appointment.');
        }

        // Fallback (should not reach here due to Gate check)
        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        if (auth()->user()->user_type != 'consultant') {
            abort(403, 'Only consultants can schedule appointments.');
        }

        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'consultant_id' => 'required|exists:users,id',
            'type' => 'required|in:meet_and_greet,initial_inspection,second_inspection,final_inspection,contract_signing,follow_up',
            'inspection_type' => 'nullable|in:scheduled,unscheduled',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:5|max:480',
            'location_type' => 'required|string|in:home,office,virtual,other',
            'location_address' => 'required_unless:location_type,virtual|nullable|string',
            'virtual_meeting_link' => 'required_if:location_type,virtual|nullable|url',
            'location_notes' => 'nullable|string',
            'preparation_notes' => 'nullable|string',
            'consultant_confirmed' => 'nullable|boolean',
            'user_timezone' => 'nullable|string',
        ]);

        $application = Application::findOrFail($validated['application_id']);

        if ($application->consultant_id != auth()->id() && auth()->user()->user_type !== 'admin') {
            abort(403, 'You do not have access to this application.');
        }

        // Validate appointment prerequisites based on type
        $prerequisiteError = $this->validateAppointmentPrerequisites($application, $validated['type']);
        if ($prerequisiteError) {
            return back()
                ->withInput()
                ->withErrors(['type' => $prerequisiteError]);
        }

        // Follow-up appointments require inspection_type
        if ($validated['type'] === 'follow_up' && empty($validated['inspection_type'])) {
            return back()
                ->withInput()
                ->withErrors(['inspection_type' => 'Inspection type is required for follow-up appointments.']);
        }

        // Check if this is an unscheduled inspection
        $isUnscheduled = $validated['type'] === 'follow_up' && $validated['inspection_type'] === 'unscheduled';

        // Parse datetime-local input to UTC
        $userTimezone = $validated['user_timezone'] ?? config('app.timezone');
        $scheduledAt = Carbon::createFromFormat('Y-m-d\TH:i', $validated['scheduled_at'], $userTimezone)
            ->setTimezone('UTC');
        $endsAt = $scheduledAt->copy()->addMinutes((int) $validated['duration']);

        // Determine location address or virtual link
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
                'inspection_type' => $validated['type'] === 'follow_up' 
                ? $validated['inspection_type'] : 'scheduled',
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
                'applicant_confirmed' => $isUnscheduled ? true : false, // Auto-confirm unscheduled
                'confirmation_token' => $isUnscheduled ? null : Str::random(60),
                'confirmation_token_expires_at' => $isUnscheduled ? null : now()->addDays(3),
            ]);

            // Update application status based on appointment type
            $newStatus = $this->getStatusForAppointmentType($validated['type']);
            if ($newStatus) {
                // Pass the isUnscheduled flag to the service
                $this->statusService->transitionTo(
                    $application,
                    $newStatus,
                    "Appointment scheduled for {$validated['type']}",
                    $appointment,
                    $isUnscheduled // Pass unscheduled flag
                );
            }

            // Only create notification for scheduled inspections
            if (!$isUnscheduled && $application->user_id) {
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
            }

            // Log the appointment creation
            \App\Models\AuditLog::log(
                'appointment_created',
                $appointment,
                "Appointment created: {$appointment->title}" . ($isUnscheduled ? ' (Unscheduled)' : ''),
                [
                    'type' => $appointment->type,
                    'inspection_type' => $appointment->inspection_type,
                ]
            );

            DB::commit();

            $message = $isUnscheduled 
                ? 'Unscheduled inspection created successfully! The applicant will not be notified.'
                : 'Appointment scheduled successfully!';

            return redirect()->route('consultant.calendar')
                ->with('success', $message);

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
        
        // Load the application relationship if not already loaded
        if (!$appointment->relationLoaded('application')) {
            $appointment->load('application');
        }
        
        // Admin can edit all appointments
        if ($user->user_type == 'admin') {
            // Allow edit
        }
        // Consultant can edit if they're assigned to the appointment or the application
        elseif ($user->user_type == 'consultant') {
            $isAssignedToAppointment = $appointment->consultant_id == $user->id;
            $isAssignedToApplication = $appointment->application && $appointment->application->consultant_id == $user->id;
            
            if (!$isAssignedToAppointment && !$isAssignedToApplication) {
                abort(403, 'Unauthorized to edit this appointment.');
            }
        }
        // Other user types cannot edit
        else {
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
        // Load necessary relationships if not already loaded
        if (!$appointment->relationLoaded('application')) {
            $appointment->load('application');
        }
        if ($appointment->applicant_id && !$appointment->relationLoaded('applicant')) {
            $appointment->load('applicant');
        }
        
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

        // Parse the datetime-local input (which is in user's local timezone) and convert to application timezone
        // datetime-local inputs don't include timezone info, so we interpret it as the application timezone
        // First, parse as if it's in the app timezone, then convert to UTC for storage
        $scheduledAt = Carbon::createFromFormat('Y-m-d\TH:i', $validated['scheduled_at'], config('app.timezone'))
            ->setTimezone('UTC'); // Store in UTC in database
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

            // Create notification for the applicant (only if they have a user account)
            if ($appointment->applicant_id) {
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
            }

            // Send email notification to the applicant
            // Use the same type-specific emails as when creating appointments
            try {
                // Ensure application is loaded
                if (!$appointment->relationLoaded('application')) {
                    $appointment->load('application');
                }
                
                // Get applicant email - use user email if available, otherwise use application email
                $applicantEmail = null;
                if ($appointment->applicant_id && $appointment->applicant) {
                    $applicantEmail = $appointment->applicant->email;
                } elseif ($appointment->application) {
                    $applicantEmail = $appointment->application->email;
                }
                
                if ($applicantEmail && $appointment->application) {
                    // Send type-specific email based on appointment type (same as creation)
                    switch ($appointment->type) {
                        case 'meet_and_greet':
                            \Mail::to($applicantEmail)->send(
                                new \App\Mail\MeetGreetScheduledEmail($appointment->application, $appointment)
                            );
                            \Log::info('Meet & Greet update email sent', [
                                'appointment_id' => $appointment->id,
                                'email' => $applicantEmail
                            ]);
                            break;
                            
                        case 'initial_inspection':
                            \Mail::to($applicantEmail)->send(
                                new \App\Mail\InitialInspectionScheduledEmail($appointment->application, $appointment)
                            );
                            \Log::info('Initial Inspection update email sent', [
                                'appointment_id' => $appointment->id,
                                'email' => $applicantEmail
                            ]);
                            break;
                            
                        default:
                            // For other appointment types, use the generic AppointmentUpdated email
                            \Mail::to($applicantEmail)->send(
                                new \App\Mail\AppointmentUpdated($appointment)
                            );
                            \Log::info('Generic appointment update email sent', [
                                'appointment_id' => $appointment->id,
                                'type' => $appointment->type,
                                'email' => $applicantEmail
                            ]);
                            break;
                    }
                } else {
                    \Log::warning('No email found for appointment update notification', [
                        'appointment_id' => $appointment->id,
                        'applicant_id' => $appointment->applicant_id,
                        'application_id' => $appointment->application_id,
                        'has_application' => $appointment->application ? true : false
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send appointment update email', [
                    'appointment_id' => $appointment->id,
                    'appointment_type' => $appointment->type ?? 'unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
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
        // Ensure application relationship is loaded for policy check
        if (!$appointment->relationLoaded('application')) {
            $appointment->load('application');
        }
        
        Gate::authorize('confirm', $appointment);

        $user = auth()->user();
        $updateData = ['confirmed_at' => now(), 'confirmation_method' => 'web'];

        if ($user->id == $appointment->consultant_id) {
            $updateData['consultant_confirmed'] = true;
        } elseif ($user->id == $appointment->applicant_id) {
            $updateData['applicant_confirmed'] = true;
        } else {
            // Handle case where user is confirming for anonymous application
            if ($appointment->application && $appointment->application->user_id == $user->id) {
                $updateData['applicant_confirmed'] = true;
            }
        }

        $appointment->update($updateData);

        if ($appointment->fresh()->isConfirmed()) {
            $appointment->update(['status' => 'confirmed']);
        }

        return back()->with('success', 'Appointment confirmed!');
    }
    public function complete(Request $request, Appointment $appointment)
    {
        // Ensure application relationship is loaded for policy check
        if (!$appointment->relationLoaded('application')) {
            $appointment->load('application');
        }
        
        Gate::authorize('complete', $appointment);

        if ($appointment->scheduled_at && $appointment->scheduled_at->isFuture()) {
            return back()->with('error', 'This appointment cannot be marked as completed before its scheduled start time.');
        }

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
    
    public function reschedule(Request $request, Appointment $appointment)
    {
    // Authorization: same level as update
    Gate::authorize('update', $appointment);

    // Allow rescheduling for scheduled, confirmed, AND rescheduled status
    if (!in_array($appointment->status, ['scheduled', 'confirmed', 'rescheduled'])) {
        return back()->with('error', 'This appointment cannot be rescheduled. Current status: ' . $appointment->status);
    }

    $validated = $request->validate([
        'scheduled_at' => 'required|date|after:now',
        'reschedule_reason' => 'nullable|string|max:1000',
        'user_timezone' => 'nullable|string',
    ]);

    $userTimezone = $validated['user_timezone'] ?? config('app.timezone');
    $scheduledAt = Carbon::createFromFormat(
        'Y-m-d\TH:i',
        $validated['scheduled_at'],
        $userTimezone
    )->setTimezone('UTC');

    $endsAt = $scheduledAt->copy()->addMinutes((int) $appointment->duration);

    DB::beginTransaction();
    try {
        $appointment->update([
            'scheduled_at' => $scheduledAt,
            'ends_at' => $endsAt,
            'consultant_confirmed' => true,
            'applicant_confirmed' => false,
            'status' => 'scheduled',
            'reschedule_reason' => $validated['reschedule_reason'] ?? $appointment->reschedule_reason,
            // Regenerate confirmation token for email confirmation
            'confirmation_token' => \Str::random(60),
            'confirmation_token_expires_at' => now()->addDays(3),
        ]);

        // Notify applicant (only if registered)
        if ($appointment->applicant_id) {
            \App\Models\Notification::create([
                'user_id' => $appointment->applicant_id,
                'application_id' => $appointment->application_id,
                'type' => 'appointment_rescheduled',
                'title' => 'Appointment Rescheduled',
                'message' => "Your appointment has been rescheduled to {$scheduledAt->format('F j, Y \\a\\t g:i A')}. Please confirm.",
                'priority' => 'high',
                'action_url' => route('applicant.appointments.show', $appointment),
            ]);
        }

        // Send email notification
        try {
            $applicantEmail = null;
            if ($appointment->applicant_id && $appointment->applicant) {
                $applicantEmail = $appointment->applicant->email;
            } elseif ($appointment->application) {
                $applicantEmail = $appointment->application->email;
            }
            
            if ($applicantEmail) {
                \Mail::to($applicantEmail)->send(
                    new \App\Mail\AppointmentUpdated($appointment)
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send reschedule email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }

        \App\Models\AuditLog::log(
            'appointment_rescheduled',
            $appointment,
            'Appointment rescheduled by consultant',
            ['scheduled_at' => $scheduledAt, 'reason' => $validated['reschedule_reason']]
        );

        DB::commit();

        return redirect()
            ->route('consultant.appointments.show', $appointment)
            ->with('success', 'Appointment rescheduled successfully. Applicant has been notified and needs to confirm.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Appointment reschedule failed', [
            'appointment_id' => $appointment->id,
            'error' => $e->getMessage(),
        ]);

        return back()->with('error', 'Failed to reschedule appointment. Please try again.');
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

    public function calendar(Request $request)
    {
        // Get the month and year from request, default to current month
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // Create Carbon instance for the selected month
        $selectedDate = Carbon::create($year, $month, 1);
        
        // Get appointments for the selected month (including previous/next week days for full calendar view)
        $weekAppointments = auth()->user()->consultantAppointments()
            ->whereBetween('scheduled_at', [
                $selectedDate->copy()->startOfMonth()->startOfWeek(), 
                $selectedDate->copy()->endOfMonth()->endOfWeek()
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

        return view('consultant.calendar', compact('weekAppointments', 'applications', 'selectedDate'));
    }
    public function applicantIndex(Request $request)
    {
        $user = auth()->user();
        
        // UPDATED: Exclude unscheduled inspections from applicant view
        $query = Appointment::with(['application', 'consultant'])
            ->where(function($q) use ($user) {
                // Appointments where user is the direct applicant
                $q->where('applicant_id', $user->id)
                // OR appointments for applications owned by this user (anonymous cases)
                ->orWhereHas('application', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            })
            // Exclude unscheduled inspections
            ->where(function($q) {
                $q->whereNull('inspection_type')
                  ->orWhere('inspection_type', '!=', 'unscheduled');
            });
        
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
        
        // UPDATED: Stats also exclude unscheduled
        $stats = [
            'total' => (clone $query)->count(),
            'upcoming' => (clone $query)
                ->where('scheduled_at', '>', now())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->count(),
            'completed' => (clone $query)
                ->where('status', 'completed')
                ->count(),
            'pending_confirmation' => (clone $query)
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


    public function getStatusForAppointmentType(string $type): ?ApplicationStatus
    {
        return match($type) {
            'meet_and_greet' => ApplicationStatus::MEET_AND_GREET_SCHEDULED,
            'initial_inspection' => ApplicationStatus::INITIAL_INSPECTION_SCHEDULED,
            'second_inspection' => ApplicationStatus::SECOND_INSPECTION_SCHEDULED,
            'final_inspection' => ApplicationStatus::FINAL_INSPECTION_SCHEDULED,
            'contract_signing' => ApplicationStatus::CONTRACT_SIGNING_SCHEDULED,
            'follow_up' => ApplicationStatus::COMPLIANCE_INSPECTION_SCHEDULED,
            default => null,
        };
    }
    public function getStatusForCompletedAppointment(string $type): ?ApplicationStatus
    {
        return match($type) {
            'meet_and_greet' => ApplicationStatus::MEET_AND_GREET_COMPLETED,
            'initial_inspection' => ApplicationStatus::INITIAL_INSPECTION_COMPLETED,
            'second_inspection' => ApplicationStatus::SECOND_INSPECTION_COMPLETED,
            'final_inspection' => ApplicationStatus::FINAL_INSPECTION_COMPLETED,
            'contract_signing' => ApplicationStatus::CONTRACT_SIGNED,
            'follow_up' => ApplicationStatus::COMPLIANCE_INSPECTION_COMPLETED,
            default => null,
        };
    }

    /**
     * Validate that prerequisites are met before scheduling an appointment
     * Returns error message if validation fails, null if validation passes
     */
   protected function validateAppointmentPrerequisites(Application $application, string $appointmentType): ?string
{
    $currentStatus = ApplicationStatus::tryFrom($application->status);
    
    if (!$currentStatus) {
        throw new \Exception('Invalid application status.');
    }

    // Define prerequisites for each appointment type
    $prerequisites = [
        'initial_inspection' => [
            'required_status' => ApplicationStatus::MEET_AND_GREET_COMPLETED,
            'required_appointment_type' => 'meet_and_greet',
            'message' => 'Cannot schedule initial inspection. The meet and greet must be completed first.',
        ],
        'second_inspection' => [
            'required_status' => ApplicationStatus::INITIAL_INSPECTION_COMPLETED,
            'required_appointment_type' => 'initial_inspection',
            'message' => 'Cannot schedule second inspection. The initial inspection must be completed first.',
        ],
        'final_inspection' => [
            'required_status' => ApplicationStatus::SECOND_INSPECTION_COMPLETED,
            'required_appointment_type' => 'second_inspection',
            'message' => 'Cannot schedule final inspection. The second inspection must be completed first.',
        ],
        'contract_signing' => [
            'required_status' => ApplicationStatus::FINAL_INSPECTION_COMPLETED,
            'required_appointment_type' => null,
            'message' => 'Cannot schedule contract signing. The final inspection must be passed first.',
        ],
        // ADD THIS: Allow follow_up for ACTIVE or COMPLIANCE_INSPECTION_DUE status
        'follow_up' => [
            'required_status' => ApplicationStatus::ACTIVE,
            'required_appointment_type' => null,
            'message' => 'Cannot schedule follow-up appointment. The dayhome must be active first.',
            'allow_from_statuses' => [
                ApplicationStatus::ACTIVE,
                ApplicationStatus::COMPLIANCE_INSPECTION_DUE,
            ],
        ],
    ];

    // Check if this appointment type has prerequisites
    if (!isset($prerequisites[$appointmentType])) {
        return null; // No prerequisites for this type (e.g., meet_and_greet)
    }

    $prerequisite = $prerequisites[$appointmentType];
    
    // Special handling for follow_up appointments
    if ($appointmentType === 'follow_up' && isset($prerequisite['allow_from_statuses'])) {
        if (!in_array($currentStatus, $prerequisite['allow_from_statuses'])) {
            return $prerequisite['message'];
        }
        return null; // Allow the appointment
    }

    $requiredStatus = $prerequisite['required_status'];
    $message = $prerequisite['message'];

    // Check if prerequisite appointment is completed
    $prerequisiteCompleted = false;
    if ($prerequisite['required_appointment_type']) {
        $prerequisiteCompleted = Appointment::where('application_id', $application->id)
            ->where('type', $prerequisite['required_appointment_type'])
            ->where('status', 'completed')
            ->exists();
    }

    // Check if application status is at or beyond required status
    $statusOrder = [
        ApplicationStatus::DRAFT,
        ApplicationStatus::SUBMITTED,
        ApplicationStatus::MEET_AND_GREET_SCHEDULED,
        ApplicationStatus::MEET_AND_GREET_COMPLETED,
        ApplicationStatus::INITIAL_INSPECTION_SCHEDULED,
        ApplicationStatus::INITIAL_INSPECTION_COMPLETED,
        ApplicationStatus::DOCUMENTS_PENDING,
        ApplicationStatus::DOCUMENTS_SUBMITTED,
        ApplicationStatus::DOCUMENTS_APPROVED,
        ApplicationStatus::SECOND_INSPECTION_SCHEDULED,
        ApplicationStatus::SECOND_INSPECTION_COMPLETED,
        ApplicationStatus::FINAL_INSPECTION_SCHEDULED,
        ApplicationStatus::FINAL_INSPECTION_COMPLETED,
        ApplicationStatus::FINAL_INSPECTION_PASSED,
        ApplicationStatus::FINAL_INSPECTION_FAILED,
        ApplicationStatus::CONTRACT_SIGNING_SCHEDULED,
        ApplicationStatus::CONTRACT_SIGNED,
        ApplicationStatus::APPROVED,
        ApplicationStatus::ACTIVE,
    ];

    $currentIndex = array_search($currentStatus, $statusOrder);
    $requiredIndex = array_search($requiredStatus, $statusOrder);

    // Status is valid if it's at or beyond the required status
    $statusValid = ($currentIndex !== false && $requiredIndex !== false && $currentIndex >= $requiredIndex);

    // For contract_signing, also check for FINAL_INSPECTION_PASSED specifically
    if ($appointmentType === 'contract_signing') {
        $statusValid = ($currentStatus === ApplicationStatus::FINAL_INSPECTION_COMPLETED || 
                       $currentStatus === ApplicationStatus::CONTRACT_SIGNING_SCHEDULED ||
                       $currentStatus === ApplicationStatus::CONTRACT_SIGNED ||
                       $currentStatus === ApplicationStatus::APPROVED ||
                       $currentStatus === ApplicationStatus::ACTIVE);
    }

    // If neither the appointment is completed nor the status is valid, return error
    if (!$prerequisiteCompleted && !$statusValid) {
        return $message;
    }

    return null; // Validation passed
}

    /**
 * Admin view of all appointments
 */
    public function adminIndex(Request $request)
    {
        $query = Appointment::with(['application.user', 'consultant', 'applicant'])
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->type, function ($q, $type) {
                return $q->where('type', $type);
            })
            ->when($request->consultant_id, function ($q, $consultantId) {
                return $q->where('consultant_id', $consultantId);
            })
            ->when($request->date_from, function ($q, $dateFrom) {
                return $q->whereDate('scheduled_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($q, $dateTo) {
                return $q->whereDate('scheduled_at', '<=', $dateTo);
            })
            ->when($request->search, function ($q, $search) {
                return $q->whereHas('applicant', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

        $appointments = $query->latest('scheduled_at')->paginate(20);

        // Get statistics
        $stats = [
            'total' => Appointment::count(),
            'scheduled' => Appointment::where('status', 'scheduled')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
            'upcoming' => Appointment::where('scheduled_at', '>', now())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->count(),
            'today' => Appointment::whereDate('scheduled_at', today())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->count(),
        ];

        // Get consultants for filter
        $consultants = \App\Models\User::consultants()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.appointments.index', compact('appointments', 'stats', 'consultants'));
    }

    public function sendManualReminder(Appointment $appointment)
    {
        try {
            // Send to applicant
            Mail::to($appointment->applicant->email)
                ->send(new AppointmentReminder24Hours($appointment, 'applicant'));

            // Send to consultant
            Mail::to($appointment->consultant->email)
                ->send(new AppointmentReminder24Hours($appointment, 'consultant'));

            return back()->with('success', 'Reminder sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
        }
    }

    public function confirmByEmail(Appointment $appointment, $token)
    {
        // Verify token
        if ($appointment->confirmation_token !== $token || 
            $appointment->confirmation_token_expires_at->isPast()) {
            return view('appointments.confirmation-expired');
        }

        return view('appointments.confirm-by-email', compact('appointment'));
    }

     public function processEmailConfirmation(Request $request, Appointment $appointment, $token)
        {
            // Ensure application relationship is loaded for notification methods
            if (!$appointment->relationLoaded('application')) {
                $appointment->load('application');
            }
            
            \Log::info('Processing email confirmation', [
                'appointment_id' => $appointment->id,
                'token' => $token,
                'confirmation' => $request->confirmation,
                'has_reschedule_reason' => $request->filled('reschedule_reason'),
                'reschedule_reason_length' => $request->filled('reschedule_reason') ? strlen($request->reschedule_reason) : 0
            ]);
    
            // Verify token
            if (!$appointment->isConfirmationTokenValid() || $appointment->confirmation_token !== $token) {
                \Log::warning('Invalid or expired confirmation token', [
                    'appointment_id' => $appointment->id,
                    'token_valid' => $appointment->isConfirmationTokenValid(),
                    'token_match' => $appointment->confirmation_token === $token
                ]);
                return redirect()->route('appointments.confirmation-expired');
            }
    
            $validated = $request->validate([
                'confirmation' => 'required|in:confirm,reschedule',
                'reschedule_reason' => 'required_if:confirmation,reschedule|string|max:500'
            ]);
    
            \Log::info('Validation passed', [
                'appointment_id' => $appointment->id,
                'validated_data' => $validated
            ]);
    
            DB::beginTransaction();
            try {
                if ($validated['confirmation'] === 'confirm') {
                    // Mark as confirmed
                    $appointment->update([
                        'applicant_confirmed' => true,
                        'applicant_confirmed_at' => now(),
                        'confirmation_method' => 'email',
                        'confirmation_token' => null,
                        'confirmation_token_expires_at' => null,
                    ]);
    
                    // Update status if both parties confirmed
                    if ($appointment->isConfirmed()) {
                        $appointment->update(['status' => 'confirmed']);
                    }
    
                    \Log::info('Appointment confirmed', [
                        'appointment_id' => $appointment->id,
                        'is_fully_confirmed' => $appointment->isConfirmed()
                    ]);
    
                    // Notify consultant
                    $this->notifyConsultantOfConfirmation($appointment);
    
                    DB::commit();
                    return redirect()->route('appointments.confirmation-success');
    
                } else {
                    // Handle reschedule request
                    $appointment->update([
                        'reschedule_reason' => $validated['reschedule_reason'],
                        'status' => 'rescheduled',
                        'confirmation_token' => null,
                        'confirmation_token_expires_at' => null,
                    ]);
    
                    \Log::info('Reschedule requested', [
                        'appointment_id' => $appointment->id,
                        'reason' => $validated['reschedule_reason']
                    ]);
    
                    // Notify consultant about reschedule request
                    $this->notifyConsultantOfRescheduleRequest($appointment);
    
                    DB::commit();
                    return redirect()->route('appointments.reschedule-requested');
                }
    
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Failed to process confirmation', [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return back()
                    ->withInput()
                    ->with('error', 'Failed to process confirmation. Please try again or contact support.');
            }
        }
        
    public function showEmailConfirmation(Appointment $appointment, $token)
    {
        // Verify token
        if (!$appointment->isConfirmationTokenValid() || $appointment->confirmation_token !== $token) {
            return redirect()->route('appointments.confirmation-expired');
        }

        $action = request()->get('action', 'confirm');
        
        return view('appointments.confirm-by-email', compact('appointment', 'action'));
    }

        public function showConfirmationExpired()
    {
        return view('appointments.confirmation-expired');
    }

    public function showConfirmationSuccess()
    {
        return view('appointments.confirmation-success');
    }

    public function showRescheduleRequested()
    {
        return view('appointments.reschedule-requested');
    }

    public function notifyConsultantOfConfirmation(Appointment $appointment)
    {
        // Ensure application relationship is loaded
        if (!$appointment->relationLoaded('application')) {
            $appointment->load('application');
        }
        
        // Get consultant_id from appointment or fall back to application's consultant_id
        $consultantId = $appointment->consultant_id;
        if (!$consultantId && $appointment->application && $appointment->application->consultant_id) {
            $consultantId = $appointment->application->consultant_id;
        }
        
        if (!$consultantId) {
            \Log::warning('Cannot notify consultant of confirmation - no consultant_id found', [
                'appointment_id' => $appointment->id,
                'application_id' => $appointment->application_id,
            ]);
            return;
        }
        
        \App\Models\Notification::create([
            'user_id' => $consultantId,
            'application_id' => $appointment->application_id,
            'type' => 'appointment_confirmed',
            'title' => 'Appointment Confirmed by Applicant',
            'message' => "{$appointment->display_name} has confirmed the {$appointment->type} appointment.",
            'priority' => 'normal',
            'action_url' => route('consultant.appointments.show', $appointment),
        ]);
    }

    public function notifyConsultantOfRescheduleRequest(Appointment $appointment)
    {
        // Ensure application relationship is loaded
        if (!$appointment->relationLoaded('application')) {
            $appointment->load('application');
        }
        
        // Get consultant_id from appointment or fall back to application's consultant_id
        $consultantId = $appointment->consultant_id;
        if (!$consultantId && $appointment->application && $appointment->application->consultant_id) {
            $consultantId = $appointment->application->consultant_id;
        }
        
        if (!$consultantId) {
            \Log::warning('Cannot notify consultant of reschedule request - no consultant_id found', [
                'appointment_id' => $appointment->id,
                'application_id' => $appointment->application_id,
            ]);
            return;
        }
        
        \App\Models\Notification::create([
            'user_id' => $consultantId,
            'application_id' => $appointment->application_id,
            'type' => 'rescheduled',
            'title' => 'Reschedule Requested by Applicant',
            'message' => "{$appointment->display_name} has requested to reschedule the {$appointment->type} appointment. Reason: {$appointment->reschedule_reason}",
            'priority' => 'high',
            'action_url' => route('consultant.appointments.show', $appointment),
        ]);
        
        \Log::info('Consultant notified of reschedule request', [
            'appointment_id' => $appointment->id,
            'consultant_id' => $consultantId,
        ]);
    }

}