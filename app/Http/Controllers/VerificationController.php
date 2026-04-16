<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailCodeMail;

class VerificationController extends Controller
{
    /**
     * Show the form to enter the verification code.
     */
    public function show(Request $request)
    {
        // Get email from authenticated user, session, or request
        $email = null;
        
        if ($request->user()) {
            // If user is authenticated, use their email
            $email = $request->user()->email;
        } elseif (session('email')) {
            // Check session for email
            $email = session('email');
        } elseif ($request->email) {
            // Check request for email
            $email = $request->email;
        }
        
        // If no email is found, redirect to register
        if (!$email) {
            return redirect()->route('register')->withErrors(['email' => 'Please register first.']);
        }

        // Ensure email is in session for the form
        if (!session('email')) {
            session(['email' => $email]);
        }

        // The user is not yet verified, show the token input form
        return view('auth.verify-token');
    }

    /**
     * Handle the submission of the verification code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            // Token validation: must be 6 digits
            'token' => 'required|string|digits:6', 
        ]);

        $user = User::where('email', $request->email)
                    ->where('email_verification_token', $request->token)
                    ->first();

        if (!$user) {
            // Token/Code is incorrect 
            return back()->withInput($request->only('email'))
                         ->withErrors(['token' => 'The provided verification code is incorrect.']);
        }
        
        // Verification Successful
        $user->email_verified_at = Carbon::now();
        $user->email_verification_token = null; // Invalidate and clear the token
        $user->save();

        // Redirect back to dashboard with success message
        return redirect()->route('dashboard')->with('success', 'Email successfully verified! You can now log in.');
    }

    /**
     * Resend the verification code.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is already verified
        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Your email is already verified. Please log in.');
        }

        // Generate a new 6-digit verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update the user's verification token
        $user->email_verification_token = $verificationCode;
        $user->save();

        // Send the verification email - always dispatch to prevent blocking
        $mail = new VerifyEmailCodeMail($verificationCode);
        
        try {
            // Always dispatch to queue (even if sync) to prevent blocking
            Mail::to($user->email)->queue($mail);
            
            // Redirect back with success message and email in session
            return back()
                ->with('email', $user->email)
                ->with('success', 'A new verification code has been sent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to queue verification code email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            
            // Still return success but with a warning about email delivery
            return back()
                ->with('email', $user->email)
                ->with('warning', 'Verification code generated, but we were unable to send the email. Please check your email configuration. Your verification code is: ' . $verificationCode);
        }
    }
}