<!-- resources/views/dashboards/applicant.blade.php -->
@extends('layouts.app')

@section('title', 'Applicant Dashboard - SPICE\'d Dayhome')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Welcome back, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Track your dayhome application progress and manage your journey.
                </p>
            </div>
            
            @if(!$activeApplication)
                <a href="{{ route('applications.create') }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Start New Application
                </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card rounded-2xl p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_applications'] }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Applications</p>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_applications'] }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">In Progress</p>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['approved_applications'] }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Approved</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            @if($activeApplication)
                <!-- Current Application Progress -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Current Application</h2>
                        <span class="status-{{ strtolower(str_replace(' ', '-', $activeApplication->status)) }}">
                            {{ ucwords(str_replace('_', ' ', $activeApplication->status)) }}
                        </span>
                    </div>

                    <!-- Application Info -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $activeApplication->business_name }}</h3>
                            <span class="text-sm text-gray-500">{{ $activeApplication->application_number }}</span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="progress-bar mb-2">
                            <div class="progress-fill" style="width: {{ $activeApplication->completion_percentage }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activeApplication->completion_percentage }}% Complete</p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('applications.show', $activeApplication) }}" class="btn-primary">
                            View Details
                        </a>
                        @if($activeApplication->canBeEdited())
                            <a href="{{ route('applications.edit', $activeApplication) }}" class="btn-secondary">
                                Edit Application
                            </a>
                        @endif
                        <a href="{{ route('documents.create', ['application_id' => $activeApplication->id]) }}" class="btn-secondary">
                            Upload Documents
                        </a>
                    </div>
                </div>
            @else
                <!-- Getting Started -->
                <div class="glass-card rounded-2xl p-8 text-center">
                    <div class="w-20 h-20 mx-auto rainbow-gradient rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Ready to Start Your Dayhome?</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Begin your journey to becoming a licensed dayhome provider. Our streamlined process will guide you through every step.
                    </p>
                    <a href="{{ route('applications.create') }}" class="btn-primary text-lg">
                        Start New Application
                    </a>
                </div>
            @endif

            <!-- Upcoming Appointments -->
            @if($upcomingAppointments->count() > 0)
                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Upcoming Appointments</h2>
                    <div class="space-y-4">
                        @foreach($upcomingAppointments as $appointment)
                            <div class="flex items-center justify-between p-4 bg-white/50 dark:bg-slate-800/50 rounded-xl">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $appointment->title }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $appointment->scheduled_at->format('M j, Y \a\t g:i A') }} with {{ $appointment->consultant->name }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('appointments.show', $appointment) }}" class="btn-secondary text-sm">
                                    View Details
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Documents -->
            @if($recentDocuments->count() > 0)
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Documents</h2>
                        <a href="{{ route('documents.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
                    </div>
                    <div class="space-y-3">
                        @foreach($recentDocuments as $document)
                            <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-slate-800/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $document->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $document->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="status-{{ $document->status }} text-xs">{{ ucfirst($document->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Notifications -->
            <div class="glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Recent Notifications</h3>
                    <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentNotifications as $notification)
                        <div class="p-3 {{ $notification->is_read ? 'bg-gray-50 dark:bg-slate-800/50' : 'bg-blue-50 dark:bg-blue-900/30' }} rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->title }}</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No recent notifications</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass-card rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if($activeApplication)
                        <a href="{{ route('documents.create', ['application_id' => $activeApplication->id]) }}" class="flex items-center p-3 bg-white/50 dark:bg-slate-800/50 rounded-lg hover:bg-white/70 dark:hover:bg-slate-700/50 transition-colors">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-sm font-medium">Upload Document</span>
                        </a>
                        <a href="{{ route('appointments.index') }}" class="flex items-center p-3 bg-white/50 dark:bg-slate-800/50 rounded-lg hover:bg-white/70 dark:hover:bg-slate-700/50 transition-colors">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium">View Calendar</span>
                        </a>
                    @endif
                    <a href="{{ route('help') }}" class="flex items-center p-3 bg-white/50 dark:bg-slate-800/50 rounded-lg hover:bg-white/70 dark:hover:bg-slate-700/50 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Get Help</span>
                    </a>
                </div>
            </div>

            <!-- Application Guide -->
            <div class="glass-card rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Application Process</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Submit Application</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 {{ $activeApplication && $activeApplication->current_stage == 'meet_and_greet' ? 'bg-blue-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs text-white font-bold">2</span>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Meet & Greet</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs text-white font-bold">3</span>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Initial Inspection</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs text-white font-bold">4</span>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Document Review</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs text-white font-bold">5</span>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Final Approval</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection