<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApplicationRegistrationController extends Controller
{
    /**
     * Show the registration form for an imported application
     */
    public function show($token)
    {
        $application = Application::where('registration_token', $token)
            ->whereNull('user_id')
            ->where('account_created', false)
            ->where('registration_token_expires_at', '>', now())
            ->firstOrFail();

        return view('applications.register', compact('application'));
    }

    /**
     * Process the registration
     */
    public function register(Request $request, $token)
    {
        // Find the application
        $application = Application::where('registration_token', $token)
            ->whereNull('user_id')
            ->where('account_created', false)
            ->where('registration_token_expires_at', '>', now())
            ->firstOrFail();

        // Validate the request
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ]);

        DB::beginTransaction();
        try {
            // Check if user already exists with this email
            $user = User::where('email', $application->email)->first();
            
            if ($user) {
                // User exists - just link the application
                if ($user->hasActiveApplication()) {
                    return back()->with('error', 'This email is already associated with an active application.');
                }
            } else {
                // Create new user account
                $user = User::create([
                    'name' => trim("{$application->educator_first_name} {$application->educator_last_name}"),
                    'email' => $application->email,
                    'phone' => $application->phone,
                    'password' => Hash::make($validated['password']),
                    'user_type' => 'applicant',
                    'is_active' => true,
                    'email_verified_at' => now(), // Auto-verify since email was validated during import
                ]);

                Log::info('New user account created from imported application', [
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                ]);
            }

            // Link application to user
            $application->update([
                'user_id' => $user->id,
                'account_created' => true,
                'account_created_at' => now(),
                'registration_token' => null, // Clear token after use
                'registration_token_expires_at' => null,
            ]);

            // Create notification for successful account creation
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'application_id' => $application->id,
                'type' => 'account_created',
                'title' => 'Welcome to SPICE\'d!',
                'message' => 'Your account has been successfully created. You can now upload documents and track your application progress.',
                'priority' => 'high',
                'action_url' => route('applicant.dashboard'),
                'action_text' => 'View Dashboard',
            ]);

            // Create audit log
            \App\Models\AuditLog::log(
                'account_created_from_import',
                $application,
                "User account created and linked to imported application"
            );

            DB::commit();

            // Auto-login the user
            Auth::login($user);

            return redirect()
                ->route('applicant.dashboard')
                ->with('success', 'Welcome to SPICE\'d! Your account has been created successfully. You can now manage your application.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to register user from imported application', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create your account. Please try again or contact support.');
        }
    }

    /**
     * Show token expired page
     */
    public function expired()
    {
        return view('applications.registration-expired');
    }
}