@extends('layouts.admin')

@section('title', 'Appointment Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.appointments.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $appointment->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ ucwords(str_replace('_', ' ', $appointment->type)) }} - 
                    <x-timezone-date :date="$appointment->scheduled_at" format="M d, Y \a\t g:i A" />
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <!-- Status Badge -->
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                {{ $appointment->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                {{ $appointment->status === 'completed' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300' : '' }}
                {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                {{ $appointment->status === 'no_show' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}">
                {{ ucfirst($appointment->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Appointment Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Appointment Details</h2>
                
                <div class="space-y-4">
                    <!-- Date & Time -->
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Date & Time</p>
                            <p class="text-base text-gray-900 dark:text-white">
                                <x-timezone-date :date="$appointment->scheduled_at" format="l, F j, Y" />
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <x-timezone-date :date="$appointment->scheduled_at" format="g:i A" /> - <x-timezone-date :date="$appointment->ends_at" format="g:i A" />
                                ({{ $appointment->duration }} minutes)
                            </p>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Location</p>
                            <p class="text-base text-gray-900 dark:text-white">
                                {{ ucfirst($appointment->location_type) }}
                            </p>
                            @if($appointment->location_address)
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $appointment->location_address }}
                                </p>
                            @endif
                            @if($appointment->location_notes)
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    {{ $appointment->location_notes }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    @if($appointment->description)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Description</p>
                                <p class="text-base text-gray-900 dark:text-white">{{ $appointment->description }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Preparation Notes -->
                    @if($appointment->preparation_notes)
                        <div class="flex items-start gap-3 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Preparation Notes</p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">{{ $appointment->preparation_notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Completion Notes -->
                    @if($appointment->status === 'completed' && $appointment->completion_notes)
                        <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">Completion Notes</p>
                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">{{ $appointment->completion_notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Internal Notes (Admin Only) -->
                    @if($appointment->internal_notes)
                        <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Internal Notes (Admin Only)</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $appointment->internal_notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Outcome & Result (If completed) -->
                    @if($appointment->status === 'completed')
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Appointment Outcome</h3>
                            <div class="grid grid-cols-2 gap-4">
                                @if($appointment->result)
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Result</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                            {{ $appointment->result === 'pass' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                            {{ $appointment->result === 'fail' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                            {{ $appointment->result === 'conditional' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                            {{ $appointment->result === 'pending' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}">
                                            {{ ucfirst($appointment->result) }}
                                        </span>
                                    </div>
                                @endif
                                @if($appointment->outcome)
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Outcome</p>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $appointment->outcome }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Confirmation Status -->
                @if($appointment->status !== 'cancelled')
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Confirmation Status</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center gap-2">
                                @if($appointment->consultant_confirmed)
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <span class="text-sm text-gray-700 dark:text-gray-300">Consultant Confirmed</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($appointment->applicant_confirmed)
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <span class="text-sm text-gray-700 dark:text-gray-300">Applicant Confirmed</span>
                            </div>
                        </div>
                        @if($appointment->confirmed_at)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Last confirmed: {{ $appointment->confirmed_at->format('M d, Y \a\t g:i A') }}
                                @if($appointment->confirmation_method)
                                    via {{ ucfirst($appointment->confirmation_method) }}
                                @endif
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Inspection Details (if exists) -->
            @if($appointment->inspection)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Inspection Report</h2>
                        <a href="{{ route('admin.inspections.show', $appointment->inspection) }}" 
                           class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400">
                            View Full Report →
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 mt-1">
                                {{ ucfirst($appointment->inspection->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Overall Score</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $appointment->inspection->overall_score ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Application Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Application</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Application Number</p>
                        <a href="{{ route('admin.applications.show', $appointment->application) }}" 
                           class="text-base font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400">
                            #{{ $appointment->application->application_number }}
                        </a>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ ucfirst(str_replace('_', ' ', $appointment->application->status)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Submitted</p>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $appointment->application->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Consultant Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Consultant</h3>
                
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white text-lg font-bold">
                        {{ $appointment->consultant->initials }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->consultant->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $appointment->consultant->email }}</p>
                    </div>
                </div>

                @if($appointment->consultant->phone)
                    <a href="tel:{{ $appointment->consultant->phone }}" 
                       class="flex items-center gap-2 text-sm text-orange-600 hover:text-orange-700 dark:text-orange-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ $appointment->consultant->phone }}
                    </a>
                @endif
            </div>

            <!-- Applicant Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Applicant</h3>
                
                <div class="flex items-center gap-3 mb-3">
                   <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                        {{ $appointment->applicant?->initials ?? substr($appointment->application->educator_first_name, 0, 1) . substr($appointment->application->educator_last_name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $appointment->applicant?->name ?? $appointment->application->educator_first_name . ' ' . $appointment->application->educator_last_name }}
                        </p>
                    <div>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $appointment->applicant?->name ?? $appointment->application->educator_first_name . ' ' . $appointment->application->educator_last_name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $appointment->applicant?->email ?? $appointment->application->email }}
                    </p>
                    </div>
                </div>

                @if($appointment->applicant->phone ?? $appointment->application->phone)
                    <a href="tel:{{ $appointment->applicant->phone ?? $appointment->application->phone }}" 
                       class="flex items-center gap-2 text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ $appointment->applicant->phone ?? $appointment->application->phone }}
                    </a>
                @endif
            </div>

            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Timeline</h3>
                
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-gray-400 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Created</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $appointment->created_at->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                    </div>

                    @if($appointment->confirmed_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Confirmed</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $appointment->confirmed_at->format('M d, Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($appointment->started_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Started</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $appointment->started_at->format('M d, Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($appointment->completed_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Completed</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $appointment->completed_at->format('M d, Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($appointment->status === 'cancelled' && $appointment->cancelled_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Cancelled</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $appointment->cancelled_at->format('M d, Y \a\t g:i A') }}
                                </p>
                                @if($appointment->cancellation_reason)
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        Reason: {{ $appointment->cancellation_reason }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.applications.show', $appointment->application) }}" 
                       class="block w-full px-4 py-2 text-center bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        View Application
                    </a>
                    <a href="{{ route('admin.applications.audit-log', $appointment->application) }}" 
                       class="block w-full px-4 py-2 text-center bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        View Audit Log
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection