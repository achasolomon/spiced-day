<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\Inspection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Universal dashboard - redirects based on user role
     */
    public function index()
    {
        $user = Auth::user();
        
        return match($user->user_type) {
            'applicant' => redirect()->route('applicant.dashboard'),
            'consultant' => redirect()->route('consultant.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => abort(403, 'Invalid user type'),
        };
    }

    /**
     * Applicant Dashboard
     */
    public function applicant()
    {
        $user = Auth::user();
        
        // Get user's active application
        $activeApplication = $user->getActiveApplication();
        
        // Get upcoming appointments
        $upcomingAppointments = $user->appointments()
            ->where('scheduled_at', '>', now())
            ->where('status', '!=', 'cancelled')
            ->with(['consultant', 'application'])
            ->orderBy('scheduled_at')
            ->take(3)
            ->get();
        
        // Get recent documents
        $recentDocuments = $user->uploadedDocuments()
            ->with(['application', 'reviewedBy'])
            ->latest()
            ->take(5)
            ->get();
        
        // Get pending documents (if they have an active application)
         $pendingDocuments = [];
            if ($activeApplication) {
                $requiredDocs = $activeApplication->getRequiredDocumentsForStage();
                $uploadedCategories = $activeApplication->documents()
                    ->where('status', '!=', 'rejected')
                    ->pluck('category')
                    ->toArray();
                    
                // Fixed: Use array_diff instead of whereNotIn
                $pendingDocuments = array_diff($requiredDocs, $uploadedCategories);
            }
        
        // Get notifications
        $notifications = $user->notifications()
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        return view('applicant.dashboard', compact(
            'activeApplication',
            'upcomingAppointments', 
            'recentDocuments',
            'pendingDocuments',
            'notifications'
        ));
    }

    /**
     * Consultant Dashboard
     */
    public function consultant()
    {
        $user = Auth::user();
        $consultant = $user->consultant;
        
        // Get assigned applications
        $assignedApplications = $user->assignedApplications()
            ->with(['user', 'stages'])
            ->whereIn('status', [
                'submitted', 'under_review', 'initial_inspection_scheduled',
                'initial_inspection_completed', 'documents_required',
                'second_inspection_scheduled', 'second_inspection_completed',
                'final_review'
            ])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Get today's appointments
        $todayAppointments = $user->consultantAppointments()
            ->whereDate('scheduled_at', today())
            ->with(['applicant', 'application'])
            ->orderBy('scheduled_at')
            ->get();
        
        // Get this week's appointments
        $weekAppointments = $user->consultantAppointments()
            ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', '!=', 'cancelled')
            ->with(['applicant', 'application'])
            ->orderBy('scheduled_at')
            ->get();
        
        // Get pending document reviews
        $pendingDocuments = Document::whereHas('application', function($query) use ($user) {
                $query->where('consultant_id', $user->id);
            })
            ->whereIn('status', ['uploaded', 'under_review'])
            ->with(['application', 'uploadedBy'])
            ->latest()
            ->take(5)
            ->get();
        
        // Get recent inspections
        $recentInspections = $user->inspections()
            ->with(['application'])
            ->latest('conducted_at')
            ->take(5)
            ->get();

        // Performance stats
        $stats = [
            'active_applications' => $assignedApplications->count(),
            'completed_this_month' => $user->assignedApplications()
                ->whereIn('status', ['approved', 'rejected'])
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'pending_inspections' => $user->consultantAppointments()
                ->where('type', 'like', '%inspection%')
                ->where('status', 'scheduled')
                ->count(),
            'documents_to_review' => $pendingDocuments->count(),
        ];

        return view('consultant.dashboard', compact(
            'assignedApplications',
            'todayAppointments',
            'weekAppointments', 
            'pendingDocuments',
            'recentInspections',
            'stats'
        ));
    }

    /**
     * Admin Dashboard
     */
    public function admin()
    {
        // System Overview Stats
        $stats = [
            'total_applications' => Application::count(),
            'active_applications' => Application::active()->count(),
            'approved_this_month' => Application::where('status', 'approved')
                ->whereMonth('approved_at', now()->month)
                ->count(),
            'pending_review' => Application::whereIn('status', [
                'submitted', 'under_review', 'final_review'
            ])->count(),
            'total_users' => User::count(),
            'active_consultants' => User::consultants()->active()->count(),
            'new_applications_today' => Application::whereDate('created_at', today())->count(),
            'documents_pending_review' => Document::pendingReview()->count(),
        ];

        // Recent Applications
        $recentApplications = Application::with(['user', 'consultant'])
            ->latest()
            ->take(10)
            ->get();

        // Application Status Distribution
        $statusDistribution = Application::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly Application Trend (last 12 months)
        $monthlyTrend = Application::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Consultant Performance
        $consultantPerformance = User::consultants()
            ->with('consultant')
            ->withCount([
                'assignedApplications as total_assigned',
                'assignedApplications as completed_this_month' => function($query) {
                    $query->whereIn('status', ['approved', 'rejected'])
                          ->whereMonth('updated_at', now()->month);
                }
            ])
            ->get()
            ->take(5);

        // System Alerts
        $alerts = [];
        
        // Check for overdue inspections
        $overdueInspections = Appointment::where('type', 'like', '%inspection%')
            ->where('scheduled_at', '<', now())
            ->where('status', 'scheduled')
            ->count();
        
        if ($overdueInspections > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$overdueInspections} inspection(s) are overdue",
                'action' => route('admin.appointments.index', ['filter' => 'overdue'])
            ];
        }
        
        // Check for expired documents
        $expiredDocs = Document::expired()->count();
        if ($expiredDocs > 0) {
            $alerts[] = [
                'type' => 'error', 
                'message' => "{$expiredDocs} document(s) have expired",
                'action' => route('admin.documents.index', ['filter' => 'expired'])
            ];
        }

        // Check for applications without consultants
        $unassignedApps = Application::whereNull('consultant_id')
            ->whereIn('status', ['submitted', 'under_review'])
            ->count();
        
        if ($unassignedApps > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$unassignedApps} application(s) need consultant assignment", 
                'action' => route('admin.applications.index', ['filter' => 'unassigned'])
            ];
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentApplications',
            'statusDistribution',
            'monthlyTrend', 
            'consultantPerformance',
            'alerts'
        ));
    }

    /**
     * Get dashboard statistics via AJAX
     */
    public function stats(Request $request)
    {
        $user = Auth::user();
        
        $stats = match($user->user_type) {
            'applicant' => $this->getApplicantStats($user),
            'consultant' => $this->getConsultantStats($user),
            'admin' => $this->getAdminStats(),
            default => [],
        };

        return response()->json($stats);
    }

    /**
     * Get recent activity for dashboard widgets
     */
    public function recentActivity(Request $request)
    {
        $user = Auth::user();
        
        $activity = match($user->user_type) {
            'applicant' => $this->getApplicantActivity($user),
            'consultant' => $this->getConsultantActivity($user), 
            'admin' => $this->getAdminActivity(),
            default => [],
        };

        return response()->json($activity);
    }

    private function getApplicantStats($user)
    {
        $activeApp = $user->getActiveApplication();
        
        return [
            'application_progress' => $activeApp?->completion_percentage ?? 0,
            'documents_uploaded' => $user->uploadedDocuments()->count(),
            'documents_approved' => $user->uploadedDocuments()->approved()->count(),
            'next_appointment' => $user->appointments()
                ->where('scheduled_at', '>', now())
                ->where('status', '!=', 'cancelled')
                ->min('scheduled_at'),
        ];
    }

    private function getConsultantStats($user)
    {
        return [
            'active_applications' => $user->assignedApplications()->active()->count(),
            'appointments_today' => $user->consultantAppointments()
                ->whereDate('scheduled_at', today())
                ->count(),
            'pending_documents' => Document::whereHas('application', function($query) use ($user) {
                    $query->where('consultant_id', $user->id);
                })
                ->pendingReview()
                ->count(),
            'inspections_this_week' => $user->consultantAppointments()
                ->where('type', 'like', '%inspection%')
                ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
    }

    private function getAdminStats()
    {
        return [
            'total_applications' => Application::count(),
            'active_applications' => Application::active()->count(),
            'new_today' => Application::whereDate('created_at', today())->count(),
            'pending_review' => Application::whereIn('status', [
                'submitted', 'under_review', 'final_review'
            ])->count(),
            'overdue_inspections' => Appointment::where('type', 'like', '%inspection%')
                ->where('scheduled_at', '<', now())
                ->where('status', 'scheduled')
                ->count(),
            'documents_pending' => Document::pendingReview()->count(),
        ];
    }

    private function getApplicantActivity($user)
    {
        $activities = [];
        
        // Recent document uploads
        $user->uploadedDocuments()->latest()->take(3)->each(function($doc) use (&$activities) {
            $activities[] = [
                'type' => 'document_uploaded',
                'message' => "Uploaded {$doc->name}",
                'timestamp' => $doc->created_at,
                'status' => $doc->status,
            ];
        });
        
        // Application status changes
        if ($activeApp = $user->getActiveApplication()) {
            $activities[] = [
                'type' => 'application_status',
                'message' => "Application status: {$activeApp->status_display}",
                'timestamp' => $activeApp->updated_at,
                'status' => $activeApp->status,
            ];
        }
        
        return collect($activities)->sortByDesc('timestamp')->values()->all();
    }

    private function getConsultantActivity($user)
    {
        $activities = [];
        
        // Recent inspections
        $user->inspections()->latest('conducted_at')->take(3)->each(function($inspection) use (&$activities) {
            $activities[] = [
                'type' => 'inspection_completed',
                'message' => "Completed {$inspection->type} for {$inspection->application->business_name}",
                'timestamp' => $inspection->conducted_at,
                'status' => $inspection->overall_result,
            ];
        });
        
        // Recent document reviews
        Document::whereHas('application', function($query) use ($user) {
                $query->where('consultant_id', $user->id);
            })
            ->where('reviewed_by', $user->id)
            ->latest('reviewed_at')
            ->take(3)
            ->each(function($doc) use (&$activities) {
                $activities[] = [
                    'type' => 'document_reviewed',
                    'message' => "Reviewed {$doc->name} - {$doc->status}",
                    'timestamp' => $doc->reviewed_at,
                    'status' => $doc->status,
                ];
            });
        
        return collect($activities)->sortByDesc('timestamp')->values()->all();
    }

    private function getAdminActivity()
    {
        $activities = [];
        
        // Recent applications
        Application::latest()->take(5)->each(function($app) use (&$activities) {
            $activities[] = [
                'type' => 'new_application',
                'message' => "New application from {$app->contact_person} ({$app->business_name})",
                'timestamp' => $app->created_at,
                'status' => $app->status,
            ];
        });
        
        // Recent approvals/rejections
        Application::whereIn('status', ['approved', 'rejected'])
            ->latest('updated_at')
            ->take(3)
            ->each(function($app) use (&$activities) {
                $activities[] = [
                    'type' => 'application_decision',
                    'message' => "Application {$app->status}: {$app->business_name}",
                    'timestamp' => $app->updated_at,
                    'status' => $app->status,
                ];
            });
        
        return collect($activities)->sortByDesc('timestamp')->values()->all();
    }
}