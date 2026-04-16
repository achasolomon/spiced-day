<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false, loading: false, showPassword: false, showConfirmPassword: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if(isset($application))
            Complete Your Profile - SPICE'd Dayhome
        @else
            Create Account - SPICE'd Dayhome
        @endif
    </title>

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
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'fade-in': 'fadeIn 0.6s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-white font-sans antialiased">
    <div class="min-h-screen flex relative">
        <!-- Left Side - Welcome Panel (1/3 width) -->
        <div class="hidden lg:block lg:w-1/3 relative overflow-hidden" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
            
            <!-- Curved edge -->
            <div class="absolute top-0 right-0 w-20 h-full">
                <svg class="w-full h-full" viewBox="0 0 80 800" preserveAspectRatio="none">
                    <path d="M0,0 Q40,400 0,800 L80,800 L80,0 Z" fill="white"/>
                </svg>
            </div>
            
            <!-- Clean geometric elements -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute top-1/4 left-1/4 w-32 h-32 rounded-full border border-white/20"></div>
                <div class="absolute bottom-1/4 left-1/2 w-20 h-20 rounded-full border border-white/10"></div>
            </div>
            
            <!-- Welcome Content -->
            <div class="relative z-10 flex flex-col justify-center h-full px-12 text-white">
                <!-- Welcome Title -->
                <h1 class="text-5xl font-bold mb-8 leading-tight">
                    Welcome
                </h1>
                
                <!-- Welcome Description -->
                <p class="text-lg text-white/90 mb-16 leading-relaxed max-w-sm">
                    @if(isset($application))
                        Complete your profile to continue your dayhome licensing journey.
                    @else
                        Join over 500 successful dayhome providers who have trusted SPICE'd with their licensing journey.
                    @endif
                </p>
                
                <!-- Login Button -->
                <div>
                    <a href="{{ route('login') }}">
                        <button class="text-white font-semibold px-12 py-4 rounded-full transition-all duration-300 hover:scale-105 border-2 border-white/30 hover:bg-white/10 backdrop-blur-sm">
                            Login
                        </button>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Registration Form (2/3 width) -->
        <div class="flex-1 lg:w-2/3 flex items-center justify-center p-8 lg:p-12 bg-white">
            <div class="w-full max-w-2xl">
                <!-- Mobile Logo (visible on mobile only) -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-lavender to-brand-purple rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white font-bold text-lg">S</span>
                    </div>
                    <h2 class="text-2xl font-bold" style="color: #553e96;">SPICE'd Dayhome</h2>
                </div>
                
                <!-- Application Success Notice -->
                @if(isset($application))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-800">
                                <strong>Application #{{ $application->application_number }}</strong><br>
                                Initial inspection completed successfully!
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Form Header -->
                <div class="text-center mb-10">
                    <!-- Logo for desktop -->
                    <div class="hidden lg:flex justify-center items-center mb-8">
                        <div class="w-100 h-20 rounded-xl flex items-center justify-center mr-4">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="SPICE'd Dayhome Logo" class="w-10 h-10 object-contain">
                        </div>
                        <h1 class="text-xl font-bold tracking-tight">
                            <span style="color: #553e96;">SPICE'd</span>
                            <span class="text-gray-800"> Dayhome</span>
                        </h1>
                    </div>
                    
                    <h2 class="text-4xl font-bold text-gray-900 mb-3">
                        @if(isset($application))
                            Complete Your Profile
                        @else
                            Create Your Account
                        @endif
                    </h2>
                    <p class="text-gray-600">
                        @if(isset($application))
                            Your initial inspection is complete! Create your profile to continue.
                        @else
                            Start your dayhome licensing journey today
                        @endif
                    </p>
                </div>
                
                <!-- Registration Form -->
                <form method="POST" 
                      action="@if(isset($application)){{ route('anonymous.register.submit', $application->anonymous_token) }}@else{{ route('register') }}@endif" 
                      x-data="{ loading: false, showPassword: false, showConfirmPassword: false }" 
                      @submit="loading = true" 
                      class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name *
                        </label>
                        <input 
                            id="first_name" 
                            name="first_name" 
                            type="text" 
                            required 
                            value="{{ old('first_name', $application->educator_first_name ?? '') }}"
                            @if(isset($application)) readonly @endif
                            class="w-full px-4 py-4 rounded-lg border @error('first_name') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900 @if(isset($application)) bg-gray-100 @endif"
                            style="focus:ring-color: #e3d4fc;"
                            placeholder="Enter your first name"
                        >
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name *
                        </label>
                        <input 
                            id="last_name" 
                            name="last_name" 
                            type="text" 
                            required 
                            value="{{ old('last_name', $application->educator_last_name ?? '') }}"
                            @if(isset($application)) readonly @endif
                            class="w-full px-4 py-4 rounded-lg border @error('last_name') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900 @if(isset($application)) bg-gray-100 @endif"
                            style="focus:ring-color: #e3d4fc;"
                            placeholder="Enter your last name"
                        >
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Email *
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email', $application->email ?? '') }}"
                            class="w-full px-4 py-4 rounded-lg border @error('email') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900"
                            style="focus:ring-color: #e3d4fc;"
                            placeholder="Enter your email address"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if(isset($application) && old('email') && old('email') !== $application->email)
                        <div class="mt-3 flex items-center">
                            <input type="checkbox" 
                                   name="confirm_different_email" 
                                   id="confirm_different_email"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="confirm_different_email" class="ml-2 block text-sm text-gray-700">
                                I confirm this email is different from my application email
                            </label>
                        </div>
                        @endif
                    </div>

                    <!-- Phone -->
                    <div class="md:col-span-2">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number *
                        </label>
                        <input 
                            id="phone" 
                            name="phone" 
                            type="tel" 
                            required 
                            value="{{ old('phone', $application->phone ?? '') }}"
                            class="w-full px-4 py-4 rounded-lg border @error('phone') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900"
                            style="focus:ring-color: #e3d4fc;"
                            placeholder="(123) 456-7890"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Location (only for normal registration) -->
                    @if(!isset($application))
                    <div class="md:col-span-2">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Location *
                        </label>
                        <input 
                            id="location" 
                            name="location" 
                            type="text" 
                            required 
                            value="{{ old('location') }}"
                            class="w-full px-4 py-4 rounded-lg border @error('location') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900"
                            style="focus:ring-color: #e3d4fc;"
                            placeholder="e.g., Calgary, Alberta"
                        >
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password *
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                :type="showPassword ? 'text' : 'password'"
                                required
                                class="w-full px-4 py-4 pr-12 rounded-lg border @error('password') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900"
                                style="focus:ring-color: #e3d4fc;"
                                placeholder="Create a strong password"
                            >
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg x-show="!showPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password *
                        </label>
                        <div class="relative">
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                :type="showConfirmPassword ? 'text' : 'password'"
                                required
                                class="w-full px-4 py-4 pr-12 rounded-lg border @error('password_confirmation') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900"
                                style="focus:ring-color: #e3d4fc;"
                                placeholder="Confirm your password"
                            >
                            <button 
                                type="button" 
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg x-show="!showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="md:col-span-2 flex items-center mt-4">
                        <input id="agree_terms" 
                               name="agree_terms" 
                               type="checkbox" 
                               required
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="agree_terms" class="ml-2 block text-sm text-gray-700">
                            I agree to the <a href="#" class="text-purple-600 hover:text-purple-500">Terms and Conditions</a>
                        </label>
                    </div>
                    @error('agree_terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <!-- Submit Button -->
                    <div class="md:col-span-2 mt-6">
                        <button 
                            type="submit" 
                            :disabled="loading"
                            class="w-full text-white font-semibold py-4 px-8 rounded-lg transition-all duration-300 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background-color: #553e96;"
                            :class="{ 'opacity-75 cursor-not-allowed': loading }"
                        >
                            <span x-show="!loading">
                                @if(isset($application))
                                    Complete Profile & Continue
                                @else
                                    Create Account
                                @endif
                            </span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @if(isset($application))
                                    Completing Profile...
                                @else
                                    Creating Account...
                                @endif
                            </span>
                        </button>
                    </div>
                </form>
                
                <!-- Login Link -->
                @if(!isset($application))
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-medium hover:underline" style="color: #553e96;">
                            Sign in here
                        </a>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Auto-hide notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('[class*="fixed top-4 right-4"]');
            notifications.forEach(function(notification) {
                setTimeout(function() {
                    notification.style.opacity = '0';
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