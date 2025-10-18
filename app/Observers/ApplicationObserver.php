<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\Notification;
use App\Models\Consultant;
use App\Enums\ApplicationStatus;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationStatusChanged;

class ApplicationObserver
{
    public function created(Application $application)
    {
        // Send notification to admin about new submission (only if submitted, not draft)
        if ($application->status === ApplicationStatus::SUBMITTED->value) {
            $this->notifyAdminsOfNewApplication($application);
        }

        // Update consultant workload if assigned
        $this->updateConsultantWorkload($application);
    }

    public function updated(Application $application)
    {
        // Update workload if consultant was assigned/changed or status changed
        if ($application->isDirty('consultant_id') || $application->isDirty('status')) {
            
            // If consultant was changed, update the old consultant's workload
            if ($application->isDirty('consultant_id')) {
                $oldConsultantId = $application->getOriginal('consultant_id');
                if ($oldConsultantId) {
                    $oldConsultant = Consultant::where('user_id', $oldConsultantId)->first();
                    if ($oldConsultant) {
                        $oldConsultant->updateWorkloadMetrics();
                    }
                }
            }
            
            // Update new/current consultant's workload
            $this->updateConsultantWorkload($application);
        }
    }

    public function deleted(Application $application)
    {
        // Update consultant workload when application is deleted
        $this->updateConsultantWorkload($application);
    }

    /**
     * Update the workload metrics for the consultant assigned to this application
     */
    private function updateConsultantWorkload(Application $application)
    {
        if ($application->consultant_id) {
            $consultant = Consultant::where('user_id', $application->consultant_id)->first();
            if ($consultant) {
                $consultant->updateWorkloadMetrics();
            }
        }
    }

    /**
     * Notify admins about new application submission
     */
    private function notifyAdminsOfNewApplication(Application $application)
    {
        $admins = \App\Models\User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'application_id' => $application->id,
                'type' => 'new_application',
                'title' => 'New Application Submitted',
                'message' => "New application {$application->application_number} from {$application->full_name}",
                'priority' => 'normal',
                'action_url' => route('admin.applications.show', $application),
            ]);
        }
    }
}