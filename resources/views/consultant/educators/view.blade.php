@extends('layouts.consultant')

@section('title', 'Educator Profile - ' . $profile->user->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Header with Back Button -->
    <div class="flex items-center gap-4">
        <a href="{{ url()->previous() }}" 
           class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Educator Profile</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $profile->user->name }}</p>
        </div>
        <span class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-lg text-sm font-semibold">
            Consultant View
        </span>
    </div>

    <!-- Profile Completion Banner -->
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-purple-100 text-sm font-medium">Profile Completion</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($profile->completion_percentage, 0) }}%</p>
                @if($profile->is_complete)
                    <p class="text-purple-100 text-sm mt-2">✓ Profile is complete</p>
                @else
                    <p class="text-purple-100 text-sm mt-2">Profile is {{ 100 - $profile->completion_percentage }}% from completion</p>
                @endif
            </div>
            <div class="w-full md:w-64">
                <div class="w-full bg-purple-400/30 rounded-full h-3">
                    <div class="bg-white h-3 rounded-full transition-all" 
                         style="width: {{ $profile->completion_percentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring/Expired Items Alert -->
    @if($profile->expiredItems->count() > 0 || $profile->expiringItems->count() > 0)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">⚠️ Attention Required</h3>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        @if($profile->expiredItems->count() > 0)
                            <p>• {{ $profile->expiredItems->count() }} item(s) have expired</p>
                        @endif
                        @if($profile->expiringItems->count() > 0)
                            <p>• {{ $profile->expiringItems->count() }} item(s) expiring within 30 days</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
           <!-- Core Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Professional Information</h2>
                </div>
                <div x-data="{ activeTab: 'personal' }" class="p-6">
                    <!-- Tabs Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button @click="activeTab = 'personal'" 
                                    :class="activeTab === 'personal' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                Personal Info
                            </button>
                            <button @click="activeTab = 'professional'" 
                                    :class="activeTab === 'professional' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                Professional Details
                            </button>
                            <button @click="activeTab = 'health'" 
                                    :class="activeTab === 'health' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                Health Information
                            </button>
                            <button @click="activeTab = 'emergency'" 
                                    :class="activeTab === 'emergency' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                Emergency Contacts
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="space-y-6">
                        <!-- Personal Information Tab -->
                        <div x-show="activeTab === 'personal'" x-transition>
                            <div class="space-y-6">
                                <!-- Profile Photo -->
                                @if($profile->profile_photo)
                                    <div class="flex items-center gap-4 pb-6 border-b border-gray-200 dark:border-gray-700">
                                        <img src="{{ route('consultant.educators.profile.photo', $profile) }}" 
                                             alt="Profile Photo" 
                                             class="w-24 h-24 rounded-full object-cover border-4 border-purple-100 dark:border-purple-900">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Profile Photo</p>
                                             <p class="text-xs text-gray-500 dark:text-gray-400">Uploaded by educator</p>

                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">First Name</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->first_name ?? 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Name</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->last_name ?? 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Hire</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->date_of_hire?->format('M d, Y') ?? 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">SIN #</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->sin_number ? 'XXX-XXX-' . substr($profile->sin_number, -3) : 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->date_of_birth?->format('M d, Y') ?? 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Marital Status</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->marital_status ?? 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Religious Beliefs</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->religious_beliefs ?? 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Ethnicity/Nationality</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->ethnicity_nationality ?? 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Details Tab -->
                        <div x-show="activeTab === 'professional'" x-transition>
                            <div class="space-y-6">
                                <!-- Bio -->
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Professional Bio</label>
                                    <p class="text-gray-900 dark:text-white mt-1">
                                        {{ $profile->professional_bio ?? 'Not provided' }}
                                    </p>
                                </div>

                                <!-- Operating Hours & Capacity -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Operating Hours</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            @if($profile->operating_hours_start && $profile->operating_hours_end)
                                                {{ $profile->operating_hours_start->format('g:i A') }} - {{ $profile->operating_hours_end->format('g:i A') }}
                                            @else
                                                Not set
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Capacity</label>
                                        <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                            {{ $profile->current_capacity }} / {{ $profile->maximum_capacity ?? 'N/A' }} children
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Available Spots</label>
                                        <p class="text-green-600 dark:text-green-400 font-semibold mt-1">
                                            {{ $profile->available_capacity }} spots
                                        </p>
                                    </div>
                                </div>

                                <!-- Specializations -->
                                @if($profile->specializations && count($profile->specializations) > 0)
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Specializations</label>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($profile->specializations as $specialization)
                                                <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 rounded-full text-sm font-medium">
                                                    {{ $specialization }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Professional Goals -->
                                @if($profile->professional_goals)
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Professional Goals</label>
                                        <p class="text-gray-900 dark:text-white mt-1">
                                            {{ $profile->professional_goals }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Health Information Tab -->
                        <div x-show="activeTab === 'health'" x-transition>
                            <div class="space-y-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Allergies</label>
                                    <p class="text-gray-900 dark:text-white mt-1">
                                        {{ $profile->allergies ?? 'Not provided' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Dietary Restrictions</label>
                                    <p class="text-gray-900 dark:text-white mt-1">
                                        {{ $profile->dietary_restrictions ?? 'Not provided' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Medical Conditions</label>
                                    <p class="text-gray-900 dark:text-white mt-1">
                                        {{ $profile->medical_conditions ?? 'Not provided' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Activity Restrictions</label>
                                    <p class="text-gray-900 dark:text-white mt-1">
                                        {{ $profile->activity_restrictions ?? 'Not provided' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contacts Tab -->
                        <div x-show="activeTab === 'emergency'" x-transition>
                            <div class="space-y-6">
                                <!-- Emergency Contact 1 -->
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4">Emergency Contact (1)</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                                            <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                                @if($profile->emergency_contact_1_first_name || $profile->emergency_contact_1_last_name)
                                                    {{ trim(($profile->emergency_contact_1_first_name ?? '') . ' ' . ($profile->emergency_contact_1_last_name ?? '')) }}
                                                @else
                                                    Not provided
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Relationship</label>
                                            <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                                {{ $profile->emergency_contact_1_relationship ?? 'Not provided' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                            <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                                {{ $profile->emergency_contact_1_phone ?? 'Not provided' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                                            <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                                @if($profile->emergency_contact_1_address_line_1)
                                                    {{ $profile->emergency_contact_1_address_line_1 }}<br>
                                                    @if($profile->emergency_contact_1_city)
                                                        {{ $profile->emergency_contact_1_city }}, 
                                                        {{ $profile->emergency_contact_1_province ?? '' }} 
                                                        {{ $profile->emergency_contact_1_postal_code ?? '' }}
                                                    @endif
                                                @else
                                                    Not provided
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact 2 -->
                                <div>
                                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4">Emergency Contact (2)</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                                            <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                                @if($profile->emergency_contact_2_first_name || $profile->emergency_contact_2_last_name)
                                                    {{ trim(($profile->emergency_contact_2_first_name ?? '') . ' ' . ($profile->emergency_contact_2_last_name ?? '')) }}
                                                @else
                                                    Not provided
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Relationship</label>
                                            <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                                {{ $profile->emergency_contact_2_relationship ?? 'Not provided' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                            <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                                {{ $profile->emergency_contact_2_phone ?? 'Not provided' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                                            <p class="text-gray-900 dark:text-white font-semibold mt-1">
                                                @if($profile->emergency_contact_2_address_line_1)
                                                    {{ $profile->emergency_contact_2_address_line_1 }}<br>
                                                    @if($profile->emergency_contact_2_city)
                                                        {{ $profile->emergency_contact_2_city }}, 
                                                        {{ $profile->emergency_contact_2_province ?? '' }} 
                                                        {{ $profile->emergency_contact_2_postal_code ?? '' }}
                                                    @endif
                                                @else
                                                    Not provided
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Items (Certifications) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Certifications & Documents</h2>
                </div>
                <div class="p-6">
                    @if($profile->activeItems->count() > 0)
                        <div class="space-y-4">
                            @foreach($profile->activeItems as $item)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-start gap-4">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                                            {{ $item->type === 'document' ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                            {{ $item->type === 'text' ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                            {{ $item->type === 'date' ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                            {{ $item->type === 'boolean' ? 'bg-purple-100 dark:bg-purple-900/30' : '' }}">
                                            @if($item->type === 'document')
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif($item->type === 'text')
                                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif($item->type === 'date')
                                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->title }}</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $item->display_value }}
                                            </p>
                                            
                                            @if($item->notes)
                                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                                    {{ $item->notes }}
                                                </p>
                                            @endif

                                            <!-- Expiry Status -->
                                            @if($item->expiry_date)
                                                <div class="mt-2">
                                                    @if($item->is_expired)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                                            Expired: {{ $item->expiry_date->format('M d, Y') }}
                                                        </span>
                                                    @elseif($item->is_expiring_soon)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                                            Expires: {{ $item->expiry_date->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                            Valid until: {{ $item->expiry_date->format('M d, Y') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- View/Download Actions -->
                                        @if($item->type === 'document')
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('consultant.educators.profile.documents.view', [$profile->id, $item->id]) }}" 
                                               target="_blank"
                                               class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                               title="View Document">
                                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('consultant.educators.profile.documents.download', [$profile->id, $item->id]) }}" 
                                               class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                               title="Download">
                                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400">No profile items added yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Application Documents -->
            @if($applicationDocuments->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Application Documents</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Documents uploaded during application process</p>
                </div>
                <div class="p-6 space-y-6">
                    @foreach($applicationDocuments as $applicationId => $documents)
                        @php
                            $application = $documents->first()->application;
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <!-- Application Header -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $application->application_number }}
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $documents->count() }} document(s) • Submitted {{ $application->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('consultant.documents.pending-review', $application) }}" 
                                       class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400 font-medium">
                                        View All →
                                    </a>
                                </div>
                            </div>

                            <!-- Documents List -->
                            <div class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($documents->take(5) as $document)
                                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 flex-1">
                                            <!-- Document Icon -->
                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $document->name }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if($document->documentRequirement)
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $document->documentRequirement->name }}
                                                        </span>
                                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                                    @endif
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ number_format($document->file_size / 1024, 1) }} KB
                                                    </span>
                                                    @if($document->expiry_date)
                                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                                        @if($document->is_expired)
                                                            <span class="text-xs text-red-600 dark:text-red-400 font-semibold">
                                                                Expired
                                                            </span>
                                                        @elseif($document->is_expiring_soon)
                                                            <span class="text-xs text-yellow-600 dark:text-yellow-400 font-semibold">
                                                                Expires Soon
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="flex items-center gap-3">
                                            @if($document->status === 'approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                    Approved
                                                </span>
                                            @elseif($document->status === 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                                    Rejected
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                                    Pending
                                                </span>
                                            @endif

                                            <!-- Actions -->
                                            <div class="flex items-center gap-1">
                                                <a href="{{ route('consultant.educators.profile.documents.view', [$profile, $document]) }}" 
                                                   target="_blank"
                                                   class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                                                   title="View">
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('consultant.educators.profile.documents.download', [$profile, $document]) }}" 
                                                   class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                                                   title="Download">
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Profile Statistics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Profile Items</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $profile->activeItems->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Documents</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $profile->documents->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">App Documents</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $applicationDocuments->flatten()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Expiring Soon</span>
                        <span class="text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ $profile->expiringItems->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Expired</span>
                        <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ $profile->expiredItems->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Applications Card -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Applications</h3>
                @if($applications->count() > 0)
                    <div class="space-y-3">
                        @foreach($applications->take(5) as $application)
                            <a href="{{ route('consultant.applications.show', $application) }}" 
                               class="block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $application->application_number }}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full font-semibold
                                        {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                        {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                        {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                        {{ $application->status === 'under_review' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Submitted {{ $application->created_at->format('M d, Y') }}
                                </p>
                            </a>
                        @endforeach
                        
                        @if($applications->count() > 5)
                            <a href="{{ route('consultant.educators.applications', $profile) }}" 
                               class="block text-center text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400 font-medium py-2">
                                View All Applications ({{ $applications->count() }}) →
                            </a>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                        No applications found
                    </p>
                @endif
            </div>

            <!-- Contact Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Name</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $profile->user->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $profile->user->email }}</p>
                    </div>
                    @if($profile->user->phone)
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Phone</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $profile->user->phone }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if($applications->count() > 0)
                        <a href="{{ route('consultant.applications.show', $applications->first()) }}" 
                           class="block w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg text-center transition-colors">
                            View Latest Application
                        </a>
                    @endif
                    
                    <button onclick="window.print()" 
                            class="block w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-sm font-medium rounded-lg text-center transition-colors">
                        Print Profile
                    </button>
                    
                    <a href="{{ url()->previous() }}" 
                       class="block w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-sm font-medium rounded-lg text-center transition-colors">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Notes Section (Optional) -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-6 border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-yellow-900 dark:text-yellow-100 mb-2">Consultant Notes</h3>
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            This is a read-only view of the educator's profile. To review applications and documents, use the Applications section.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Print-friendly styles
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white;
    }
    
    .dark\:bg-gray-800,
    .dark\:bg-gray-700 {
        background: white !important;
        border: 1px solid #e5e7eb !important;
    }
    
    .dark\:text-white,
    .dark\:text-gray-400 {
        color: #000 !important;
    }
}
</script>
@endpush