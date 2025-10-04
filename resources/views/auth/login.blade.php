<!-- resources/views/auth/login.blade.php -->
<x-guest-layout>
    @section('content')
    <div class="min-h-[50vh] flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Header - Made more compact -->
            <div class="text-center mb-6">
                <div class="flex justify-center mb-3">
                    <div class="w-50 h-14  flex items-center justify-center animate-float">
                      <img src="{{ asset('assets/images/logo.png') }}" alt="SPICE'd Dayhome Logo" class="w-50 h-10 object-contain">
                    </div>
                     <h1 class="text-xl font-bold tracking-tight">
                            <span style="color: #553e96;">SPICE'd</span>
                            <span class="text-gray-800"> Dayhome</span>
                        </h1>
                </div>
                <h2 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    Welcome Back
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Sign in to your account
                </p>
            </div>
            
            <!-- Login Form - Made more compact -->
            <div class="glass-card rounded-xl p-6 hover-lift">
                <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    
                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email Address
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email') }}"
                            class="form-input w-full px-3 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="your@email.com"
                            autocomplete="email"
                            autofocus
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-4" x-data="{ show: false }">
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Password
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                :type="show ? 'text' : 'password'"
                                required
                                class="form-input w-full px-3 py-2.5 pr-10 rounded-lg border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                            >
                            <button 
                                type="button" 
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                tabindex="-1"
                            >
                                <svg x-show="!show" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="show" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="flex items-center mb-5">
                        <input id="remember_me" name="remember" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                        <label for="remember_me" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        :disabled="loading"
                        class="w-full btn-primary py-2.5 text-base font-medium"
                        :class="{ 'opacity-75 cursor-not-allowed': loading }"
                    >
                        <span x-show="!loading">Sign In</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Signing in...
                        </span>
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="mt-5 mb-4 relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white/50 dark:bg-slate-800/50 text-gray-500">New to our platform?</span>
                    </div>
                </div>
                
                <!-- Register Link -->
                <div>
                    <a href="{{ route('register') }}" class="w-full btn-secondary text-center py-2.5 text-base font-medium block">
                        Create New Account
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-guest-layout>