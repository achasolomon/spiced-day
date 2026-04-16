@extends('layouts.consultant')

@section('title', 'Document Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Document Management</h1>
        <p class="text-gray-600 dark:text-gray-400">Review and manage all application documents</p>
    </div>
    

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Review</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Approved</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Rejected</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Documents</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Status Tabs - NO FORM, just links -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-2">
            <a href="{{ route('consultant.documents.pending-review', ['status' => 'all']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors {{ request('status', 'uploaded') == 'all' ? 'bg-orange-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                All Documents
            </a>
            <a href="{{ route('consultant.documents.pending-review', ['status' => 'uploaded']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors {{ (request('status', 'uploaded') == 'uploaded' || !request()->has('status')) ? 'bg-orange-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Pending Review ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('consultant.documents.pending-review', ['status' => 'approved']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors {{ request('status') == 'approved' ? 'bg-green-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Approved ({{ $stats['approved'] }})
            </a>
            <a href="{{ route('consultant.documents.pending-review', ['status' => 'rejected']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors {{ request('status') == 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Rejected ({{ $stats['rejected'] }})
            </a>
        </div>
    </div>

    <!-- Documents Grouped by Applicant -->
    <div class="space-y-4">
        @php
            $groupedDocuments = $documents->groupBy('application_id');
        @endphp
        
        @forelse($groupedDocuments as $applicationId => $appDocuments)
            @php
                $application = $appDocuments->first()->application;
                $docCount = $appDocuments->count();
                $pendingCount = $appDocuments->whereIn('status', ['uploaded', 'under_review'])->count();
                $approvedCount = $appDocuments->where('status', 'approved')->count();
                $rejectedCount = $appDocuments->where('status', 'rejected')->count();
                
                // Determine which status tab is active
                $currentStatus = request('status', 'uploaded');
                $shouldShow = true;
                
                if ($currentStatus !== 'all') {
                    if ($currentStatus === 'uploaded' && $pendingCount === 0) $shouldShow = false;
                    if ($currentStatus === 'approved' && $approvedCount === 0) $shouldShow = false;
                    if ($currentStatus === 'rejected' && $rejectedCount === 0) $shouldShow = false;
                }
            @endphp
            
            @if($shouldShow)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" 
                 x-data="{ expanded: {{ $pendingCount > 0 ? 'true' : 'false' }} }" 
                 data-application-id="{{ $application->id }}">
                
                <!-- Applicant Header -->
                <button @click="expanded = !expanded" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($application->educator_first_name, 0, 1) }}{{ substr($application->educator_last_name, 0, 1) }}
                        </div>
                        <div class="text-left">
                            <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $application->full_name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $application->application_number }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $docCount }} document(s)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Status Badges -->
                        <div class="flex items-center gap-2">
                            @if($pendingCount > 0)
                                <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $pendingCount }} Pending
                                </span>
                            @endif
                            @if($approvedCount > 0)
                                <span class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $approvedCount }} Approved
                                </span>
                            @endif
                            @if($rejectedCount > 0)
                                <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $rejectedCount }} Rejected
                                </span>
                            @endif
                        </div>
                        
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': expanded }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </button>

                <!-- Documents List (Expandable) -->
                <div x-show="expanded" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($appDocuments as $document)
                            @php
                                $shouldShowDocument = true;
                                if ($currentStatus === 'uploaded' && !in_array($document->status, ['uploaded', 'under_review'])) $shouldShowDocument = false;
                                if ($currentStatus === 'approved' && $document->status !== 'approved') $shouldShowDocument = false;
                                if ($currentStatus === 'rejected' && $document->status !== 'rejected') $shouldShowDocument = false;
                            @endphp
                            
                            @if($shouldShowDocument)
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700" x-data="{ showPreview: false, showRejectModal: false }">
                                <!-- Document Header -->
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $document->name }}</h4>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $document->documentRequirement->name ?? ucfirst(str_replace('_', ' ', $document->category)) }}
                                                </p>
                                            </div>
                                            <div class="flex flex-col items-end gap-1">
                                                <!-- Status Badge -->
                                                @if($document->status === 'approved')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Approved
                                                    </span>
                                                @elseif($document->status === 'rejected')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                                        Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200">
                                                        Pending
                                                    </span>
                                                @endif
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $document->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Info -->
                                <div class="grid grid-cols-2 gap-2 mb-3 text-xs">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Size:</span>
                                        <span class="text-gray-900 dark:text-white font-medium">{{ number_format($document->file_size / 1024, 0) }} KB</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Type:</span>
                                        <span class="text-gray-900 dark:text-white font-medium uppercase">{{ $document->file_type }}</span>
                                    </div>
                                    @if($document->expiry_date)
                                        <div class="col-span-2">
                                            <span class="text-gray-500 dark:text-gray-400">Expires:</span>
                                            @if(method_exists($document, 'isExpired') && $document->isExpired())
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 ml-2">
                                                    Expired: {{ $document->expiry_date->format('M j, Y') }}
                                                </span>
                                            @elseif(method_exists($document, 'isExpiringSoon') && $document->isExpiringSoon())
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 ml-2">
                                                    Expires: {{ $document->expiry_date->format('M j, Y') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 ml-2">
                                                    Valid until: {{ $document->expiry_date->format('M j, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if($document->review_notes)
                                    <div class="mb-3 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded text-xs">
                                        <p class="font-medium text-red-900 dark:text-red-200 mb-1">Rejection Reason:</p>
                                        <p class="text-red-800 dark:text-red-300">{{ $document->review_notes }}</p>
                                    </div>
                                @endif

                                <!-- Preview Toggle -->
                                <button @click="showPreview = !showPreview" class="w-full mb-3 px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                    <span x-text="showPreview ? 'Hide Preview' : 'Show Preview'"></span>
                                </button>

                                <!-- Document Preview -->
                                <div x-show="showPreview" x-collapse class="mb-3 border border-gray-300 dark:border-gray-600 rounded overflow-hidden">
                                    @if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ route('consultant.documents.preview', $document) }}" alt="{{ $document->name }}" class="w-full h-48 object-contain bg-gray-100 dark:bg-gray-900">
                                    @elseif($document->file_type === 'pdf')
                                        <iframe src="{{ route('consultant.documents.preview', $document) }}" class="w-full h-64 bg-gray-100 dark:bg-gray-900"></iframe>
                                    @else
                                        <div class="p-6 text-center bg-gray-100 dark:bg-gray-900">
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Preview not available</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2">
                                    @if($document->status !== 'approved')
                                        <form action="{{ route('consultant.documents.approve', $document) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve this document?')" class="w-full px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium transition-colors">
                                                Approve
                                            </button>
                                        </form>
                                    @endif

                                    @if($document->status !== 'rejected' && $document->status !== 'approved')
                                        <button @click="showRejectModal = true" class="flex-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium transition-colors">
                                            Reject
                                        </button>
                                    @endif

                                    <a href="{{ route('consultant.documents.download', $document) }}" class="px-3 py-1.5 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </div>

                                <!-- Reject Modal -->
                                <div x-show="showRejectModal" 
                                     x-transition
                                     class="fixed inset-0 z-50 overflow-y-auto" 
                                     style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showRejectModal = false"></div>
                                        
                                        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6 z-10">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Reject Document</h3>
                                            
                                            <form action="{{ route('consultant.documents.reject', $document) }}" method="POST">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Rejection*</label>
                                                    <textarea name="review_notes" 
                                                              rows="4" 
                                                              required
                                                              placeholder="Please explain why this document is being rejected..."
                                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">The applicant will see this message</p>
                                                </div>
                                                
                                                <div class="flex items-center gap-3">
                                                    <button type="button" @click="showRejectModal = false" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                                        Reject Document
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="w-20 h-20 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No documents found</h3>
                <p class="text-gray-600 dark:text-gray-400">No documents match your current filter</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($documents->hasPages())
        <div class="flex justify-center mt-6">
            {{ $documents->links() }}
        </div>
    @endif
</div>
@endsection