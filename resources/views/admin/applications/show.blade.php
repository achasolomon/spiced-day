@extends('layouts.admin')

@section('title', 'Application Details - ' . $application->application_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.applications.index') }}" 
                   class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Application {{ $application->application_number }}
                </h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">
                Submitted {{ $application->created_at->diffForHumans() }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Status Badge -->
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium
                {{ $application->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                {{ $application->status === 'under_review' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                {{ $application->status_display }}
            </span>

            <!-- Action Buttons -->
            <div x-data="{ showActions: false }" class="relative">
                <button @click="showActions = !showActions" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                    Actions
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="showActions" 
                     @click.away="showActions = false"
                     x-cloak
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-10">
                    
                    @if(!$application->consultant_id)
                        <button onclick="openAssignModal()" 
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                            Assign Consultant
                        </button>
                    @else
                        <button onclick="openReassignModal()" 
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                            Reassign Consultant
                        </button>
                    @endif

                    @if(in_array($application->status, ['submitted', 'under_review', 'final_review']))
                        <form method="POST" action="{{ route('admin.applications.approve', $application) }}" 
                              onsubmit="return confirm('Are you sure you want to approve this application?')">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-600 dark:text-green-400">
                                Approve Application
                            </button>
                        </form>

                        <button onclick="openRejectModal()" 
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400">
                            Reject Application
                        </button>
                    @endif

                    <a href="{{ route('admin.applications.audit-log', $application) }}" 
                       class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                        View Audit Log
                    </a>

                    <button onclick="window.print()" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                        Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Progress</p>
            <p class="text-2xl font-bold text-purple-600">{{ $application->completion_percentage ?? 0 }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Documents</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $stats['approved_documents'] }}/{{ $stats['total_documents'] }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Pending Docs</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_documents'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Appointments</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_appointments'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Inspections</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_inspections'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Days Active</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ number_format($application->created_at->floatDiffInDays(now()), 2) }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content - Left Side (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Applicant Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Applicant Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Full Name</label>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $application->full_name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Email</label>
                        <p class="text-gray-900 dark:text-white">{{ $application->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Phone</label>
                        <p class="text-gray-900 dark:text-white">{{ $application->phone ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Date of Birth</label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $application->date_of_birth ? \Carbon\Carbon::parse($application->date_of_birth)->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Address</label>
                        <p class="text-gray-900 dark:text-white">{{ $application->full_address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
             <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Documents</h2>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.documents.application', $application) }}" 
                    class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400">
                        View All →
                    </a>
                @else
                    <a href="{{ route('applicant.documents.index', $application) }}" 
                    class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400">
                        View All →
                    </a>
                @endif
            </div>

                @if($missingDocuments)
                    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-2">
                            Missing Documents ({{ count($missingDocuments) }})
                        </p>
                        <ul class="text-sm text-yellow-700 dark:text-yellow-400 space-y-1">
                            @foreach($missingDocuments as $missing)
                                <li>{{ $missing->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-3">
                    @forelse($application->documents()->latest()->take(5)->get() as $document)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $document->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Uploaded {{ $document->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $document->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                {{ $document->status === 'uploaded' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                {{ $document->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                                {{ ucfirst($document->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">No documents uploaded yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Appointments Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Appointments</h2>
                    @if($application->consultant_id)
                        <a href="{{ route('consultant.appointments.create', ['application_id' => $application->id]) }}" 
                           class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400">
                            Schedule New →
                        </a>
                    @endif
                </div>

                @if($nextAppointment)
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-1">Next Appointment</p>
                        <p class="text-sm text-blue-700 dark:text-blue-400">
                            {{ $nextAppointment->title }} - {{ $nextAppointment->scheduled_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                @endif

                <div class="space-y-3">
                    @forelse($application->appointments()->latest('scheduled_at')->take(5)->get() as $appointment)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $appointment->scheduled_at->format('M d, Y \a\t g:i A') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                {{ $appointment->status === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}
                                {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">No appointments scheduled</p>
                    @endforelse
                </div>
            </div>

            <!-- Inspections Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Inspections</h2>
                </div>

                @if($latestInspection)
                    <div class="mb-4 p-4 border-l-4 {{ $latestInspection->overall_result === 'pass' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20' }} rounded">
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Latest Inspection</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ ucwords(str_replace('_', ' ', $latestInspection->type)) }} - 
                            {{ $latestInspection->conducted_at->format('M d, Y') }}
                        </p>
                        <p class="text-sm font-medium mt-2 {{ $latestInspection->overall_result === 'pass' ? 'text-green-600' : 'text-red-600' }}">
                            Result: {{ ucfirst($latestInspection->overall_result) }} (Score: {{ $latestInspection->overall_score }}%)
                        </p>
                    </div>
                @endif

                <div class="space-y-3">
                    @forelse($application->inspections()->latest('conducted_at')->take(5)->get() as $inspection)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ ucwords(str_replace('_', ' ', $inspection->type)) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $inspection->conducted_at->format('M d, Y') }} by {{ $inspection->consultant->name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $inspection->overall_result === 'pass' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $inspection->overall_result === 'fail' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $inspection->overall_result === 'conditional_pass' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $inspection->overall_result)) }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Score: {{ $inspection->overall_score }}%</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">No inspections conducted yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Activity Timeline</h2>
                
                <div class="space-y-4">
                    @forelse($timeline as $activity)
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                            <div class="flex-1 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $activity->created_at->format('M d, Y \a\t g:i A') }}
                                    @if($activity->user)
                                        by {{ $activity->user->name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">No activity yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar - Right Side (1/3) -->
        <div class="space-y-6">
            
            <!-- Assigned Consultant -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Assigned Consultant</h3>
                
                @if($application->consultant)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ $application->consultant->initials }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $application->consultant->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $application->consultant->email }}</p>
                        </div>
                    </div>
                    
                    @if($application->consultant->consultant)
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                                <span class="text-gray-900 dark:text-white">{{ $application->consultant->consultant->phone ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="text-green-600 dark:text-green-400">{{ ucfirst($application->consultant->consultant->employment_status) }}</span>
                            </div>
                        </div>
                    @endif
                    
                    <button onclick="openReassignModal()" 
                            class="w-full mt-4 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Reassign Consultant
                    </button>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No consultant assigned</p>
                        <button onclick="openAssignModal()" 
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                            Assign Consultant
                        </button>
                    </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Overall Progress</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $application->completion_percentage ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all" 
                                 style="width: {{ $application->completion_percentage ?? 0 }}%"></div>
                        </div>
                    </div>
                    
<div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Documents Approved</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $stats['approved_documents'] }}/{{ $stats['total_documents'] }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Pending Review</span>
                            <span class="font-medium text-orange-600">{{ $stats['pending_documents'] }}</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Upcoming Appointments</span>
                            <span class="font-medium text-blue-600">{{ $stats['upcoming_appointments'] }}</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Completed Appointments</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $stats['completed_appointments'] }}</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Inspections Passed</span>
                            <span class="font-medium text-green-600">
                                {{ $stats['passed_inspections'] }}/{{ $stats['total_inspections'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Status Flow -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Status Progress</h3>
                
                <div class="space-y-3">
                    @php
                        $statuses = [
                            'submitted' => 'Submitted',
                            'under_review' => 'Under Review',
                            'meet_and_greet_scheduled' => 'Meet & Greet Scheduled',
                            'meet_and_greet_completed' => 'Meet & Greet Completed',
                            'documents_pending' => 'Documents Pending',
                            'documents_submitted' => 'Documents Submitted',
                            'documents_approved' => 'Documents Approved',
                            'initial_inspection_scheduled' => 'Initial Inspection Scheduled',
                            'initial_inspection_completed' => 'Initial Inspection Completed',
                            'second_inspection_scheduled' => 'Second Inspection Scheduled',
                            'second_inspection_completed' => 'Second Inspection Completed',
                            'final_review' => 'Final Review',
                            'approved' => 'Approved',
                        ];
                        
                        $currentStatus = $application->status;
                        $currentIndex = array_search($currentStatus, array_keys($statuses));
                    @endphp
                    
                    @foreach($statuses as $statusKey => $statusLabel)
                        @php
                            $index = array_search($statusKey, array_keys($statuses));
                            $isCompleted = $index <= $currentIndex;
                            $isCurrent = $statusKey === $currentStatus;
                        @endphp
                        
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                {{ $isCompleted ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400' }}
                                {{ $isCurrent ? 'ring-4 ring-purple-200 dark:ring-purple-900' : '' }}">
                                @if($isCompleted && !$isCurrent)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <span class="text-xs font-semibold">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <span class="text-sm {{ $isCurrent ? 'font-bold text-purple-600 dark:text-purple-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Important Dates -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Important Dates</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Submitted</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $application->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    
                    @if($application->submitted_at)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Formally Submitted</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($application->submitted_at)->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                    @endif
                    
                    @if($nextAppointment)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Next Appointment</p>
                            <p class="font-medium text-blue-600">
                                {{ $nextAppointment->scheduled_at->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                    @endif
                    
                    @if($application->approved_at)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Approved</p>
                            <p class="font-medium text-green-600">
                                {{ \Carbon\Carbon::parse($application->approved_at)->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                    @endif
                    
                    @if($application->rejected_at)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Rejected</p>
                            <p class="font-medium text-red-600">
                                {{ \Carbon\Carbon::parse($application->rejected_at)->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                    @endif
                    
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Last Updated</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $application->updated_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Applicant -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Contact Applicant</h3>
                
                <div class="space-y-3">
                    <a href="mailto:{{ $application->email }}" 
                       class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm text-gray-900 dark:text-white">Send Email</span>
                    </a>
                    
                    @if($application->phone)
                        <a href="tel:{{ $application->phone }}" 
                           class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm text-gray-900 dark:text-white">Call {{ $application->phone }}</span>
                        </a>
                    @endif
                    
                    <button onclick="openSendNotificationModal()" 
                            class="w-full flex items-center justify-center gap-3 p-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="text-sm font-medium">Send Notification</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Consultant Modal -->
<div x-data="{ showAssignModal: false }" 
     @keydown.escape.window="showAssignModal = false">
    <div x-show="showAssignModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showAssignModal = false"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Assign Consultant</h3>
                
                <form method="POST" action="{{ route('admin.applications.assign-consultant', $application) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Consultant
                        </label>
                        <select name="consultant_id" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Choose a consultant...</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}">
                                    {{ $consultant->name }} 
                                    @if($consultant->consultant)
                                        ({{ $consultant->assignedApplications()->count() }} active applications)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Add any notes for the consultant..."></textarea>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" @click="showAssignModal = false"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
                            Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Application Modal -->
<div x-data="{ showRejectModal: false }" 
     @keydown.escape.window="showRejectModal = false">
    <div x-show="showRejectModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showRejectModal = false"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Reject Application</h3>
                
                <form method="POST" action="{{ route('admin.applications.reject', $application) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason for Rejection *
                        </label>
                        <textarea name="rejection_reason" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Provide a detailed reason for rejecting this application..."></textarea>
                    </div>
                    
                    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-300">
                            <strong>Warning:</strong> This action will notify the applicant and they will not be able to proceed with this application.
                        </p>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" @click="showRejectModal = false"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                            Reject Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Send Notification Modal -->
<div x-data="{ showNotificationModal: false }" 
     @keydown.escape.window="showNotificationModal = false">
    <div x-show="showNotificationModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showNotificationModal = false"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Send Notification</h3>
                
                <form method="POST" action="{{ route('notifications.send', $application->user) }}">
                    @csrf
                    <input type="hidden" name="application_id" value="{{ $application->id }}">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Title *
                        </label>
                        <input type="text" name="title" required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                               placeholder="Notification title">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Message *
                        </label>
                        <textarea name="message" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Your message to the applicant..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Priority
                        </label>
                        <select name="priority"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                            <option value="low">Low</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" @click="showNotificationModal = false"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
                            Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openAssignModal() {
    Alpine.store('app').showAssignModal = true;
    document.querySelector('[x-data*="showAssignModal"]').__x.$data.showAssignModal = true;
}

function openReassignModal() {
    document.querySelector('[x-data*="showAssignModal"]').__x.$data.showAssignModal = true;
}

function openRejectModal() {
    document.querySelector('[x-data*="showRejectModal"]').__x.$data.showRejectModal = true;
}

function openSendNotificationModal() {
    document.querySelector('[x-data*="showNotificationModal"]').__x.$data.showNotificationModal = true;
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection