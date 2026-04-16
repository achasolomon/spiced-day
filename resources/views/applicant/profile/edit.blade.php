@extends('layouts.dashboard')

@section('title', 'Edit Educator Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Profile</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Update your professional information</p>
        </div>
        <a href="{{ route('applicant.profile.index') }}" 
           class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
            Cancel
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-sm text-green-800 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-sm text-red-800 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- Edit Form -->
    <form method="POST" action="{{ route('applicant.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Profile Photo -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Profile Photo</h2>
            
            <div class="flex items-center gap-6">
                @if($profile->profile_photo)
                    <img src="{{ Storage::url($profile->profile_photo) }}" 
                         alt="Current Profile Photo" 
                         id="current-photo"
                         class="w-24 h-24 rounded-full object-cover border-4 border-purple-100 dark:border-purple-900">
                @else
                    <div id="current-photo" class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="flex-1">
                    <label for="profile_photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload New Photo
                    </label>
                    <input type="file" 
                           id="profile_photo" 
                           name="profile_photo" 
                           accept="image/*"
                           onchange="previewPhoto(event)"
                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-purple-50 file:text-purple-700
                                  hover:file:bg-purple-100
                                  dark:file:bg-purple-900/30 dark:file:text-purple-300
                                  dark:hover:file:bg-purple-900/50">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                    @error('profile_photo')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

                <!-- Educator Profile Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Educator Profile</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="first_name" name="first_name" required
                           value="{{ old('first_name', $profile->first_name ?? $application->educator_first_name ?? '') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('first_name')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="last_name" name="last_name" required
                           value="{{ old('last_name', $profile->last_name ?? $application->educator_last_name ?? '') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('last_name')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_hire" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date of Hire
                    </label>
                    <input type="date" id="date_of_hire" name="date_of_hire"
                           value="{{ old('date_of_hire', $profile->date_of_hire?->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('date_of_hire')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sin_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        SIN #
                    </label>
                    <input type="text" id="sin_number" name="sin_number"
                           value="{{ old('sin_number', $profile->sin_number) }}"
                           placeholder="XXX-XXX-XXX"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('sin_number')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Birthday
                    </label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                           value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('date_of_birth')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Marital Status
                    </label>
                    <select id="marital_status" name="marital_status"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select...</option>
                        <option value="Single" {{ old('marital_status', $profile->marital_status) === 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ old('marital_status', $profile->marital_status) === 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Divorced" {{ old('marital_status', $profile->marital_status) === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="Widowed" {{ old('marital_status', $profile->marital_status) === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="Common Law" {{ old('marital_status', $profile->marital_status) === 'Common Law' ? 'selected' : '' }}>Common Law</option>
                        <option value="Other" {{ old('marital_status', $profile->marital_status) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('marital_status')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="religious_beliefs" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Religious Beliefs?
                    </label>
                    <input type="text" id="religious_beliefs" name="religious_beliefs"
                           value="{{ old('religious_beliefs', $profile->religious_beliefs) }}"
                           placeholder="e.g., Hindu, Christian, Muslim, None"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('religious_beliefs')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ethnicity_nationality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ethnicity/Nationality
                    </label>
                    <input type="text" id="ethnicity_nationality" name="ethnicity_nationality"
                           value="{{ old('ethnicity_nationality', $profile->ethnicity_nationality) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('ethnicity_nationality')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Educator's Health Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Educator's Health</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="allergies" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        1) Do you have any allergies? If so, please list.
                    </label>
                    <textarea id="allergies" name="allergies" rows="2" placeholder="No"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">{{ old('allergies', $profile->allergies) }}</textarea>
                    @error('allergies')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dietary_restrictions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        2) Dietary restrictions?
                    </label>
                    <textarea id="dietary_restrictions" name="dietary_restrictions" rows="2" placeholder="None"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">{{ old('dietary_restrictions', $profile->dietary_restrictions) }}</textarea>
                    @error('dietary_restrictions')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="medical_conditions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        3) Do you have any medical conditions we should be aware of? Any speech, hearing, visual or mental health concerns?
                    </label>
                    <textarea id="medical_conditions" name="medical_conditions" rows="3" placeholder="None"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">{{ old('medical_conditions', $profile->medical_conditions) }}</textarea>
                    @error('medical_conditions')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="activity_restrictions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        4) Would there be any restrictions to do activities? (Eg. "Have asthma, can't run for too long")
                    </label>
                    <textarea id="activity_restrictions" name="activity_restrictions" rows="2" placeholder="No"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">{{ old('activity_restrictions', $profile->activity_restrictions) }}</textarea>
                    @error('activity_restrictions')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Emergency Contacts Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Emergency Contacts</h2>
            
            <!-- Emergency Contact 1 -->
            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact (1)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="emergency_contact_1_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                        <input type="text" id="emergency_contact_1_first_name" name="emergency_contact_1_first_name"
                               value="{{ old('emergency_contact_1_first_name', $profile->emergency_contact_1_first_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_1_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                        <input type="text" id="emergency_contact_1_last_name" name="emergency_contact_1_last_name"
                               value="{{ old('emergency_contact_1_last_name', $profile->emergency_contact_1_last_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_1_relationship" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Relationship</label>
                        <input type="text" id="emergency_contact_1_relationship" name="emergency_contact_1_relationship"
                               value="{{ old('emergency_contact_1_relationship', $profile->emergency_contact_1_relationship) }}"
                               placeholder="e.g., Spouse, Parent, Friend"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_1_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                        <input type="tel" id="emergency_contact_1_phone" name="emergency_contact_1_phone"
                               value="{{ old('emergency_contact_1_phone', $profile->emergency_contact_1_phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_1_address_line_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address Line 1</label>
                        <input type="text" id="emergency_contact_1_address_line_1" name="emergency_contact_1_address_line_1"
                               value="{{ old('emergency_contact_1_address_line_1', $profile->emergency_contact_1_address_line_1) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_1_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <input type="text" id="emergency_contact_1_city" name="emergency_contact_1_city"
                               value="{{ old('emergency_contact_1_city', $profile->emergency_contact_1_city) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_1_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State/Province / Region</label>
                        <input type="text" id="emergency_contact_1_province" name="emergency_contact_1_province"
                               value="{{ old('emergency_contact_1_province', $profile->emergency_contact_1_province) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_1_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal / Zip Code</label>
                        <input type="text" id="emergency_contact_1_postal_code" name="emergency_contact_1_postal_code"
                               value="{{ old('emergency_contact_1_postal_code', $profile->emergency_contact_1_postal_code) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Emergency Contact 2 -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact (2)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="emergency_contact_2_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                        <input type="text" id="emergency_contact_2_first_name" name="emergency_contact_2_first_name"
                               value="{{ old('emergency_contact_2_first_name', $profile->emergency_contact_2_first_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_2_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                        <input type="text" id="emergency_contact_2_last_name" name="emergency_contact_2_last_name"
                               value="{{ old('emergency_contact_2_last_name', $profile->emergency_contact_2_last_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_2_relationship" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Relationship</label>
                        <input type="text" id="emergency_contact_2_relationship" name="emergency_contact_2_relationship"
                               value="{{ old('emergency_contact_2_relationship', $profile->emergency_contact_2_relationship) }}"
                               placeholder="e.g., Spouse, Parent, Friend"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_2_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                        <input type="tel" id="emergency_contact_2_phone" name="emergency_contact_2_phone"
                               value="{{ old('emergency_contact_2_phone', $profile->emergency_contact_2_phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_2_address_line_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address Line 1</label>
                        <input type="text" id="emergency_contact_2_address_line_1" name="emergency_contact_2_address_line_1"
                               value="{{ old('emergency_contact_2_address_line_1', $profile->emergency_contact_2_address_line_1) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_2_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <input type="text" id="emergency_contact_2_city" name="emergency_contact_2_city"
                               value="{{ old('emergency_contact_2_city', $profile->emergency_contact_2_city) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_2_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State/Province / Region</label>
                        <input type="text" id="emergency_contact_2_province" name="emergency_contact_2_province"
                               value="{{ old('emergency_contact_2_province', $profile->emergency_contact_2_province) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="emergency_contact_2_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal / Zip Code</label>
                        <input type="text" id="emergency_contact_2_postal_code" name="emergency_contact_2_postal_code"
                               value="{{ old('emergency_contact_2_postal_code', $profile->emergency_contact_2_postal_code) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- Operating Hours & Capacity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Operating Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="operating_hours_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opening Time
                    </label>
                    <input type="time" 
                           id="operating_hours_start" 
                           name="operating_hours_start" 
                           value="{{ old('operating_hours_start', $profile->operating_hours_start?->format('H:i')) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('operating_hours_start')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="operating_hours_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Closing Time
                    </label>
                    <input type="time" 
                           id="operating_hours_end" 
                           name="operating_hours_end" 
                           value="{{ old('operating_hours_end', $profile->operating_hours_end?->format('H:i')) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('operating_hours_end')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="current_capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Current Capacity (Children Enrolled)
                    </label>
                    <input type="number" 
                           id="current_capacity" 
                           name="current_capacity" 
                           min="0"
                           value="{{ old('current_capacity', $profile->current_capacity) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('current_capacity')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="maximum_capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Maximum Capacity (Licensed For)
                    </label>
                    <input type="number" 
                           id="maximum_capacity" 
                           name="maximum_capacity" 
                           min="0"
                           value="{{ old('maximum_capacity', $profile->maximum_capacity) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    @error('maximum_capacity')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Specializations -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Specializations</h2>
            
            <div x-data="specializationsManager()" class="space-y-4">
                <div class="space-y-2" x-ref="specializationsList">
                    <template x-for="(spec, index) in specializations" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   :name="'specializations[' + index + ']'"
                                   x-model="specializations[index]"
                                   placeholder="e.g., Infant Care, Montessori, Special Needs"
                                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                            <button type="button" 
                                    @click="removeSpecialization(index)"
                                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" 
                        @click="addSpecialization()"
                        class="px-4 py-2 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors text-sm font-medium">
                    + Add Specialization
                </button>
            </div>

            @error('specializations')
                <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('applicant.profile.index') }}" 
               class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                Save Profile
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Preview photo before upload
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('current-photo');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-24 h-24 rounded-full object-cover border-4 border-purple-100 dark:border-purple-900">`;
        };
        reader.readAsDataURL(file);
    }
}

// Specializations manager
function specializationsManager() {
    return {
        specializations: @json(old('specializations', $profile->specializations ?? [])),
        
        addSpecialization() {
            this.specializations.push('');
        },
        
        removeSpecialization(index) {
            this.specializations.splice(index, 1);
        }
    };
}
</script>
@endpush