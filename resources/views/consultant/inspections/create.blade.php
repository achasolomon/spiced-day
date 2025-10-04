@extends('layouts.consultant')

@section('title', 'New Inspection')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('consultant.applications.show', $appointment->application ?? request('application_id')) }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">New Inspection</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Complete the inspection checklist</p>
        </div>
    </div>

    <form action="{{ route('consultant.inspections.store') }}" method="POST" enctype="multipart/form-data" x-data="inspectionForm()">
        @csrf
        
        <input type="hidden" name="application_id" value="{{ $appointment->application_id ?? request('application_id') }}">
        <input type="hidden" name="appointment_id" value="{{ $appointment->id ?? '' }}">

        <!-- Inspection Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Inspection Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Inspection Type*</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select type...</option>
                        <option value="initial_inspection">Initial Inspection</option>
                        <option value="second_inspection">Second Inspection</option>
                        <option value="final_inspection">Final Inspection</option>
                        <option value="follow_up_inspection">Follow-up Inspection</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Inspection Date & Time*</label>
                    <input type="datetime-local" name="conducted_at" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes)</label>
                    <input type="number" name="duration" min="30" max="480" value="120" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Weather Conditions</label>
                    <input type="text" name="weather_conditions" placeholder="e.g., Sunny, 20°C" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Inspection Checklist -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Inspection Checklist</h2>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Progress: <span x-text="completedItems"></span> / <span x-text="totalItems"></span>
                </div>
            </div>

            <!-- Safety Category -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Safety & Security
                </h3>
                
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">Fire Extinguisher</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Accessible, charged, and within expiry date</p>
                            </div>
                            <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-semibold px-2 py-1 rounded">Critical</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[fire_extinguisher][status]" value="pass" @click="updateProgress()" class="text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Pass</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[fire_extinguisher][status]" value="fail" @click="updateProgress()" class="text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Fail</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[fire_extinguisher][status]" value="n/a" @click="updateProgress()" class="text-gray-600 focus:ring-gray-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">N/A</span>
                            </label>
                        </div>
                        <input type="hidden" name="checklist_results[fire_extinguisher][is_critical]" value="1">
                        <input type="hidden" name="checklist_results[fire_extinguisher][points_possible]" value="5">
                        <textarea name="checklist_results[fire_extinguisher][notes]" rows="2" placeholder="Add notes..." class="mt-3 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">Smoke Detectors</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Working, tested, and properly located</p>
                            </div>
                            <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-semibold px-2 py-1 rounded">Critical</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[smoke_detectors][status]" value="pass" @click="updateProgress()" class="text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Pass</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[smoke_detectors][status]" value="fail" @click="updateProgress()" class="text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Fail</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[smoke_detectors][status]" value="n/a" @click="updateProgress()" class="text-gray-600 focus:ring-gray-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">N/A</span>
                            </label>
                        </div>
                        <input type="hidden" name="checklist_results[smoke_detectors][is_critical]" value="1">
                        <input type="hidden" name="checklist_results[smoke_detectors][points_possible]" value="5">
                        <textarea name="checklist_results[smoke_detectors][notes]" rows="2" placeholder="Add notes..." class="mt-3 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">Emergency Exits</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Clear, accessible, and marked</p>
                            </div>
                            <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-semibold px-2 py-1 rounded">Critical</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[emergency_exits][status]" value="pass" @click="updateProgress()" class="text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Pass</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[emergency_exits][status]" value="fail" @click="updateProgress()" class="text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Fail</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[emergency_exits][status]" value="n/a" @click="updateProgress()" class="text-gray-600 focus:ring-gray-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">N/A</span>
                            </label>
                        </div>
                        <input type="hidden" name="checklist_results[emergency_exits][is_critical]" value="1">
                        <input type="hidden" name="checklist_results[emergency_exits][points_possible]" value="5">
                        <textarea name="checklist_results[emergency_exits][notes]" rows="2" placeholder="Add notes..." class="mt-3 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                </div>
            </div>

            <!-- Environment Category -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Environment & Cleanliness
                </h3>
                
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">Play Area Safety</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Clean, organized, age-appropriate toys</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[play_area][status]" value="pass" @click="updateProgress()" class="text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Pass</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[play_area][status]" value="fail" @click="updateProgress()" class="text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Fail</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[play_area][status]" value="n/a" @click="updateProgress()" class="text-gray-600 focus:ring-gray-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">N/A</span>
                            </label>
                        </div>
                        <input type="hidden" name="checklist_results[play_area][is_critical]" value="0">
                        <input type="hidden" name="checklist_results[play_area][points_possible]" value="3">
                        <textarea name="checklist_results[play_area][notes]" rows="2" placeholder="Add notes..." class="mt-3 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">Kitchen Hygiene</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Clean surfaces, proper food storage</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[kitchen][status]" value="pass" @click="updateProgress()" class="text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Pass</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[kitchen][status]" value="fail" @click="updateProgress()" class="text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Fail</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="checklist_results[kitchen][status]" value="n/a" @click="updateProgress()" class="text-gray-600 focus:ring-gray-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">N/A</span>
                            </label>
                        </div>
                        <input type="hidden" name="checklist_results[kitchen][is_critical]" value="0">
                        <input type="hidden" name="checklist_results[kitchen][points_possible]" value="3">
                        <textarea name="checklist_results[kitchen][notes]" rows="2" placeholder="Add notes..." class="mt-3 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Inspection Summary</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Overall Observations</label>
                    <textarea name="observations" rows="4" placeholder="Describe overall condition and observations..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consultant Notes</label>
                    <textarea name="consultant_notes" rows="3" placeholder="Internal notes (not visible to applicant)..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('consultant.applications.show', $appointment->application ?? request('application_id')) }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                Complete Inspection
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function inspectionForm() {
    return {
        completedItems: 0,
        totalItems: 5,
        
        updateProgress() {
            setTimeout(() => {
                const radios = document.querySelectorAll('input[type="radio"]:checked');
                this.completedItems = radios.length;
            }, 10);
        }
    }
}
</script>
@endpush
@endsection