<?php

namespace App\Mail;

use App\Models\Inspection;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class InspectionCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $inspection;

    /**
     * Create a new message instance.
     */
    public function __construct(Inspection $inspection)
    {
        $this->inspection = $inspection;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $resultEmoji = match($this->inspection->overall_result) {
            'pass' => '✅',
            'fail' => '❌',
            'conditional_pass' => '⚠️',
            default => '📋'
        };

        return new Envelope(
            subject: "{$resultEmoji} Inspection Report - " . ucwords(str_replace('_', ' ', $this->inspection->type)),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inspection-completed',
            with: [
                'inspection' => $this->inspection,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Optionally attach PDF report if generated
        return [];
    }
}