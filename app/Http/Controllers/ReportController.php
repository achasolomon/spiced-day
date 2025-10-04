<?php

// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\Inspection;
use App\Models\Consultant;
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

        if ($total === 0) {
            return 0;
        }

        $approved = $consultant->assignedApplications()
            ->where('status', 'approved')
            ->count();

        return round(($approved / $total) * 100, 1);
    }
}