@extends('layouts.dashboard')

@section('title', 'Appointment Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('applicant.appointments.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Appointment Details</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ ucfirst(str_replace('_', ' ', $appointment->type)) }}</p>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Status</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                    {{ $appointment->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                    {{ $appointment->status == 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                    {{ $appointment->status == 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                    {{ $appointment->status == 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            @if($appointment->status == 'scheduled' && !$appointment->applicant_confirmed)
                <form action="{{ route('applicant.appointments.confirm', $appointment) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        Confirm Appointment
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Appointment Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date & Time</label>
                <p class="text-gray-900 dark:text-white"><x-timezone-date :date="$appointment->scheduled_at" format="l, F j, Y" /></p>
                <p class="text-gray-900 dark:text-white font-semibold"><x-timezone-date :date="$appointment->scheduled_at" format="g:i A" /></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration</label>
                <p class="text-gray-900 dark:text-white">{{ $appointment->duration }} minutes</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Consultant</label>
                <p class="text-gray-900 dark:text-white">{{ $appointment->consultant->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location Type</label>
                <p class="text-gray-900 dark:text-white">{{ ucfirst($appointment->location_type) }}</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                <p class="text-gray-900 dark:text-white">{{ $appointment->location_address }}</p>
            </div>

            @if($appointment->description)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <p class="text-gray-900 dark:text-white">{{ $appointment->description }}</p>
            </div>
            @endif

            @if($appointment->preparation_notes)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preparation Notes</label>
                <p class="text-gray-900 dark:text-white">{{ $appointment->preparation_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Application Link -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Related Application</h3>
        <a href="{{ route('applicant.applications.show', $appointment->application) }}" class="text-orange-600 hover:text-orange-700 font-medium">
            View Application #{{ $appointment->application->application_number }}
        </a>
    </div>
</div>
@endsection