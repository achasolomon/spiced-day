@extends('layouts.consultant')

@section('title', 'Appointment Details')

@section('content')
@php
    $appointmentHasStarted = $appointment->scheduled_at && $appointment->scheduled_at->lte(now());
    $canMarkAsComplete = $appointment->status === 'confirmed' && $appointmentHasStarted;
@endphp
<div class="space-y-4 md:space-y-6" x-data="{ showCompleteModal: false, showRescheduleModal: false }">
        <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('consultant.calendar') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $appointment->title }}</h1>
            </div>
            <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">
                Appointment #{{ $appointment->id }} • {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
            </p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            @if($appointment->status === 'scheduled' || $appointment->status === 'confirmed')
                <a href="{{ route('consultant.appointments.edit', $appointment) }}" 
                   class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm">
                    Edit
                </a>
            @endif
            
            @if(!$appointment->consultant_confirmed && in_array($appointment->status, ['scheduled']))
                <form action="{{ route('consultant.appointments.confirm', $appointment) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm">
                        Confirm
                    </button>
                </form>
            @endif
            
            @if($canMarkAsComplete)
                <form action="{{ route('consultant.appointments.complete', $appointment) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                        Mark Complete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Status Badge -->
    <div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
            {{ $appointment->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
            {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
            {{ $appointment->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
            {{ $appointment->status === 'rescheduled' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
            {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
            {{ ucfirst($appointment->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 md:space-y-6">
            <!-- Appointment Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-4">Appointment Details</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Date & Time</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $appointment->scheduled_at->format('l, F j, Y') }}
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ $appointment->scheduled_at->format('g:i A') }} - {{ $appointment->ends_at->format('g:i A') }}
                                <span class="text-sm">({{ $appointment->duration }} minutes)</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $appointment->location_address }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($appointment->location_type) }}</p>
                            @if($appointment->location_notes)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $appointment->location_notes }}</p>
                            @endif
                        </div>
                    </div>

                    @if($appointment->description)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                                <p class="text-gray-900 dark:text-white">{{ $appointment->description }}</p>
                            </div>
                        </div>
                    @endif

                    @if($appointment->preparation_notes)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Preparation Notes</p>
                                <p class="text-gray-900 dark:text-white">{{ $appointment->preparation_notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Confirmation Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-4">Confirmation Status</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex-shrink-0">
                            @if($appointment->consultant_confirmed)
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Consultant</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $appointment->consultant_confirmed ? 'Confirmed' : 'Pending' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex-shrink-0">
                            @if($appointment->applicant_confirmed)
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Applicant</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $appointment->applicant_confirmed ? 'Confirmed' : 'Pending' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($appointment->completion_notes || $appointment->outcome)
                <!-- Completion Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-4">Completion Details</h2>
                    
                    @if($appointment->outcome)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Outcome</p>
                            <p class="text-gray-900 dark:text-white">{{ $appointment->outcome }}</p>
                        </div>
                    @endif

                    @if($appointment->result)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Result</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                {{ $appointment->result === 'pass' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                {{ $appointment->result === 'fail' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                {{ $appointment->result === 'conditional' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}">
                                {{ ucfirst($appointment->result) }}
                            </span>
                        </div>
                    @endif

                    @if($appointment->completion_notes)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                            <p class="text-gray-900 dark:text-white">{{ $appointment->completion_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 md:space-y-6">
            <!-- Applicant Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Applicant</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                       <span class="text-orange-600 dark:text-orange-400 font-semibold text-lg">
                            {{ substr($appointment->applicant?->name ?? $appointment->application->educator_first_name, 0, 1) }}
                        </span>
                    </div>
                   <div>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $appointment->applicant?->name ?? $appointment->application->educator_first_name . ' ' . $appointment->application->educator_last_name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $appointment->applicant?->email ?? $appointment->application->email }}
                    </p>
                </div>
                </div>
                @if($appointment->applicant?->phone ?? $appointment->application->phone)
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Phone:</span> {{ $appointment->applicant?->phone ?? $appointment->application->phone }}
                    </p>
                @endif
            </div>

           <!-- Application Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Related Application</h3>
            <div class="space-y-2">
                <p class="text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Application #:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $appointment->application->application_number }}</span>
                </p>
                <p class="text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Status:</span>
                    @php
                        $statusEnum = \App\Enums\ApplicationStatus::from($appointment->application->status);
                        $statusColor = $statusEnum->color();
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900 dark:text-{{ $statusColor }}-200">
                        {{ $statusEnum->label() }}
                    </span>
                </p>
                <a href="{{ route('consultant.applications.show', $appointment->application) }}" 
                   class="inline-block mt-3 text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 text-sm font-medium">
                    View Application →
                </a>
            </div>
        </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('consultant.calendar') }}" class="block w-full px-4 py-2 text-center bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
                        Back to Calendar
                    </a>
                    @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                        <a href="{{ route('consultant.appointments.edit', $appointment) }}" class="block w-full px-4 py-2 text-center bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
                            Edit Appointment
                        </a>
                    @endif
                    
                    @if(in_array($appointment->status, ['scheduled', 'rescheduled', 'confirmed']))
                        <button
                            type="button"
                            @click="showRescheduleModal = true; document.getElementById('reschedule_user_timezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;"
                            class="w-full px-4 py-2 text-center bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors text-sm">
                            Reschedule Appointment
                        </button>
                    @endif


                        @if($canMarkAsComplete)
                            <button 
                                type="button" 
                                @click="showCompleteModal = true"
                                class="w-full px-4 py-2 text-center bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm">
                                Mark as Completed
                            </button>
                        @endif
                </div>
            </div>
        </div>
    </div>

  
    <!-- Mark as Completed Confirmation Modal -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div 
        x-show="showCompleteModal"
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
    <!-- Backdrop -->
    <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="showCompleteModal = false"
    ></div>

    <!-- Modal -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div 
            class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
                            Mark Appointment as Completed
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Are you sure you want to mark this {{ ucfirst(str_replace('_', ' ', $appointment->type)) }} appointment as completed? This action will update the application status and cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <form action="{{ route('consultant.appointments.complete', $appointment) }}" method="POST" class="w-full sm:ml-3 sm:w-auto">
                    @csrf
                    <button 
                        type="submit"
                        class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto">
                        Yes, Mark as Completed
                    </button>
                </form>
                <button 
                    type="button"
                    @click="showCompleteModal = false"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

  <!-- reschedule Modal -->
    
   <!-- Reschedule Modal -->
<div
    x-show="showRescheduleModal"
    x-cloak
    class="fixed inset-0 z-[9999] overflow-y-auto"
    style="display: none;"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         @click="showRescheduleModal = false"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.stop
        >
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Reschedule Appointment
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    This will only update the appointment date and time.
                </p>
            </div>

            <form
                action="{{ route('consultant.appointments.reschedule', $appointment) }}"
                method="POST"
                class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_timezone" id="reschedule_user_timezone">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        New Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="datetime-local"
                        name="scheduled_at"
                        required
                        min="{{ now(config('app.timezone'))->format('Y-m-d\TH:i') }}"
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               focus:ring-2 focus:ring-orange-500 focus:border-orange-500 
                               placeholder-gray-400 dark:placeholder-gray-500
                               transition-colors">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        Time is in {{ config('app.timezone') === 'America/Toronto' ? 'Eastern Time' : config('app.timezone') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason (optional)
                    </label>
                    <textarea
                        name="reschedule_reason"
                        rows="3"
                        placeholder="Why is this appointment being rescheduled?"
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               focus:ring-2 focus:ring-orange-500 focus:border-orange-500 
                               placeholder-gray-400 dark:placeholder-gray-500
                               resize-none transition-colors"></textarea>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        This reason will be shared with the applicant
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        @click="showRescheduleModal = false"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 dark:bg-gray-700 
                               text-gray-700 dark:text-gray-300 
                               hover:bg-gray-300 dark:hover:bg-gray-600 
                               transition-colors">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-orange-600 
                               hover:bg-orange-700 text-white 
                               transition-colors">
                        Reschedule Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
@endsection