<x-guest-layout>
    <div class="flex items-center justify-center py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full">
            <!-- Header Section -->
            <div class="text-center mb-6">
                <div class="flex justify-center mb-3">
                    <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center animate-pulse">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-purple-700 bg-clip-text text-transparent">
                    Verify Your Email
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    We sent a 6-digit code to your email address
                </p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Main Content Wrapper -->
            <div x-data="verifyEmail()">
                <!-- Hidden Resend Form -->
                <form x-ref="resendForm" method="POST" action="{{ route('token.resend') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">
                </form>

                <!-- Main Form -->
                <form method="POST" action="{{ route('token.verify') }}" @submit="handleSubmit">
                    @csrf

                    <!-- Email Display -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <input 
                                id="email" 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 cursor-not-allowed transition-all" 
                                type="email" 
                                name="email" 
                                value="{{ session('email') ?? old('email') }}" 
                                required 
                                readonly 
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">This is the email we sent your verification code to</p>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Verification Code Input -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Verification Code
                            </label>
                            <button 
                                type="button"
                                @click="resendCode()"
                                :disabled="countdown > 0 || resendLoading"
                                :class="countdown > 0 || resendLoading ? 'opacity-50 cursor-not-allowed' : 'hover:text-purple-700'"
                                class="text-xs text-purple-600 font-medium transition-colors"
                            >
                                <template x-if="!resendLoading && countdown === 0">
                                    <span>Resend Code</span>
                                </template>
                                <template x-if="resendLoading">
                                    <span class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-purple-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Sending...
                                    </span>
                                </template>
                                <template x-if="countdown > 0">
                                    <span>Resend in <span x-text="countdown"></span>s</span>
                                </template>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-6 gap-2" @paste="handlePaste($event)">
                            <template x-for="index in 6" :key="index">
                                <input
                                    :ref="'digit' + (index - 1)"
                                    x-model="digits[index - 1]"
                                    @input="handleInput($event, index - 1)"
                                    @keydown.backspace="handleBackspace($event, index - 1)"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="1"
                                    class="w-full aspect-square text-center text-xl font-semibold border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-800 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all"
                                />
                            </template>
                        </div>
                        <input 
                            x-ref="hiddenToken"
                            id="token" 
                            type="hidden" 
                            name="token"
                            :value="digits.join('')"
                            required
                        />
                        
                        <p class="mt-2 text-xs text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Enter the 6-digit code sent to your email
                        </p>
                        <x-input-error :messages="$errors->get('token')" class="mt-2" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        <a 
                            href="{{ route('login') }}" 
                            class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors font-medium"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Login
                        </a>

                        <button 
                            type="submit"
                            :disabled="loading"
                            :class="loading ? 'opacity-75 cursor-not-allowed' : 'hover:shadow-lg'"
                            class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                        >
                            <template x-if="!loading">
                                <span class="flex items-center">
                                    Verify Email
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Verifying...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>

                <!-- Help Text -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500">
                        Didn't receive the code? Check your spam folder or click the resend button above.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function verifyEmail() {
            return {
                loading: false,
                resendLoading: false,
                countdown: 0,
                digits: ['', '', '', '', '', ''],
                
                init() {
                    // Auto-focus first input
                    this.$nextTick(() => {
                        const firstInput = this.$refs['digit0'];
                        if (firstInput) firstInput.focus();
                    });
                },
                
                handleInput(event, index) {
                    const value = event.target.value.replace(/[^0-9]/g, '').slice(0, 1);
                    this.digits[index] = value;
                    
                    if (value && index < 5) {
                        this.$nextTick(() => {
                            const nextInput = this.$refs['digit' + (index + 1)];
                            if (nextInput) nextInput.focus();
                        });
                    }
                },
                
                handleBackspace(event, index) {
                    if (!this.digits[index] && index > 0) {
                        event.preventDefault();
                        const prevInput = this.$refs['digit' + (index - 1)];
                        if (prevInput) prevInput.focus();
                    }
                },
                
                handlePaste(event) {
                    const paste = event.clipboardData.getData('text');
                    if (/^\d{6}$/.test(paste)) {
                        this.digits = paste.split('');
                        event.preventDefault();
                    }
                },
                
                handleSubmit(event) {
                    this.loading = true;
                },
                
                startCountdown() {
                    this.countdown = 30;
                    const interval = setInterval(() => {
                        this.countdown--;
                        if (this.countdown <= 0) {
                            clearInterval(interval);
                        }
                    }, 1000);
                },
                
                resendCode() {
                    this.resendLoading = true;
                    this.startCountdown();
                    this.$refs.resendForm.submit();
                }
            }
        }
    </script>

    <style>
        input[type='text']:focus {
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
        }
    </style>
</x-guest-layout>