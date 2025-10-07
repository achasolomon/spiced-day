@extends('layouts.consultant')

@section('title', 'Select Application for Inspection')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('consultant.inspections.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Select Application</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Choose an application to inspect</p>
        </div>
    </div>

    <!-- Applications List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Application #</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($applications as $application)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $application->application_number }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $application->full_name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $application->user->email }}</p>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $application->status == 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $application->status == 'inspection_pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $application->status == 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $application->created_at->format('M j, Y') }}</span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('consultant.inspections.create', ['application_id' => $application->id]) }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Inspect
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection