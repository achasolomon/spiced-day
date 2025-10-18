<?php
// app/Mail/AppointmentReminder24Hours.php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
// Remove: implements ShouldQueue

class AppointmentReminder24Hours extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $recipient;

    public function __construct(Appointment $appointment, string $recipient)
    {
        $this->appointment = $appointment->load(['consultant', 'applicant', 'application']);
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Reminder - Tomorrow at ' . $this->appointment->scheduled_at->format('g:i A'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-reminder-24hours',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}