<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Notification;
use App\Enums\ApplicationStatus;
use App\Mail\ApplicationStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ApplicationStatusService
{
    public function transitionTo(Application $application, ApplicationStatus|string $newStatus, ?string $notes = null): bool
    {
        // Convert string to enum if needed
        if (is_string($newStatus)) {
            $newStatus = ApplicationStatus::from($newStatus);
        }

        $oldStatus = ApplicationStatus::from($application->status);

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
            'draft' => 'intake',
            'submitted' => 'intake',
            'meet_and_greet_scheduled' => 'meet_and_greet',
            'meet_and_greet_completed' => 'meet_and_greet',
            'initial_inspection_scheduled' => 'initial_inspection',
            'initial_inspection_completed' => 'initial_inspection',
            'documents_pending' => 'document_collection',
            'documents_submitted' => 'document_collection',
            'documents_approved' => 'document_collection',
            'second_inspection_scheduled' => 'second_inspection',
            'second_inspection_completed' => 'second_inspection',
            'contract_signing_scheduled' => 'contract_signing',
            'contract_signed' => 'contract_signing',
            'approved' => 'approved',
            'rejected' => 'approved',
            'cancelled' => 'approved',
        ];

        DB::beginTransaction();
        try {
            // Update application status and current_stage
            $application->update([
                'status' => $newStatus->value,
                'current_stage' => $statusToStageMap[$newStatus->value] ?? 'intake',
            ]);

            // Create audit log
            \App\Models\AuditLog::log(
                'application_status_changed',
                $application,
                "Status changed from {$oldStatus->label()} to {$newStatus->label()}",
                [
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'new_stage' => $application->current_stage,
                    'notes' => $notes
                ]
            );

            // Send notifications
            $this->sendNotifications($application, $oldStatus, $newStatus, $notes);

            DB::commit();

            Log::info('Application status changed successfully', [
                'application_id' => $application->id,
                'from' => $oldStatus->value,
                'to' => $newStatus->value,
                'stage' => $application->current_stage
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

    private function sendNotifications(Application $application, ApplicationStatus $oldStatus, ApplicationStatus $newStatus, ?string $notes)
    {
        // Notify applicant
        $this->notifyApplicant($application, $newStatus, $notes);

        // Notify consultant if assigned
        if ($application->consultant_id) {
            $this->notifyConsultant($application, $newStatus, $notes);
        }
    }

    private function notifyApplicant(Application $application, ApplicationStatus $newStatus, ?string $notes)
    {
        $message = $this->getApplicantMessage($newStatus, $notes);
        $priority = $this->getPriority($newStatus);

        // Create database notification
        Notification::create([
            'user_id' => $application->user_id,
            'application_id' => $application->id,
            'type' => 'application_status_changed',
            'title' => 'Application Status Updated',
            'message' => $message,
            'priority' => $priority,
            'action_url' => route('applicant.applications.show', $application),
        ]);

        // Send email notification
        try {
            Mail::to($application->user->email)->send(
                new ApplicationStatusChanged($application, $newStatus, $message)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send email to applicant', [
                'application_id' => $application->id,
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

        // Send email notification
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

    private function getApplicantMessage(ApplicationStatus $status, ?string $notes): string
    {
        $messages = [
            ApplicationStatus::SUBMITTED->value => 'Your application has been submitted successfully. We will review it shortly.',
            ApplicationStatus::MEET_AND_GREET_SCHEDULED->value => 'Your Meet & Greet appointment has been scheduled. Please check your appointments.',
            ApplicationStatus::MEET_AND_GREET_COMPLETED->value => 'Your Meet & Greet has been completed. We will schedule your Initial Inspection soon.',
            ApplicationStatus::INITIAL_INSPECTION_SCHEDULED->value => 'Your Initial Inspection has been scheduled. Please prepare your home accordingly.',
            ApplicationStatus::INITIAL_INSPECTION_COMPLETED->value => 'Your Initial Inspection has been completed. You can now upload required documents.',
            ApplicationStatus::DOCUMENTS_PENDING->value => 'Please upload all required documents to proceed with your application.',
            ApplicationStatus::DOCUMENTS_SUBMITTED->value => 'Thank you for submitting your documents. Our team will review them shortly.',
            ApplicationStatus::SECOND_INSPECTION_SCHEDULED->value => 'Your Second Inspection has been scheduled.',
            ApplicationStatus::SECOND_INSPECTION_COMPLETED->value => 'Your Second Inspection has been completed successfully. We will schedule contract signing.',
            ApplicationStatus::CONTRACT_SIGNING_SCHEDULED->value => 'Your Contract Signing appointment has been scheduled.',
            ApplicationStatus::CONTRACT_SIGNED->value => 'Congratulations! Your contract has been signed. Final approval pending.',
            ApplicationStatus::APPROVED->value => 'Congratulations! Your application has been approved. Welcome to our network!',
            ApplicationStatus::REJECTED->value => 'Unfortunately, your application has been rejected.' . ($notes ? " Reason: $notes" : ''),
            ApplicationStatus::CANCELLED->value => 'Your application has been cancelled.' . ($notes ? " Reason: $notes" : ''),
        ];

        return $messages[$status->value] ?? "Your application status has been updated to: {$status->label()}";
    }

    private function getConsultantMessage(ApplicationStatus $status, Application $application): string
    {
        $appNumber = $application->application_number;
        $appName = $application->full_name;

        return match($status) {
            ApplicationStatus::SUBMITTED => "New application $appNumber from $appName has been submitted.",
            ApplicationStatus::MEET_AND_GREET_COMPLETED => "Meet & Greet completed for $appNumber. Ready to schedule Initial Inspection.",
            ApplicationStatus::INITIAL_INSPECTION_COMPLETED => "Initial Inspection completed for $appNumber. Applicant can now upload documents.",
            ApplicationStatus::DOCUMENTS_SUBMITTED => "Documents submitted for $appNumber. Please review.",
            ApplicationStatus::SECOND_INSPECTION_COMPLETED => "Second Inspection completed for $appNumber. Ready for contract signing.",
            ApplicationStatus::CONTRACT_SIGNED => "Contract signed for $appNumber. Ready for final approval.",
            default => "Application $appNumber status updated to: {$status->label()}"
        };
    }

    private function getPriority(ApplicationStatus $status): string
    {
        return match($status) {
            ApplicationStatus::REJECTED,
            ApplicationStatus::CANCELLED,
            ApplicationStatus::APPROVED,
            ApplicationStatus::DOCUMENTS_PENDING,
            ApplicationStatus::MEET_AND_GREET_SCHEDULED,
            ApplicationStatus::INITIAL_INSPECTION_SCHEDULED,
            ApplicationStatus::SECOND_INSPECTION_SCHEDULED,
            ApplicationStatus::CONTRACT_SIGNING_SCHEDULED => 'high',
            default => 'normal'
        };
    }
}
?>