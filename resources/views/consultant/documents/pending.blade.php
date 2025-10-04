@extends('layouts.consultant')

@section('title', 'Document Review')

@section('content')
<div class="space-y-6" x-data="{ selectedDocs: [], selectAll: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Document Review</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Review and approve submitted documents</p>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <span class="font-semibold text-orange-600">{{ $documents->count() }}</span> documents pending
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div x-show="selectedDocs.length > 0" 
         x-transition
         class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                <span x-text="selectedDocs.length"></span> document(s) selected
            </p>
            <div class="flex items-center gap-3">
                <button @click="bulkDownload()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Download Selected
                </button>
                <button @click="selectedDocs = []" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Clear Selection
                </button>
            </div>
        </div>
    </div>

    <!-- Applicants with Pending Documents -->
    <div class="space-y-4">
        @php
            $groupedDocuments = $documents->groupBy('application_id');
        @endphp
        
        @forelse($groupedDocuments as $applicationId => $appDocuments)
            @php
                $application = $appDocuments->first()->application;
                $docCount = $appDocuments->count();
            @endphp
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" x-data="{ expanded: false }" data-application-id="{{ $application->id }}">
                <!-- Applicant Header -->
                <button @click="expanded = !expanded" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($application->educator_first_name, 0, 1) }}{{ substr($application->educator_last_name, 0, 1) }}
                        </div>
                        <div class="text-left">
                            <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $application->full_name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $application->application_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 px-4 py-2 rounded-full text-sm font-semibold">
                            {{ $docCount }} {{ Str::plural('document', $docCount) }}
                        </span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': expanded }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </button>

                <!-- Documents List (Expandable) -->
                <div x-show="expanded" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($appDocuments as $document)
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700" x-data="{ showPreview: false, showRejectModal: false }">
                                <!-- Document Header with Checkbox -->
                                <div class="flex items-start gap-3 mb-3">
                                    <input type="checkbox" 
                                           :value="{{ $document->id }}" 
                                           x-model="selectedDocs"
                                           class="mt-1 w-4 h-4 text-orange-600 focus:ring-orange-500 rounded">
                                    
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $document->name }}</h4>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ ucfirst(str_replace('_', ' ', $document->category)) }}</p>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $document->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Info -->
                                <div class="grid grid-cols-2 gap-2 mb-3 text-xs">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Size:</span>
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $document->file_size_human }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Type:</span>
                                        <span class="text-gray-900 dark:text-white font-medium uppercase">{{ $document->file_type }}</span>
                                    </div>
                                </div>

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
                                        <a href="{{ route('consultant.documents.download', $document) }}" 
                                        class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" 
                                        title="Download">
                                            <!-- Download icon -->
                                        </a>                                       
                                     </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('consultant.documents.approve', $document) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Approve this document?')" class="w-full px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium transition-colors">
                                            ✓ Approve
                                        </button>
                                    </form>

                                    <button @click="showRejectModal = true" class="flex-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium transition-colors">
                                        ✕ Reject
                                    </button>

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
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" x-data="{ showPreview: false, showRejectModal: false }">
                <!-- Document Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-900/50 dark:to-gray-800/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $document->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $document->application->full_name }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ ucfirst($document->category) }}
                        </span>
                    </div>
                </div>

                <!-- Document Info -->
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Uploaded</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $document->created_at->format('M j, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">File Size</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $document->file_size_human }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Type</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white uppercase">{{ $document->file_type }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Expires</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $document->expiry_date ? $document->expiry_date->format('M j, Y') : 'No expiry' }}
                            </p>
                        </div>
                    </div>

                    @if($document->description)
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $document->description }}</p>
                        </div>
                    @endif

                    <!-- Preview Toggle -->
                    <button @click="showPreview = !showPreview" class="w-full mb-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm font-medium flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span x-text="showPreview ? 'Hide Preview' : 'Show Preview'"></span>
                    </button>

                    <!-- Document Preview -->
                    <div x-show="showPreview" x-transition class="mb-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                        @if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ route('applicant.documents.preview', [$document->application, $document]) }}" alt="{{ $document->name }}" class="w-full h-64 object-contain bg-gray-100 dark:bg-gray-900">
                        @elseif($document->file_type === 'pdf')
                            <iframe src="{{ route('applicant.documents.preview', [$document->application, $document]) }}" class="w-full h-96 bg-gray-100 dark:bg-gray-900"></iframe>
                        @else
                            <div class="p-8 text-center bg-gray-50 dark:bg-gray-900">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Preview not available</p>
                                <a href="{{ route('consultant.documents.download', [$document->application, $document]) }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium mt-2 inline-block">Download to view</a>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3">
                        <form action="{{ route('consultant.documents.approve', $document) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" onclick="return confirm('Approve this document?')" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Approve
                            </button>
                        </form>

                        <button @click="showRejectModal = true" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Reject
                        </button>

                        <a href="{{ route('consultant.documents.download', [$document->application, $document]) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div x-show="showRejectModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 overflow-y-auto" 
                     style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRejectModal = false"></div>
                        
                        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
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
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="w-20 h-20 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">All caught up!</h3>
                <p class="text-gray-600 dark:text-gray-400">No documents pending review at this time</p>
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

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    window.bulkDownload = function() {
        // Get all checked checkboxes
        const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
        
        if (checkedBoxes.length === 0) {
            alert('Please select at least one document to download');
            return;
        }

        // Get selected document IDs
        const selectedDocs = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Get application ID from first checked box
        const firstCheckbox = checkedBoxes[0];
        const applicationId = firstCheckbox.closest('[data-application-id]')?.dataset.applicationId;
        
        if (!applicationId) {
            alert('Could not determine application ID');
            return;
        }

        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/consultant/documents/bulk-download/${applicationId}`;
        
        // CSRF token
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        // Add document IDs
        selectedDocs.forEach(docId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'document_ids[]';
            input.value = docId;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
});
</script>
@endpush
@endsection