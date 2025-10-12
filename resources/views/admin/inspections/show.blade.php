@extends('layouts.admin')

@section('title', 'Inspection Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.inspections.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Inspection Report</h1>
                @if($inspection->is_final)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Finalized
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">
                        Pending Finalization
                    </span>
                @endif
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
            @if(!$inspection->is_final)
                <form action="{{ route('admin.inspections.finalize', $inspection) }}" method="POST" onsubmit="return confirm('Are you sure you want to finalize this inspection? This action cannot be undone.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                        Finalize Inspection
                    </button>
                </form>
            @endif
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Application Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Application Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Applicant Name</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->application->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Application Number</p>
                        <p class="font-medium text-gray-900 dark:text-white font-mono">{{ $inspection->application->application_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->application->full_address }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->application->phone }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.applications.show', $inspection->application) }}" class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                        View Full Application →
                    </a>
                </div>
            </div>

            <!-- Inspection Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Inspection Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Inspection Type</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $inspection->type)) }}</p>
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
                        <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->consultant->name ?? 'N/A' }}</p>
                    </div>
                    @if($inspection->weather_conditions)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Weather Conditions</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->weather_conditions }}</p>
                        </div>
                    @endif
                    @if($inspection->temperature)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Temperature</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->temperature }}°C</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Checklist Results -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Inspection Checklist</h2>
                <div class="space-y-3">
                    @php
                        $checklistResults = is_array($inspection->checklist_results) ? $inspection->checklist_results : json_decode($inspection->checklist_results, true);
                    @endphp
                    @if($checklistResults)
                        @foreach($checklistResults as $itemId => $result)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    @if($result['status'] === 'pass')
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @elseif($result['status'] === 'fail')
                                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">N/A</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $result['item_name'] ?? "Item $itemId" }}</p>
                                        @if(isset($result['notes']) && $result['notes'])
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $result['notes'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-sm font-semibold {{ $result['status'] === 'pass' ? 'text-green-600' : '' }}
                                                                      {{ $result['status'] === 'fail' ? 'text-red-600' : '' }}
                                                                      {{ $result['status'] === 'n/a' ? 'text-gray-600' : '' }}">
                                    {{ strtoupper($result['status']) }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No checklist data available</p>
                    @endif
                </div>
            </div>

            <!-- Observations & Notes -->
            @if($inspection->observations || $inspection->consultant_notes || $inspection->environmental_factors)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Notes & Observations</h2>
                    
                    @if($inspection->environmental_factors)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Environmental Factors</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $inspection->environmental_factors }}</p>
                        </div>
                    @endif

                    @if($inspection->observations)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Observations</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $inspection->observations }}</p>
                        </div>
                    @endif

                    @if($inspection->consultant_notes)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Consultant Notes</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $inspection->consultant_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Failed Items -->
            @if($inspection->items_failed > 0 && $inspection->failed_items)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-red-900 dark:text-red-300 mb-4">Failed Items Requiring Attention</h2>
                    <ul class="space-y-2">
                        @php
                            $failedItems = is_array($inspection->failed_items) ? $inspection->failed_items : json_decode($inspection->failed_items, true);
                        @endphp
                        @foreach($failedItems as $itemId)
                            @if(isset($checklistResults[$itemId]))
                                <li class="flex items-start gap-2 text-red-800 dark:text-red-300">
                                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $checklistResults[$itemId]['item_name'] ?? "Item $itemId" }}</span>
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
                                {{ $inspection->items_checked > 0 ? number_format(($inspection->items_passed / $inspection->items_checked) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reinspection Required -->
            @if($inspection->requires_reinspection)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold text-yellow-900 dark:text-yellow-300 mb-1">Reinspection Required</h3>
                            <p class="text-sm text-yellow-800 dark:text-yellow-400">This inspection requires a follow-up due to failed items or conditional pass status.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Status Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Status</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Finalization Status</p>
                        @if($inspection->is_final)
                            <p class="font-medium text-purple-600">Finalized</p>
                            @if($inspection->approved_by)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    By {{ $inspection->approvedBy->name ?? 'N/A' }} on {{ $inspection->approved_at->format('M d, Y') }}
                                </p>
                            @endif
                        @else
                            <p class="font-medium text-orange-600">Pending Finalization</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Created</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $inspection->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Last Updated</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $inspection->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Actions</h2>
                <div class="space-y-3">
                    @if($inspection->appointment)
                        <a href="{{ route('admin.appointments.show', $inspection->appointment) }}" 
                           class="block w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-center font-medium transition-colors">
                            View Appointment
                        </a>
                    @endif
                    <a href="{{ route('admin.applications.show', $inspection->application) }}" 
                       class="block w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-center font-medium transition-colors">
                        View Application
                    </a>
                    @if(!$inspection->is_final)
                        <a href="{{ route('admin.inspections.edit', $inspection) }}" 
                           class="block w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-center font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Edit Inspection
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection