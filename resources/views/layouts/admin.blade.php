{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - SPICE'd Dayhome Agency</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
                        <button @click="sidebarOpen = !sidebarOpen" type="button" class="inline-flex items-center p-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <a href="{{ route('admin.dashboard') }}" class="flex items-center ml-2 lg:ml-0 space-x-3">
                            <div class="w-20 h-10 flex items-center justify-center">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="SPICE'd Logo" class="w-20 h-10 rounded-lg">
                            </div>
                            <h1 class="text-xl font-bold tracking-tight">
                                <span class="text-purple-600 dark:text-purple-400">SPICE'd</span>
                                <span class="text-neutral-800 dark:text-neutral-200"> Admin</span>
                            </h1>
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- System Health Indicator -->
                        <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-green-50 dark:bg-green-900/20 rounded-full">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-medium text-green-700 dark:text-green-300">System Healthy</span>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <!-- <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                        </button> -->

                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <!-- Mobile: Direct link -->
                            <a href="{{ route('notifications.index') }}" class="lg:hidden relative p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                </svg>
                                @if(auth()->user()->getUnreadNotificationsCount() > 0)
                                    <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center">
                                        {{ auth()->user()->getUnreadNotificationsCount() }}
                                    </span>
                                @endif
                            </a>

                            <!-- Desktop: Dropdown -->
                            <button @click="open = !open" class="hidden lg:block relative p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                </svg>
                                @if(auth()->user()->getUnreadNotificationsCount() > 0)
                                    <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center">
                                        {{ auth()->user()->getUnreadNotificationsCount() }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-800 dark:to-gray-700">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                                        <a href="{{ route('notifications.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">View All</a>
                                    </div>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @php $recentNotifs = auth()->user()->notifications()->latest()->take(5)->get(); @endphp
                                    @forelse($recentNotifs as $notification)
                                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 {{ !$notification->is_read ? 'bg-purple-50/50 dark:bg-purple-900/10' : '' }}">
                                            <a href="{{ $notification->action_url ?? route('notifications.index') }}" class="block">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification->title }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $notification->message }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </a>
                                        </div>
                                    @empty
                                        <div class="p-8 text-center text-sm text-gray-500">No notifications</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-full p-1 pr-3">
                                <img class="w-8 h-8 rounded-full" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                                <span class="hidden md:block text-gray-900 dark:text-white font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-50 mt-2 w-56 bg-white dark:bg-gray-700 divide-y divide-gray-100 dark:divide-gray-600 rounded-lg shadow-lg">
                                <div class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                    <li><a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Profile Settings</a></li>
                                    <li><a href="{{ route('notifications.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Notifications</a></li>
                                </ul>
                                <div class="py-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">Sign out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-30 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside id="admin-sidebar" 
               class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform duration-300 border-r border-gray-700 transform lg:translate-x-0"
               style="background: linear-gradient(180deg, #7c3aed 0%, #5b21b6 100%);"
               :class="{ '-translate-x-full': !sidebarOpen }">
            <div class="h-full px-3 pb-4 overflow-y-auto">
                <ul class="space-y-1 font-medium text-sm">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>

                    <!-- Applications -->
                    <li>
                        <a href="{{ route('admin.applications.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.applications.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3">Applications</span>
                        </a>
                    </li>

                    <!-- Users -->
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                            </svg>
                            <span class="ml-3">Users</span>
                        </a>
                    </li>

                    <!-- Consultants -->
                    <li>
                        <a href="{{ route('admin.consultants.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.consultants.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3">Consultants</span>
                        </a>
                    </li>

                    <!-- Appointments -->
                    <li>
                        <a href="{{ route('admin.appointments.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.appointments.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3">Appointments</span>
                        </a>
                    </li>

                    <!-- Inspections -->
                    <li>
                        <a href="{{ route('admin.inspections.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.inspections.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3">Inspections</span>
                        </a>
                    </li>

                    <!-- Documents -->
                    <li>
                        <a href="{{ route('admin.documents.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.documents.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3">Documents</span>
                        </a>
                    </li>

                    <!-- Document Requirements -->
                    <li>
                        <a href="{{ route('admin.document-requirements.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.document-requirements.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3">Document Requirements</span>
                        </a>
                    </li>

                    <!-- Region -->
                    <li>
                        <a href="{{ route('admin.regions.index') }}" 
                        class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.regions.*') ? 'bg-white/20' : '' }}">
                            <!-- 🗺 Region Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M3 7l6-3 6 3 6-3v13l-6 3-6-3-6 3V7zM9 4v13m6-10v13" />
                            </svg>
                            <span class="ml-3">Regions</span>
                        </a>
                    </li>

                    <!-- Postal Codes -->
                    <li>
                        <a href="{{ route('admin.postal-codes.index') }}" 
                        class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.postal-codes.*') ? 'bg-white/20' : '' }}">
                            <!-- 📮 Postal Code Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l9 6 9-6M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" />
                            </svg>
                            <span class="ml-3">Postal Codes</span>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.reports.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                            <span class="ml-3">Reports</span>
                        </a>
                    </li>

                    <!-- Divider -->
                    <li class="pt-2">
                        <hr class="border-white/20">
                    </li>

                    <!-- Activity Log -->
                    <li>
                        <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3">Activity Log</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="lg:pl-64 pt-20">
            <div class="p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                        <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>