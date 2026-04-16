<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Notification;
use App\Enums\ApplicationStatus;
use App\Mail\ApplicationStatusChanged;
use App\Mail\MeetGreetScheduledEmail;
use App\Mail\InitialInspectionScheduledEmail;
use App\Mail\MeetGreetCompletedEmail;
use App\Mail\InitialInspectionCompleted;    
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApplicationStatusService
{
    public function transitionTo(
        Application $application, 
        ApplicationStatus|string $newStatus, 
        ?string $notes = null, 
        ?\App\Models\Appointment $appointment = null,
        bool $isUnscheduled = false // New parameter
    ): bool
    {
        // Convert string to enum if needed
        if (is_string($newStatus)) {
            $newStatus = ApplicationStatus::from($newStatus);
        }

        $oldStatus = ApplicationStatus::from($application->status);

        // If already in the target status, return true (idempotent operation)
        if ($oldStatus === $newStatus) {
            Log::info('Status transition skipped - already in target status', [
                'application_id' => $application->id,
                'status' => $oldStatus->value
            ]);
            return true;
        }

        // Check if transition is valid
        if (!$oldStatus->canTransitionTo($newStatus)) {
            Log::warning('Invalid status transition attempted', [
                'application_id' => $application->id,
                'from' => $oldStatus->value,
                'to' => $newStatus->value
            ]);
            return false;
        }

        // Define status to current_stage mapping
        $statusToStageMap = [
            'draft' => 'application',
            'submitted' => 'application',
            'meet_and_greet_scheduled' => 'meet_and_greet',
            'meet_and_greet_completed' => 'meet_and_greet',
            'initial_inspection_scheduled' => 'initial_inspection',
            'initial_inspection_completed' => 'initial_inspection',
            'documents_pending' => 'document_submission',
            'documents_submitted' => 'document_submission',
            'documents_approved' => 'document_submission',
            'second_inspection_scheduled' => 'second_inspection',
            'second_inspection_completed' => 'second_inspection',
            'final_inspection_scheduled' => 'final_inspection',
            'final_inspection_completed' => 'final_inspection',
            'final_inspection_passed' => 'final_inspection',
            'final_inspection_failed' => 'final_inspection',
            'contract_signing_scheduled' => 'contract_signing',
            'contract_signed' => 'contract_signing',
            'approved' => 'approval',
            'active' => 'active',
            'compliance_inspection_due' => 'compliance_monitoring',
            'compliance_inspection_scheduled' => 'compliance_monitoring',
            'compliance_inspection_completed' => 'compliance_monitoring',
            'suspended' => 'suspended',
            'under_review' => 'suspended',
            'remediation_required' => 'suspended',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            'terminated' => 'terminated',
        ];

        DB::beginTransaction();
        try {
            // Update application status and current_stage
            $application->update([
                'status' => $newStatus->value,
                'current_stage' => $statusToStageMap[$newStatus->value] ?? 'application',
            ]);

            $application->updateCompletionPercentage();

            // Create audit log
            \App\Models\AuditLog::log(
                'application_status_changed',
                $application,
                "Status changed from {$oldStatus->label()} to {$newStatus->label()}" . ($isUnscheduled ? ' (Unscheduled)' : ''),
                [
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'new_stage' => $application->current_stage,
                    'completion_percentage' => $application->completion_percentage,
                    'notes' => $notes,
                    'is_unscheduled' => $isUnscheduled,
                ]
            );

            // Only send notifications if NOT unscheduled
            if (!$isUnscheduled) {
                $this->sendNotifications($application, $oldStatus, $newStatus, $notes, $appointment);
            } else {
                Log::info('Notifications skipped for unscheduled inspection', [
                    'application_id' => $application->id,
                    'status' => $newStatus->value,
                ]);
            }

            DB::commit();

            Log::info('Application status changed successfully', [
                'application_id' => $application->id,
                'from' => $oldStatus->value,
                'to' => $newStatus->value,
                'stage' => $application->current_stage,
                'completion_percentage' => $application->completion_percentage,
                'is_unscheduled' => $isUnscheduled,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to change application status', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function sendNotifications(Application $application, ApplicationStatus $oldStatus, ApplicationStatus $newStatus, ?string $notes, ?\App\Models\Appointment $appointment = null)
    {
        // Notify applicant with separate email threads
        $this->notifyApplicant($application, $newStatus, $notes, $appointment);

        // Notify consultant if assigned
        if ($application->consultant_id) {
            $this->notifyConsultant($application, $newStatus, $notes);
        }
    }

    private function notifyApplicant(Application $application, ApplicationStatus $newStatus, ?string $notes, ?\App\Models\Appointment $appointment = null)
    {
        $message = $this->getApplicantMessage($newStatus, $notes);
        $priority = $this->getPriority($newStatus);
        $applicantEmail = $this->getApplicantEmail($application);

        // Create database notification (only for registered users)
        if ($application->user_id) {
            Notification::create([
                'user_id' => $application->user_id,
                'application_id' => $application->id,
                'type' => 'application_status_changed',
                'title' => 'Application Status Updated',
                'message' => $message,
                'priority' => $priority,
                'action_url' => route('applicant.applications.show', $application),
            ]);
        }

        // Send specific email based on status (separate threads)
        try {
            // If appointment not provided, try to find it
            if (!$appointment) {
                $appointment = \App\Models\Appointment::where('application_id', $application->id)
                    ->where('type', $this->getAppointmentTypeFromStatus($newStatus->value))
                    ->latest()
                    ->first();
            }

            // Use string values that match your actual statuses
            switch ($newStatus->value) {
                case 'meet_and_greet_scheduled':
                    if ($appointment) {
                        Mail::to($applicantEmail)->send(new \App\Mail\MeetGreetScheduledEmail($application, $appointment));
                    } else {
                        Mail::to($applicantEmail)->send(
                            new ApplicationStatusChanged($application, $newStatus, $message)
                        );
                    }
                    break;

                case 'initial_inspection_scheduled':
                    if ($appointment) {
                        try {
                            Mail::to($applicantEmail)->send(new \App\Mail\InitialInspectionScheduledEmail($application, $appointment));
                            Log::info('Initial Inspection scheduled email sent', [
                                'application_id' => $application->id,
                                'appointment_id' => $appointment->id,
                                'email' => $applicantEmail
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to send Initial Inspection scheduled email', [
                                'application_id' => $application->id,
                                'appointment_id' => $appointment->id,
                                'email' => $applicantEmail,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            // Fallback to generic email
                            try {
                                Mail::to($applicantEmail)->send(
                                    new ApplicationStatusChanged($application, $newStatus, $message)
                                );
                            } catch (\Exception $e2) {
                                Log::error('Failed to send fallback email', [
                                    'application_id' => $application->id,
                                    'error' => $e2->getMessage()
                                ]);
                            }
                        }
                    } else {
                        try {
                            Mail::to($applicantEmail)->send(
                                new ApplicationStatusChanged($application, $newStatus, $message)
                            );
                        } catch (\Exception $e) {
                            Log::error('Failed to send appointment scheduled email', [
                                'application_id' => $application->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    break;

                case 'final_inspection_scheduled':
                case 'compliance_inspection_scheduled':
                    // Only send email for scheduled inspections (not unscheduled)
                    if ($appointment) {
                        Mail::to($applicantEmail)->send(
                            new ApplicationStatusChanged($application, $newStatus, $message)
                        );
                    } else {
                        Mail::to($applicantEmail)->send(
                            new ApplicationStatusChanged($application, $newStatus, $message)
                        );
                    }
                    break;

                case 'meet_and_greet_completed':
                    Mail::to($applicantEmail)->send(new MeetGreetCompletedEmail($application));
                    break;

                case 'initial_inspection_completed':
                    try {
                        $token = $this->generateRegistrationToken($application);
                        Mail::to($applicantEmail)->send(new InitialInspectionCompleted($application, $token));
                        Log::info('Initial Inspection Completed email sent with registration token', [
                            'application_id' => $application->id,
                            'email' => $applicantEmail,
                            'token' => substr($token, 0, 10) . '...'
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send Initial Inspection Completed email', [
                            'application_id' => $application->id,
                            'email' => $applicantEmail,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                    break;

                default:
                    Mail::to($applicantEmail)->send(
                        new ApplicationStatusChanged($application, $newStatus, $message)
                    );
                    break;
            }

            Log::info('Status email sent successfully', [
                'application_id' => $application->id,
                'status' => $newStatus->value,
                'email' => $applicantEmail
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send email to applicant', [
                'application_id' => $application->id,
                'status' => $newStatus->value,
                'email' => $applicantEmail,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function notifyConsultant(Application $application, ApplicationStatus $newStatus, ?string $notes)
    {
        $message = $this->getConsultantMessage($newStatus, $application);

        // Create database notification
        Notification::create([
            'user_id' => $application->consultant_id,
            'application_id' => $application->id,
            'type' => 'application_status_changed',
            'title' => 'Application Status Updated',
            'message' => $message,
            'priority' => 'normal',
            'action_url' => route('consultant.applications.show', $application),
        ]);

        // Send email notification to consultant
        try {
            Mail::to($application->consultant->email)->send(
                new ApplicationStatusChanged($application, $newStatus, $message, true)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send email to consultant', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getApplicantEmail(Application $application): string
    {
        if ($application->user_id && $application->user) {
            return $application->user->email;
        }
        
        return $application->email;
    }

    private function generateRegistrationToken(Application $application): string
    {
        $token = Str::random(60);
        
        $application->update([
            'registration_token' => $token,
            'registration_token_expires_at' => now()->addDays(7),
        ]);

        Log::info('Registration token generated', [
            'application_id' => $application->id,
            'token' => $token
        ]);

        return $token;
    }

    private function getApplicantMessage(ApplicationStatus $status, ?string $notes): string
    {
        $messages = [
            'draft' => 'Your application has been saved as draft.',
            'submitted' => 'Your application has been submitted successfully. We will review it shortly.',
            'meet_and_greet_scheduled' => 'Your Meet & Greet appointment has been scheduled. Please check your mail to confirm appointments.',
            'meet_and_greet_completed' => 'Your Meet & Greet has been completed. We will schedule your Initial Inspection soon.',
            'initial_inspection_scheduled' => 'Your Initial Inspection has been scheduled, Kindly check your mail to confirm appointment. Please prepare your home accordingly.',
            'initial_inspection_completed' => 'Your Initial Inspection has been completed. You can now upload required documents.',
            'documents_pending' => 'Please login to upload all required documents to proceed with your application.',
            'documents_submitted' => 'Thank you for submitting your documents. Our team will review them shortly.',
            'documents_approved' => 'Your documents have been approved. We will schedule your Second Inspection.',
            'second_inspection_scheduled' => 'Your Second Inspection has been scheduled. Kindly login to your portal to confirm appointment',
            'second_inspection_completed' => 'Your Second Inspection has been completed successfully. We will schedule your Final Inspection.',
            'final_inspection_scheduled' => 'Your Final Inspection has been scheduled. This is the last step before contract signing, kindly login to your portal to confirm appointment',
            'final_inspection_completed' => 'Your Final Inspection has been completed. Results will be available shortly.',
            'final_inspection_passed' => 'Congratulations! Your Final Inspection has been passed. We will schedule contract signing.',
            'final_inspection_failed' => 'Your Final Inspection did not meet requirements. Our team will contact you with next steps.',
            'contract_signing_scheduled' => 'Your Contract Signing appointment has been scheduled.',
            'contract_signed' => 'Congratulations! Your contract has been signed. Final approval pending.',
            'approved' => 'Congratulations! Your application has been approved. Your dayhome will be activated soon.',
            'active' => 'Congratulations! Your dayhome is now active and operational.',
            'compliance_inspection_due' => 'A compliance inspection is due for your dayhome. We will contact you to schedule it.',
            'compliance_inspection_scheduled' => 'Your compliance inspection has been scheduled. Kindly login to your portal to confirm appointment',
            'compliance_inspection_completed' => 'Your compliance inspection has been completed.',
            'suspended' => 'Your dayhome license has been suspended. Please contact us for more information.' . ($notes ? " Reason: $notes" : ''),
            'under_review' => 'Your dayhome is under review. We will contact you with updates.',
            'remediation_required' => 'Remediation is required for your dayhome. Please address the issues identified.',
            'rejected' => 'Unfortunately, your application has been rejected.' . ($notes ? " Reason: $notes" : ''),
            'cancelled' => 'Your application has been cancelled.' . ($notes ? " Reason: $notes" : ''),
            'terminated' => 'Your dayhome license has been terminated.' . ($notes ? " Reason: $notes" : ''),
        ];

        return $messages[$status->value] ?? "Your application status has been updated to: {$status->label()}";
    }

    private function getConsultantMessage(ApplicationStatus $status, Application $application): string
    {
        $appNumber = $application->application_number;
        $appName = $application->full_name;

        return match($status->value) {
            'submitted' => "New application $appNumber from $appName has been submitted.",
            'meet_and_greet_completed' => "Meet & Greet completed for $appNumber. Ready to schedule Initial Inspection.",
            'initial_inspection_completed' => "Initial Inspection completed for $appNumber. Applicant can now upload documents.",
            'documents_submitted' => "Documents submitted for $appNumber. Please review.",
            'second_inspection_completed' => "Second Inspection completed for $appNumber. Ready for Final Inspection.",
            'final_inspection_completed' => "Final Inspection completed for $appNumber. Results pending.",
            'final_inspection_passed' => "Final Inspection passed for $appNumber. Ready for contract signing.",
            'final_inspection_failed' => "Final Inspection failed for $appNumber. Requires action.",
            'contract_signed' => "Contract signed for $appNumber. Ready for final approval.",
            'approved' => "Application $appNumber approved. Ready for activation.",
            'active' => "Dayhome $appNumber is now active and operational.",
            'compliance_inspection_due' => "Compliance inspection due for dayhome $appNumber.",
            'compliance_inspection_scheduled' => "Compliance inspection scheduled for dayhome $appNumber.",
            'compliance_inspection_completed' => "Compliance inspection completed for dayhome $appNumber.",
            'suspended' => "Dayhome $appNumber has been suspended.",
            'under_review' => "Dayhome $appNumber is under review.",
            'remediation_required' => "Remediation required for dayhome $appNumber.",
            'terminated' => "Dayhome $appNumber has been terminated.",
            default => "Application $appNumber status updated to: {$status->label()}"
        };
    }

    private function getPriority(ApplicationStatus $status): string
    {
        return match($status->value) {
            'rejected',
            'cancelled',
            'terminated',
            'suspended',
            'final_inspection_failed',
            'remediation_required' => 'high',
            
            'approved',
            'active',
            'documents_pending',
            'meet_and_greet_scheduled',
            'initial_inspection_scheduled',
            'second_inspection_scheduled',
            'final_inspection_scheduled',
            'contract_signing_scheduled',
            'compliance_inspection_scheduled',
            'compliance_inspection_due' => 'normal',
            
            default => 'normal'
        };
    }

    private function getAppointmentTypeFromStatus(string $status): string
    {
        return match($status) {
            'meet_and_greet_scheduled', 'meet_and_greet_completed' => 'meet_and_greet',
            'initial_inspection_scheduled', 'initial_inspection_completed' => 'initial_inspection',
            'second_inspection_scheduled', 'second_inspection_completed' => 'second_inspection',
            'final_inspection_scheduled', 'final_inspection_completed' => 'final_inspection',
            'contract_signing_scheduled', 'contract_signed' => 'contract_signing',
            'compliance_inspection_scheduled', 'compliance_inspection_completed' => 'compliance_inspection',
            default => 'follow_up'
        };
    }
}