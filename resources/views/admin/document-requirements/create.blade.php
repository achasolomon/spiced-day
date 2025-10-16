@extends('layouts.admin')

@section('title', 'Create Document Requirement')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form action="{{ route('admin.document-requirements.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Basic Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Basic Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                               required>
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category *</label>
                                        <input type="text" name="category" id="category" value="{{ old('category') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                               placeholder="e.g., Identity, Financial, Legal"
                                               required>
                                        @error('category')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description *</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-4">
                                    <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instructions</label>
                                    <textarea name="instructions" id="instructions" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500">{{ old('instructions') }}</textarea>
                                    @error('instructions')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="document_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Category</label>
                                        <select name="document_category_id" id="document_category_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500">
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('document_category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('document_category_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="stage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stage *</label>
                                        <select name="stage" id="stage"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                                required>
                                            @foreach($stages as $stage)
                                                <option value="{{ $stage->value }}" {{ old('stage') == $stage->value ? 'selected' : '' }}>
                                                    {{ \App\Enums\ApplicationStage::from($stage->value)->getDescription() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('stage')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- File Requirements -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">File Requirements</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Accepted Formats *</label>
                                        <div class="mt-1 space-y-2">
                                            @foreach($acceptedFormats as $format)
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="accepted_formats[]" value="{{ $format }}"
                                                           id="format-{{ $format }}"
                                                           {{ in_array($format, old('accepted_formats', [])) ? 'checked' : '' }}
                                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                                    <label for="format-{{ $format }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ strtoupper($format) }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('accepted_formats')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label for="max_file_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max File Size (KB)</label>
                                            <input type="number" name="max_file_size" id="max_file_size" value="{{ old('max_file_size', 10240) }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                                   min="1024" max="20480">
                                            @error('max_file_size')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="max_files" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Files *</label>
                                            <input type="number" name="max_files" id="max_files" value="{{ old('max_files', 1) }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                                   required min="1" max="10">
                                            @error('max_files')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Requirement Settings -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Requirement Settings</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_required" id="is_required" value="1"
                                               {{ old('is_required', true) ? 'checked' : '' }}
                                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                        <label for="is_required" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Required Document</label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_conditional" id="is_conditional" value="1"
                                               {{ old('is_conditional') ? 'checked' : '' }}
                                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded"
                                               onchange="toggleConditionalFields()">
                                        <label for="is_conditional" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Conditional Requirement</label>
                                    </div>

                                    <div id="conditional_fields" style="display: none;">
                                        <label for="conditions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conditions (JSON)</label>
                                        <textarea name="conditions" id="conditions" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                                  placeholder='{"field": "value"}'>{{ old('conditions') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Enter conditions in JSON format</p>
                                        @error('conditions')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Validity Settings -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Validity Settings</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="has_expiry" id="has_expiry" value="1"
                                               {{ old('has_expiry') ? 'checked' : '' }}
                                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded"
                                               onchange="toggleExpiryFields()">
                                        <label for="has_expiry" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Document Has Expiry</label>
                                    </div>

                                    <div id="expiry_fields" style="display: none;">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="validity_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Validity Period (Days)</label>
                                                <input type="number" name="validity_period" id="validity_period" value="{{ old('validity_period') }}"
                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                                       min="1">
                                                @error('validity_period')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="flex items-center">
                                                <input type="checkbox" name="requires_annual_renewal" id="requires_annual_renewal" value="1"
                                                       {{ old('requires_annual_renewal') ? 'checked' : '' }}
                                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                                <label for="requires_annual_renewal" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Requires Annual Renewal</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Processing Settings -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Processing Settings</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="requires_review" id="requires_review" value="1"
                                               {{ old('requires_review', true) ? 'checked' : '' }}
                                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                        <label for="requires_review" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Requires Review</label>
                                    </div>

                                    <div>
                                        <label for="review_priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Review Priority (1-10) *</label>
                                        <input type="number" name="review_priority" id="review_priority" value="{{ old('review_priority', 5) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                               required min="1" max="10">
                                        <p class="mt-1 text-xs text-gray-500">Higher number = higher priority</p>
                                        @error('review_priority')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="review_criteria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Review Criteria (JSON)</label>
                                        <textarea name="review_criteria" id="review_criteria" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                                  placeholder='["Check validity", "Verify signature"]'>{{ old('review_criteria') }}</textarea>
                                        @error('review_criteria')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="rejection_reasons" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Common Rejection Reasons</label>
                                        <textarea name="rejection_reasons" id="rejection_reasons" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                                  placeholder="One reason per line">{{ old('rejection_reasons') }}</textarea>
                                        @error('rejection_reasons')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Display Settings -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Display Settings</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order *</label>
                                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                               required min="0">
                                        @error('sort_order')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon</label>
                                        <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                               placeholder="e.g., document, file-text">
                                        @error('icon')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="help_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Help Text</label>
                                    <input type="text" name="help_text" id="help_text" value="{{ old('help_text') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                           placeholder="Brief help text for users">
                                    @error('help_text')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-4">
                                    <label for="example_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Example URL</label>
                                    <input type="url" name="example_url" id="example_url" value="{{ old('example_url') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                           placeholder="https://example.com/sample-document.pdf">
                                    @error('example_url')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-4 flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500">
                                    Create Document Requirement
                                </button>
                                <a href="{{ route('admin.document-requirements.index') }}"
                                   class="ml-4 text-sm text-gray-700 dark:text-gray-300 hover:underline">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleConditionalFields() {
            const checkbox = document.getElementById('is_conditional');
            const fields = document.getElementById('conditional_fields');
            fields.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleExpiryFields() {
            const checkbox = document.getElementById('has_expiry');
            const fields = document.getElementById('expiry_fields');
            fields.style.display = checkbox.checked ? 'block' : 'none';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleConditionalFields();
            toggleExpiryFields();
        });
    </script>
@endsection