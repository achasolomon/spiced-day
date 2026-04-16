<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MeetGreetScheduledEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;
    public Appointment $appointment;

    public function __construct(Application $application, Appointment $appointment)
    {
        $this->application = $application;
        $this->appointment = $appointment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Meet & Greet Scheduled",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.meet-greet-scheduled',
        );
    }
}