@extends('layouts.consultant')

@section('title', 'Application Details - ' . $application->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('consultant.applications.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $application->full_name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Application #{{ $application->application_number }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button     
               @click="window.dispatchEvent(new CustomEvent('open-appointment-modal', {
                    detail: {
                        applicationId: {{ $application->id }},
                        applicantId: {{ $application->user_id }},
                        applicantAddress: '{{ $application->full_address }}'
                    }
                }));" 
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                Schedule Appointment
            </button>
            <button type="button" onclick="addNote()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Add Note
            </button>
        </div>
    </div>

    <!-- Status & Progress Bar -->
    <div class="bg-gradient-to-r from-white-500 to-white-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-purple-600 text-sm font-medium">Current Status</p>
                <p class="text-2xl  text-purple-600 font-bold mt-1">{{ $application->status_display }}</p>
                <p class="text-purple-600 text-sm mt-2">{{ $application->current_stage_display }}</p>
            </div>
            <div class="w-full md:w-64">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-green-700">Progress</span>
                    <span class="text-sm text-green-700 font-semibold">{{ number_format($application->completion_percentage, 0) }}%</span>
                </div>
                <div class="w-full bg-purple-400/30 rounded-full h-3">
                    <div class="bg-green-700 h-3 rounded-full transition-all" style="width: {{ $application->completion_percentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Applicant Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Applicant Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->full_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->phone }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->full_address }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Childcare Level</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->childcare_level ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Desired Start Date</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->desired_start_date?->format('M j, Y') ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Home Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Home Details</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Home Type</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1 capitalize">{{ $application->home_type ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Ownership</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1 capitalize">{{ $application->home_ownership ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Residents</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->home_residents_count ?? 0 }} people</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Fenced Backyard</label>
                            <p class="text-gray-900 dark:text-white font-semibold mt-1">{{ $application->fenced_backyard ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Pets</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $application->has_pets ? ($application->pets_details ?? 'Yes') : 'No pets' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Documents</h2>
                    <a href="{{ route('applicant.documents.index', $application) }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">View All →</a>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @forelse($application->documents()->latest()->take(5)->get() as $document)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $document->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $document->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $document->status === 'uploaded' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $document->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                                    {{ ucfirst($document->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No documents uploaded yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <button  @click="window.dispatchEvent(new CustomEvent('open-appointment-modal', {
                                detail: {
                                    applicationId: {{ $application->id }},
                                    applicantId: {{ $application->user_id }},
                                    applicantAddress: '{{ $application->full_address }}'
                                }
                            }));"
                        class="w-full px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors text-sm font-medium text-left flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        Schedule Appointment
                    </button>
                    <button onclick="conductInspection()" class="w-full px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors text-sm font-medium text-left flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                        </svg>
                        Conduct Inspection
                    </button>
                    <button onclick="reviewDocuments()" class="w-full px-4 py-2 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors text-sm font-medium text-left flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                        </svg>
                        Review Documents
                    </button>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Activity Timeline</h3>
                <div class="space-y-4">
                    @if($application->submitted_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Application Submitted</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->submitted_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @foreach($application->appointments()->latest('scheduled_at')->take(3)->get() as $appointment)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $appointment->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                    
                    <a href="#" class="text-sm text-orange-600 hover:text-orange-700 font-medium">View Full History →</a>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Consultant Notes</h3>
                <textarea rows="4" 
                          placeholder="Add internal notes about this application..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white text-sm">{{ $application->admin_notes }}</textarea>
                <button type="button" class="mt-3 w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors text-sm">
                    Save Notes
                </button>
            </div>
        </div>
    </div>
</div>


<x-appointments.schedule-modal />

@push('scripts')
<script>

function conductInspection() {
    window.location.href = "{{ route('consultant.inspections.create', ['application_id' => $application->id]) }}";
}

function reviewDocuments() {
    window.location.href = "{{ route('applicant.documents.index', $application) }}";
}

function addNote() {
    alert('Add note functionality');
}
</script>
@endpush
@endsection