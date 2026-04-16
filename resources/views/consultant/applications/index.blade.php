@extends('layouts.consultant')

@section('title', 'Applications')

@section('content')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('import', {
            openImportModal: false,
            importing: false,
            results: null,
        });
    });
</script>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Applications</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and track your assigned applications</p>
        </div>
        <div class="flex gap-3">
            <button @click="$store.import.openImportModal = true"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Import Excel
            </button>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('consultant.applications.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by name, email, or application number..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="phone_interview_scheduled" {{ request('status') == 'phone_interview_scheduled' ? 'selected' : '' }}>Phone Interview</option>
                        <option value="meet_and_greet_scheduled" {{ request('status') == 'meet_and_greet_scheduled' ? 'selected' : '' }}>Meet & Greet</option>
                        <option value="initial_inspection_scheduled" {{ request('status') == 'initial_inspection_scheduled' ? 'selected' : '' }}>Initial Inspection</option>
                        <option value="documents_submitted" {{ request('status') == 'documents_submitted' ? 'selected' : '' }}>Documents Submitted</option>
                    </select>
                </div>

                <!-- Stage Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stage</label>
                    <select name="stage" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Stages</option>
                        <option value="intake" {{ request('stage') == 'intake' ? 'selected' : '' }}>Intake</option>
                        <option value="phone_interview" {{ request('stage') == 'phone_interview' ? 'selected' : '' }}>Phone Interview</option>
                        <option value="meet_and_greet" {{ request('stage') == 'meet_and_greet' ? 'selected' : '' }}>Meet & Greet</option>
                        <option value="initial_inspection" {{ request('stage') == 'initial_inspection' ? 'selected' : '' }}>Initial Inspection</option>
                        <option value="document_collection" {{ request('stage') == 'document_collection' ? 'selected' : '' }}>Documents</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('consultant.applications.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Clear Filters</a>
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

   <!-- Import Modal -->
    <div 
        x-show="$store.import.openImportModal"
        x-cloak
        @keydown.escape.window="$store.import.openImportModal = false"
        class="fixed inset-0 z-50 overflow-y-auto">
        
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="$store.import.openImportModal = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Import Applications from Excel</h2>
                    <button @click="$store.import.openImportModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="importForm" @submit.prevent="submitImport" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Excel File (.xlsx, .xls)
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls" required
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="mt-1 text-xs text-gray-500">Max size: 10 MB</p>
                    </div>

                    <div x-show="$store.import.importing" class="mb-4 p-3 bg-blue-50 text-blue-700 rounded-lg text-sm">
                        Processing your file… This may take a few moments.
                    </div>

                    <div x-show="$store.import.results !== null" 
                        class="mb-4 p-4 rounded-lg"
                        :class="$store.import.results?.success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'">
                        <p class="font-medium" x-text="$store.import.results?.message"></p>
                        <p class="text-sm mt-1">
                            Success: <span x-text="$store.import.results?.imported || 0"></span> |
                            Failed: <span x-text="$store.import.results?.failed || 0"></span>
                        </p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button"
                                @click="$store.import.openImportModal = false"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="$store.import.importing"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center gap-2">
                            <span x-show="$store.import.importing">Importing…</span>
                            <span x-show="!$store.import.importing">Start Import</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Application #</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Stage</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($applications as $application)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <!-- Applicant -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($application->educator_first_name, 0, 1) }}{{ substr($application->educator_last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->full_name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $application->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Application Number -->
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $application->application_number }}</span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $application->status_badge_color == 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $application->status_badge_color == 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $application->status_badge_color == 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $application->status_badge_color == 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $application->status_badge_color == 'gray' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}">
                                    {{ $application->status_display }}
                                </span>
                            </td>

                            <!-- Stage -->
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $application->current_stage_display }}</span>
                            </td>

                            <!-- Progress -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-orange-500 h-2 rounded-full transition-all" style="width: {{ $application->application_progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ number_format($application->application_progress_percentage, 0) }}%</span>
                                </div>
                            </td>

                            <!-- Submitted Date -->
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $application->submitted_at?->format('M j, Y') ?? 'Draft' }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('consultant.applications.show', $application) }}" 
                                       class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg font-medium transition-colors">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No applications found</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
<script>
    
function submitImport() {
    const form = document.getElementById('importForm');
    const data = new FormData(form);
    const store = Alpine.store('import');

    store.importing = true;
    store.results = null;

    fetch('{{ route('consultant.applications.import') }}', {
        method: 'POST',
        body: data,
        headers: {
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
        }
    })
    .then(r => r.json())
    .then(json => {
        store.importing = false;
        store.results = json;

        if (json.success) {
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(err => {
        console.error(err);
        store.importing = false;
        store.results = { success: false, message: 'Network error. Please try again.' };
    });
}

</script>
@endsection