{{-- resources/views/layouts/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }"
      x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SPICE'd Dayhome Agency</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

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
            <div class="px-2 sm:px-4 py-2 sm:py-3 lg:px-6 lg:pl-3">
                <div class="flex items-center justify-between">

                    <!-- Left Section -->
                    <div class="flex items-center">
                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = !sidebarOpen"
                                type="button"
                                class="inline-flex items-center p-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 5h14a1 1 0 110 2H3a1 1 0 010-2zm0 5h14a1 1 0 110 2H3a1 1 0 010-2zm0 5h14a1 1 0 110 2H3a1 1 0 010-2z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}" class="flex items-center ml-2 lg:ml-0 space-x-2 sm:space-x-3">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="SPICE'd Logo" class="w-16 sm:w-20 h-10 rounded-lg">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight">
                                <span class="text-purple-600 dark:text-purple-400">SPICE'd</span>
                                <span class="text-neutral-800 dark:text-neutral-200"> Dayhome</span>
                            </h1>
                        </a>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center space-x-1 sm:space-x-2 md:space-x-3">
                        <!-- Notifications -->
                        <div class="relative flex-shrink-0" x-data="{ open: false }">
                            <!-- Mobile: Direct link to notifications page -->
                            <a href="{{ route('notifications.index') }}" 
                            class="lg:hidden relative inline-flex items-center justify-center p-1.5 sm:p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                                @if(auth()->user()->getUnreadNotificationsCount() > 0)
                                    <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 rounded-full text-white text-[10px] font-medium flex items-center justify-center px-1">
                                        {{ auth()->user()->getUnreadNotificationsCount() > 9 ? '9+' : auth()->user()->getUnreadNotificationsCount() }}
                                    </span>
                                @endif
                            </a>
                            
                            <!-- Desktop: Dropdown -->
                            <button @click="open = !open" 
                                    class="hidden lg:inline-flex relative items-center justify-center p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                                @if(auth()->user()->getUnreadNotificationsCount() > 0)
                                    <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 rounded-full text-white text-[10px] font-medium flex items-center justify-center px-1">
                                        {{ auth()->user()->getUnreadNotificationsCount() > 9 ? '9+' : auth()->user()->getUnreadNotificationsCount() }}
                                    </span>
                                @endif
                            </button>

                            <!-- Notification Dropdown (Desktop only) -->
                            <div x-show="open" 
                                @click.away="open = false" 
                                x-transition
                                x-cloak
                                class="absolute right-0 mt-2 w-80 lg:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-gray-800 dark:to-gray-700 flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">View All</a>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @php $recentNotifs = auth()->user()->notifications()->latest()->take(5)->get(); @endphp
                                    @forelse($recentNotifs as $notification)
                                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 {{ !$notification->is_read ? 'bg-purple-50/50 dark:bg-purple-900/10' : '' }}">
                                            <a href="{{ $notification->action_url ?? route('notifications.index') }}"
                                            class="block"
                                            data-notification-id="{{ $notification->id }}"
                                            data-is-read="{{ $notification->is_read ? '1' : '0' }}">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification->title }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $notification->message }}</p>
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
                        <div class="relative flex-shrink-0" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-1 sm:space-x-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-full p-1 pr-2 sm:pr-3 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <img class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex-shrink-0" 
                                    src="{{ auth()->user()->avatar_url }}" 
                                    alt="{{ auth()->user()->name }}">
                                <span class="hidden md:block text-gray-900 dark:text-white font-medium truncate max-w-[100px] lg:max-w-[120px]">
                                    {{ explode(' ', auth()->user()->name)[0] }}
                                </span>
                                <svg class="hidden md:block w-4 h-4 text-gray-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                @click.away="open = false" 
                                x-transition
                                x-cloak
                                class="absolute right-0 z-50 mt-2 w-56 bg-white dark:bg-gray-700 divide-y divide-gray-100 dark:divide-gray-600 rounded-lg shadow-lg">
                                <div class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                    <li><a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Profile Settings</a></li>
                                    <li><a href="{{ route('notifications.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Notifications</a></li>
                                    <li><a href="{{ route('applicant.help') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Help & Support</a></li>
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
        <aside id="logo-sidebar"
               class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform duration-300 border-r border-gray-700 transform lg:translate-x-0"
               style="background: linear-gradient(180deg, #553e96 0%, #3d2d6b 100%);"
               :class="{ '-translate-x-full': !sidebarOpen }">
            <div class="h-full px-3 pb-4 overflow-y-auto">
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="{{ route('applicant.dashboard') }}"
                           class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('applicant.dashboard') ? 'bg-white/20' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2v-3a1 1 0 011-1h4a1 1 0 011 1v3h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>

                    @if(auth()->user()->hasActiveApplication())
                        <li>
                            <a href="{{ route('applicant.applications.show', auth()->user()->getActiveApplication()) }}"
                               class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('applicant.applications.show') ? 'bg-white/20' : '' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-3">My Application</span>
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('applicant.documents.index', auth()->user()->getActiveApplication() ?? 0) }}"
                           class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('applicant.documents.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586L12 2.586 15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-3">Documents</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('applicant.appointments.index') }}"
                           class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('applicant.appointments.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 10-2 0v1zm0 5h8a1 1 0 100-2H6a1 1 0 000 2z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-3">Appointments</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('applicant.help') }}"
                           class="flex items-center p-2 rounded-lg text-white hover:bg-white/10 {{ request()->routeIs('applicant.help') ? 'bg-white/20' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-3">Help & Support</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="pt-20 lg:pl-64 transition-all duration-300">
            <div class="p-4 sm:p-6 lg:p-8">
                @yield('content')
            </div>
        </main>

    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Handle notification clicks
    document.addEventListener('click', function(e) {
        const notificationLink = e.target.closest('a[data-notification-id]');
        
        if (notificationLink) {
            const notificationId = notificationLink.dataset.notificationId;
            const isRead = notificationLink.dataset.isRead === '1';
            
            // If notification is already read, allow normal navigation
            if (isRead) {
                return;
            }
            
            // Prevent default navigation
            e.preventDefault();
            
            const targetUrl = notificationLink.href;
            
            // Mark notification as read
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Navigate to the target URL after marking as read
                    window.location.href = targetUrl;
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                // Navigate anyway even if there's an error
                window.location.href = targetUrl;
            });
        }
    });
});
    </script>
    
</body>
</html>