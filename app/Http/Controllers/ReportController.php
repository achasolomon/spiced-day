<?php

// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\Inspection;
use App\Models\Consultant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function applications(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonths(3)->startOfMonth();
        $dateTo = $request->date_to ?? now()->endOfMonth();

        $applications = Application::with(['user', 'consultant'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->consultant_id, function ($q, $consultantId) {
                return $q->where('consultant_id', $consultantId);
            })
            ->get();

        $stats = [
            'total' => $applications->count(),
            'by_status' => $applications->groupBy('status')->map->count(),
            'by_type' => $applications->groupBy('dayhome_type')->map->count(),
            'approval_rate' => $applications->where('status', 'approved')->count() / max($applications->count(), 1) * 100,
            'avg_processing_time' => $this->calculateAverageProcessingTime($applications),
        ];

        return view('reports.applications', compact('applications', 'stats', 'dateFrom', 'dateTo'));
    }

    public function inspections(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonths(3)->startOfMonth();
        $dateTo = $request->date_to ?? now()->endOfMonth();

        $inspections = Inspection::with(['application.user', 'consultant'])
            ->whereBetween('conducted_at', [$dateFrom, $dateTo])
            ->when($request->type, function ($q, $type) {
                return $q->where('type', $type);
            })
            ->when($request->consultant_id, function ($q, $consultantId) {
                return $q->where('consultant_id', $consultantId);
            })
            ->get();

        $stats = [
            'total' => $inspections->count(),
            'by_result' => $inspections->groupBy('overall_result')->map->count(),
            'by_type' => $inspections->groupBy('type')->map->count(),
            'pass_rate' => $inspections->whereIn('overall_result', ['pass', 'conditional_pass'])->count() / max($inspections->count(), 1) * 100,
            'avg_score' => $inspections->avg('overall_score'),
        ];

        return view('reports.inspections', compact('inspections', 'stats', 'dateFrom', 'dateTo'));
    }

    public function consultants(Request $request)
    {
        $consultants = Consultant::with('user')
            ->when($request->department, function ($q, $department) {
                return $q->where('department', $department);
            })
            ->get();

        $performanceData = [];
        foreach ($consultants as $consultant) {
            $performanceData[] = [
                'consultant' => $consultant,
                'applications_handled' => $consultant->assignedApplications()->count(),
                'inspections_completed' => $consultant->inspections()->count(),
                'avg_completion_time' => $this->getConsultantAvgTime($consultant),
                'approval_rate' => $this->getConsultantApprovalRate($consultant),
            ];
        }

        return view('reports.consultants', compact('consultants', 'performanceData'));
    }

    private function calculateAverageProcessingTime($applications)
    {
        $completedApps = $applications->whereIn('status', ['approved', 'rejected']);
        
        if ($completedApps->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($completedApps as $app) {
            $endDate = $app->approved_at ?? $app->updated_at;
            $totalDays += $app->created_at->diffInDays($endDate);
        }

        return round($totalDays / $completedApps->count(), 1);
    }

    private function getConsultantAvgTime($consultant)
    {
        $applications = $consultant->assignedApplications()
            ->whereIn('status', ['approved', 'rejected'])
            ->get();

        if ($applications->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($applications as $app) {
            $endDate = $app->approved_at ?? $app->updated_at;
            $totalDays += $app->created_at->diffInDays($endDate);
        }

        return round($totalDays / $applications->count(), 1);
    }

    private function getConsultantApprovalRate($consultant)
    {
        $total = $consultant->assignedApplications()
            ->whereIn('status', ['approved', 'rejected'])
            ->count();

        if ($total == 0) {
            return 0;
        }

        $approved = $consultant->assignedApplications()
            ->where('status', 'approved')
            ->count();

        return round(($approved / $total) * 100, 1);
    }

        /**
     * Admin reports dashboard
     */
    public function adminIndex(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(3)->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

        // Applications Statistics
        $applicationStats = [
            'total' => Application::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'submitted' => Application::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'submitted')->count(),
            'under_review' => Application::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'under_review')->count(),
            'approved' => Application::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'approved')->count(),
            'rejected' => Application::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'rejected')->count(),
        ];

        // Monthly application trend
        $monthlyApplications = Application::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Inspections Statistics
        $inspectionStats = [
            'total' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])->count(),
            'passed' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])
                ->where('overall_result', 'pass')->count(),
            'failed' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])
                ->where('overall_result', 'fail')->count(),
            'conditional' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])
                ->where('overall_result', 'conditional_pass')->count(),
            'avg_score' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])
                ->avg('overall_score') ?? 0,
        ];

        // Documents Statistics
        $documentStats = [
            'total' => Document::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'pending' => Document::whereIn('status', ['uploaded', 'under_review'])->count(),
            'approved' => Document::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'approved')->count(),
            'rejected' => Document::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'rejected')->count(),
            'expired' => Document::where('expires', true)
                ->where('expiry_date', '<', now())->count(),
        ];

        // Appointments Statistics
        $appointmentStats = [
            'total' => Appointment::whereBetween('scheduled_at', [$dateFrom, $dateTo])->count(),
            'completed' => Appointment::whereBetween('scheduled_at', [$dateFrom, $dateTo])
                ->where('status', 'completed')->count(),
            'cancelled' => Appointment::whereBetween('scheduled_at', [$dateFrom, $dateTo])
                ->where('status', 'cancelled')->count(),
            'upcoming' => Appointment::where('scheduled_at', '>', now())
                ->whereIn('status', ['scheduled', 'confirmed'])->count(),
        ];

        // Consultant Performance
        $consultantPerformance = \App\Models\User::consultants()
            ->withCount([
                'assignedApplications as total_applications',
                'assignedApplications as completed_applications' => function($query) use ($dateFrom, $dateTo) {
                    $query->whereIn('status', ['approved', 'rejected'])
                        ->whereBetween('updated_at', [$dateFrom, $dateTo]);
                },
                'inspections as total_inspections' => function($query) use ($dateFrom, $dateTo) {
                    $query->whereBetween('conducted_at', [$dateFrom, $dateTo]);
                }
            ])
            ->having('completed_applications', '>', 0)
            ->orderByDesc('completed_applications')
            ->take(10)
            ->get();

        // Status Distribution
        $statusDistribution = Application::selectRaw('status, count(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('status')
            ->get();

        // Processing Time Analysis
        $avgProcessingTime = $this->calculateAverageProcessingTime(
            Application::whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereIn('status', ['approved', 'rejected'])
                ->get()
        );

        return view('admin.reports.index', compact(
            'applicationStats',
            'monthlyApplications',
            'inspectionStats',
            'documentStats',
            'appointmentStats',
            'consultantPerformance',
            'statusDistribution',
            'avgProcessingTime',
            'dateFrom',
            'dateTo'
        ));
    }

/**
 * Applications detailed report
 */
public function applicationsReport(Request $request)
{
    $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(3)->startOfMonth();
    $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

    $applications = Application::with(['user', 'consultant'])
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        })
        ->when($request->consultant_id, function ($q, $consultantId) {
            return $q->where('consultant_id', $consultantId);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(50);

    $stats = [
        'total' => $applications->total(),
        'by_status' => Application::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status'),
        'approval_rate' => $applications->where('status', 'approved')->count() / max($applications->total(), 1) * 100,
        'avg_processing_time' => $this->calculateAverageProcessingTime(
            Application::whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereIn('status', ['approved', 'rejected'])
                ->get()
        ),
    ];

    $consultants = User::consultants()->orderBy('name')->get();

    return view('admin.reports.applications', compact('applications', 'stats', 'dateFrom', 'dateTo', 'consultants'));
}

/**
 * Consultants performance report
 */
public function consultantsReport(Request $request)
{
    $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(3)->startOfMonth();
    $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

    $consultants = User::consultants()
        ->with('consultant')
        ->withCount([
            'assignedApplications as total_applications',
            'assignedApplications as completed_applications' => function($query) use ($dateFrom, $dateTo) {
                $query->whereIn('status', ['approved', 'rejected'])
                      ->whereBetween('updated_at', [$dateFrom, $dateTo]);
            },
            'inspections as total_inspections' => function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('conducted_at', [$dateFrom, $dateTo]);
            },
            'consultantAppointments as total_appointments' => function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('scheduled_at', [$dateFrom, $dateTo]);
            }
        ])
        ->get();

    $performanceData = $consultants->map(function($consultant) {
        return [
            'consultant' => $consultant,
            'applications_handled' => $consultant->total_applications,
            'completed_applications' => $consultant->completed_applications,
            'inspections_completed' => $consultant->total_inspections,
            'appointments_scheduled' => $consultant->total_appointments,
            'avg_completion_time' => $this->getConsultantAvgTime($consultant),
            'approval_rate' => $this->getConsultantApprovalRate($consultant),
        ];
    });

    return view('admin.reports.consultants', compact('performanceData', 'dateFrom', 'dateTo'));
}

/**
 * Documents report
 */
public function documentsReport(Request $request)
{
    $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(3)->startOfMonth();
    $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

    // Build base query for filtering
    $baseQuery = Document::whereBetween('created_at', [$dateFrom, $dateTo])
        ->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        })
        ->when($request->category, function ($q, $category) {
            return $q->where('document_category_id', $category);
        });

    // Get paginated documents with relationships
    $documents = (clone $baseQuery)
        ->with(['application.user', 'uploadedBy', 'reviewedBy', 'documentCategory'])
        ->orderBy('created_at', 'desc')
        ->paginate(50);

    // Calculate stats
    $totalDocuments = (clone $baseQuery)->count();
    $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
    
    // Get category stats by joining with document_categories table
    // Need to rebuild the query with explicit table prefixes for the join
    $categoryQuery = Document::whereBetween('documents.created_at', [$dateFrom, $dateTo])
        ->when($request->status, function ($q, $status) {
            return $q->where('documents.status', $status);
        })
        ->when($request->category, function ($q, $category) {
            return $q->where('documents.document_category_id', $category);
        })
        ->join('document_categories', 'documents.document_category_id', '=', 'document_categories.id')
        ->selectRaw('document_categories.name as category, count(*) as count')
        ->groupBy('document_categories.name');
    
    $categoryStats = $categoryQuery->pluck('count', 'category');
    
    $stats = [
        'total' => $totalDocuments,
        'by_status' => (clone $baseQuery)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status'),
        'by_category' => $categoryStats,
        'approval_rate' => $totalDocuments > 0 ? ($approvedCount / $totalDocuments) * 100 : 0,
        'expired_count' => Document::where('expires', true)
            ->where('expiry_date', '<', now())
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count(),
        'avg_review_time' => $this->calculateAvgDocumentReviewTime(
            (clone $baseQuery)
                ->whereNotNull('reviewed_at')
                ->get()
        ),
    ];

    return view('admin.reports.documents', compact('documents', 'stats', 'dateFrom', 'dateTo'));
}

/**
 * Inspections report
 */
public function inspectionsReport(Request $request)
{
    $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(3)->startOfMonth();
    $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

    $inspections = Inspection::with(['application.user', 'consultant'])
        ->whereBetween('conducted_at', [$dateFrom, $dateTo])
        ->when($request->type, function ($q, $type) {
            return $q->where('type', $type);
        })
        ->when($request->result, function ($q, $result) {
            return $q->where('overall_result', $result);
        })
        ->when($request->consultant_id, function ($q, $consultantId) {
            return $q->where('consultant_id', $consultantId);
        })
        ->orderBy('conducted_at', 'desc')
        ->paginate(50);

    $stats = [
        'total' => $inspections->total(),
        'by_result' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])
            ->selectRaw('overall_result, count(*) as count')
            ->groupBy('overall_result')
            ->pluck('count', 'overall_result'),
        'by_type' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type'),
        'pass_rate' => $inspections->whereIn('overall_result', ['pass', 'conditional_pass'])->count() / max($inspections->total(), 1) * 100,
        'avg_score' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])
            ->avg('overall_score') ?? 0,
        'requires_reinspection' => Inspection::whereBetween('conducted_at', [$dateFrom, $dateTo])
            ->where('requires_reinspection', true)
            ->count(),
    ];

    $consultants = User::consultants()->orderBy('name')->get();

    return view('admin.reports.inspections', compact('inspections', 'stats', 'dateFrom', 'dateTo', 'consultants'));
}

/**
 * Calculate average document review time
 */
private function calculateAvgDocumentReviewTime($documents)
{
    if ($documents->isEmpty()) {
        return 0;
    }

    $totalHours = 0;
    foreach ($documents as $doc) {
        if ($doc->reviewed_at) {
            $totalHours += $doc->created_at->diffInHours($doc->reviewed_at);
        }
    }

    return round($totalHours / $documents->count(), 1);
}

}