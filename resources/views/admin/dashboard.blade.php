@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header - Mobile Responsive -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">System Overview</h1>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-1">Monitor and manage your application system</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.reports.index') }}" 
                   class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center text-sm font-medium">
                    View Reports
                </a>
                <button 
                    @click="$dispatch('open-create-user-modal')" 
                    class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors text-center text-sm">
                    Add New User
                </button>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if(count($alerts) > 0)
        <div class="space-y-3">
            @foreach($alerts as $alert)
                <div class="bg-{{ $alert['type'] }}-50 dark:bg-{{ $alert['type'] }}-900/20 border border-{{ $alert['type'] }}-200 dark:border-{{ $alert['type'] }}-800 rounded-xl p-3 sm:p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            @if($alert['type'] === 'warning')
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($alert['type'] === 'error')
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base break-words">{{ $alert['message'] }}</p>
                            @if(isset($alert['action']))
                                <a href="{{ $alert['action'] }}" class="text-xs sm:text-sm text-{{ $alert['type'] }}-600 hover:text-{{ $alert['type'] }}-700 font-medium mt-1 inline-block">
                                    View Details →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Stats Grid - Mobile Responsive -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Applications -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl sm:rounded-2xl p-5 sm:p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="bg-white/20 p-2.5 sm:p-3 rounded-lg sm:rounded-xl">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-purple-100 text-xs sm:text-sm">New Today</p>
                    <p class="text-xl sm:text-2xl font-bold">+{{ $stats['new_applications_today'] }}</p>
                </div>
            </div>
            <div>
                <p class="text-purple-100 text-xs sm:text-sm">Total Applications</p>
                <p class="text-3xl sm:text-4xl font-bold mt-1">{{ number_format($stats['total_applications']) }}</p>
            </div>
        </div>

        <!-- Active Applications -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl sm:rounded-2xl p-5 sm:p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="bg-white/20 p-2.5 sm:p-3 rounded-lg sm:rounded-xl">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-blue-100 text-xs sm:text-sm">Pending Review</p>
                    <p class="text-xl sm:text-2xl font-bold">{{ $stats['pending_review'] }}</p>
                </div>
            </div>
            <div>
                <p class="text-blue-100 text-xs sm:text-sm">Active Applications</p>
                <p class="text-3xl sm:text-4xl font-bold mt-1">{{ number_format($stats['active_applications']) }}</p>
            </div>
        </div>

        <!-- Approved This Month -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl sm:rounded-2xl p-5 sm:p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="bg-white/20 p-2.5 sm:p-3 rounded-lg sm:rounded-xl">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-green-100 text-xs sm:text-sm">Approval Rate</p>
                    <p class="text-xl sm:text-2xl font-bold">{{ number_format(($stats['approved_this_month'] / max($stats['total_applications'], 1)) * 100, 1) }}%</p>
                </div>
            </div>
            <div>
                <p class="text-green-100 text-xs sm:text-sm">Approved This Month</p>
                <p class="text-3xl sm:text-4xl font-bold mt-1">{{ number_format($stats['approved_this_month']) }}</p>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl sm:rounded-2xl p-5 sm:p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="bg-white/20 p-2.5 sm:p-3 rounded-lg sm:rounded-xl">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-orange-100 text-xs sm:text-sm">Consultants</p>
                    <p class="text-xl sm:text-2xl font-bold">{{ $stats['active_consultants'] }}</p>
                </div>
            </div>
            <div>
                <p class="text-orange-100 text-xs sm:text-sm">Total Users</p>
                <p class="text-3xl sm:text-4xl font-bold mt-1">{{ number_format($stats['total_users']) }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Recent Applications -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Recent Applications</h2>
                    <a href="{{ route('admin.applications.index') }}" class="text-xs sm:text-sm text-purple-600 hover:text-purple-700 font-medium whitespace-nowrap">View All →</a>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-3 sm:space-y-4">
                        @forelse($recentApplications as $app)
                            {{-- Mobile Layout --}}
                            <div class="sm:hidden flex flex-col gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ substr($app->educator_first_name, 0, 1) }}{{ substr($app->educator_last_name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-white text-sm break-words">{{ $app->full_name }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 break-all">{{ $app->application_number }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $app->status_badge_color == 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                        {{ $app->status_badge_color == 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                        {{ $app->status_badge_color == 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}">
                                        {{ $app->status_display }}
                                    </span>
                                    <a href="{{ route('admin.applications.show', $app) }}" 
                                       class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                                        View
                                    </a>
                                </div>
                            </div>

                            {{-- Desktop Layout --}}
                            <div class="hidden sm:flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                        {{ substr($app->educator_first_name, 0, 1) }}{{ substr($app->educator_last_name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $app->full_name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $app->application_number }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                                        {{ $app->status_badge_color == 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                        {{ $app->status_badge_color == 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                        {{ $app->status_badge_color == 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}">
                                        {{ $app->status_display }}
                                    </span>
                                    <a href="{{ route('admin.applications.show', $app) }}" 
                                       class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                                        View
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8 text-sm">No recent applications</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Application Status Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">Status Distribution</h3>
                <div class="space-y-3">
                    @foreach($statusDistribution as $status => $count)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 break-words">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                <span class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white ml-2">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ ($count / max(array_sum($statusDistribution), 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

          <!-- Top Consultants -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">Top Consultants</h3>
                <div class="space-y-3">
                    @forelse($consultantPerformance as $performance)
                        @if($performance->user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-semibold text-xs sm:text-sm flex-shrink-0">
                                        {{ $performance->user->initials ?? 'N/A' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-white text-xs sm:text-sm truncate">{{ $performance->user->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $performance->total_assigned }} assigned</p>
                                    </div>
                                </div>
                                <span class="text-xs sm:text-sm font-semibold text-green-600 ml-2">{{ $performance->completed_this_month }}</span>
                            </div>
                        @else
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">User data unavailable</p>
                        @endif
                    @empty
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 text-center py-4">No consultants available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<x-users.create-modal />
@endsection