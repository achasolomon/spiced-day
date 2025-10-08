@extends('layouts.admin')

@section('title', 'Audit Log - ' . $application->application_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.applications.show', $application) }}" 
                   class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Audit Log
                </h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">
                Complete activity history for {{ $application->application_number }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Logs</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_logs'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Status Changes</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['status_changes'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Documents</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['document_actions'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Appointments</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['appointment_actions'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Inspections</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['inspection_actions'] }}</p>
        </div>
    </div>

    <!-- Application Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Applicant</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $application->full_name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $application->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $application->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                    {{ $application->status === 'under_review' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                    {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                    {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                    {{ $application->status_display }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Consultant</p>
                @if($application->consultant)
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $application->consultant->name }}</p>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Not assigned</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Filter by Action
                </label>
                <select name="action"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Filter by Category
                </label>
                <select name="category"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $category)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Date From
                </label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Date To
                </label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="md:col-span-2 lg:col-span-4 flex gap-2">
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('admin.applications.audit-log', $application) }}"
                   class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Log Timeline -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Activity Timeline</h2>

        <div class="space-y-6">
            @forelse($auditLogs as $log)
                <div class="relative pl-8 pb-6 border-l-2 border-gray-200 dark:border-gray-700 last:border-l-0 last:pb-0">
                    <!-- Timeline Dot -->
                    <div class="absolute left-0 top-0 -ml-[9px] w-4 h-4 rounded-full 
                        {{ str_contains($log->action, 'created') || str_contains($log->action, 'approved') ? 'bg-green-500' : '' }}
                        {{ str_contains($log->action, 'updated') || str_contains($log->action, 'status') ? 'bg-blue-500' : '' }}
                        {{ str_contains($log->action, 'deleted') || str_contains($log->action, 'rejected') || str_contains($log->action, 'cancelled') ? 'bg-red-500' : '' }}
                        {{ !str_contains($log->action, 'created') && !str_contains($log->action, 'updated') && !str_contains($log->action, 'deleted') && !str_contains($log->action, 'approved') && !str_contains($log->action, 'rejected') && !str_contains($log->action, 'cancelled') && !str_contains($log->action, 'status') ? 'bg-gray-400' : '' }}
                        border-4 border-white dark:border-gray-800"></div>

                    <!-- Log Content -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $log->description }}
                                </p>
                            </div>
                            
                            @if($log->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->category === 'authentication' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $log->category === 'application_management' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                                    {{ $log->category === 'document_management' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $log->category === 'appointment' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                                    {{ $log->category === 'inspection' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300' : '' }}
                                    {{ !in_array($log->category, ['authentication', 'application_management', 'document_management', 'appointment', 'inspection']) ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}">
                                    {{ ucwords(str_replace('_', ' ', $log->category)) }}
                                </span>
                            @endif
                        </div>

                        <!-- Severity Badge -->
                        @if($log->severity)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mr-2
                                {{ $log->severity === 'low' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                {{ $log->severity === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                {{ $log->severity === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                                {{ ucfirst($log->severity) }} Severity
                            </span>
                        @endif

                        <!-- Metadata -->
                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mt-3">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>{{ $log->user ? $log->user->name : 'System' }}</span>
                            </div>
                            
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $log->created_at->format('M d, Y \a\t g:i A') }}</span>
                            </div>

                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>{{ $log->created_at->diffForHumans() }}</span>
                            </div>

                            @if($log->ip_address)
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                    <span>{{ $log->ip_address }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Additional Data -->
                        @if($log->metadata && is_array($log->metadata) && count($log->metadata) > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <details class="text-xs">
                                    <summary class="cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium">
                                        View Additional Details
                                    </summary>
                                    <pre class="mt-2 p-3 bg-gray-100 dark:bg-gray-800 rounded text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                                </details>
                            </div>
                        @endif

                        <!-- Old/New Values -->
                        @if(($log->old_values && count($log->old_values) > 0) || ($log->new_values && count($log->new_values) > 0))
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-4">
                                @if($log->old_values && count($log->old_values) > 0)
                                    <div>
                                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Old Values:</p>
                                        <pre class="text-xs p-2 bg-red-50 dark:bg-red-900/20 rounded text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                @endif
                                
                                @if($log->new_values && count($log->new_values) > 0)
                                    <div>
                                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">New Values:</p>
                                        <pre class="text-xs p-2 bg-green-50 dark:bg-green-900/20 rounded text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No audit logs found</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Activity will appear here as actions are performed</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($auditLogs->hasPages())
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                {{ $auditLogs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection