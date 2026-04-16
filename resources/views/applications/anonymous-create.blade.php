<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apply for Dayhome License - SPICE'd</title>

    <link rel="icon" type="image/jpg" href="{{ asset('logo.jpeg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        'brand': {
                            'lavender': '#e3d4fc',
                            'cyan': '#d4f6ff', 
                            'purple': '#553e96'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <!-- Main Container -->
    <div class="min-h-screen flex items-start justify-center p-4 sm:p-8 lg:p-12">
        <div class="w-full max-w-4xl" x-data="applicationWizard()">
            
            <!-- Logo and Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center items-center mb-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="SPICE'd" class="w-10 h-10 sm:w-12 sm:h-12 object-contain">
                    </div>
                    <div class="text-left">
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight">
                            <span style="color: #553e96;">SPICE'd</span>
                            <span class="text-gray-800"> Dayhome</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-gray-600">Dayhome Approval Application</p>
                    </div>
                </div>

                <!-- Helper Text -->
                <div class="max-w-2xl mx-auto mb-6">
                    <p class="text-sm text-gray-600">
                        Complete all 5 steps to submit your application. Already have an account? 
                        <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: #553e96;">Login here</a>
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Application Progress</h2>
                    <span class="text-sm font-medium text-gray-600">
                        Step <span x-text="currentStep"></span> of 5
                    </span>
                </div>
                
                <div class="relative">
                    <div class="overflow-hidden h-2 text-xs flex rounded-full bg-gray-200">
                        <div class="transition-all duration-500 ease-out rounded-full" 
                             style="background: linear-gradient(90deg, #553e96 0%, #7c3aed 100%);"
                             :style="`width: ${(currentStep / 5) * 100}%`">
                        </div>
                    </div>
                    
                    <!-- Step Indicators -->
                    <div class="flex justify-between mt-4">
                        <template x-for="step in 5" :key="step">
                            <div class="flex flex-col items-center" 
                                 :class="step <= currentStep ? 'opacity-100' : 'opacity-40'">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold mb-1 transition-all duration-300"
                                     :class="step <= currentStep ? 'bg-gradient-to-br from-purple-600 to-purple-800 text-white' : 'bg-gray-300 text-gray-600'">
                                    <span x-text="step"></span>
                                </div>
                                <span class="text-xs text-center text-gray-600 hidden sm:block" 
                                      x-text="stepTitles[step - 1]"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('applications.anonymous.store') }}" x-ref="applicationForm" novalidate>
                @csrf

                {{-- Step 1: Personal Info --}}
                    <div x-show="currentStep === 1" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-10"
                         x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="p-8">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">Personal Information</h2>
                                        <p class="text-sm text-gray-600">Let's start with your basic details</p>
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            First Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="educator_first_name"
                                               x-model="formData.educator_first_name"
                                               required
                                               value="{{ old('educator_first_name') }}"
                                               class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Last Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="educator_last_name"
                                               x-model="formData.educator_last_name"
                                               required
                                               value="{{ old('educator_last_name') }}"
                                               class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" 
                                               name="email"
                                               x-model="formData.email"
                                               required
                                               value="{{ old('email') }}"
                                               class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Phone <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" 
                                               name="phone"
                                               x-model="formData.phone"
                                               required
                                               value="{{ old('phone') }}"
                                               placeholder="(403) 123-4567"
                                               class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="md:col-span-2 space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Street Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="address_line_1"
                                               x-model="formData.address_line_1"
                                               required
                                               value="{{ old('address_line_1') }}"
                                               placeholder="123 Main Street"
                                               class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            City <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="city"
                                               x-model="formData.city"
                                               required
                                               value="{{ old('city') }}"
                                               class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Province <span class="text-red-500">*</span>
                                        </label>
                                        <select name="province"
                                                x-model="formData.province"
                                                required
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                            <option value="">Select Province</option>
                                            <option value="Alberta" {{ old('province') == 'Alberta' ? 'selected' : '' }}>Alberta</option>
                                            <option value="British Columbia" {{ old('province') == 'British Columbia' ? 'selected' : '' }}>British Columbia</option>
                                            <option value="Manitoba" {{ old('province') == 'Manitoba' ? 'selected' : '' }}>Manitoba</option>
                                            <option value="Saskatchewan" {{ old('province') == 'Saskatchewan' ? 'selected' : '' }}>Saskatchewan</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Postal Code <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="postal_code"
                                               x-model="formData.postal_code"
                                               required
                                               value="{{ old('postal_code') }}"
                                               placeholder="T2X 1X1"
                                               class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Qualifications --}}
                    <div x-show="currentStep === 2" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-10"
                         x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="p-8">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">Professional Qualifications</h2>
                                        <p class="text-sm text-gray-600">Tell us about your certifications</p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-semibold text-gray-700">
                                                Childcare Level <span class="text-red-500">*</span>
                                            </label>
                                            <select name="childcare_level"
                                                    x-model="formData.childcare_level"
                                                    required
                                                    class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                                <option value="">Select Level</option>
                                                <option value="Level 1" {{ old('childcare_level') == 'Level 1' ? 'selected' : '' }}>Level 1</option>
                                                <option value="Level 2" {{ old('childcare_level') == 'Level 2' ? 'selected' : '' }}>Level 2</option>
                                                <option value="Level 3" {{ old('childcare_level') == 'Level 3' ? 'selected' : '' }}>Level 3</option>
                                                <option value="Other" {{ old('childcare_level') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-semibold text-gray-700">
                                                Referred By (Optional)
                                            </label>
                                            <input type="text" 
                                                   name="referred_by"
                                                   x-model="formData.referred_by"
                                                   value="{{ old('referred_by') }}"
                                                   placeholder="Educator name"
                                                   class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                        </div>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-4">
                                        <label class="flex items-start space-x-3 p-5 bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                            <input type="checkbox" 
                                                   name="has_criminal_record_check"
                                                   x-model="formData.has_criminal_record_check"
                                                   {{ old('has_criminal_record_check') ? 'checked' : '' }}
                                                   class="mt-1 w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                            <div>
                                                <div class="font-semibold text-gray-900">Criminal Record Check</div>
                                                <div class="text-sm text-gray-600">Clear check within 6 months</div>
                                            </div>
                                        </label>

                                        <label class="flex items-start space-x-3 p-5 bg-gradient-to-br from-green-50 to-teal-50 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                            <input type="checkbox" 
                                                   name="has_first_aid_cpr"
                                                   x-model="formData.has_first_aid_cpr"
                                                   {{ old('has_first_aid_cpr') ? 'checked' : '' }}
                                                   class="mt-1 w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                            <div>
                                                <div class="font-semibold text-gray-900">First Aid & CPR</div>
                                                <div class="text-sm text-gray-600">Valid certificate</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Languages Spoken <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="languages_spoken"
                                               x-model="formData.languages_spoken"
                                               required
                                               value="{{ old('languages_spoken') }}"
                                               placeholder="English, Spanish, French..."
                                               class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="space-y-2 md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Childcare Education & Training <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="childcare_education"
                                                  x-model="formData.childcare_education"
                                                  required
                                                  rows="4"
                                                  placeholder="Describe your education, certifications, and training..."
                                                  class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all resize-y">{{ old('childcare_education') }}</textarea>
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
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="p-8">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">Home Environment</h2>
                                        <p class="text-sm text-gray-600">Tell us about your home</p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-semibold text-gray-700">
                                                Home Type <span class="text-red-500">*</span>
                                            </label>
                                            <select name="home_type"
                                                    x-model="formData.home_type"
                                                    required
                                                    class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                                <option value="">Select Type</option>
                                                <option value="house" {{ old('home_type') == 'house' ? 'selected' : '' }}>House</option>
                                                <option value="duplex" {{ old('home_type') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                                                <option value="townhouse" {{ old('home_type') == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                                            </select>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-semibold text-gray-700">
                                                Ownership <span class="text-red-500">*</span>
                                            </label>
                                            <select name="home_ownership"
                                                    x-model="formData.home_ownership"
                                                    required
                                                    class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                                <option value="">Select</option>
                                                <option value="own" {{ old('home_ownership') == 'own' ? 'selected' : '' }}>Own</option>
                                                <option value="rent" {{ old('home_ownership') == 'rent' ? 'selected' : '' }}>Rent</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Number of Residents <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" 
                                            name="home_residents_count"
                                            x-model="formData.home_residents_count"
                                            required
                                            min="1"
                                            value="{{ old('home_residents_count') }}"
                                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Resident Details <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="home_residents_details"
                                                x-model="formData.home_residents_details"
                                                required
                                                rows="3"
                                                placeholder="Names, ages, occupations..."
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none">{{ old('home_residents_details') }}</textarea>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-4">
                                        <label class="flex items-center space-x-3 p-5 bg-gray-50 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                            <input type="checkbox" 
                                                name="has_pets"
                                                x-model="formData.has_pets"
                                                {{ old('has_pets') ? 'checked' : '' }}
                                                class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                            <span class="font-medium text-gray-900">Have Pets</span>
                                        </label>

                                        <label class="flex items-center space-x-3 p-5 bg-gray-50 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                            <input type="checkbox" 
                                                name="fenced_backyard"
                                                x-model="formData.fenced_backyard"
                                                {{ old('fenced_backyard') ? 'checked' : '' }}
                                                class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                            <span class="font-medium text-gray-900">Fenced Backyard</span>
                                        </label>
                                    </div>

                                    <div x-show="formData.has_pets" class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Pet Details
                                        </label>
                                        <input type="text" 
                                            name="pets_details"
                                            x-model="formData.pets_details"
                                            value="{{ old('pets_details') }}"
                                            placeholder="Type and number of pets"
                                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Smoking Status <span class="text-red-500">*</span>
                                        </label>
                                        <select name="smoking_status"
                                                x-model="formData.smoking_status"
                                                required
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                            <option value="">Select</option>
                                            <option value="no" {{ old('smoking_status') == 'no' ? 'selected' : '' }}>No smoking</option>
                                            <option value="yes_please_specify" {{ old('smoking_status') == 'yes_please_specify' ? 'selected' : '' }}>Yes (specify below)</option>
                                        </select>
                                    </div>

                                    <div x-show="formData.smoking_status === 'yes_please_specify'" class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Smoking Details
                                        </label>
                                        <input type="text" 
                                            name="smoking_details"
                                            x-model="formData.smoking_details"
                                            value="{{ old('smoking_details') }}"
                                            placeholder="Who smokes and where?"
                                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Step 4: Experience & Operation --}}
                    <div x-show="currentStep === 4" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-10"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="p-8">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.951 22.951 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">Experience & Operation</h2>
                                        <p class="text-sm text-gray-600">Your childcare background</p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Current Dayhome Operation
                                        </label>
                                        <textarea name="current_operation_details"
                                                x-model="formData.current_operation_details"
                                                rows="4"
                                                placeholder="Are you currently operating? If yes, provide details..."
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none">{{ old('current_operation_details') }}</textarea>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-semibold text-gray-700">
                                                Desired Start Date <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" 
                                                name="desired_start_date"
                                                x-model="formData.desired_start_date"
                                                required
                                                value="{{ old('desired_start_date') }}"
                                                :min="new Date().toISOString().split('T')[0]"
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all">
                                        </div>

                                        <div class="flex items-end">
                                            <label class="flex items-center space-x-3 p-5 bg-gray-50 rounded-xl cursor-pointer hover:shadow-md transition-all w-full">
                                                <input type="checkbox" 
                                                    name="comfortable_special_needs"
                                                    x-model="formData.comfortable_special_needs"
                                                    {{ old('comfortable_special_needs') ? 'checked' : '' }}
                                                    class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                                <span class="font-medium text-gray-900">Comfortable with Special Needs</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-4">
                                        <label class="flex items-center space-x-3 p-5 bg-gray-50 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                            <input type="checkbox" 
                                                name="currently_operating"
                                                x-model="formData.currently_operating"
                                                {{ old('currently_operating') ? 'checked' : '' }}
                                                class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                            <span class="font-medium text-gray-900">Currently Operating Dayhome</span>
                                        </label>

                                        <label class="flex items-center space-x-3 p-5 bg-gray-50 rounded-xl cursor-pointer hover:shadow-md transition-all">
                                            <input type="checkbox" 
                                                name="evening_overnight_care"
                                                x-model="formData.evening_overnight_care"
                                                {{ old('evening_overnight_care') ? 'checked' : '' }}
                                                class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                            <span class="font-medium text-gray-900">Willing to provide Evening/Overnight Care</span>
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
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="p-8">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">Your Philosophy</h2>
                                        <p class="text-sm text-gray-600">Share your vision and approach</p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Why become a dayhome educator? <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="motivation"
                                                x-model="formData.motivation"
                                                required
                                                rows="4"
                                                placeholder="Share your passion and motivation..."
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none">{{ old('motivation') }}</textarea>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Why SPICE'd Dayhome Agency? <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="why_spiced"
                                                x-model="formData.why_spiced"
                                                required
                                                rows="3"
                                                placeholder="What attracted you to our agency?"
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none">{{ old('why_spiced') }}</textarea>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Early Childhood Education Philosophy <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="education_philosophy"
                                                x-model="formData.education_philosophy"
                                                required
                                                rows="4"
                                                placeholder="Describe your educational approach and beliefs..."
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none">{{ old('education_philosophy') }}</textarea>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Program Planning Process <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="program_planning_process"
                                                x-model="formData.program_planning_process"
                                                required
                                                rows="4"
                                                placeholder="How do you plan activities and curriculum?"
                                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all resize-none">{{ old('program_planning_process') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between bg-white rounded-2xl shadow-xl border border-gray-200 p-4 sm:p-6 mt-6">
                    <button type="button"
                            @click="previousStep()"
                            x-show="currentStep > 1"
                            class="px-4 sm:px-6 py-2 sm:py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all text-sm sm:text-base">
                        Previous
                    </button>
                    
                    <div class="flex gap-2 sm:gap-3 ml-auto">
                        <button type="button"
                                @click="nextStep()"
                                x-show="currentStep < 5"
                                class="px-4 sm:px-6 py-2 sm:py-3 text-white rounded-xl font-semibold transition-all hover:shadow-lg text-sm sm:text-base"
                                style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                Next Step
                        </button>
                        
                        <button type="submit"
                                x-show="currentStep === 5"
                                class="px-6 sm:px-8 py-2 sm:py-3 text-white rounded-xl font-semibold transition-all hover:shadow-lg text-sm sm:text-base"
                                style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                            Submit Application
                        </button>
                    </div>
                </div>
            </form>

            <!-- Footer Info -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    Need help? Email us at <a href="mailto:executive@spicedchildcare.com" class="font-semibold hover:underline" style="color: #553e96;">executive@spicedchildcare.com</a>
                </p>
            </div>
        </div>
    </div>


<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('applicationWizard', () => ({
        currentStep: 1,
        stepTitles: ['Personal', 'Qualifications', 'Home', 'Experience', 'Philosophy'],
        formData: {
            // Step 1
            educator_first_name: @json(old('educator_first_name', '')),
            educator_last_name: @json(old('educator_last_name', '')),
            email: @json(old('email', '')),
            phone: @json(old('phone', '')),
            address_line_1: @json(old('address_line_1', '')),
            city: @json(old('city', '')),
            province: @json(old('province', 'Alberta')),
            postal_code: @json(old('postal_code', '')),
            // Step 2
            childcare_level: @json(old('childcare_level', '')),
            referred_by: @json(old('referred_by', '')),
            has_criminal_record_check: {{ old('has_criminal_record_check') ? 'true' : 'false' }},
            has_first_aid_cpr: {{ old('has_first_aid_cpr') ? 'true' : 'false' }},
            languages_spoken: @json(old('languages_spoken', '')),
            childcare_education: @json(old('childcare_education', '')),
            // Step 3
            home_type: @json(old('home_type', '')),
            home_ownership: @json(old('home_ownership', '')),
            home_residents_count: @json(old('home_residents_count', '')),
            home_residents_details: @json(old('home_residents_details', '')),
            has_pets: {{ old('has_pets') ? 'true' : 'false' }},
            pets_details: @json(old('pets_details', '')),
            fenced_backyard: {{ old('fenced_backyard') ? 'true' : 'false' }},
            smoking_status: @json(old('smoking_status', '')),
            smoking_details: @json(old('smoking_details', '')),
            // Step 4
            current_operation_details: @json(old('current_operation_details', '')),
            desired_start_date: @json(old('desired_start_date', '')),
            comfortable_special_needs: {{ old('comfortable_special_needs') ? 'true' : 'false' }},
            currently_operating: {{ old('currently_operating') ? 'true' : 'false' }},
            evening_overnight_care: {{ old('evening_overnight_care') ? 'true' : 'false' }},
            // Step 5
            motivation: @json(old('motivation', '')),
            why_spiced: @json(old('why_spiced', '')),
            education_philosophy: @json(old('education_philosophy', '')),
            program_planning_process: @json(old('program_planning_process', ''))
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

        validateCurrentStep() {
            const form = this.$refs.applicationForm;
            
            // Find the current step div that is visible
            const allSteps = form.querySelectorAll('[x-show^="currentStep"]');
            let currentStepElement = null;
            
            // Find which step is currently visible
            for (let stepDiv of allSteps) {
                const display = window.getComputedStyle(stepDiv).display;
                if (display !== 'none') {
                    currentStepElement = stepDiv;
                    break;
                }
            }
            
            if (!currentStepElement) {
                console.log('No current step element found');
                return true;
            }
            
            // Get all required inputs in the current visible step
            const requiredInputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            let firstInvalidField = null;
            
            requiredInputs.forEach(input => {
                // Check if the input is visible (not hidden by x-show)
                const isVisible = window.getComputedStyle(input).display !== 'none' && 
                                 window.getComputedStyle(input.closest('div') || input).display !== 'none';
                
                if (isVisible) {
                    const value = input.value ? input.value.trim() : '';
                    
                    if (!value || value === '') {
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = input;
                        }
                        // Add red border to invalid fields
                        input.classList.add('!border-red-500', '!ring-2', '!ring-red-500');
                    } else {
                        // Remove red border from valid fields
                        input.classList.remove('!border-red-500', '!ring-2', '!ring-red-500');
                    }
                }
            });
            
            if (!isValid && firstInvalidField) {
                firstInvalidField.focus();
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Show alert
                alert('Please fill in all required fields marked with * before continuing');
            }
            
            return isValid;
        }
    }));
});

// Auto-hide error messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const notifications = document.querySelectorAll('.fixed.top-4');
    notifications.forEach(function(notification) {
        setTimeout(function() {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease-out';
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 5000);
    });
});
</script>
    <!-- Toast Container -->
    <x-toast-container />
</body>
</html>