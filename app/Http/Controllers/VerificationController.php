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
        // Ensure we have an email passed from the registration redirect or previous attempt
        if (!session('email') && !$request->email) {
             // If no email is known, redirect back to register
             return redirect()->route('register')->withErrors(['email' => 'Please register first.']);
        }

        // The user is not yet verified, show the token input form
        return view('auth.verify-token'); // New Blade file we'll create
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

        // Redirect back to login page with success message
        return redirect()->route('login')->with('success', 'Email successfully verified! You can now log in.');
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

        // Send the verification email
        Mail::to($user->email)->send(new VerifyEmailCodeMail($verificationCode, $user));

        // Redirect back with success message and email in session
        return back()
            ->with('email', $user->email)
            ->with('success', 'A new verification code has been sent to your email.');
    }
}