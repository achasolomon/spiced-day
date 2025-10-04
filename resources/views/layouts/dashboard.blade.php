{{-- resources/views/layouts/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false, profileOpen: false }" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - SPICE'd Dayhome Agency</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <nav class="fixed top-0 z-50 w-full bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 lg:px-6 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                type="button" 
                                class="inline-flex items-center p-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" 
                                aria-controls="logo-sidebar" 
                                aria-expanded="false">
                            <span class="sr-only">Toggle sidebar</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}" class="flex items-center ml-2 lg:ml-0 space-x-3">
                            <div class="w-20 h-10 bg-gradient-to-r from-spiced-red via-spiced-yellow to-spiced-purple rounded-full flex items-center justify-center">
                                 <img src="{{ asset('assets/images/logo.png') }}" alt="logo spiced dayhome agency" class="w-20 h-10  rounded-lg">

                            </div>
                           <h1 class="text-xl font-bold tracking-tight">
                            <span class="text-purple-600 dark:text-orange-400">SPICE'd</span>
                            <span class="text-neutral-800 dark:text-neutral-200"> Dayhome</span>
                        </h1>
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                                class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600" 
                                aria-label="Toggle dark mode">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                        </button>

                        <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="relative p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600"
                            aria-label="View notifications">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                        @if(auth()->user()->getUnreadNotificationsCount() > 0)
                            <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center">
                                {{ auth()->user()->getUnreadNotificationsCount() }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown --}}
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                        <a href="{{ route('notifications.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                            View All
                        </a>
                    </div>
                </div>
                
                <div class="max-h-96 overflow-y-auto">
                    @php
                        $recentNotifs = auth()->user()->notifications()->latest()->take(5)->get();
                    @endphp
                    
                    @forelse($recentNotifs as $notification)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 transition-colors {{ !$notification->is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                            <a href="{{ $notification->action_url ?? route('notifications.index') }}" class="block">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                        {{ $notification->priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-purple-100 dark:bg-purple-900/30' }}">
                                        <svg class="w-4 h-4 {{ $notification->priority === 'urgent' ? 'text-red-600' : 'text-purple-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification->title }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$notification->is_read)
                                        <span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full"></span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
                        </div>
                    @endforelse
                </div>
        
                            @if($recentNotifs->count() > 0)
                                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="text-center">
                                        @csrf
                                        <button type="submit" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                            Mark all as read
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    type="button" 
                                    class="flex items-center space-x-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-full p-1 pr-3 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600"
                                    aria-haspopup="true" 
                                    :aria-expanded="open">
                                <img class="w-8 h-8 rounded-full" src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}" alt="{{ auth()->user()->name }}'s avatar">
                                <span class="hidden md:block text-gray-900 dark:text-white font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 z-50 mt-2 w-56 origin-top-right bg-white dark:bg-gray-700 divide-y divide-gray-100 dark:divide-gray-600 rounded-lg shadow-lg">
                                <div class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                    <li>
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Profile Settings
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Notifications
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('applicant.help') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Help & Support
                                        </a>
                                    </li>
                                </ul>
                                <div class="py-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <!-- Sidebar -->
<aside id="logo-sidebar" 
       class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform border-r border-gray-700 lg:translate-x-0"
       style="background: linear-gradient(180deg, #553e96 0%, #3d2a6b 100%);"
       :class="{ '-translate-x-full': !sidebarOpen }"
       @click.away="sidebarOpen = false"
       aria-label="Sidebar navigation">
    <div class="h-full px-3 pb-4 overflow-y-auto">
        <ul class="space-y-2 font-medium">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('applicant.dashboard') }}" 
                   class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 group {{ request()->routeIs('applicant.dashboard') ? 'bg-white/20' : '' }}"
                   aria-current="{{ request()->routeIs('applicant.dashboard') ? 'page' : 'false' }}">
                    <svg class="w-5 h-5 transition duration-75" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>

            <!-- My Application -->
            @if(auth()->user()->hasActiveApplication())
                <li>
                    <a href="{{ route('applicant.applications.show', auth()->user()->getActiveApplication()) }}" 
                       class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 group {{ request()->routeIs('applicant.applications.show') ? 'bg-white/20' : '' }}"
                       aria-current="{{ request()->routeIs('applicant.applications.show') ? 'page' : 'false' }}">
                        <svg class="w-5 h-5 transition duration-75" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-3">My Application</span>
                    </a>
                </li>
            @endif

            <!-- Documents -->
            <li>
                <a href="{{ route('applicant.documents.index', auth()->user()->getActiveApplication() ?? 0) }}" 
                   class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 group {{ request()->routeIs('applicant.documents.*') ? 'bg-white/20' : '' }}"
                   aria-current="{{ request()->routeIs('applicant.documents.*') ? 'page' : 'false' }}">
                    <svg class="w-5 h-5 transition duration-75" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">Documents</span>
                </a>
            </li>

            <!-- Appointments -->
            <li>
                <a href="{{ route('applicant.appointments.index') }}" 
                   class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 group {{ request()->routeIs('applicant.appointments.*') ? 'bg-white/20' : '' }}"
                   aria-current="{{ request()->routeIs('applicant.appointments.*') ? 'page' : 'false' }}">
                    <svg class="w-5 h-5 transition duration-75" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">Appointments</span>
                </a>
            </li>

            <!-- Help & Support -->
            <li>
                <a href="{{ route('applicant.help') }}" 
                   class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 group {{ request()->routeIs('applicant.help') ? 'bg-white/20' : '' }}"
                   aria-current="{{ request()->routeIs('applicant.help') ? 'page' : 'false' }}">
                    <svg class="w-5 h-5 transition duration-75" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">Help & Support</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

        <!-- Main Content -->
        <main class="lg:pl-64 pt-20">
            <div class="p-4 sm:p-6 lg:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>