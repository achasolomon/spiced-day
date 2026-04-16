<?php
namespace App\Mail;

use App\Models\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Application $application;

    public function __construct(User $user, Application $application)
    {
        $this->user = $user;
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('Your Account Has Been Created Successfully')
            ->view('emails.account-created');
    }
}
