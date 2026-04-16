@extends('layouts.consultant')

@section('title', 'Edit Draft Inspection')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('consultant.inspections.show', $inspection) }}" 
           class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Draft Inspection</h1>
                <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 rounded-full text-sm font-semibold">
                    Draft
                </span>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                {{ $checklist->name }} • {{ ucwords(str_replace('_', ' ', $inspection->type)) }}
            </p>
            @if($inspection->draft_saved_at)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Last saved: {{ $inspection->draft_saved_at->diffForHumans() }}
                </p>
            @endif
        </div>
    </div>

    <!-- Draft Notice -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 dark:text-blue-300">Draft Mode</h3>
                <p class="text-sm text-blue-800 dark:text-blue-400 mt-1">
                    You can save progress at any time. Required fields are only enforced when completing the inspection.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('consultant.inspections.update-draft', $inspection) }}" 
          method="POST" 
          enctype="multipart/form-data" 
          x-data="inspectionForm({{ json_encode($inspection->checklist_results ?? []) }})">
        @csrf
        @method('PUT')

        <!-- Inspection Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Inspection Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Inspection Type</label>
                    <input type="text" value="{{ ucwords(str_replace('_', ' ', $inspection->type)) }}" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white" 
                           readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Inspection Date & Time <span class="text-red-500" x-show="!isDraft">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="conducted_at" 
                           value="{{ $inspection->conducted_at ? $inspection->conducted_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes)</label>
                    <input type="number" 
                           name="duration" 
                           min="30" 
                           max="480" 
                           value="{{ $inspection->duration ?? $checklist->estimated_duration ?? 120 }}" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Weather Conditions</label>
                    <input type="text" 
                           name="weather_conditions" 
                           value="{{ $inspection->weather_conditions }}"
                           placeholder="e.g., Sunny, 20°C" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Temperature (°C)</label>
                    <input type="number" 
                           step="0.1" 
                           name="temperature" 
                           value="{{ $inspection->temperature }}"
                           placeholder="e.g., 22.5" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Environmental Factors</label>
                    <input type="text" 
                           name="environmental_factors" 
                           value="{{ $inspection->environmental_factors }}"
                           placeholder="Any environmental notes" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Inspection Checklist -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Inspection Checklist</h2>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Progress: <span x-text="completedItems"></span> / <span x-text="totalItems"></span>
                </div>
            </div>

            @foreach($checklistItems as $category => $items)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    @switch($category)
                        @case('safety')
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            @break
                        @case('health')
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                            @break
                        @case('environment')
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            @break
                        @default
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                    @endswitch
                    {{ ucwords(str_replace('_', ' ', $category)) }}
                </h3>
                
                <div class="space-y-4">
                    @foreach($items as $item)
                    @php
                        $existingResult = $inspection->checklist_results[$item->code] ?? null;
                    @endphp
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg" 
                         x-data="{ 
                             itemStatus: '{{ $existingResult['status'] ?? '' }}', 
                            showNotes: {{ (($existingResult['status'] ?? null) === 'fail') || $item->is_critical ? 'true' : 'false' }},
                             isCritical: {{ $item->is_critical ? 'true' : 'false' }}
                         }">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $item->title }}</h4>
                                @if($item->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $item->description }}</p>
                                @endif
                                @if($item->criteria)
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                    <strong>Criteria:</strong> {{ $item->criteria }}
                                </p>
                                @endif
                            </div>
                            @if($item->is_critical)
                            <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-semibold px-2 py-1 rounded flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                Critical
                            </span>
                            @endif
                        </div>

                        @if($item->help_text)
                        <div class="mb-3 text-xs text-gray-500 dark:text-gray-400 italic">
                            💡 {{ $item->help_text }}
                        </div>
                        @endif

                        <!-- Response Input Based on Type -->
                        @if($item->response_type === 'yes_no' || $item->response_type === 'yes_no_na')
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" 
                                       name="checklist_results[{{ $item->code }}][status]" 
                                       value="pass" 
                                       @click="updateProgress(); itemStatus = 'pass'; showNotes = isCritical; checkFailures()" 
                                       {{ ($existingResult['status'] ?? '') === 'pass' ? 'checked' : '' }}
                                       class="text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Yes/Pass</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" 
                                       name="checklist_results[{{ $item->code }}][status]" 
                                       value="fail" 
                                       @click="updateProgress(); itemStatus = 'fail'; showNotes = true; checkFailures()" 
                                       {{ ($existingResult['status'] ?? '') === 'fail' ? 'checked' : '' }}
                                       class="text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">No/Fail</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" 
                                       name="checklist_results[{{ $item->code }}][status]" 
                                       value="n/a" 
                                       @click="updateProgress(); itemStatus = 'n/a'; showNotes = false; checkFailures()" 
                                       {{ ($existingResult['status'] ?? '') === 'n/a' ? 'checked' : '' }}
                                       class="text-gray-600 focus:ring-gray-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">N/A</span>
                            </label>
                        </div>
                        @elseif($item->response_type === 'rating_scale')
                        <div class="flex items-center gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="radio" 
                                       name="checklist_results[{{ $item->code }}][status]" 
                                       value="{{ $i }}" 
                                       @click="updateProgress(); itemStatus = '{{ $i }}'; showNotes = ({{ $i }} <= 2); checkFailures()"
                                       {{ ($existingResult['status'] ?? '') == $i ? 'checked' : '' }}
                                       class="text-orange-600 focus:ring-orange-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $i }}</span>
                            </label>
                            @endfor
                        </div>
                        @elseif($item->response_type === 'numeric')
                        <input type="number" 
                               name="checklist_results[{{ $item->code }}][value]" 
                               value="{{ $existingResult['value'] ?? '' }}"
                               @input="itemStatus = $event.target.value"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                               placeholder="Enter value">
                        @elseif($item->response_type === 'text')
                        <textarea name="checklist_results[{{ $item->code }}][value]" 
                                  rows="2" 
                                  @input="itemStatus = $event.target.value"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Enter details">{{ $existingResult['value'] ?? '' }}</textarea>
                        @endif

                        <!-- Hidden Fields -->
                        <input type="hidden" name="checklist_results[{{ $item->code }}][is_critical]" value="{{ $item->is_critical ? '1' : '0' }}" data-critical="{{ $item->is_critical ? 'true' : 'false' }}">
                        <input type="hidden" name="checklist_results[{{ $item->code }}][points_possible]" value="{{ $item->points_possible }}">
                        <input type="hidden" name="checklist_results[{{ $item->code }}][item_id]" value="{{ $item->id }}">
                        <input type="hidden" name="checklist_results[{{ $item->code }}][included_in_second_final]" value="{{ ($item->included_in_second || $item->included_in_final) ? '1' : '0' }}">

                        <!-- Notes/Comments -->
                        <div x-show="showNotes" x-transition class="mt-3">
                            <textarea name="checklist_results[{{ $item->code }}][notes]" 
                                      rows="2" 
                                      :placeholder="itemStatus === 'fail' ? 'Explain the failure (REQUIRED)...' : 'Add notes (Optional)...'"
                                      :required="itemStatus === 'fail'"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                      :class="{ 'border-red-500 dark:border-red-500': itemStatus === 'fail' }">{{ $existingResult['notes'] ?? '' }}</textarea>
                            <p x-show="itemStatus === 'fail'" class="mt-1 text-xs text-red-600 dark:text-red-400">
                                <strong>Note required:</strong> Explain why this item failed
                            </p>
                        </div>

                        <!-- Photo Upload if Required -->
                        @if($item->requires_photo)
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Photo Evidence @if(!$existingResult || !isset($existingResult['photo']))<span x-show="!isDraft" class="text-red-500">*</span>@endif
                            </label>
                            @if($existingResult && isset($existingResult['photo']))
                                <p class="text-xs text-green-600 dark:text-green-400 mb-2">✓ Photo uploaded</p>
                            @endif
                            <input type="file" 
                                   name="checklist_results[{{ $item->code }}][photo]" 
                                   accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Inspection Summary</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Overall Observations <span class="text-red-500">*</span>
                    </label>
                    <textarea name="observations" 
                              rows="4"
                              placeholder="Describe overall condition and observations..." 
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ $inspection->observations }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Consultant Notes (Internal) <span class="text-red-500">*</span>
                    </label>
                    <textarea name="consultant_notes" 
                              rows="3"
                              placeholder="Internal notes (not visible to applicant)..." 
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ $inspection->consultant_notes }}</textarea>
                </div>
            </div>
        </div>

        <!-- Consultant Decision (shown when ANY item fails) -->
        <div x-show="hasFailed && !isDraft" 
             x-transition
             class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 mt-6">
            <h3 class="text-lg font-bold text-yellow-900 dark:text-yellow-300 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Inspection Failed - Decision Required
            </h3>
            <p class="text-sm text-yellow-800 dark:text-yellow-400 mb-4">
                One or more items have failed. Please decide how to proceed with this application.
            </p>
            
            <div class="space-y-3">
                <label class="flex items-start gap-3 p-3 border border-yellow-300 dark:border-yellow-700 rounded-lg cursor-pointer hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                    <input type="radio" name="consultant_decision" value="proceed_to_next_stage" 
                           {{ ($inspection->consultant_decision ?? '') === 'proceed_to_next_stage' ? 'checked' : '' }}
                           class="mt-1 text-green-600 focus:ring-green-500">
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Proceed to Next Stage</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Mark inspection as completed and allow application to continue</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 border border-yellow-300 dark:border-yellow-700 rounded-lg cursor-pointer hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                    <input type="radio" name="consultant_decision" value="schedule_follow_up" 
                           {{ ($inspection->consultant_decision ?? '') === 'schedule_follow_up' ? 'checked' : '' }}
                           class="mt-1 text-orange-600 focus:ring-orange-500">
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Schedule Follow-Up Inspection</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Failed items need to be reinspected before proceeding</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 border border-yellow-300 dark:border-yellow-700 rounded-lg cursor-pointer hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                    <input type="radio" name="consultant_decision" value="reject_application" 
                           {{ ($inspection->consultant_decision ?? '') === 'reject_application' ? 'checked' : '' }}
                           class="mt-1 text-red-600 focus:ring-red-500">
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">Reject Application</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Critical issues cannot be resolved - application must be rejected</div>
                    </div>
                </label>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Decision Notes <span class="text-red-500">*</span>
                </label>
                <textarea name="decision_notes" rows="3" 
                          placeholder="Explain your decision and what needs to be addressed..." 
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ $inspection->decision_notes ?? '' }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('consultant.inspections.show', $inspection) }}" 
               class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Cancel
            </a>
            <div class="flex gap-3">
                <button type="submit" 
                        name="save_as_draft" 
                        value="1"
                        @click="saveDraft()"
                        class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                    Save Draft
                </button>
                <button type="submit"
                        class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                    Complete Inspection
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function inspectionForm(existingResults) {
    return {
        completedItems: 0,
        totalItems: {{ $checklistItems->flatten()->count() }},
        hasFailed: false,
        isDraft: false,
        
        init() {
            this.calculateProgress();
            this.checkFailures();
        },
        
        calculateProgress() {
            setTimeout(() => {
                const radios = document.querySelectorAll('input[type="radio"]:checked');
                this.completedItems = radios.length;
                this.checkFailures();
            }, 10);
        },
        
        updateProgress() {
            this.calculateProgress();
        },
        
        checkFailures() {
            const failedRadios = document.querySelectorAll('input[type="radio"][value="fail"]:checked');
            this.hasFailed = failedRadios.length > 0;
        },
        
        saveDraft() {
            this.isDraft = true;
        }
    }
}
</script>
@endpush
@endsection