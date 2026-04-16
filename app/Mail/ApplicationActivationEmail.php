<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationActivationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $registrationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        
        // Generate registration token if not exists
        if (!$application->registration_token) {
            $application->update([
                'registration_token' => \Illuminate\Support\Str::random(64),
                'registration_token_expires_at' => now()->addDays(7),
            ]);
        }
        
        $this->registrationUrl = route('anonymous.register.form', [
        'token' => $application->registration_token
        ]);

    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Create Your SPICE\'d Profile - Application #' . $this->application->application_number)
                    ->view('emails.application-activation')
                    ->with([
                        'application' => $this->application,
                        'registrationUrl' => $this->registrationUrl,
                    ]);
    }
}