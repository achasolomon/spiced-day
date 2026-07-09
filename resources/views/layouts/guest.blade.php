<!-- resources/views/layouts/guest.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SPICE\'d Dayhome Agency')</title>

    <link rel="icon" type="image/jpg" href="{{ asset('logo.jpeg') }}">

    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    <style>
        .rainbow-gradient {
            background: linear-gradient(135deg, #ef4444 0%, #f97316 20%, #eab308 40%, #22c55e 60%, #3b82f6 80%, #8b5cf6 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
    
    @stack('styles')

    <!-- Timezone Detection Script -->
    <script>
        (function() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            sessionStorage.setItem('userTimezone', timezone);
            if (window.fetch) {
                const originalFetch = window.fetch;
                window.fetch = function(...args) {
                    const options = args[1] || {};
                    options.headers = options.headers || {};
                    options.headers['X-User-Timezone'] = timezone;
                    return originalFetch.apply(this, [args[0], options]);
                };
            }
        })();
    </script>
</head>

<body class="font-sans antialiased">
    <!-- Animated Background -->
    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900"></div>
        
        <!-- Floating Shapes -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-200/20 rounded-full animate-float"></div>
            <div class="absolute top-3/4 right-1/4 w-96 h-96 bg-purple-200/20 rounded-full animate-float" style="animation-delay: -2s;"></div>
            <div class="absolute bottom-1/4 left-1/3 w-48 h-48 bg-green-200/20 rounded-full animate-float" style="animation-delay: -4s;"></div>
        </div>
    </div>
    
    <!-- Dark Mode Toggle (Fixed) -->
    <!-- <div class="fixed top-4 right-4 z-50">
        <button @click="darkMode = !darkMode" class="p-3 rounded-full glass-card hover-lift">
            <svg x-show="!darkMode" class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
            </svg>
            <svg x-show="darkMode" class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
        </button>
    </div> -->
    
    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 glass-card shadow-xl overflow-hidden sm:rounded-lg">
            
            {{ $slot }} 
            
            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>

