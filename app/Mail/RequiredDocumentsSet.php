<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequiredDocumentsSet extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $message;

    public function __construct(Application $application, string $message)
    {
        $this->application = $application;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject('Required Documents Updated for Your Application')
                    ->markdown('emails.required-documents-set')
                    ->with([
                        'application' => $this->application,
                        'message' => $this->message,
                        'actionUrl' => route('applicant.documents.index', $this->application),
                    ]);
    }
}
?>