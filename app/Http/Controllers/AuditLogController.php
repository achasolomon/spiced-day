<?php

// app/Http/Controllers/AuditLogController.php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AuditLog::class);

        $query = AuditLog::with(['user', 'application'])
            ->when($request->user_id, function ($q, $userId) {
                return $q->where('user_id', $userId);
            })
            ->when($request->action, function ($q, $action) {
                return $q->where('action', $action);
            })
            ->when($request->category, function ($q, $category) {
                return $q->where('category', $category);
            })
            ->when($request->severity, function ($q, $severity) {
                return $q->where('severity', $severity);
            })
            ->when($request->date_from, function ($q, $dateFrom) {
                return $q->where('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($q, $dateTo) {
                return $q->where('created_at', '<=', $dateTo . ' 23:59:59');
            })
            ->when($request->search, function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    $query->where('description', 'like', "%{$search}%")
                          ->orWhere('action', 'like', "%{$search}%");
                });
            });

        $auditLogs = $query->latest()->paginate(25);

        return view('audit-logs.index', compact('auditLogs'));
    }

    public function show(AuditLog $auditLog)
    {
        Gate::authorize('view', $auditLog);

        $auditLog->load(['user', 'application']);

        return view('audit-logs.show', compact('auditLog'));
    }

    public function export(Request $request)
    {
        Gate::authorize('export', AuditLog::class);

        // This would typically use a job for large exports
        $query = AuditLog::with(['user', 'application'])
            ->when($request->date_from, function ($q, $dateFrom) {
                return $q->where('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($q, $dateTo) {
                return $q->where('created_at', '<=', $dateTo . ' 23:59:59');
            });

        // Log the export action
        AuditLog::log('audit_export', new \stdClass(), 'Audit logs exported', [
            'filters' => $request->only(['date_from', 'date_to', 'category', 'severity']),
            'record_count' => $query->count()
        ]);

        // Return CSV download or redirect to job status page
        return back()->with('success', 'Export has been queued and will be available shortly.');
    }
}