@extends('layouts.admin')

@section('title', 'Consultants Performance Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.reports.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Consultants Performance Report</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">Detailed performance metrics for all consultants</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
            Print Report
        </button>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
            </div>
            <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                Update Report
            </button>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Consultants</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $performanceData->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Applications</p>
            <p class="text-2xl font-bold text-blue-600">{{ $performanceData->sum('applications_handled') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
            <p class="text-2xl font-bold text-green-600">{{ $performanceData->sum('completed_applications') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Inspections</p>
            <p class="text-2xl font-bold text-purple-600">{{ $performanceData->sum('inspections_completed') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Avg Approval Rate</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $performanceData->count() > 0 ? number_format($performanceData->avg('approval_rate'), 1) : 0 }}%</p>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Most Applications Completed</h2>
            @php
                $topByApplications = $performanceData->sortByDesc('completed_applications')->take(3);
            @endphp
            <div class="space-y-3">
                @foreach($topByApplications as $index => $data)
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <span class="text-sm font-bold text-blue-600">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $data['consultant']->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $data['completed_applications'] }} completed</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Highest Approval Rate</h2>
            @php
                $topByApproval = $performanceData->where('completed_applications', '>', 0)->sortByDesc('approval_rate')->take(3);
            @endphp
            <div class="space-y-3">
                @foreach($topByApproval as $index => $data)
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <span class="text-sm font-bold text-green-600">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $data['consultant']->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($data['approval_rate'], 1) }}% approval</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Most Inspections</h2>
            @php
                $topByInspections = $performanceData->sortByDesc('inspections_completed')->take(3);
            @endphp
            <div class="space-y-3">
                @foreach($topByInspections as $index => $data)
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <span class="text-sm font-bold text-purple-600">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $data['consultant']->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $data['inspections_completed'] }} inspections</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detailed Performance Metrics</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Consultant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Department</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Apps</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Completed</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Inspections</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Appointments</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Approval Rate</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Avg Time (days)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($performanceData as $data)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $data['consultant']->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $data['consultant']->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $data['consultant']->consultant->department ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white">
                                {{ $data['applications_handled'] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    {{ $data['completed_applications'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white">
                                {{ $data['inspections_completed'] }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white">
                                {{ $data['appointments_scheduled'] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($data['approval_rate'], 1) }}%</span>
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-1">
                                        <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $data['approval_rate'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($data['avg_completion_time'], 1) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No consultant data available for the selected period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection