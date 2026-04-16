@extends('layouts.consultant')

@section('title', 'Select Application for Inspection')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('consultant.inspections.index') }}" 
           class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Select Application</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Choose an application to conduct inspection</p>
        </div>
    </div>

    @if($applications->isEmpty())
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 text-center">
        <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-300 mb-2">No Applications Available</h3>
        <p class="text-yellow-800 dark:text-yellow-400">There are currently no applications ready for inspection.</p>
    </div>
    @else
    <!-- Applications List -->
    <div class="space-y-4">
        @foreach($applications as $application)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:border-orange-500 dark:hover:border-orange-500 transition-all">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $application->educator_first_name }} {{ $application->educator_last_name }}
                        </h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            @if($application->status === 'meet_and_greet_completed') 
                                bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                            @elseif($application->status === 'initial_inspection_scheduled') 
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                            @elseif($application->status === 'documents_approved') 
                                bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                            @else 
                                bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300
                            @endif">
                            {{ ucwords(str_replace('_', ' ', $application->status)) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-3 text-sm">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            <span>{{ $application->application_number }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Applied: <x-date-display :date="$application->created_at" format="M d, Y" fallback="N/A" /></span>
                        </div>
                        @if($application->user)
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $application->user->email }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>{{ $application->user->phone ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>

                    @if($application->physical_address)
                    <div class="mt-3 flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $application->physical_address }}</span>
                    </div>
                    @endif
                </div>

                <div class="ml-6">
            @php
                $inspection = null;
                $label = null;
                $color = null;
        
                switch ($application->status) {
                    case 'initial_inspection_scheduled':
                        $inspection = 'initial_inspection';
                        $label = 'Initial Inspection';
                        $color = 'bg-blue-600 hover:bg-blue-700';
                        break;
        
                    case 'second_inspection_scheduled':
                        $inspection = 'second_inspection';
                        $label = 'Second Inspection';
                        $color = 'bg-yellow-600 hover:bg-yellow-700';
                        break;
        
                    case 'final_inspection_scheduled':
                        $inspection = 'final_inspection';
                        $label = 'Final Inspection';
                        $color = 'bg-green-600 hover:bg-green-700';
                        break;
        
                    case 'compliance_inspection_scheduled':
                        $inspection = 'compliance_inspection';
                        $label = 'Compliance Inspection';
                        $color = 'bg-purple-600 hover:bg-purple-700';
                        break;
                }
            @endphp
        
            @if($inspection)
                <a href="{{ route('consultant.inspections.create', [
                    'application_id' => $application->id,
                    'type' => $inspection
                ]) }}"
                   class="px-4 py-2 {{ $color }} text-white rounded-lg font-medium transition-colors text-sm text-center whitespace-nowrap">
                    {{ $label }}
                </a>
            @else
                <span class="text-sm text-gray-500 dark:text-gray-400 italic">
                    No inspection available
                </span>
            @endif
        </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection