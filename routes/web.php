<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\PostalCodeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// About, Contact, etc.
Route::view('/about', 'public.about')->name('about');
Route::view('/contact', 'public.contact')->name('contact');
Route::view('/services', 'public.services')->name('services');
Route::view('/faq', 'public.faq')->name('faq');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Email Verification Routes (Must be OUTSIDE auth middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('email')->group(function () {
    Route::get('/verify-code-form', [VerificationController::class, 'show'])
        ->name('token.notice');
    Route::post('/verify-code-submit', [VerificationController::class, 'verify'])
        ->name('token.verify');
    Route::post('/verify-code-resend', [VerificationController::class, 'resend'])
        ->name('token.resend');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (All Users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Universal Dashboard (redirects based on role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
     Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
       Route::post('/send-to-user/{user}', [NotificationController::class, 'sendToUser'])->name('send');

    });

    // File Downloads
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
});

/*
|--------------------------------------------------------------------------
| Applicant Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'user.type:applicant'])->prefix('applicant')->name('applicant.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'applicant'])->name('dashboard');

    // Applications
    Route::resource('applications', ApplicationController::class)->except(['index']);
    Route::post('/applications/{application}/submit', [ApplicationController::class, 'submit'])
        ->name('applications.submit');

    // Documents
  Route::prefix('applications/{application}/documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('index');
    Route::post('/', [DocumentController::class, 'store'])->name('store');
    Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
    Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
    Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('preview');
});

    // Appointments
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'applicantIndex'])->name('index');
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        Route::post('/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('confirm');
        Route::post('/{appointment}/request-reschedule', [AppointmentController::class, 'requestReschedule'])->name('request-reschedule');
    });

    // Status & Help
    Route::view('/status', 'applicant.status')->name('status');
    Route::view('/help', 'applicant.help')->name('help');
});

/*
|--------------------------------------------------------------------------
| Consultant Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'user.type:consultant'])->prefix('consultant')->name('consultant.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'consultant'])->name('dashboard');

    // Applications
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [ApplicationController::class, 'consultantIndex'])->name('index');
        Route::get('/{application}', [ApplicationController::class, 'show'])->name('show');
        Route::put('/{application}', [ApplicationController::class, 'consultantUpdate'])->name('update');
        Route::post('/{application}/assign-to-me', [ApplicationController::class, 'assignToMe'])->name('assign-to-me');
        Route::post('/{application}/move-stage', [ApplicationController::class, 'moveStage'])->name('move-stage');
    });

    // Inspections
    Route::resource('inspections', InspectionController::class);
    Route::get('/inspections/{inspection}/checklist', [InspectionController::class, 'checklist'])->name('inspections.checklist');
    Route::post('/inspections/{inspection}/submit-checklist', [InspectionController::class, 'submitChecklist'])->name('inspections.submit-checklist');

    // Document Review
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/pending-review', [DocumentController::class, 'pendingReview'])->name('pending-review');
        Route::post('/{document}/approve', [DocumentController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [DocumentController::class, 'reject'])->name('reject');
        Route::post('/{document}/request-replacement', [DocumentController::class, 'requestReplacement'])->name('request-replacement');
        
        Route::post('/bulk-download/{application}', [DocumentController::class, 'bulkDownload'])
            ->name('bulk-download');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download'); // Changed name and method
        Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('preview');
    });

    // Calendar
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');

    // Appointments

 Route::prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('index'); // Add this if missing
    Route::get('/create', [AppointmentController::class, 'create'])->name('create'); // ADD THIS LINE
    Route::post('/', [AppointmentController::class, 'store'])->name('store');
    Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
    Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
    Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
    Route::post('/{appointment}/start', [AppointmentController::class, 'start'])->name('start');
    Route::post('/{appointment}/complete', [AppointmentController::class, 'complete'])->name('complete');
    Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
    Route::post('/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('confirm');
});    

    // // Reports
    // Route::prefix('reports')->name('reports.')->group(function () {
    //     Route::get('/', [ReportController::class, 'consultantIndex'])->name('index');
    //     Route::get('/performance', [ReportController::class, 'performance'])->name('performance');
    //     Route::get('/applications', [ReportController::class, 'applications'])->name('applications');
    // });

    // Profile
    Route::get('/profile', [ConsultantController::class, 'profile'])->name('profile');
    Route::put('/profile', [ConsultantController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'user.type:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::get('/create', [AdminController::class, 'createUser'])->name('create');
        Route::post('/', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
        Route::get('/{user}/data', [AdminController::class, 'getUserData'])->name('data'); // Add this line
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('destroy');
        Route::post('/{user}/activate', [AdminController::class, 'activateUser'])->name('activate');
        Route::post('/{user}/deactivate', [AdminController::class, 'deactivateUser'])->name('deactivate');
    });

    // Consultants
    Route::prefix('consultants')->name('consultants.')->group(function () {
            Route::get('/', [ConsultantController::class, 'index'])->name('index');
            Route::get('/available-users', [ConsultantController::class, 'getAvailableUsers'])->name('available-users');
            Route::get('/create', [ConsultantController::class, 'create'])->name('create');
            Route::post('/', [ConsultantController::class, 'store'])->name('store');
            Route::get('/{consultant}', [ConsultantController::class, 'show'])->name('show');
            Route::get('/{consultant}/data', [ConsultantController::class, 'getConsultantData'])->name('data');
            Route::get('/{consultant}/edit', [ConsultantController::class, 'edit'])->name('edit');
            Route::put('/{consultant}', [ConsultantController::class, 'update'])->name('update');
            Route::delete('/{consultant}', [ConsultantController::class, 'destroy'])->name('destroy');
            Route::post('/{consultant}/toggle-availability', [ConsultantController::class, 'toggleAvailability'])->name('toggle-availability');
            Route::post('/{consultant}/update-workload', [ConsultantController::class, 'updateWorkload'])->name('update-workload');

        });

         // Region Routes
        Route::prefix('regions')->name('regions.')->group(function () {
            Route::get('/', [RegionController::class, 'index'])->name('index');
            Route::get('/all', [RegionController::class, 'getAllRegions'])->name('all'); // Changed this line
            Route::post('/', [RegionController::class, 'store'])->name('store');
            Route::get('/{region}/data', [RegionController::class, 'showData'])->name('data');
            Route::put('/{region}', [RegionController::class, 'update'])->name('update');
            Route::delete('/{region}', [RegionController::class, 'destroy'])->name('destroy');
        });

        // Postal Code Routes
        Route::prefix('postal-codes')->name('postal-codes.')->group(function () {
            Route::get('/', [PostalCodeController::class, 'index'])->name('index');
            Route::post('/', [PostalCodeController::class, 'store'])->name('store');
            Route::get('/{postalCode}/data', [PostalCodeController::class, 'showData'])->name('data');
            Route::put('/{postalCode}', [PostalCodeController::class, 'update'])->name('update');
            Route::delete('/{postalCode}', [PostalCodeController::class, 'destroy'])->name('destroy');
        });
 


    // Applications
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [ApplicationController::class, 'adminIndex'])->name('index');
        Route::get('/{application}', [ApplicationController::class, 'adminShow'])->name('show');
        Route::post('/{application}/approve', [ApplicationController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [ApplicationController::class, 'reject'])->name('reject');
        Route::post('/{application}/assign-consultant', [ApplicationController::class, 'assignConsultant'])->name('assign-consultant');
        Route::get('/{application}/audit-log', [ApplicationController::class, 'auditLog'])->name('audit-log');
    });

     // Appointments
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'adminIndex'])->name('index');
        Route::get('/create', [AppointmentController::class, 'create'])->name('create');
        Route::post('/', [AppointmentController::class, 'store'])->name('store');
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
        Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
        Route::post('/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('reschedule');
        Route::get('/calendar', [AppointmentController::class, 'adminCalendar'])->name('calendar');
    });

    // Inspections
    Route::prefix('inspections')->name('inspections.')->group(function () {
        Route::get('/', [InspectionController::class, 'adminIndex'])->name('index');
        Route::get('/{inspection}', [InspectionController::class, 'show'])->name('show');
        Route::get('/{inspection}/edit', [InspectionController::class, 'edit'])->name('edit');
        Route::put('/{inspection}', [InspectionController::class, 'update'])->name('update');
        Route::delete('/{inspection}', [InspectionController::class, 'destroy'])->name('destroy');
        Route::get('/{inspection}/report', [InspectionController::class, 'report'])->name('report');
        Route::post('/{inspection}/report/export', [InspectionController::class, 'exportReport'])->name('report.export');
    });

    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'adminIndex'])->name('index');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::post('/{document}/approve', [DocumentController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [DocumentController::class, 'reject'])->name('reject');
        Route::post('/{document}/request-revision', [DocumentController::class, 'requestRevision'])->name('request-revision');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
        Route::get('/pending-review', [DocumentController::class, 'pendingReview'])->name('pending-review');
        Route::get('/expired', [DocumentController::class, 'expired'])->name('expired');
    });

    // Audit Logs / Activity Log
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
        Route::get('/user/{user}', [AuditLogController::class, 'userActivity'])->name('user-activity');
        Route::get('/application/{application}', [AuditLogController::class, 'applicationActivity'])->name('application-activity');
        Route::post('/export', [AuditLogController::class, 'export'])->name('export');
        Route::delete('/clear-old', [AuditLogController::class, 'clearOld'])->name('clear-old');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'adminIndex'])->name('index');
        Route::get('/overview', [ReportController::class, 'overview'])->name('overview');
        Route::get('/applications', [ReportController::class, 'applicationsReport'])->name('applications');
        Route::get('/consultants', [ReportController::class, 'consultantsReport'])->name('consultants');
        Route::get('/documents', [ReportController::class, 'documentsReport'])->name('documents');
        Route::get('/inspections', [ReportController::class, 'inspectionsReport'])->name('inspections');
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });
});
