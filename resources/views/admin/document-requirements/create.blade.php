@extends('layouts.admin')

@section('title', 'Create Document Requirement')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Document Requirement</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Add a new document requirement that consultants can assign to applications
                </p>
            </div>
            <a href="{{ route('admin.document-requirements.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.document-requirements.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Basic Information Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Essential details about the document requirement</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Document Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               placeholder="e.g., Criminal Record Check"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Application Stage <span class="text-red-500">*</span>
                        </label>
                        <select name="stage" 
                                id="stage"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            <option value="">Select Stage</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->value }}" {{ old('stage') == $stage->value ? 'selected' : '' }}>
                                    {{ \App\Enums\ApplicationStage::from($stage->value)->getDescription() }}
                                </option>
                            @endforeach
                        </select>
                        @error('stage')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="document_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Document Category
                    </label>
                    <select name="document_category_id" 
                            id="document_category_id"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        <option value="">-- Select Category (Optional) --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('document_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('document_category_id')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              required
                              placeholder="Brief description of what this document is and why it's needed..."
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Instructions for Applicants
                    </label>
                    <textarea name="instructions" 
                              id="instructions" 
                              rows="3"
                              placeholder="Detailed instructions on how to obtain or prepare this document..."
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">This will be shown to applicants when they upload this document</p>
                </div>
            </div>
        </div>

        {{-- File Requirements Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">File Requirements</h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Specify accepted formats and file size limits</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Accepted File Formats <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($acceptedFormats as $format)
                            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <input type="checkbox" 
                                       name="accepted_formats[]" 
                                       value="{{ $format }}"
                                       id="format-{{ $format }}"
                                       {{ in_array($format, old('accepted_formats', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ strtoupper($format) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('accepted_formats')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="max_file_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maximum File Size (KB)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="max_file_size" 
                                   id="max_file_size" 
                                   value="{{ old('max_file_size', 10240) }}"
                                   min="1024" 
                                   max="20480"
                                   placeholder="10240"
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">KB</span>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Default: 10MB (10240 KB). Max: 20MB</p>
                        @error('max_file_size')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_files" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maximum Files <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="max_files" 
                               id="max_files" 
                               value="{{ old('max_files', 1) }}"
                               required
                               min="1" 
                               max="10"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">How many files can be uploaded for this requirement?</p>
                        @error('max_files')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Requirement Settings Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Requirement Settings</h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Configure when and how this document is required</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <input type="checkbox" 
                           name="is_required" 
                           id="is_required" 
                           value="1"
                           {{ old('is_required', true) ? 'checked' : '' }}
                           class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                    <div class="ml-3">
                        <label for="is_required" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            Required Document
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">This document must be uploaded by applicants</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <input type="checkbox" 
                           name="is_conditional" 
                           id="is_conditional" 
                           value="1"
                           {{ old('is_conditional') ? 'checked' : '' }}
                           class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded"
                           onchange="toggleConditionalFields()">
                    <div class="ml-3">
                        <label for="is_conditional" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            Conditional Requirement
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Only required under certain conditions</p>
                    </div>
                </div>

                <div id="conditional_fields" class="hidden transition-all duration-200">
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <label for="conditions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Conditions (JSON Format)
                        </label>
                        <textarea name="conditions" 
                                  id="conditions" 
                                  rows="3"
                                  placeholder='{"field": "value", "condition": "equals"}'
                                  class="w-full px-4 py-2.5 border border-amber-300 dark:border-amber-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors font-mono text-sm">{{ old('conditions') }}</textarea>
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Enter conditions in valid JSON format</p>
                        @error('conditions')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Validity Settings Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Validity Settings</h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Configure document expiration and renewal requirements</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <input type="checkbox" 
                           name="has_expiry" 
                           id="has_expiry" 
                           value="1"
                           {{ old('has_expiry') ? 'checked' : '' }}
                           class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded"
                           onchange="toggleExpiryFields()">
                    <div class="ml-3">
                        <label for="has_expiry" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            Document Has Expiry Date
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Check if this document expires and needs renewal</p>
                    </div>
                </div>

                <div id="expiry_fields" class="hidden transition-all duration-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div>
                            <label for="validity_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Validity Period (Days)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="validity_period" 
                                       id="validity_period" 
                                       value="{{ old('validity_period') }}"
                                       min="1"
                                       placeholder="365"
                                       class="w-full px-4 py-2.5 border border-green-300 dark:border-green-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">days</span>
                            </div>
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">How many days until the document expires?</p>
                            @error('validity_period')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-green-200 dark:border-green-800">
                            <input type="checkbox" 
                                   name="requires_annual_renewal" 
                                   id="requires_annual_renewal" 
                                   value="1"
                                   {{ old('requires_annual_renewal') ? 'checked' : '' }}
                                   class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <div class="ml-3">
                                <label for="requires_annual_renewal" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                    Requires Annual Renewal
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Document must be renewed every year</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Processing Settings Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Processing Settings</h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Configure review and approval workflow</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <input type="checkbox" 
                           name="requires_review" 
                           id="requires_review" 
                           value="1"
                           {{ old('requires_review', true) ? 'checked' : '' }}
                           class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                    <div class="ml-3">
                        <label for="requires_review" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            Requires Consultant Review
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Documents must be reviewed and approved by a consultant</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="review_priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Review Priority <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="review_priority" 
                                   id="review_priority" 
                                   value="{{ old('review_priority', 5) }}"
                                   required
                                   min="1" 
                                   max="10"
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400">1-10</span>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Higher number = higher priority for review</p>
                        @error('review_priority')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Display Order <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="sort_order" 
                               id="sort_order" 
                               value="{{ old('sort_order', 0) }}"
                               required
                               min="0"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first in lists</p>
                        @error('sort_order')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="review_criteria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Review Criteria (JSON)
                    </label>
                    <textarea name="review_criteria" 
                              id="review_criteria" 
                              rows="3"
                              placeholder='["Check validity", "Verify signature", "Confirm completeness"]'
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors font-mono text-sm resize-none">{{ old('review_criteria') }}</textarea>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">List of criteria to check during review (JSON array format)</p>
                    @error('review_criteria')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rejection_reasons" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Common Rejection Reasons
                    </label>
                    <textarea name="rejection_reasons" 
                              id="rejection_reasons" 
                              rows="3"
                              placeholder="Document is expired&#10;Document is not clear/readable&#10;Missing required information"
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('rejection_reasons') }}</textarea>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">One reason per line. These will be available as quick-select options when rejecting documents.</p>
                    @error('rejection_reasons')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Display Settings Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Display Settings</h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Configure how this requirement appears to users</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="help_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Help Text
                        </label>
                        <input type="text" 
                               name="help_text" 
                               id="help_text" 
                               value="{{ old('help_text') }}"
                               placeholder="e.g., Must be valid and include vulnerable sector check"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Brief hint text shown next to the document name</p>
                        @error('help_text')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Icon Name
                        </label>
                        <input type="text" 
                               name="icon" 
                               id="icon" 
                               value="{{ old('icon') }}"
                               placeholder="e.g., document, file-text, certificate"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Icon identifier for display (optional)</p>
                        @error('icon')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="example_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Example Document URL
                    </label>
                    <input type="url" 
                           name="example_url" 
                           id="example_url" 
                           value="{{ old('example_url') }}"
                           placeholder="https://example.com/sample-document.pdf"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Link to an example document that applicants can reference</p>
                    @error('example_url')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                    <div class="ml-3">
                        <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            Active
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Only active requirements are shown to consultants and applicants</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.document-requirements.index') }}"
               class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Document Requirement
            </button>
        </div>
    </form>
</div>

<script>
    function toggleConditionalFields() {
        const checkbox = document.getElementById('is_conditional');
        const fields = document.getElementById('conditional_fields');
        if (checkbox.checked) {
            fields.classList.remove('hidden');
            fields.classList.add('block');
        } else {
            fields.classList.add('hidden');
            fields.classList.remove('block');
        }
    }

    function toggleExpiryFields() {
        const checkbox = document.getElementById('has_expiry');
        const fields = document.getElementById('expiry_fields');
        if (checkbox.checked) {
            fields.classList.remove('hidden');
            fields.classList.add('block');
        } else {
            fields.classList.add('hidden');
            fields.classList.remove('block');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleConditionalFields();
        toggleExpiryFields();
    });
</script>
@endsection
