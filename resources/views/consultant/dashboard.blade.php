@extends('layouts.consultant')

@section('title', 'Consultant Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Here's what's happening with your applications today</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('consultant.applications.index') }}" class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                View All Applications
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Active Applications -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Active Applications</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['active_applications'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Today's Appointments</p>
                    <p class="text-4xl font-bold mt-2">{{ $todayAppointments->count() }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Documents -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Pending Documents</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['documents_to_review'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed This Month -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed This Month</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['completed_this_month'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Applications Kanban Board -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Applications Pipeline</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-data="kanbanBoard()">
                        <!-- Submitted Column -->
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Submitted</h3>
                                <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $assignedApplications->where('status', 'submitted')->count() }}
                                </span>
                            </div>
                            <div class="space-y-3">
                                @forelse($assignedApplications->where('status', 'submitted') as $app)
                                    <a href="{{ route('consultant.applications.show', $app) }}" class="block bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $app->full_name }}</h4>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $app->submitted_at?->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $app->city }}, {{ $app->province }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded">New</span>
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">No submitted applications</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- In Progress Column -->
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white">In Progress</h3>
                                <span class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200 text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $assignedApplications->whereIn('status', ['phone_interview_scheduled', 'meet_and_greet_scheduled', 'initial_inspection_scheduled', 'initial_inspection_completed', 'documents_pending', 'documents_submitted', 'documents_approved', 'second_inspection_scheduled', 'second_inspection_completed'])->count() }}
                                </span>
                            </div>
                            <div class="space-y-3">
                                @forelse($assignedApplications->whereIn('status', ['phone_interview_scheduled', 'meet_and_greet_scheduled', 'meet_and_greet_completed', 'initial_inspection_scheduled', 'initial_inspection_completed', 'documents_pending', 'documents_submitted', 'documents_approved', 'second_inspection_scheduled', 'second_inspection_completed',]) as $app)
                                    <a href="{{ route('consultant.applications.show', $app) }}" class="block bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $app->full_name }}</h4>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $app->updated_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $app->current_stage_display }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-1 rounded">{{ $app->status_display }}</span>
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">No applications in progress</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Final Review Column -->
                        <!-- Final Review Column -->
<div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900 dark:text-white">Final Review</h3>
        <span class="bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200 text-xs font-semibold px-2 py-1 rounded-full">
            {{ $assignedApplications->whereIn('status', [
                'documents_submitted',
                'second_inspection_completed',
                'final_inspection_scheduled',
                'final_inspection_completed',
                'contract_signing_scheduled',
                'contract_signed'
            ])->count() }}
        </span>
    </div>

    <div class="space-y-3">
        @forelse($assignedApplications->whereIn('status', [
            'documents_submitted',
            'second_inspection_completed',
            'final_inspection_scheduled',
            'final_inspection_completed',
            'contract_signing_scheduled',
            'contract_signed'
        ]) as $app)
            <a href="{{ route('consultant.applications.show', $app) }}"
               class="block bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">

                <div class="flex items-start justify-between mb-2">
                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm">
                        {{ $app->full_name }}
                    </h4>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $app->updated_at->diffForHumans() }}
                    </span>
                </div>

                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                    {{ $app->current_stage_display }}
                </p>

                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-2">
                    <div class="bg-green-500 h-1.5 rounded-full"
                         style="width: {{ $app->completion_percentage }}%">
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $app->completion_percentage }}% complete
                </p>
            </a>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                No applications in final review
            </p>
        @endforelse
    </div>
</div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Today's Appointments -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Today's Schedule</h2>
                </div>
                <div class="p-6">
                    @forelse($todayAppointments as $appointment)
                        <div class="mb-4 last:mb-0">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
                                        <x-timezone-date :date="$appointment->scheduled_at" format="H:i" />
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $appointment->title }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $appointment->applicant->name ?? $appointment->application->educator_first_name .''. $appointment->application->educator_last_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $appointment->location_address }}</p>
                                    <a href="{{ route('consultant.appointments.show', $appointment) }}" class="text-xs text-orange-600 hover:text-orange-700 mt-2 inline-block">View Details →</a>
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="border-t border-gray-100 dark:border-gray-700 my-4"></div>
                        @endif
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No appointments today</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pending Documents -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Pending Documents</h2>
                </div>
                <div class="p-6">
                    @forelse($pendingDocuments->take(5) as $document)
                        <div class="mb-4 last:mb-0">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $document->name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $document->application->full_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $document->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="border-t border-gray-100 dark:border-gray-700 my-4"></div>
                        @endif
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No pending documents</p>
                        </div>
                    @endforelse
                    
                    @if($pendingDocuments->count() > 0)
                        <a href="{{ route('consultant.documents.pending-review') }}" class="block text-center text-sm text-orange-600 hover:text-orange-700 font-medium mt-4">
                            View All Documents →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function kanbanBoard() {
    return {
        // Add drag-and-drop functionality here if needed
    }
}
</script>
@endpush
@endsection