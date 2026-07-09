@extends('layouts.dashboard')

@section('title', 'My Appointments')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Appointments</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    View and manage your scheduled appointments
                </p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Upcoming</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Confirmation</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pending_confirmation'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Upcoming Appointments --}}
    @if($upcomingAppointments->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Upcoming Appointments</h2>
        </div>

        <div class="p-4 sm:p-6 space-y-4">
            @foreach($upcomingAppointments as $appointment)
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-5 hover:shadow-md transition-shadow">
                    {{-- Mobile Layout --}}
                    <div class="flex flex-col sm:hidden space-y-4">
                        {{-- Header with Date and Status --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="flex-shrink-0 w-14 h-14 rounded-xl flex flex-col items-center justify-center text-white" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                    <span class="text-xs font-semibold">{{ $appointment->scheduled_at->format('M') }}</span>
                                    <span class="text-xl font-bold">{{ $appointment->scheduled_at->format('d') }}</span>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1 break-words">
                                        {{ $appointment->title }}
                                    </h3>
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Details --}}
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="flex-1 break-words">
                                    <div><x-timezone-date :date="$appointment->scheduled_at" format="l, F j, Y" /></div>
                                    <div><x-timezone-date :date="$appointment->scheduled_at" format="g:i A" /> • {{ $appointment->duration }} min</div>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="flex-1 break-words">{{ $appointment->location_address }}</span>
                            </div>
                            
                            @if($appointment->consultant)
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="flex-1">With {{ $appointment->consultant->name }}</span>
                                </div>
                            @endif
                        </div>

                        @if($appointment->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 break-words">
                                {{ $appointment->description }}
                            </p>
                        @endif

                        @if(!$appointment->applicant_confirmed && $appointment->status === 'scheduled')
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <p class="text-xs text-yellow-800 dark:text-yellow-200 font-medium">
                                    Please confirm your attendance for this appointment
                                </p>
                            </div>
                        @endif

                        {{-- Action Buttons (Mobile) --}}
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('applicant.appointments.show', $appointment) }}" 
                            class="w-full px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium text-sm transition-colors text-center">
                                View Details
                            </a>
                            
                            @if(!$appointment->applicant_confirmed && $appointment->status === 'scheduled')
                                <form action="{{ route('applicant.appointments.confirm', $appointment) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                                        Confirm Attendance
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Desktop Layout --}}
                    <div class="hidden sm:flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex-shrink-0 w-16 h-16 rounded-xl flex flex-col items-center justify-center text-white" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                <span class="text-xs font-semibold">{{ $appointment->scheduled_at->format('M') }}</span>
                                <span class="text-2xl font-bold">{{ $appointment->scheduled_at->format('d') }}</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $appointment->title }}
                                    </h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                                        {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                                
                                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><x-timezone-date :date="$appointment->scheduled_at" format="l, F j, Y \a\t g:i A" /></span>
                                        <span>•</span>
                                        <span>{{ $appointment->duration }} minutes</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>{{ $appointment->location_address }}</span>
                                    </div>
                                    
                                    @if($appointment->consultant)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span>With {{ $appointment->consultant->name }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if($appointment->description)
                                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $appointment->description }}
                                    </p>
                                @endif

                                @if(!$appointment->applicant_confirmed && $appointment->status === 'scheduled')
                                    <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">
                                            Please confirm your attendance for this appointment
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('applicant.appointments.show', $appointment) }}" 
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium text-sm transition-colors text-center whitespace-nowrap">
                                View Details
                            </a>
                            
                            @if(!$appointment->applicant_confirmed && $appointment->status === 'scheduled')
                                <form action="{{ route('applicant.appointments.confirm', $appointment) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors whitespace-nowrap">
                                        Confirm
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Past Appointments --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Past Appointments</h2>
        </div>

        @if($pastAppointments->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($pastAppointments as $appointment)
                    <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $appointment->title }}
                                    </h3>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $appointment->status === 'no_show' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <x-timezone-date :date="$appointment->scheduled_at" format="F j, Y \a\t g:i A" />
                                </p>
                            </div>
                            
                            <a href="{{ route('applicant.appointments.show', $appointment) }}" 
                               class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                                View Details →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($pastAppointments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $pastAppointments->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-gray-600 dark:text-gray-400">No past appointments</p>
            </div>
        @endif
    </div>
</div>
@endsection