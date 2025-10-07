@extends('layouts.consultant')

@section('title', 'Calendar')

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Calendar</h1>
            <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 mt-1">View and manage your appointments</p>
        </div>
            <button 
                    @click="$dispatch('open-appointment-modal', { 
                        applicationId: null, 
                        applicantId: null, 
                        applicantAddress: '' 
                    })"
                    class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition">
            Schedule Appointment
        </button>
    </div>

    <!-- Calendar Navigation -->
    <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h2 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white">{{ now()->format('F Y') }}</h2>
            <div class="flex items-center gap-1 md:gap-2">
                <button class="p-1.5 md:p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <button class="px-3 md:px-4 py-1.5 md:py-2 bg-orange-600 text-white rounded-lg text-xs md:text-sm font-medium">Today</button>
                <button class="p-1.5 md:p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Week Days -->
        <div class="grid grid-cols-7 gap-1 md:gap-2 mb-2">
            <div class="text-center text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400 py-1 md:py-2">Sun</div>
            <div class="text-center text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400 py-1 md:py-2">Mon</div>
            <div class="text-center text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400 py-1 md:py-2">Tue</div>
            <div class="text-center text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400 py-1 md:py-2">Wed</div>
            <div class="text-center text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400 py-1 md:py-2">Thu</div>
            <div class="text-center text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400 py-1 md:py-2">Fri</div>
            <div class="text-center text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400 py-1 md:py-2">Sat</div>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1 md:gap-2">
            @php
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $startDay = $startOfMonth->copy()->startOfWeek();
                $endDay = $endOfMonth->copy()->endOfWeek();
                $currentDay = $startDay->copy();
            @endphp

            @while($currentDay <= $endDay)
                @php
                    $isCurrentMonth = $currentDay->month === now()->month;
                    $isToday = $currentDay->isToday();
                    $dayAppointments = $weekAppointments->filter(function($apt) use ($currentDay) {
                        return $apt->scheduled_at->isSameDay($currentDay);
                    });
                @endphp

                <div class="min-h-20 md:min-h-32 p-1 md:p-2 border border-gray-200 dark:border-gray-700 rounded-md md:rounded-lg {{ $isToday ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-300 dark:border-orange-600' : ($isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900') }}">
                    <div class="flex items-center justify-between mb-1 md:mb-2">
                        <span class="text-xs md:text-sm font-semibold {{ $isToday ? 'text-orange-600' : ($isCurrentMonth ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-600') }}">
                            {{ $currentDay->format('j') }}
                        </span>
                        @if($dayAppointments->count() > 0)
                            <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-1.5 md:px-2 py-0.5 rounded-full">
                                {{ $dayAppointments->count() }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-1 hidden md:block">
                        @foreach($dayAppointments->take(2) as $appointment)
                            <a href="{{ route('consultant.appointments.show', $appointment) }}" 
                               class="block p-1.5 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded text-xs transition-colors">
                                <p class="font-semibold text-blue-900 dark:text-blue-200 truncate">{{ $appointment->scheduled_at->format('g:i A') }}</p>
                                <p class="text-blue-700 dark:text-blue-300 truncate">{{ $appointment->applicant->name }}</p>
                            </a>
                        @endforeach

                        @if($dayAppointments->count() > 2)
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">+{{ $dayAppointments->count() - 2 }} more</p>
                        @endif
                    </div>
                </div>

                @php $currentDay->addDay(); @endphp
            @endwhile
        </div>
    </div>

    <!-- Upcoming Appointments List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">Upcoming Appointments</h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="space-y-3 md:space-y-4">
                @forelse($weekAppointments->where('scheduled_at', '>', now())->sortBy('scheduled_at')->take(5) as $appointment)
                    <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4 p-3 md:p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                        <div class="flex-shrink-0 w-14 md:w-16 text-center">
                            <p class="text-xl md:text-2xl font-bold text-orange-600">{{ $appointment->scheduled_at->format('j') }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $appointment->scheduled_at->format('M') }}</p>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm md:text-base">{{ $appointment->title }}</h3>
                                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">{{ $appointment->applicant->name }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 md:px-3 py-1 rounded-full text-xs font-semibold self-start
                                    {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $appointment->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-xs md:text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $appointment->scheduled_at->format('g:i A') }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="truncate">{{ Str::limit($appointment->location_address, 30) }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('consultant.appointments.show', $appointment) }}" class="w-full sm:w-auto sm:flex-shrink-0 px-3 md:px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs md:text-sm font-medium transition-colors text-center">
                            View
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-10 h-10 md:w-12 md:h-12 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm md:text-base text-gray-500 dark:text-gray-400">No upcoming appointments</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
<x-appointments.schedule-modal :applications="$applications" />
@endsection