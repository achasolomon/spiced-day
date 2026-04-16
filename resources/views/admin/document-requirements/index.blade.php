@extends('layouts.admin')

@section('title', 'Document Requirements')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">Document Requirements</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage required documents for applications</p>
                </div>
                <a href="{{ route('admin.document-requirements.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wide transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create New
                </a>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            @if($documentRequirements->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No document requirements found.</p>
                    <a href="{{ route('admin.document-requirements.create') }}" class="mt-4 inline-flex items-center text-sm text-green-600 hover:text-green-700 font-medium">
                        Create your first document requirement →
                    </a>
                </div>
            @else
                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-4">
                    @foreach($documentRequirements as $req)
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <!-- Name and Stage -->
                            <div class="mb-3">
                                <h4 class="font-semibold text-gray-900 dark:text-white text-base mb-1 break-words">
                                    {{ $req->name }}
                                </h4>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                    {{ \App\Enums\ApplicationStage::from($req->stage)->getDescription() }}
                                </span>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Required</p>
                                    <span class="inline-flex items-center text-sm">
                                        @if($req->is_required)
                                            <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-green-700 dark:text-green-400 font-medium">Yes</span>
                                        @else
                                            <svg class="w-4 h-4 text-gray-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-400">No</span>
                                        @endif
                                    </span>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                    <span class="inline-flex items-center text-sm">
                                        @if($req->is_active)
                                            <span class="flex items-center text-green-700 dark:text-green-400 font-medium">
                                                <span class="w-2 h-2 bg-green-600 rounded-full mr-1.5"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="flex items-center text-gray-600 dark:text-gray-400">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-1.5"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('admin.document-requirements.edit', $req) }}" 
                                   class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg text-sm font-medium transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.document-requirements.destroy', $req) }}" 
                                      method="POST" 
                                      class="flex-1 delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Stage
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Required
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($documentRequirements as $req)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $req->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                            {{ \App\Enums\ApplicationStage::from($req->stage)->getDescription() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($req->is_required)
                                            <span class="inline-flex items-center text-green-700 dark:text-green-400 font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Yes
                                            </span>
                                        @else
                                            <span class="text-gray-600 dark:text-gray-400">No</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($req->is_active)
                                            <span class="inline-flex items-center text-green-700 dark:text-green-400 font-medium">
                                                <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-gray-600 dark:text-gray-400">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.document-requirements.edit', $req) }}" 
                                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.document-requirements.destroy', $req) }}" 
                                                  method="POST" 
                                                  class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($documentRequirements->hasPages())
                    <div class="mt-6 px-2">
                        {{ $documentRequirements->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this document requirement? This action cannot be undone.')) {
                form.submit();
            }
        });
    });
</script>
@endpush
@endsection