@extends('layouts.consultant')

@section('title', 'Reinspect Failed Items')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reinspect Failed Items</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        @if(empty($failedItems))
            <div class="text-center py-8">
                <p class="text-gray-600 dark:text-gray-400">No failed items to reinspect.</p>
                <a href="{{ route('consultant.inspections.show', $inspection) }}" 
                   class="mt-4 inline-block px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                    Back to Inspection
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('consultant.inspections.reinspect.store', $inspection) }}">
                @csrf

                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">
                        Inspection Details
                    </h2>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>Application:</strong> {{ $inspection->application->application_number }}</p>
                        <p><strong>Type:</strong> {{ ucwords(str_replace('_', ' ', $inspection->type)) }}</p>
                        <p><strong>Conducted:</strong> <x-date-display :date="$inspection->conducted_at" format="M d, Y" fallback="TBA" /></p>
                    </div>
                </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Reinspection Form</h2>
                
                <div class="space-y-4">
                    @foreach($failedItems as $itemId)
                        @php 
                            $result = $checklistResults[$itemId] ?? ['status' => 'fail', 'notes' => ''];
                            // Get the actual item title from the result if available, otherwise use itemId
                            $itemTitle = $result['title'] ?? "Item #{$itemId}";
                            $itemDescription = $result['description'] ?? '';
                        @endphp
                        
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <h3 class="font-bold text-red-800 dark:text-red-300 mb-1">{{ $itemTitle }}</h3>
                            
                            @if($itemDescription)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $itemDescription }}</p>
                            @endif
            
                            @if(!empty($result['notes']))
                                <div class="mb-3 p-2 bg-white dark:bg-gray-800 rounded text-sm">
                                    <strong class="text-gray-700 dark:text-gray-300">Previous notes:</strong>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $result['notes'] }}</p>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Status <span class="text-red-600">*</span>
                                    </label>
                                    <select name="items[{{ $itemId }}][status]" 
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500">
                                        <option value="fail" {{ $result['status'] === 'fail' ? 'selected' : '' }}>Fail</option>
                                        <option value="pass" {{ $result['status'] === 'pass' ? 'selected' : '' }}>Pass</option>
                                        <option value="n/a" {{ $result['status'] === 'n/a' ? 'selected' : '' }}>N/A</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Reinspection Notes
                                    </label>
                                    <input type="text" 
                                           name="items[{{ $itemId }}][notes]" 
                                           value="" 
                                           placeholder="Add notes about this reinspection..."
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            
            </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" 
                            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium transition-colors shadow-sm">
                        Complete Reinspection
                    </button>
                    <a href="{{ route('consultant.inspections.show', $inspection) }}" 
                       class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors shadow-sm">
                        Cancel
                    </a>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection