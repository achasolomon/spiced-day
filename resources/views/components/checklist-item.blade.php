@props(['name', 'label', 'description', 'critical' => false])

<div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
            <h4 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                {{ $label }}
                @if($critical)
                    <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-semibold px-2 py-1 rounded">
                        Critical
                    </span>
                @endif
            </h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $description }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-4 mb-3">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" 
                   name="checklist_results[{{ $name }}][status]" 
                   value="pass" 
                   @click="updateProgress()"
                   class="text-green-600 focus:ring-green-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">✓ Pass</span>
        </label>
        
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" 
                   name="checklist_results[{{ $name }}][status]" 
                   value="fail" 
                   @click="updateProgress()"
                   class="text-red-600 focus:ring-red-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">✗ Fail</span>
        </label>
        
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" 
                   name="checklist_results[{{ $name }}][status]" 
                   value="n/a" 
                   @click="updateProgress()"
                   class="text-gray-600 focus:ring-gray-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">N/A</span>
        </label>
    </div>
    
    <input type="hidden" name="checklist_results[{{ $name }}][is_critical]" value="{{ $critical ? '1' : '0' }}">
    <input type="hidden" name="checklist_results[{{ $name }}][points_possible]" value="{{ $critical ? '5' : '3' }}">
    
    <div x-data="{ showNotes: false }">
        <button type="button" 
                @click="showNotes = !showNotes"
                class="text-sm text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span x-text="showNotes ? 'Hide Notes' : 'Add Notes'"></span>
        </button>
        
        <div x-show="showNotes" 
             x-transition
             class="mt-3">
            <textarea name="checklist_results[{{ $name }}][notes]" 
                      rows="2" 
                      placeholder="Add specific observations, deficiencies, or recommendations..."
                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"></textarea>
        </div>
    </div>
</div>