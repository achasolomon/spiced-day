{{-- resources/views/applicant/applications/edit.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Edit Application')

@section('content')
<div class="max-w-5xl mx-auto" x-data="applicationWizard()">
    {{-- Progress Bar --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Application</h1>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                Step <span x-text="currentStep"></span> of 5
            </span>
        </div>
        
        <div class="relative">
            <div class="overflow-hidden h-2 text-xs flex rounded-full bg-gray-200 dark:bg-gray-700">
                <div class="transition-all duration-500 ease-out rounded-full" 
                     style="background: linear-gradient(90deg, #553e96 0%, #7c3aed 100%);"
                     :style="`width: ${(currentStep / 5) * 100}%`">
                </div>
            </div>
            
            {{-- Step Indicators --}}
            <div class="flex justify-between mt-4">
                <template x-for="step in 5" :key="step">
                    <div class="flex flex-col items-center" 
                         :class="step <= currentStep ? 'opacity-100' : 'opacity-40'">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold mb-1 transition-all duration-300"
                             :class="step <= currentStep ? 'bg-gradient-to-br from-purple-600 to-purple-800 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'">
                            <span x-text="step"></span>
                        </div>
                        <span class="text-xs text-center text-gray-600 dark:text-gray-400" 
                              x-text="stepTitles[step - 1]"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

<form method="POST" action="{{ route('applicant.applications.update', $application) }}" class="space-y-6" x-ref="applicationForm" novalidate>
    @csrf
    @method('PUT')
    
            {{-- Step 1: Personal Info --}}
        <div x-show="currentStep === 1" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-10"
             x-transition:enter-end="opacity-100 transform translate-x-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Personal Information</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Let's start with your basic details</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="educator_first_name"
                                   x-model="formData.educator_first_name"
                                   required
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="educator_last_name"
                                   x-model="formData.educator_last_name"
                                   required
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email"
                                   x-model="formData.email"
                                   required
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Phone <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   name="phone"
                                   x-model="formData.phone"
                                   required
                                   placeholder="(403) 123-4567"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Street Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="address_line_1"
                                   x-model="formData.address_line_1"
                                   required
                                   placeholder="123 Main Street"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="city"
                                   x-model="formData.city"
                                   required
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Province <span class="text-red-500">*</span>
                            </label>
                            <select name="province"
                                    x-model="formData.province"
                                    required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                <option value="">Select Province</option>
                                <option value="Alberta">Alberta</option>
                                <option value="British Columbia">British Columbia</option>
                                <option value="Manitoba">Manitoba</option>
                                <option value="Saskatchewan">Saskatchewan</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Postal Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="postal_code"
                                   x-model="formData.postal_code"
                                   required
                                   placeholder="T2X 1X1"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Copy Steps 2-5 exactly from create.blade.php --}}
        {{-- I'll include them all below --}}

        {{-- Step 2: Qualifications --}}
        <div x-show="currentStep === 2" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-10"
             x-transition:enter-end="opacity-100 transform translate-x-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Professional Qualifications</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tell us about your certifications</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Childcare Level <span class="text-red-500">*</span>
                                </label>
                                <select name="childcare_level"
                                        x-model="formData.childcare_level"
                                        required
                                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    <option value="">Select Level</option>
                                    <option value="Level 1">Level 1</option>
                                    <option value="Level 2">Level 2</option>
                                    <option value="Level 3">Level 3</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Referred By (Optional)
                                </label>
                                <input type="text" 
                                       name="referred_by"
                                       x-model="formData.referred_by"
                                       placeholder="Educator name"
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <label class="flex items-start space-x-3 p-5 bg-gradient-to-br from-purple-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                <input type="checkbox" 
                                       name="has_criminal_record_check"
                                       x-model="formData.has_criminal_record_check"
                                       class="mt-1 w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">Criminal Record Check</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Clear check within 6 months</div>
                                </div>
                            </label>

                            <label class="flex items-start space-x-3 p-5 bg-gradient-to-br from-green-50 to-teal-50 dark:from-gray-700 dark:to-gray-800 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                <input type="checkbox" 
                                       name="has_first_aid_cpr"
                                       x-model="formData.has_first_aid_cpr"
                                       class="mt-1 w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">First Aid & CPR</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Valid certificate</div>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Languages Spoken <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="languages_spoken"
                                   x-model="formData.languages_spoken"
                                   required
                                   placeholder="English, Spanish, French..."
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Childcare Education & Training <span class="text-red-500">*</span>
                            </label>
                            <textarea name="childcare_education"
                                      x-model="formData.childcare_education"
                                      required
                                      rows="4"
                                      placeholder="Describe your education, certifications, and training..."
                                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Home Details --}}
        <div x-show="currentStep === 3" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-10"
             x-transition:enter-end="opacity-100 transform translate-x-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Home Environment</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tell us about your home</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Home Type <span class="text-red-500">*</span>
                                </label>
                                <select name="home_type"
                                        x-model="formData.home_type"
                                        required
                                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    <option value="">Select Type</option>
                                    <option value="house">House</option>
                                    <option value="duplex">Duplex</option>
                                    <option value="townhouse">Townhouse</option>
                                    <option value="apartment">Apartment</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Ownership <span class="text-red-500">*</span>
                                </label>
                                <select name="home_ownership"
                                        x-model="formData.home_ownership"
                                        required
                                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    <option value="">Select</option>
                                    <option value="own">Own</option>
                                    <option value="rent">Rent</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Number of Residents <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="home_residents_count"
                                   x-model="formData.home_residents_count"
                                   required
                                   min="0"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Resident Details <span class="text-red-500">*</span>
                            </label>
                            <textarea name="home_residents_details"
                                      x-model="formData.home_residents_details"
                                      required
                                      rows="3"
                                      placeholder="Names, ages, occupations..."
                                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none"></textarea>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 p-5 bg-gray-50 dark:bg-gray-700 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                <input type="checkbox" 
                                       name="has_pets"
                                       x-model="formData.has_pets"
                                       class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="font-medium text-gray-900 dark:text-white">Have Pets</span>
                            </label>

                            <label class="flex items-center space-x-3 p-5 bg-gray-50 dark:bg-gray-700 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                <input type="checkbox" 
                                       name="fenced_backyard"
                                       x-model="formData.fenced_backyard"
                                       class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="font-medium text-gray-900 dark:text-white">Fenced Backyard</span>
                            </label>
                        </div>

                        <div x-show="formData.has_pets" class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Pet Details
                            </label>
                            <input type="text" 
                                   name="pets_details"
                                   x-model="formData.pets_details"
                                   placeholder="Type and number of pets"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4: Experience --}}
        <div x-show="currentStep === 4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-10"
             x-transition:enter-end="opacity-100 transform translate-x-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.951 22.951 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Experience & Operation</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Your childcare background</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Current Dayhome Operation <span class="text-red-500">*</span>
                            </label>
                            <textarea name="current_operation_details"
                                      x-model="formData.current_operation_details"
                                      required
                                      rows="4"
                                      placeholder="Are you currently operating? If yes, provide details..."
                                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Smoking Status <span class="text-red-500">*</span>
                            </label>
                            <select name="smoking_status"
                                    x-model="formData.smoking_status"
                                    required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                <option value="">Select</option>
                                <option value="no">No smoking</option>
                                <option value="yes_please_specify">Yes (specify below)</option>
                            </select>
                        </div>

                        <div x-show="formData.smoking_status === 'yes_please_specify'" class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Smoking Details
                            </label>
                            <input type="text" 
                                   name="smoking_details"
                                   x-model="formData.smoking_details"
                                   placeholder="Who smokes?"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Desired Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="desired_start_date"
                                       x-model="formData.desired_start_date"
                                       required
                                       :min="new Date().toISOString().split('T')[0]"
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                            </div>

                            <label class="flex items-center space-x-3 p-5 bg-gray-50 dark:bg-gray-700 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                <input type="checkbox" 
                                       name="comfortable_special_needs"
                                       x-model="formData.comfortable_special_needs"
                                       class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="font-medium text-gray-900 dark:text-white">Comfortable with Special Needs</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 5: Philosophy --}}
        <div x-show="currentStep === 5" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-10"
             x-transition:enter-end="opacity-100 transform translate-x-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Philosophy</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Share your vision and approach</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Why become a dayhome educator? <span class="text-red-500">*</span>
                            </label>
                            <textarea name="motivation"
                                      x-model="formData.motivation"
                                      required
                                      rows="4"
                                      placeholder="Share your passion and motivation..."
                                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Why SPICE'd Dayhome Agency? <span class="text-red-500">*</span>
                            </label>
                            <textarea name="why_spiced"
                                      x-model="formData.why_spiced"
                                      required
                                      rows="3"
                                      placeholder="What attracted you to our agency?"
                                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Early Childhood Education Philosophy <span class="text-red-500">*</span>
                            </label>
                            <textarea name="education_philosophy"
                                      x-model="formData.education_philosophy"
                                      required
                                      rows="4"
                                      placeholder="Describe your educational approach and beliefs..."
                                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Program Planning Process <span class="text-red-500">*</span>
                            </label>
                            <textarea name="program_planning_process"
                                      x-model="formData.program_planning_process"
                                      required
                                      rows="4"
                                      placeholder="How do you plan activities and curriculum?"
                                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       {{-- Navigation Buttons --}}
        <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
            <button type="button"
                    @click="previousStep()"
                    x-show="currentStep > 1"
                    class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                Previous
            </button>
            
            <div class="flex gap-3">
                <button type="button"
                        @click="saveDraft()"
                        class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl font-semibold transition-all">
                    Save Draft
                </button>
                
                <button type="button"
                        @click="nextStep()"
                        x-show="currentStep < 5"
                        class="px-6 py-3 text-white rounded-xl font-semibold transition-all hover:shadow-lg"
                        style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                    Next Step
                </button>
                
                <button type="submit"
                        x-show="currentStep === 5"
                        class="px-8 py-3 text-white rounded-xl font-semibold transition-all hover:shadow-lg"
                        style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                    Submit Application
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('applicationWizard', () => ({
        currentStep: 1,
        stepTitles: ['Personal', 'Qualifications', 'Home', 'Experience', 'Philosophy'],
        formData: {
            educator_first_name: @json($application->educator_first_name ?? ''),
            educator_last_name: @json($application->educator_last_name ?? ''),
            email: @json($application->email ?? ''),
            phone: @json($application->phone ?? ''),
            address_line_1: @json($application->address_line_1 ?? ''),
            city: @json($application->city ?? ''),
            province: @json($application->province ?? ''),
            postal_code: @json($application->postal_code ?? ''),
            childcare_level: @json($application->childcare_level ?? ''),
            referred_by: @json($application->referred_by ?? ''),
            has_criminal_record_check: {{ $application->has_criminal_record_check ? 'true' : 'false' }},
            has_first_aid_cpr: {{ $application->has_first_aid_cpr ? 'true' : 'false' }},
            languages_spoken: @json($application->languages_spoken ?? ''),
            childcare_education: @json($application->childcare_education ?? ''),
            home_type: @json($application->home_type ?? ''),
            home_ownership: @json($application->home_ownership ?? ''),
            home_residents_count: @json($application->home_residents_count ?? ''),
            home_residents_details: @json($application->home_residents_details ?? ''),
            has_pets: {{ $application->has_pets ? 'true' : 'false' }},
            pets_details: @json($application->pets_details ?? ''),
            fenced_backyard: {{ $application->fenced_backyard ? 'true' : 'false' }},
            current_operation_details: @json($application->current_operation_details ?? ''),
            smoking_status: @json($application->smoking_status ?? ''),
            smoking_details: @json($application->smoking_details ?? ''),
            desired_start_date: @json($application->desired_start_date ? $application->desired_start_date->format('Y-m-d') : ''),
            comfortable_special_needs: {{ $application->comfortable_special_needs ? 'true' : 'false' }},
            motivation: @json($application->motivation ?? ''),
            why_spiced: @json($application->why_spiced ?? ''),
            education_philosophy: @json($application->education_philosophy ?? ''),
            program_planning_process: @json($application->program_planning_process ?? '')
        },

        nextStep() {
            if (this.validateCurrentStep()) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        previousStep() {
            this.currentStep--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
     saveDraft() {
        const formData = new FormData(this.$refs.applicationForm);
        formData.append('is_draft', '1');
        
        fetch('{{ route("applicant.applications.update", $application) }}', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Draft saved successfully!');
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } else {
                alert(data.message || 'Failed to save draft');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save draft. Please try again.');
        });
    },
            
        validateCurrentStep() {
            const visibleStep = this.$el.querySelector(`[x-show="currentStep === ${this.currentStep}"]`);
            if (!visibleStep) return true;
            
            const requiredInputs = visibleStep.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            let firstInvalidField = null;
            
            for (let input of requiredInputs) {
                if (!input.value || input.value.trim() === '') {
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = input;
                    }
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            }
            
            if (!isValid && firstInvalidField) {
                firstInvalidField.focus();
                alert('Please fill in all required fields before continuing');
            }
            
            return isValid;
        }
    }))
});
</script>
@endpush