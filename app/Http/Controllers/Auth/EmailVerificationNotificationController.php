<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Use custom token-based verification instead of Laravel's default
        $user = $request->user();
        
        // Generate a new 6-digit verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update the user's verification token
        $user->email_verification_token = $verificationCode;
        $user->save();

        // Send the verification email - always dispatch to prevent blocking
        $mail = new \App\Mail\VerifyEmailCodeMail($verificationCode);
        
        // Always dispatch to queue (even if sync) to prevent blocking
        // This way if mail fails, it won't timeout the request
        try {
            // Dispatch to queue - this is non-blocking even with sync driver
            Mail::to($user->email)->queue($mail);
            
            return redirect()->route('token.notice')
                         ->with('status', 'verification-link-sent')
                         ->with('success', 'A verification code has been sent to your email address.')
                         ->with('email', $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to queue email verification notification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            
            // Return the code directly if email queueing fails
            return redirect()->route('token.notice')
                         ->with('warning', 'We were unable to send the verification email. Your verification code is: ' . $verificationCode)
                         ->with('email', $user->email);
        }
    }
}
