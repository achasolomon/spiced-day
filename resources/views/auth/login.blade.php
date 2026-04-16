<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SPICE'd Dayhome</title>
    
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
                    Welcome Back
                </h1>
                
                <!-- Welcome Description -->
                <p class="text-lg text-white/90 mb-16 leading-relaxed max-w-sm">
                    Sign in to continue your dayhome licensing journey with SPICE'd.
                </p>
                
                <!-- Register Button -->
                <div>
                    <a href="/apply">
                        <button class="text-white font-semibold px-12 py-4 rounded-full transition-all duration-300 hover:scale-105 border-2 border-white/30 hover:bg-white/10 backdrop-blur-sm">
                            Create Account
                        </button>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form (2/3 width) -->
        <div class="flex-1 lg:w-2/3 flex items-center justify-center p-8 lg:p-12 bg-white">
            <div class="w-full max-w-md">
                <!-- Mobile Logo (visible on mobile only) -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-lavender to-brand-purple rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white font-bold text-lg">S</span>
                    </div>
                    <h2 class="text-2xl font-bold" style="color: #553e96;">SPICE'd Dayhome</h2>
                </div>
                
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
                        Welcome Back
                    </h2>
                    <p class="text-gray-600">
                        Sign in to your account
                    </p>
                </div>
                
                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" x-data="{ loading: false, showPassword: false }" @submit="loading = true" class="space-y-6">
                    @csrf
                    
                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address *
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-4 rounded-lg border @error('email') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900"
                            style="focus:ring-color: #e3d4fc;"
                            placeholder="Enter your email address"
                            autocomplete="email"
                            autofocus
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password *
                            </label>
                            <a href="{{ route('password.request') }}" class="text-sm font-medium hover:underline" style="color: #553e96;">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                :type="showPassword ? 'text' : 'password'"
                                required
                                class="w-full px-4 py-4 pr-12 rounded-lg border @error('password') border-red-300 @else border-gray-200 @enderror focus:ring-2 focus:border-transparent transition-all bg-white text-gray-900"
                                style="focus:ring-color: #e3d4fc;"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                            >
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                tabindex="-1"
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
                    
                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="rounded border-gray-300 focus:ring-2 h-4 w-4" style="color: #553e96; focus:ring-color: #e3d4fc;">
                        <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="mt-8">
                        <button 
                            type="submit" 
                            :disabled="loading"
                            class="w-full text-white font-semibold py-4 px-8 rounded-lg transition-all duration-300 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background-color: #553e96;"
                            :class="{ 'opacity-75 cursor-not-allowed': loading }"
                        >
                            <span x-show="!loading">Sign In</span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Signing in...
                            </span>
                        </button>
                    </div>
                </form>
                
                <!-- Register Link -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Don't have an account? 
                        <a href="/apply" class="font-medium hover:underline" style="color: #553e96;">
                            Create one here
                        </a>
                    </p>
                </div>
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