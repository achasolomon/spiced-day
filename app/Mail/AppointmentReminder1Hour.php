<?php
// app/Mail/AppointmentReminder1Hour.php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder1Hour extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $recipient; // 'applicant' or 'consultant'

    public function __construct(Appointment $appointment, string $recipient)
    {
        $this->appointment = $appointment->load(['consultant', 'applicant', 'application']);
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Appointment Starting Soon - In 1 Hour!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-reminder-1hour',
            with: [
                'appointment' => $this->appointment,
                'recipient' => $this->recipient,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}