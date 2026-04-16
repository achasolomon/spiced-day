<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\Notification;
use App\Models\Consultant;
use App\Enums\ApplicationStatus;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationStatusChanged; 

class ApplicationStageMap
{
    public static function fromStatus(?string $status): string
    {
        return match ($status) {
            'draft',
            'submitted' => 'application',

            'initial_inspection_scheduled',
            'initial_inspection_completed' => 'initial_inspection',

            'documents_pending',
            'documents_submitted',
            'documents_approved' => 'document_submission',

            'second_inspection_scheduled',
            'second_inspection_completed' => 'second_inspection',

            'final_inspection_scheduled',
            'final_inspection_completed' => 'final_inspection',

          

            'contract_signing_scheduled',
            'contract_signed' => 'contract_signing',

            'approved' => 'approval',

            default => 'application',
        };
    }
}
