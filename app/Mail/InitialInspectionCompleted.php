<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InitialInspectionCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;
    public string $registrationToken;
    public string $registrationUrl;

    public function __construct(Application $application, string $registrationToken)
    {
        $this->application = $application;
        $this->registrationToken = $registrationToken;
        $this->registrationUrl = route('anonymous.register.form', ['token' => $registrationToken]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Congratulations! Initial Inspection Completed - Create Your Profile",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.initial-inspection-completed',
        );
    }
}