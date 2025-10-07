<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\Notification;
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
    }

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