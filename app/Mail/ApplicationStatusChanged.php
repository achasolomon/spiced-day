<?php

namespace App\Mail;

use App\Models\Application;
use App\Enums\ApplicationStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;
    public ApplicationStatus $newStatus;
    public string $statusMessage; 
    public bool $isConsultant;

    public function __construct(Application $application, ApplicationStatus $newStatus, string $message, bool $isConsultant = false)
    {
        $this->application = $application;
        $this->newStatus = $newStatus;
        $this->statusMessage = $message;  
        $this->isConsultant = $isConsultant;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Application Status Update",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-status-changed',
            with: [
                'application' => $this->application,
                'newStatus' => $this->newStatus,
                'statusMessage' => $this->statusMessage,
                'isConsultant' => $this->isConsultant,
            ]
        );
    }
}