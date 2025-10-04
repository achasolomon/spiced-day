@extends('layouts.consultant')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Consultant Profile</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your profile and preferences</p>
    </div>

    <form action="{{ route('consultant.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Personal Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Personal Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                        <input type="text" value="{{ auth()->user()->name }}" disabled class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" disabled class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work Phone</label>
                        <input type="tel" name="work_phone" value="{{ auth()->user()->consultant->work_phone ?? '' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee ID</label>
                        <input type="text" value="{{ auth()->user()->consultant->employee_id ?? 'N/A' }}" disabled class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Professional Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                        <textarea name="bio" rows="4" placeholder="Tell us about your experience..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ auth()->user()->consultant->bio ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specializations</label>
                        <input type="text" name="specializations" value="{{ is_array(auth()->user()->consultant->specializations ?? null) ? implode(', ', auth()->user()->consultant->specializations) : '' }}" placeholder="e.g., Infant care, Special needs, Bilingual education" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Separate with commas</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Languages Spoken</label>
                        <input type="text" name="languages" value="{{ is_array(auth()->user()->consultant->languages ?? null) ? implode(', ', auth()->user()->consultant->languages) : '' }}" placeholder="e.g., English, Spanish, French" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Separate with commas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workload Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Workload Settings</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Concurrent Applications</label>
                        <input type="number" name="max_concurrent_applications" min="1" max="50" value="{{ auth()->user()->consultant->max_concurrent_applications ?? 10 }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum number of active applications you can handle</p>
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="accepts_new_applications" value="1" {{ (auth()->user()->consultant->accepts_new_applications ?? true) ? 'checked' : '' }} class="w-5 h-5 text-orange-600 focus:ring-orange-500 rounded">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white block">Accept New Applications</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Uncheck if you're at capacity</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Emergency Contact</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ auth()->user()->consultant->emergency_contact_name ?? '' }}" placeholder="Full name" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone" value="{{ auth()->user()->consultant->emergency_contact_phone ?? '' }}" placeholder="+1 (555) 000-0000" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Stats (Read-only) -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-6">Your Performance</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-orange-100 text-sm">Applications Handled</p>
                    <p class="text-3xl font-bold mt-1">{{ auth()->user()->consultant->total_applications_handled ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-orange-100 text-sm">Inspections Done</p>
                    <p class="text-3xl font-bold mt-1">{{ auth()->user()->consultant->completed_inspections ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-orange-100 text-sm">Avg. Completion</p>
                    <p class="text-3xl font-bold mt-1">{{ auth()->user()->consultant->average_completion_time ?? 0 }}<span class="text-lg">d</span></p>
                </div>
                <div>
                    <p class="text-orange-100 text-sm">Approval Rate</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format(auth()->user()->consultant->approval_rate ?? 0, 0) }}%</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('consultant.dashboard') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection