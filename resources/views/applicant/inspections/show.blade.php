@extends('layouts.dashboard')

@section('title', 'Inspection Report')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('applicant.applications.show', $inspection->application) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Inspection Report</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $inspection->type)) }} conducted on {{ $inspection->conducted_at->format('F j, Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Report
            </button>
            <a href="{{ route('applicant.inspections.download', $inspection) }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download PDF
            </a>
        </div>
    </div>

    <!-- Overall Result Card -->
    <div class="bg-gradient-to-br {{ $inspection->overall_result === 'pass' ? 'from-green-500 to-green-600' : '' }}
                                    {{ $inspection->overall_result === 'fail' ? 'from-red-500 to-red-600' : '' }}
                                    {{ $inspection->overall_result === 'conditional_pass' ? 'from-yellow-500 to-yellow-600' : '' }}
                                    {{ $inspection->overall_result === 'incomplete' ? 'from-gray-500 to-gray-600' : '' }} rounded-xl p-6 text-white shadow-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-white/80 text-sm mb-1">Overall Result</p>
                <p class="text-3xl font-bold">{{ ucwords(str_replace('_', ' ', $inspection->overall_result)) }}</p>
            </div>
            <div>
                <p class="text-white/80 text-sm mb-1">Overall Score</p>
                <p class="text-3xl font-bold">{{ number_format($inspection->overall_score, 1) }}%</p>
            </div>
            <div>
                <p class="text-white/80 text-sm mb-1">Items Passed</p>
                <p class="text-3xl font-bold">{{ $inspection->items_passed }} / {{ $inspection->items_checked }}</p>
            </div>
            <div>
                <p class="text-white/80 text-sm mb-1">Items Failed</p>
                <p class="text-3xl font-bold">{{ $inspection->items_failed }}</p>
            </div>
        </div>
    </div>

    <!-- Important Notice for Failed/Conditional -->
    @if($inspection->overall_result === 'fail' || $inspection->overall_result === 'conditional_pass')
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-300">
                    {{ $inspection->overall_result === 'fail' ? 'Action Required' : 'Conditional Pass - Improvements Needed' }}
                </h3>
                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                    <p>Please review the failed items below and make necessary corrections. Your consultant will schedule a follow-up inspection to verify improvements.</p>
                    @if($inspection->follow_up_required_by)
                    <p class="mt-2 font-semibold">Follow-up required by: {{ $inspection->follow_up_required_by->format('F j, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Inspection Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Inspection Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Inspection Type</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $inspection->type)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Inspection Number</p>
                        <p class="font-medium text-gray-900 dark:text-white font-mono">{{ $inspection->inspection_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Conducted Date</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->conducted_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Duration</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->duration ?? 'N/A' }} minutes</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Consultant</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->consultant->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <p class="font-medium {{ $inspection->is_final ? 'text-green-600' : 'text-orange-600' }}">
                            {{ $inspection->is_final ? 'Finalized' : 'Pending Finalization' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Checklist Results by Category -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Inspection Checklist Results</h2>
                
                @php
                    $checklistResults = is_array($inspection->checklist_results) ? $inspection->checklist_results : json_decode($inspection->checklist_results, true);
                    // Group results by category if available
                    $groupedResults = [];
                    foreach($checklistResults as $itemId => $result) {
                        $category = $result['category'] ?? 'General';
                        if (!isset($groupedResults[$category])) {
                            $groupedResults[$category] = [];
                        }
                        $groupedResults[$category][$itemId] = $result;
                    }
                @endphp

                @if($checklistResults)
                    @foreach($groupedResults as $category => $items)
                    <div class="mb-6 last:mb-0">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                            {{ ucwords(str_replace('_', ' ', $category)) }}
                            <span class="text-sm font-normal text-gray-500">({{ count($items) }} items)</span>
                        </h3>
                        <div class="space-y-2">
                            @foreach($items as $itemId => $result)
                            <div class="flex items-start justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg 
                                        {{ $result['status'] === 'fail' ? 'border-l-4 border-red-500' : '' }}">
                                <div class="flex items-start gap-3 flex-1">
                                    @if($result['status'] === 'pass')
                                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @elseif($result['status'] === 'fail')
                                        <div class="w-6 h-6 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">N/A</span>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $result['title'] ?? "Item $itemId" }}
                                        </p>
                                        @if(isset($result['notes']) && $result['notes'])
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 italic">
                                                <strong>Note:</strong> {{ $result['notes'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs font-semibold px-2 py-1 rounded
                                            {{ $result['status'] === 'pass' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                            {{ $result['status'] === 'fail' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                            {{ $result['status'] === 'n/a' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                    {{ strtoupper($result['status']) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No checklist data available</p>
                @endif
            </div>

            <!-- Observations & Recommendations -->
            @if($inspection->observations || $inspection->recommendations_text)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Inspector's Observations</h2>
                    
                    @if($inspection->observations)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">General Observations</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $inspection->observations }}</p>
                        </div>
                    @endif

                    @if($inspection->recommendations_text)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Recommendations</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $inspection->recommendations_text }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Failed Items -->
            @if($inspection->items_failed > 0 && $inspection->failed_items)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-red-900 dark:text-red-300 mb-2">Items Requiring Immediate Attention</h2>
                    <p class="text-sm text-red-800 dark:text-red-400 mb-4">Please address these items before the follow-up inspection:</p>
                    <ul class="space-y-2">
                        @php
                            $failedItems = is_array($inspection->failed_items) ? $inspection->failed_items : json_decode($inspection->failed_items, true);
                        @endphp
                        @foreach($failedItems as $itemId)
                            @if(isset($checklistResults[$itemId]))
                                <li class="flex items-start gap-2 text-red-800 dark:text-red-300 p-3 bg-white dark:bg-red-900/10 rounded">
                                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium">{{ $checklistResults[$itemId]['title'] ?? "Item $itemId" }}</p>
                                        @if(isset($checklistResults[$itemId]['notes']) && $checklistResults[$itemId]['notes'])
                                            <p class="text-sm mt-1">{{ $checklistResults[$itemId]['notes'] }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Right Column - Summary & Actions -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Summary</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Items Checked</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $inspection->items_checked }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Passed</span>
                        <span class="font-bold text-green-600">{{ $inspection->items_passed }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Failed</span>
                        <span class="font-bold text-red-600">{{ $inspection->items_failed }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Not Applicable</span>
                        <span class="font-bold text-gray-600">{{ $inspection->items_not_applicable }}</span>
                    </div>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Pass Rate</span>
                            <span class="font-bold text-gray-900 dark:text-white">
                                {{ $inspection->pass_rate }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            @if($inspection->requires_reinspection)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
                    <h3 class="font-bold text-yellow-900 dark:text-yellow-300 mb-2">Next Steps</h3>
                    <p class="text-sm text-yellow-800 dark:text-yellow-400 mb-3">A follow-up inspection is required. Please:</p>
                    <ol class="text-sm text-yellow-800 dark:text-yellow-400 space-y-2 list-decimal list-inside">
                        <li>Review all failed items above</li>
                        <li>Make necessary corrections</li>
                        <li>Contact your consultant when ready</li>
                        <li>Schedule follow-up inspection</li>
                    </ol>
                </div>
            @endif

            <!-- Contact Consultant -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Your Consultant</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->consultant->name }}</p>
                    </div>
                    @if($inspection->consultant->email)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <a href="mailto:{{ $inspection->consultant->email }}" class="text-orange-600 hover:text-orange-700">
                            {{ $inspection->consultant->email }}
                        </a>
                    </div>
                    @endif
                    @if($inspection->consultant->phone)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                        <a href="tel:{{ $inspection->consultant->phone }}" class="text-orange-600 hover:text-orange-700">
                            {{ $inspection->consultant->phone }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('applicant.applications.show', $inspection->application) }}" 
                       class="block w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-center font-medium transition-colors">
                        Back to Application
                    </a>
                    <button onclick="window.print()" class="block w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-center font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection