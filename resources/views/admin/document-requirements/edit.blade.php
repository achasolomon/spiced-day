@extends('layouts.admin')

    @section('title', 'Edit Document Requirement')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form action="{{ route('admin.document-requirements.update', $documentRequirement) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $documentRequirement->name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea name="description" id="description" rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500">{{ old('description', $documentRequirement->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="stage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stage</label>
                                <select name="stage" id="stage"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                        required>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->value }}" {{ old('stage', $documentRequirement->stage) == $stage->value ? 'selected' : '' }}>{{ \App\Enums\ApplicationStage::from($stage->value)->getDescription() }}</option>
                                    @endforeach
                                </select>
                                @error('stage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Accepted Formats</label>
                                <div class="mt-1 space-y-2">
                                    @foreach($acceptedFormats as $format)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="accepted_formats[]" value="{{ $format }}"
                                                   id="format-{{ $format }}"
                                                   {{ in_array($format, old('accepted_formats', json_decode($documentRequirement->accepted_formats, true) ?? [])) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                            <label for="format-{{ $format }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ strtoupper($format) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('accepted_formats')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="max_file_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max File Size (KB)</label>
                                <input type="number" name="max_file_size" id="max_file_size" value="{{ old('max_file_size', $documentRequirement->max_file_size) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                       required min="1024" max="20480">
                                @error('max_file_size')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $documentRequirement->sort_order) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-orange-500 focus:ring-orange-500"
                                       required min="0">
                                @error('sort_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_required" id="is_required" value="1"
                                       {{ old('is_required', $documentRequirement->is_required) ? 'checked' : '' }}
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="is_required" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Required by Default</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $documentRequirement->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</label>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500">
                                Update Document Requirement
                            </button>
                            <a href="{{ route('admin.document-requirements.index') }}"
                               class="ml-4 text-sm text-gray-700 dark:text-gray-300 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection