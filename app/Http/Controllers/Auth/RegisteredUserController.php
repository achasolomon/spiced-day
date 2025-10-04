<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailCodeMail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'location'   => ['required', 'string', 'max:255'],
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'email.unique'        => 'This email is already registered.',
            'password.confirmed'  => 'Password confirmation does not match.',
            'location.required'   => 'Location is required.',
        ]);

        // Combine first and last name
        $fullName = trim($request->first_name . ' ' . $request->last_name);

        // Parse location
        $locationParts = explode(',', $request->location);
        $city     = trim($locationParts[0] ?? $request->location);
        $province = trim($locationParts[1] ?? 'Alberta'); // Default to Alberta

        // 1. GENERATE THE 6-DIGIT TOKEN
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'      => $fullName,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'city'      => $city,
            'province'  => $province,
            'user_type' => 'applicant',
            'is_active' => true,
            
            // 2. SET TOKEN AND VERIFIED_AT TO NULL
            'email_verification_token' => $token,
            'email_verified_at' => null, 
            
            'preferences' => [
                'theme' => 'light',
                'notifications' => [
                    'email'    => true,
                    'browser'  => true,
                    'sms'      => false,
                ],
                'language' => 'en',
            ],
        ]);
        
        // 3. SEND EMAIL WITH THE CODE
        // You MUST create the Mailable class 'VerifyEmailCode'
        Mail::to($user->email)->send(new VerifyEmailCodeMail($token));
        
        // 4. REMOVE AUTH::LOGIN($user);
        // The user is NOT logged in yet.

        // 5. REDIRECT TO THE TOKEN VERIFICATION FORM
        return redirect()->route('token.notice')->with([
             'success' => 'Your account has been created. A verification code has been sent to your email.',
             'email' => $user->email // Pass email for the verification form
        ]);
    }
}