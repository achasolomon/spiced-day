@extends('layouts.consultant')

@section('title', 'Inspections')

@section('content')
<div class="space-y-6">
    <!-- Header -->
   <div class="flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Inspections</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage your inspection history</p>
    </div>
    <a href="{{ route('consultant.inspections.create') }}" class="px-6 py-3 bg-purple-800 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        New Inspection
    </a>
</div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('consultant.inspections.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Inspection Type</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    <option value="initial_inspection" {{ request('type') == 'initial_inspection' ? 'selected' : '' }}>Initial Inspection</option>
                    <option value="second_inspection" {{ request('type') == 'second_inspection' ? 'selected' : '' }}>Second Inspection</option>
                    <option value="final_inspection" {{ request('type') == 'final_inspection' ? 'selected' : '' }}>Final Inspection</option>
                    <option value="follow_up_inspection" {{ request('type') == 'follow_up_inspection' ? 'selected' : '' }}>Follow-up</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Result</label>
                <select name="result" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Results</option>
                    <option value="pass" {{ request('result') == 'pass' ? 'selected' : '' }}>Pass</option>
                    <option value="conditional_pass" {{ request('result') == 'conditional_pass' ? 'selected' : '' }}>Conditional Pass</option>
                    <option value="fail" {{ request('result') == 'fail' ? 'selected' : '' }}>Fail</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('consultant.inspections.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Inspections List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Inspection #</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Result</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($inspections as $inspection)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $inspection->inspection_number }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $inspection->application->full_name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $inspection->application->application_number }}</p>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $inspection->type) }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-white"><x-date-display :date="$inspection->conducted_at" format="M j, Y" fallback="TBA" /></span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $inspection->overall_result == 'pass' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $inspection->overall_result == 'conditional_pass' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $inspection->overall_result == 'fail' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $inspection->overall_result == 'incomplete' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $inspection->overall_result ?? 'pending')) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $inspection->overall_score ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ number_format($inspection->overall_score ?? 0, 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('consultant.inspections.show', $inspection) }}"
                                   class="px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-700 rounded-lg">
                                   View
                                </a>
                        
                                @if($inspection->overall_result === 'fail')
                                    <a href="{{ route('consultant.inspections.reinspect', $inspection) }}"
                                       class="px-3 py-1.5 text-sm bg-yellow-600 text-white rounded-lg">
                                       Reinspect
                                    </a>
                                @endif
                            </div>
                        </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No inspections found</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($inspections->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection