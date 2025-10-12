@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive system insights and performance metrics</p>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-4">
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
            <button type="button" onclick="window.print()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Print
            </button>
        </form>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.reports.applications') }}" class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white hover:from-purple-600 hover:to-purple-700 transition-all shadow-lg">
            <svg class="w-8 h-8 mb-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"></path>
            </svg>
            <h3 class="text-lg font-bold">Applications Report</h3>
            <p class="text-sm text-white/80 mt-1">Detailed application analytics</p>
        </a>

        <a href="{{ route('admin.reports.inspections') }}" class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg">
            <svg class="w-8 h-8 mb-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6z" clip-rule="evenodd"></path>
            </svg>
            <h3 class="text-lg font-bold">Inspections Report</h3>
            <p class="text-sm text-white/80 mt-1">Inspection performance metrics</p>
        </a>

        <a href="{{ route('admin.reports.consultants') }}" class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white hover:from-green-600 hover:to-green-700 transition-all shadow-lg">
            <svg class="w-8 h-8 mb-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
            </svg>
            <h3 class="text-lg font-bold">Consultant Performance</h3>
            <p class="text-sm text-white/80 mt-1">Team productivity analysis</p>
        </a>

        <a href="{{ route('admin.reports.documents') }}" class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg">
            <svg class="w-8 h-8 mb-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
            </svg>
            <h3 class="text-lg font-bold">Documents Report</h3>
            <p class="text-sm text-white/80 mt-1">Document processing stats</p>
        </a>
    </div>

    <!-- Overview Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Applications -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Applications</h3>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Total</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $applicationStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Approved</span>
                    <span class="font-bold text-green-600">{{ $applicationStats['approved'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Under Review</span>
                    <span class="font-bold text-yellow-600">{{ $applicationStats['under_review'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Rejected</span>
                    <span class="font-bold text-red-600">{{ $applicationStats['rejected'] }}</span>
                </div>
            </div>
        </div>

        <!-- Inspections -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Inspections</h3>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Total</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $inspectionStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Passed</span>
                    <span class="font-bold text-green-600">{{ $inspectionStats['passed'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Failed</span>
                    <span class="font-bold text-red-600">{{ $inspectionStats['failed'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Avg Score</span>
                    <span class="font-bold text-blue-600">{{ number_format($inspectionStats['avg_score'], 1) }}%</span>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Documents</h3>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Total</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $documentStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Approved</span>
                    <span class="font-bold text-green-600">{{ $documentStats['approved'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Pending</span>
                    <span class="font-bold text-yellow-600">{{ $documentStats['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Expired</span>
                    <span class="font-bold text-red-600">{{ $documentStats['expired'] }}</span>
                </div>
            </div>
        </div>

        <!-- Appointments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Appointments</h3>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Total</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $appointmentStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Completed</span>
                    <span class="font-bold text-green-600">{{ $appointmentStats['completed'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Upcoming</span>
                    <span class="font-bold text-blue-600">{{ $appointmentStats['upcoming'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Cancelled</span>
                    <span class="font-bold text-red-600">{{ $appointmentStats['cancelled'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Applications Trend -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Monthly Application Trend</h2>
            <div class="space-y-3">
                @foreach($monthlyApplications as $month)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($month->month . '-01')->format('F Y') }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($month->count / max($monthlyApplications->max('count'), 1)) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white w-8 text-right">{{ $month->count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Consultants -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Top Performing Consultants</h2>
            <div class="space-y-3">
                @forelse($consultantPerformance as $consultant)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ $consultant->initials }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $consultant->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $consultant->completed_applications }} completed</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $consultant->total_inspections }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">inspections</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Key Performance Indicators</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Average Processing Time</p>
                <p class="text-3xl font-bold text-purple-600">{{ $avgProcessingTime }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">days</p>
            </div>
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Approval Rate</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ $applicationStats['total'] > 0 ? number_format(($applicationStats['approved'] / $applicationStats['total']) * 100, 1) : 0 }}%
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">of applications</p>
            </div>
            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Inspection Pass Rate</p>
                <p class="text-3xl font-bold text-blue-600">
                    {{ $inspectionStats['total'] > 0 ? number_format((($inspectionStats['passed'] + $inspectionStats['conditional']) / $inspectionStats['total']) * 100, 1) : 0 }}%
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">of inspections</p>
            </div>
        </div>
    </div>
</div>
@endsection