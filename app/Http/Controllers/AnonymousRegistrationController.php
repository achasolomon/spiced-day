<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailCodeMail;
use Illuminate\Validation\Rules;

class AnonymousRegistrationController extends Controller
{
   
    /**
     * Show registration form for anonymous application
     */
    public function showRegistrationForm($token)
    {
        try {
            // Check if registration_token column exists by trying to query it
            // If it doesn't exist, fall back to anonymous_token only
            $hasRegistrationTokenColumn = \Schema::hasColumn('applications', 'registration_token');
            
            $application = Application::where(function($query) use ($token, $hasRegistrationTokenColumn) {
                    if ($hasRegistrationTokenColumn) {
                        $query->where('registration_token', $token)
                              ->orWhere('anonymous_token', $token);
                    } else {
                        // Fallback if column doesn't exist yet
                        $query->where('anonymous_token', $token);
                    }
                })
                ->whereNull('user_id')
                ->firstOrFail();
            
            // Check if registration token is expired (only if column exists)
            if ($hasRegistrationTokenColumn && $application->registration_token && $application->registration_token_expires_at) {
                // Ensure it's a Carbon instance (cast might not be applied)
                $expiresAt = $application->registration_token_expires_at;
                if (is_string($expiresAt)) {
                    $expiresAt = \Carbon\Carbon::parse($expiresAt);
                }
                
                if ($expiresAt && $expiresAt->isPast()) {
                    return redirect()->route('home')
                        ->with('error', 'This registration link has expired. Please contact support for a new link.');
                }
            }
            
            // Check if initial inspection is completed
            if (!$application->canCreateAccount()) {
                return redirect()->route('home')
                    ->with('error', 'You must complete the initial inspection before creating an account.');
            }
            
            // Reuse the existing register view but pass the application
            return view('auth.register', compact('application'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors (e.g., missing column)
            Log::error('Error loading registration form', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('home')
                ->with('error', 'There was an error processing your registration link. Please contact support for assistance.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in showRegistrationForm', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('home')
                ->with('error', 'An error occurred. Please contact support for assistance.');
        }
    }
    
    /**
     * Register user account and link to anonymous application
     */
    public function register(Request $request, $token)
    {
        $application = null;
        
        try {
            // Check if registration_token column exists
            $hasRegistrationTokenColumn = \Schema::hasColumn('applications', 'registration_token');
            
            $application = Application::where(function($query) use ($token, $hasRegistrationTokenColumn) {
                    if ($hasRegistrationTokenColumn) {
                        $query->where('registration_token', $token)
                              ->orWhere('anonymous_token', $token);
                    } else {
                        // Fallback if column doesn't exist yet
                        $query->where('anonymous_token', $token);
                    }
                })
                ->whereNull('user_id')
                ->firstOrFail();
            
            // Check if registration token is expired (only if column exists)
            if ($hasRegistrationTokenColumn && $application->registration_token && $application->registration_token_expires_at) {
                // Ensure it's a Carbon instance (cast might not be applied)
                $expiresAt = $application->registration_token_expires_at;
                if (is_string($expiresAt)) {
                    $expiresAt = \Carbon\Carbon::parse($expiresAt);
                }
                
                if ($expiresAt && $expiresAt->isPast()) {
                    return redirect()->route('home')
                        ->with('error', 'This registration link has expired. Please contact support for a new link.');
                }
            }
            
            // Validate that application can create account
            if (!$application->canCreateAccount()) {
                return back()->with('error', 'Account creation is not available at this stage.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in register method', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('home')
                ->with('error', 'There was an error processing your registration. Please contact support for assistance.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in register method', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', 'An error occurred during registration. Please try again or contact support.');
        }
        
        // If we get here, application was found successfully
        if (!$application) {
            return redirect()->route('home')
                ->with('error', 'Application not found. Please contact support for assistance.');
        }
        
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'agree_terms' => ['required', 'accepted'],
        ]);
        
        // Check if email matches application email
        $emailMatches = $validated['email'] === $application->email;
        
        if (!$emailMatches && !$request->has('confirm_different_email')) {
            return back()->withInput()->withErrors([
                'email' => 'The email you entered differs from your application email. Please check the confirmation box if this is intentional.'
            ]);
        }
        
        DB::beginTransaction();
        try {
            // Combine names
            $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);
            
            // Generate verification token
            $verificationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Create user account
            $user = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'user_type' => 'applicant',
                'is_active' => true,
                'city' => $application->city,
                'province' => $application->province,
                'email_verification_token' => $verificationToken,
                'email_verified_at' => null, // Will verify via token
                'preferences' => [
                    'theme' => 'light',
                    'notifications' => [
                        'email' => true,
                        'browser' => true,
                        'sms' => false
                    ],
                    'language' => 'en'
                ],
            ]);
            
            // Link application to user
            $application->update([
                'user_id' => $user->id,
                'account_created' => true,
                'account_created_at' => now(),
                // Update application data if email changed
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
            
            // Clear anonymous token from session
            $request->session()->forget('anonymous_application_token');
            $request->session()->forget('anonymous_application_id');
            
            // Send verification email
            Mail::to($user->email)->send(new VerifyEmailCodeMail($verificationToken));
            
            // Log the account creation
            Log::info('Anonymous application linked to user account', [
                'application_id' => $application->id,
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            \App\Models\AuditLog::log(
                'account_created',
                $application,
                'User account created and linked to application',
                ['user_id' => $user->id]
            );
            
            // Note: We don't fire the Registered event here because:
            // 1. We're already manually sending the verification email (line 214)
            // 2. Laravel's default Registered event listener would send another verification email
            // 3. This would result in duplicate emails being sent
            // If you need the Registered event for other purposes, you can uncomment it,
            // but you'll need to disable the default email verification notification

            // Send registration success email
            Mail::to($user->email)->send(
                new \App\Mail\AccountCreatedMail($user, $application)
            );

            DB::commit();
            
            // Redirect to token verification
            return redirect()->route('token.notice')->with([
                'success' => 'Your account has been created successfully! A verification code has been sent to your email.',
                'email' => $user->email
            ]);
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration failed for anonymous application', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Failed to create account. Please try again.');
        }
    }
    
    /**
     * Check if email is already in use
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = User::where('email', $email)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This email is already registered.' : 'Email is available.'
        ]);
    }
}