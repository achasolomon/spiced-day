<?php
// app/Providers/RouteServiceProvider.php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter for login attempts
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email.$request->ip());
        });

        // Rate limiter for file uploads
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Define route model bindings
        $this->defineModelBindings();
    }

    /**
     * Define route model bindings
     */
    protected function defineModelBindings(): void
    {
        // Application binding with eager loading
        Route::bind('application', function ($value) {
            return \App\Models\Application::with([
                'user', 
                'consultant', 
                'stages', 
                'documents.documentRequirement',
                'appointments.consultant'
            ])->findOrFail($value);
        });

        // User binding (for admin routes)
        Route::bind('user', function ($value) {
            return \App\Models\User::with(['consultant', 'roles', 'permissions'])
                ->findOrFail($value);
        });

        // Appointment binding
        Route::bind('appointment', function ($value) {
            return \App\Models\Appointment::with([
                'application.user',
                'consultant', 
                'applicant'
            ])->findOrFail($value);
        });

        // Document binding
        Route::bind('document', function ($value) {
            return \App\Models\Document::with([
                'application', 
                'uploadedBy', 
                'reviewedBy',
                'documentRequirement'
            ])->findOrFail($value);
        });

        // Inspection binding
        Route::bind('inspection', function ($value) {
            return \App\Models\Inspection::with([
                'application', 
                'consultant', 
                'appointment'
            ])->findOrFail($value);
        });

        // Consultant binding
        Route::bind('consultant', function ($value) {
            return \App\Models\Consultant::with(['user'])
                ->findOrFail($value);
        });

        // Notification binding
        Route::bind('notification', function ($value) {
            return \App\Models\Notification::where('user_id', auth()->id())
                ->findOrFail($value);
        });

        // Document Requirement binding (for admin)
        Route::bind('documentRequirement', function ($value) {
            return \App\Models\DocumentRequirement::findOrFail($value);
        });

        Route::bind('document-requirement', function ($value) {
            return \App\Models\DocumentRequirement::findOrFail($value);
        });

        // Inspection Checklist binding (for admin)
        Route::bind('inspectionChecklist', function ($value) {
            return \App\Models\InspectionChecklist::with(['items'])
                ->findOrFail($value);
        });

        Route::bind('inspection-checklist', function ($value) {
            return \App\Models\InspectionChecklist::with(['items'])
                ->findOrFail($value);
        });

        // Application by application number (public lookup)
        Route::bind('applicationNumber', function ($value) {
            return \App\Models\Application::where('application_number', $value)
                ->firstOrFail();
        });

        // Certificate lookup by certificate number
        Route::bind('certificateNumber', function ($value) {
            return \App\Models\Application::where('certificate_number', $value)
                ->where('status', 'approved')
                ->firstOrFail();
        });
    }
}