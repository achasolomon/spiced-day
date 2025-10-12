@extends('layouts.admin')

@section('title', 'Inspections Report')

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
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Inspections Report</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">Comprehensive inspection results and statistics</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
            Print Report
        </button>
    </div>

    <!-- Date Range & Filters -->
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
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    <option value="initial" {{ request('type') == 'initial' ? 'selected' : '' }}>Initial</option>
                    <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                    <option value="follow_up" {{ request('type') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                    <option value="complaint" {{ request('type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Result</label>
                <select name="result" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Results</option>
                    <option value="pass" {{ request('result') == 'pass' ? 'selected' : '' }}>Pass</option>
                    <option value="conditional_pass" {{ request('result') == 'conditional_pass' ? 'selected' : '' }}>Conditional Pass</option>
                    <option value="fail" {{ request('result') == 'fail' ? 'selected' : '' }}>Fail</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consultant</label>
                <select name="consultant_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Consultants</option>
                    @foreach($consultants as $consultant)
                        <option value="{{ $consultant->id }}" {{ request('consultant_id') == $consultant->id ? 'selected' : '' }}>
                            {{ $consultant->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                Update Report
            </button>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Pass</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['by_result']['pass'] ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Conditional</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['by_result']['conditional_pass'] ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Fail</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['by_result']['fail'] ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Avg Score</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['avg_score'], 1) }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Reinspection</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['requires_reinspection'] }}</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Result Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Result Distribution</h2>
            <div class="space-y-3">
                @foreach($stats['by_result'] as $result => $count)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $result) }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $count }} ({{ $stats['total'] > 0 ? number_format(($count / $stats['total']) * 100, 1) : 0 }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all
                                {{ $result === 'pass' ? 'bg-green-600' : '' }}
                                {{ $result === 'fail' ? 'bg-red-600' : '' }}
                                {{ $result === 'conditional_pass' ? 'bg-yellow-600' : '' }}" 
                                 style="width: {{ $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Type Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Inspection Type Distribution</h2>
            <div class="space-y-3">
                @forelse($stats['by_type'] as $type => $count)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $type) }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No type data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Key Performance Indicators</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Pass Rate</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($stats['pass_rate'], 1) }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pass + Conditional Pass</p>
            </div>
            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Average Score</p>
                <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['avg_score'], 1) }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Overall average</p>
            </div>
            <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Reinspection Required</p>
                <p class="text-3xl font-bold text-orange-600">{{ $stats['requires_reinspection'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stats['total'] > 0 ? number_format(($stats['requires_reinspection'] / $stats['total']) * 100, 1) : 0 }}% of total</p>
            </div>
        </div>
    </div>

    <!-- Inspections Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Recent Inspections</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Application</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Consultant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Result</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Conducted</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reinspection</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($inspections as $inspection)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inspection->application->application_number ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $inspection->application->user->name ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white capitalize">
                                {{ str_replace('_', ' ', $inspection->type) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $inspection->consultant->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    {{ $inspection->overall_result === 'pass' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $inspection->overall_result === 'fail' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $inspection->overall_result === 'conditional_pass' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $inspection->overall_result)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($inspection->overall_score, 1) }}%</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $inspection->conducted_at ? $inspection->conducted_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($inspection->requires_reinspection)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">
                                        Required
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No inspections found for the selected period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inspections->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection