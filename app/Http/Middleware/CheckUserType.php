<?php
// app/Http/Middleware/CheckUserType.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $userType): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['account' => 'Your account has been deactivated. Please contact support.']);
        }

        // Check if user has the required user type
        if ($user->user_type !== $userType) {
            // Redirect to appropriate dashboard based on their actual role
            $dashboardRoute = match($user->user_type) {
                'applicant' => 'applicant.dashboard',
                'consultant' => 'consultant.dashboard',
                'admin' => 'admin.dashboard',
                default => 'dashboard',
            };
            return redirect()->route($dashboardRoute)
                ->with('error', 'You do not have permission to access that area.');
        }

        return $next($request);
    }
}