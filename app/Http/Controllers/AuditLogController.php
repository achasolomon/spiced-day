<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Display audit logs index
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'model'])
            ->when($request->search, function ($q, $search) {
                return $q->where('description', 'like', "%{$search}%")
                         ->orWhere('action', 'like', "%{$search}%");
            })
            ->when($request->action, function ($q, $action) {
                return $q->where('action', $action);
            })
            ->when($request->user_id, function ($q, $userId) {
                return $q->where('user_id', $userId);
            })
            ->when($request->auditable_type, function ($q, $type) {
                return $q->where('auditable_type', $type);
            })
            ->when($request->date_from, function ($q, $dateFrom) {
                return $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($q, $dateTo) {
                return $q->whereDate('created_at', '<=', $dateTo);
            });

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $auditLogs = $query->orderBy($sortBy, $sortOrder)->paginate(50);

        // Get users for filter
        $users = User::orderBy('name')->get();

        // Get available actions
        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Statistics
        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => AuditLog::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.audit-logs.index', compact('auditLogs', 'users', 'actions', 'stats'));
    }

    /**
     * Display a specific audit log entry
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'auditable']);
        
        return view('admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * Display user activity
     */
    public function userActivity(User $user, Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth();
        $dateTo = $request->date_to ?? now();

        $activities = AuditLog::with('auditable')
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total_actions' => AuditLog::where('user_id', $user->id)->count(),
            'actions_today' => AuditLog::where('user_id', $user->id)
                ->whereDate('created_at', today())->count(),
            'actions_this_month' => AuditLog::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.audit-logs.user-activity', compact('user', 'activities', 'stats', 'dateFrom', 'dateTo'));
    }

    /**
     * Display application activity
     */
    public function applicationActivity(Application $application)
    {
        $activities = AuditLog::where('auditable_type', 'App\Models\Application')
            ->where('auditable_id', $application->id)
            ->orWhere(function($query) use ($application) {
                $query->where('auditable_type', 'App\Models\Document')
                      ->whereHas('auditable', function($q) use ($application) {
                          $q->where('application_id', $application->id);
                      });
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.audit-logs.application-activity', compact('application', 'activities'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = AuditLog::with(['user', 'auditable'])
            ->when($request->date_from, function ($q, $dateFrom) {
                return $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($q, $dateTo) {
                return $q->whereDate('created_at', '<=', $dateTo);
            });

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Action',
                'Description',
                'User',
                'User Email',
                'Auditable Type',
                'Auditable ID',
                'IP Address',
                'User Agent',
                'Timestamp',
            ]);

            // Add data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->action,
                    $log->description,
                    $log->user->name ?? 'N/A',
                    $log->user->email ?? 'N/A',
                    class_basename($log->auditable_type ?? ''),
                    $log->auditable_id ?? '',
                    $log->ip_address ?? '',
                    $log->user_agent ?? '',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear old audit logs
     */
    public function clearOld(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:30|max:365',
        ]);

        $cutoffDate = now()->subDays($validated['days']);
        
        $deletedCount = AuditLog::where('created_at', '<', $cutoffDate)->delete();

        return back()->with('success', "Successfully deleted {$deletedCount} audit log entries older than {$validated['days']} days.");
    }
}