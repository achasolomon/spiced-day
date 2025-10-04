@extends('layouts.dashboard')

@section('title', 'My Documents')

@section('content')
<div class="max-w-7xl mx-auto space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Documents</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                Application #{{ $application->application_number }}
            </p>
        </div>
        <a href="{{ route('applicant.applications.show', $application) }}" 
           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            ← Back to Application
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 text-sm text-green-800 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 text-sm text-red-800 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    @if(count($pendingDocuments) > 0)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-900 dark:text-amber-200">
                        {{ count($pendingDocuments) }} required document(s) missing
                    </p>
                    <ul class="mt-1 text-xs text-amber-800 dark:text-amber-300 list-disc list-inside">
                        @foreach($pendingDocuments as $pending)
                            <li>{{ $documentCategories[$pending] ?? $pending }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4" x-data="documentManager()">
        {{-- Upload Section --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Upload Documents</h2>
                
                <form action="{{ route('applicant.documents.store', $application) }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Drop Zone --}}
                    <div class="relative border-2 border-dashed rounded-lg transition-colors mb-3"
                         :class="isDragging ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/10' : 'border-gray-300 dark:border-gray-600'"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)">
                        
                        <input type="file" 
                               name="files[]"
                               id="fileInput"
                               multiple
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               @change="handleFileSelect($event)"
                               class="hidden">
                        
                        <label for="fileInput" class="block cursor-pointer p-6 text-center">
                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Drop files or click to browse
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                PDF, DOC, JPG, PNG • Max 10MB
                            </p>
                        </label>
                    </div>

                    {{-- Selected Files Preview --}}
                    <div x-show="selectedFiles.length > 0" class="space-y-2 mb-3">
                        <template x-for="(fileData, index) in selectedFiles" :key="index">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-2 border border-gray-200 dark:border-gray-700">
                                {{-- File Header with Preview --}}
                                <div class="flex items-center gap-2 mb-2">
                                    {{-- Preview Thumbnail --}}
                                    <div class="relative w-10 h-10 flex-shrink-0 bg-gray-200 dark:bg-gray-700 rounded overflow-hidden cursor-pointer"
                                         @click="openPreview(fileData)">
                                        <template x-if="fileData.preview">
                                            <img :src="fileData.preview" class="w-full h-full object-cover" alt="Preview">
                                        </template>
                                        <template x-if="!fileData.preview">
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-900 dark:text-white truncate" x-text="fileData.file.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatFileSize(fileData.file.size)"></p>
                                    </div>
                                    
                                    <button type="button"
                                            @click="removeFile(index)"
                                            class="p-1 text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Compact Form Fields --}}
                                <div class="space-y-2">
                                    <select x-model="fileData.category"
                                            :name="'documents[' + index + '][category]'"
                                            required
                                            class="w-full px-2 py-1.5 text-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-500">
                                        <option value="">Category *</option>
                                        @foreach($documentCategories as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <input type="text" 
                                           x-model="fileData.name"
                                           :name="'documents[' + index + '][name]'"
                                           required
                                           placeholder="Document name *"
                                           class="w-full px-2 py-1.5 text-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-500">
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="date" 
                                               x-model="fileData.issue_date"
                                               :name="'documents[' + index + '][issue_date]'"
                                               max="{{ date('Y-m-d') }}"
                                               placeholder="Issue date"
                                               class="w-full px-2 py-1.5 text-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-500">
                                        
                                        <input type="date" 
                                               x-model="fileData.expiry_date"
                                               :name="'documents[' + index + '][expiry_date]'"
                                               min="{{ date('Y-m-d') }}"
                                               placeholder="Expiry date"
                                               class="w-full px-2 py-1.5 text-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-purple-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Actions --}}
                    <div x-show="selectedFiles.length > 0" class="flex gap-2">
                        <button type="submit"
                                class="flex-1 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Upload (<span x-text="selectedFiles.length"></span>)
                        </button>
                        <button type="button"
                                @click="clearAll()"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            Clear
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Uploaded Documents List --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Uploaded Documents</h2>
                </div>
                
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($uploadedDocuments as $category => $documents)
                        <div class="p-4">
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                {{ $documentCategories[$category] ?? $category }}
                            </h3>
                            <div class="space-y-2">
                                @foreach($documents as $doc)
                                    <div class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                        {{-- Document Icon/Preview --}}
                                        <div class="w-10 h-10 flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center cursor-pointer"
                                             @click="viewDocument({{ $doc->id }}, '{{ addslashes($doc->name) }}', '{{ $doc->file_type }}', '{{ route('applicant.documents.preview', ['application' => $application, 'document' => $doc]) }}')">
                                            @if(in_array($doc->file_type, ['jpg', 'jpeg', 'png']))
                                                <img src="{{ route('applicant.documents.preview', ['application' => $application, 'document' => $doc]) }}" 
                                                     class="w-full h-full object-cover rounded" 
                                                     alt="{{ $doc->name }}"
                                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<svg class=\'w-5 h-5 text-gray-400\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z\' clip-rule=\'evenodd\'></path></svg>';">
                                            @else
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $doc->name }}</p>
                                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span>{{ strtoupper($doc->file_type) }}</span>
                                                <span>•</span>
                                                <span>{{ number_format($doc->file_size / 1024, 0) }} KB</span>
                                                <span>•</span>
                                                <span>{{ $doc->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        
                                        {{-- Status Badge --}}
                                        <div>
                                            @if($doc->status === 'approved')
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Approved
                                                </span>
                                            @elseif($doc->status === 'rejected')
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                                    Rejected
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                        
                                        {{-- Actions --}}
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('applicant.documents.download', ['application' => $application, 'document' => $doc]) }}" 
                                               class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                               title="Download">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>
                                            
                                            @if(in_array($doc->status, ['uploaded', 'rejected']))
                                                <form action="{{ route('applicant.documents.destroy', [$application, $doc]) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Delete this document?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-1.5 text-gray-400 hover:text-red-600"
                                                            title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No documents uploaded yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Document Viewer Modal --}}
        <div x-show="showModal" 
             x-cloak
             @keydown.escape.window="showModal = false"
             class="fixed inset-0 z-50 overflow-hidden">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"
                 @click="showModal = false"></div>
            
            {{-- Modal Content --}}
            <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full h-[95vh] flex flex-col"  style="max-width: 900px;"
                                          @click.stop>
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="currentDoc.name"></h3>
                        <div class="flex items-center gap-2">
                            <a :href="`{{ route('applicant.documents.index', $application) }}/${currentDoc.id}/download`"
                               x-show="currentDoc.id"
                               class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                               title="Download">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                            <button @click="showModal = false" 
                                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 overflow-auto p-4 bg-gray-100 dark:bg-gray-900">
                        {{-- Image Viewer --}}
                        <template x-if="['jpg', 'jpeg', 'png'].includes(currentDoc.type)">
                            <div class="flex items-center justify-center h-full">
                                <img :src="currentDoc.url" 
                                     :alt="currentDoc.name"
                                     class="max-w-full max-h-full object-contain rounded shadow-lg">
                            </div>
                        </template>
                        
                        {{-- PDF Viewer --}}
                        <template x-if="currentDoc.type === 'pdf'">
                            <div class="h-full">
                                <iframe :src="currentDoc.url" 
                                        class="w-full h-full border-0 rounded"
                                        type="application/pdf"></iframe>
                            </div>
                        </template>
                        
                        {{-- Other Documents --}}
                        <template x-if="!['jpg', 'jpeg', 'png', 'pdf'].includes(currentDoc.type)">
                            <div class="flex flex-col items-center justify-center h-full text-center">
                                <svg class="w-20 h-20 text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Preview not available for this file type</p>
                                    <a :href="currentDoc.id ? '{{ route('applicant.documents.index', $application) }}/' + currentDoc.id + '/download' : '#'"      
                             x-show="currentDoc.id"
                                   class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium">
                                    Download to View
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('documentManager', () => ({
        selectedFiles: [],
        isDragging: false,
        showModal: false,
        currentDoc: {
            id: null,
            name: '',
            type: '',
            url: ''
        },

        handleFileSelect(event) {
            this.processFiles(Array.from(event.target.files));
        },

        handleDrop(event) {
            this.isDragging = false;
            this.processFiles(Array.from(event.dataTransfer.files));
        },

        processFiles(files) {
            const validTypes = ['application/pdf', 'application/msword', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 10 * 1024 * 1024;
            
            files.forEach(file => {
                if (!validTypes.includes(file.type)) {
                    alert(`${file.name} is not a valid file type.`);
                    return;
                }
                
                if (file.size > maxSize) {
                    alert(`${file.name} exceeds 10MB limit.`);
                    return;
                }
                
                const fileData = {
                    file: file,
                    name: file.name.replace(/\.[^/.]+$/, ""),
                    category: '',
                    description: '',
                    issue_date: '',
                    expiry_date: '',
                    preview: null
                };
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        fileData.preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
                
                this.selectedFiles.push(fileData);
            });

            this.updateFileInput();
        },

        updateFileInput() {
            const dataTransfer = new DataTransfer();
            this.selectedFiles.forEach(fileData => {
                dataTransfer.items.add(fileData.file);
            });
            document.getElementById('fileInput').files = dataTransfer.files;
        },

        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            if (this.selectedFiles.length === 0) {
                document.getElementById('fileInput').value = '';
            } else {
                this.updateFileInput();
            }
        },

        clearAll() {
            this.selectedFiles = [];
            document.getElementById('fileInput').value = '';
        },

        openPreview(fileData) {
            if (fileData.preview) {
                this.currentDoc = {
                    id: null,
                    name: fileData.file.name,
                    type: fileData.file.type.startsWith('image/') ? 'jpg' : 'other',
                    url: fileData.preview
                };
                this.showModal = true;
            }
        },

        viewDocument(id, name, type, url) {
            this.currentDoc = {
                id: id,
                name: name,
                type: type,
                url: url
            };
            this.showModal = true;
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    }));
});
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endpush

@endsection