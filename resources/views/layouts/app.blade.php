<!--landing page-->
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SPICE\'d Dayhome Agency')</title>
    
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
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .dark .glass-nav {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
</head>

<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 min-h-screen">
    <div id="app">
        <!-- Navigation -->
        <nav class="glass-nav fixed w-full z-50 transition-all duration-300" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                                <div class="w-10 h-10 rainbow-gradient rounded-full flex items-center justify-center animate-float">
                                    <span class="text-white font-bold text-lg">S</span>
                                </div>
                                <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                    SPICE'd Dayhome
                                </span>
                            </a>
                        </div>
                        
                        <!-- Desktop Navigation -->
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            @auth
                                @if(auth()->user()->isApplicant())
                                    <a href="{{ route('applicant.dashboard') }}" class="nav-link {{ request()->routeIs('applicant.*') ? 'active' : '' }}">Dashboard</a>
                                    <a href="{{ route('applications.index') }}" class="nav-link {{ request()->routeIs('applications.*') ? 'active' : '' }}">My Applications</a>
                                    <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">Appointments</a>
                                    <a href="{{ route('documents.index') }}" class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">Documents</a>
                                @elseif(auth()->user()->isConsultant())
                                    <a href="{{ route('consultant.dashboard') }}" class="nav-link {{ request()->routeIs('consultant.*') ? 'active' : '' }}">Dashboard</a>
                                    <a href="{{ route('applications.index') }}" class="nav-link {{ request()->routeIs('applications.*') ? 'active' : '' }}">Applications</a>
                                    <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">Calendar</a>
                                    <a href="{{ route('inspections.index') }}" class="nav-link {{ request()->routeIs('inspections.*') ? 'active' : '' }}">Inspections</a>
                                @elseif(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Dashboard</a>
                                    <a href="{{ route('applications.index') }}" class="nav-link {{ request()->routeIs('applications.*') ? 'active' : '' }}">Applications</a>
                                    <a href="{{ route('consultants.index') }}" class="nav-link {{ request()->routeIs('consultants.*') ? 'active' : '' }}">Consultants</a>
                                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">Reports</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    
                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-lg glass-card hover-lift">
                            <svg x-show="!darkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                        </button>
                        
                        @auth
                            <!-- Notifications -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 rounded-lg glass-card hover-lift relative">
                                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                                    </svg>
                                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                                </button>
                                
                                <!-- Notifications Dropdown -->
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 glass-card rounded-xl shadow-lg z-50">
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Notifications</h3>
                                        <!-- Notification items would go here -->
                                        <div class="space-y-3">
                                            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                                <p class="text-sm font-medium text-blue-900 dark:text-blue-200">Application Update</p>
                                                <p class="text-xs text-blue-700 dark:text-blue-300">Your inspection has been scheduled</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('notifications.index') }}" class="block mt-3 text-center text-sm text-blue-600 hover:text-blue-800">View all notifications</a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg glass-card hover-lift">
                                    <img class="w-8 h-8 rounded-full" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                                    <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <!-- User Dropdown -->
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 glass-card rounded-xl shadow-lg z-50">
                                    <div class="py-1">
                                        <a href="{{ route('profile.show') }}" class="dropdown-item">Profile Settings</a>
                                        <a href="{{ route('help') }}" class="dropdown-item">Help & Support</a>
                                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item w-full text-left">Sign Out</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-secondary">Sign In</a>
                            <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
                        @endauth
                        
                        <!-- Mobile menu button -->
                        <button @click="open = !open" class="md:hidden p-2 rounded-lg glass-card">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div x-show="open" x-transition class="md:hidden glass-card mx-4 mb-4 rounded-xl">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <!-- Mobile nav items would go here based on user role -->
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="pt-16 min-h-screen">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-8 h-8 rainbow-gradient rounded-full flex items-center justify-center">
                                <span class="text-white font-bold">S</span>
                            </div>
                            <span class="text-lg font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                SPICE'd Dayhome Agency
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Transforming dayhome licensing with modern digital solutions.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Support</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600">Help Center</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600">Contact Us</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600">FAQ</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Legal</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 dark:border-gray-700 pt-8 mt-8">
                    <p class="text-center text-gray-500 dark:text-gray-400">
                        © {{ date('Y') }} SPICE'd Dayhome Agency. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2">
        <!-- Toast messages will be inserted here -->
    </div>
    
    @stack('scripts')
</body>
</html>
