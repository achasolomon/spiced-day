<!--landing page-->
<!-- resources/views/layouts/app.blade.php -->
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .rainbow-gradient {
            background: linear-gradient(135deg, #ef4444 0%, #f97316 20%, #eab308 40%, #22c55e 60%, #3b82f6 80%, #8b5cf6 100%);
        }
        
        /* Enhanced Footer Styles */
        .footer-link {
            position: relative;
            display: inline-block;
        }

        .footer-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: linear-gradient(90deg, #a78bfa, #8b5cf6);
            transition: width 0.3s ease;
        }

        .footer-link:hover::after {
            width: 100%;
        }

        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 30px rgba(167, 139, 250, 0.5);
            border-color: #a78bfa;
        }

        .social-icon:hover i {
            transform: scale(1.2);
            color: white;
        }

        .land-acknowledgement {
            position: relative;
            overflow: hidden;
        }
        .land-acknowledgement p {
            color: #000;
        }

        .land-acknowledgement::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(167, 139, 250, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .land-acknowledgement:hover::before {
            left: 100%;
        }

        .hours-list li {
            padding-left: 1.5rem;
            position: relative;
        }

        .hours-list li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #a78bfa;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .section-heading {
            position: relative;
            display: inline-block;
            padding-bottom: 0.5rem;
        }

        .section-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #a78bfa, transparent);
            border-radius: 2px;
        }

        .contact-link {
            transition: all 0.3s ease;
        }

        .contact-link:hover {
            color: #c4b5fd !important;
            transform: translateX(5px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-on-scroll {
            animation: fadeInUp 0.6s ease forwards;
        }
    </style>
    
    @stack('styles')
    
      <!-- Timezone Detection Script -->
    <script>
        (function() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            // Store in session storage for the middleware to use
            sessionStorage.setItem('userTimezone', timezone);
            // Add to all AJAX requests and set timezone hidden input in forms
            document.addEventListener('DOMContentLoaded', function() {
                // Set as default header for fetch requests
                if (window.fetch) {
                    const originalFetch = window.fetch;
                    window.fetch = function(...args) {
                        const options = args[1] || {};
                        options.headers = options.headers || {};
                        options.headers['X-User-Timezone'] = timezone;
                        return originalFetch.apply(this, [args[0], options]);
                    };
                }

                document.querySelectorAll('input[name="user_timezone"]').forEach(el => {
                    el.value = timezone;
                });
            });
        })();
    </script>
</head>

<body class="font-sans antialiased bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-100 transition-colors duration-300" :class="{ 'dark': darkMode }">
    <div id="app">
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 bg-white/90 dark:bg-neutral-900/90 backdrop-blur-lg border-b border-neutral-200/50 dark:border-neutral-700/50 transition-all duration-300" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 from-brand-500 to-brand-600 rounded-xl flex items-center justify-center">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="logo spiced dayhome agency" class="w-20 h-10 rounded-lg">
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight">
                                <span class="text-purple-600 dark:text-orange-400">SPICE'd</span>
                                <span class="text-neutral-800 dark:text-neutral-200"> Dayhome</span>
                            </h1>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Professional Approval Agency</p>
                        </div>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        @php
                            $user = auth()->user();
                            $homeRoute = $user ? match($user->user_type) {
                                'applicant' => route('applicant.dashboard'),
                                'consultant' => route('consultant.dashboard'),
                                'admin' => route('admin.dashboard'),
                                default => route('dashboard'),
                            } : route('home');
                        @endphp
                        <a href="{{ $homeRoute }}" class="text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-medium">{{ $user ? 'Dashboard' : 'Home' }}</a>
                                     
                           <a href="{{ route('login') }}" class="text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-medium">Login</a>
                            <a href="/apply" class="bg-purple-600 hover:bg-brand-700 text-white px-6 py-2.5 rounded-lg font-semibold transition-all duration-200 hover:shadow-lg">
                                Get Started
                            </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button @click="open = !open" class="md:hidden p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800">
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="md:hidden bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-700">
                <div class="px-6 py-4 space-y-4">
                    @php
                        $user = auth()->user();
                        $homeRoute = $user ? match($user->user_type) {
                            'applicant' => route('applicant.dashboard'),
                            'consultant' => route('consultant.dashboard'),
                            'admin' => route('admin.dashboard'),
                            default => route('dashboard'),
                        } : route('home');
                    @endphp
                    <a href="{{ $homeRoute }}" class="block text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400">{{ $user ? 'Dashboard' : 'Home' }}</a>
                    <a href="{{ route('about') }}" class="block text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400">About</a>
                    <a href="{{ route('services') }}" class="block text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400">Services</a>
                    <a href="{{ route('contact') }}" class="block text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400">Contact</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="block text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400">Login</a>
                        <a href="/apply" class="block text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400">Get Started</a>
                    @endauth
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="pt-20 min-h-screen">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-neutral-900 text-white py-16">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <!-- Top Row -->
                <div class="grid lg:grid-cols-3 gap-12 mb-12">
                    <!-- Left: Business Info -->
                    <div class="animate-on-scroll">
                        <h3 class="text-3xl font-bold mb-2">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">SPICE'd</span> 
                            <span class="text-white">Childcare Services</span>
                        </h3>
                        <p class="text-neutral-300 mb-6 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-purple-400"></i>
                            Edmonton, AB
                        </p>
                        <div class="space-y-3">
                            <p class="flex items-center">
                                <i class="fas fa-phone mr-3 text-purple-400"></i>
                                <span class="font-semibold text-white mr-2">Phone:</span> 
                                <a href="tel:8258894233" class="contact-link text-brand-400 hover:underline">825-889-4233</a>
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-envelope mr-3 text-purple-400"></i>
                                <span class="font-semibold text-white mr-2">Email:</span> 
                                <a href="mailto:executive@spicedchildcare.com" class="contact-link text-brand-400 hover:underline break-all">executive@spicedchildcare.com</a>
                            </p>
                        </div>
                    </div>

                    <!-- Middle: Land Acknowledgement -->
                    <div class="lg:col-span-2 animate-on-scroll" style="animation-delay: 0.1s;">
                        <div class="land-acknowledgement from-dark-800 to-dark-900 bg-white text-dark-200 italic p-8 rounded-2xl shadow-lg border border-neutral-700 hover:border-purple-500/50">
                            <i class="fas fa-leaf text-purple-400 mb-3 text-2xl block"></i>
                            <p class="leading-relaxed">
                                We acknowledge that the land on which we gather is Treaty 6 territory and a traditional meeting ground and home for many Indigenous Peoples, including Cree, Saulteaux, Niisitapi (Blackfoot), Métis, and Nakota Sioux.
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="border-neutral-800 mb-12 opacity-50" />

                <!-- Middle Row -->
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                    <!-- Hours of Service -->
                    <div class="animate-on-scroll" style="animation-delay: 0.2s;">
                        <h4 class="section-heading text-lg font-semibold mb-6 text-purple-400">
                            <i class="fas fa-clock mr-2"></i>Hours of Service
                        </h4>
                        <ul class="hours-list space-y-2 text-neutral-300">
                            <li class="hover:text-white hover:translate-x-1 transition-all">Monday: 8:00 AM - 5:00 PM</li>
                            <li class="hover:text-white hover:translate-x-1 transition-all">Tuesday: 8:00 AM - 5:00 PM</li>
                            <li class="hover:text-white hover:translate-x-1 transition-all">Wednesday: 8:00 AM - 5:00 PM</li>
                            <li class="hover:text-white hover:translate-x-1 transition-all">Thursday: 8:00 AM - 5:00 PM</li>
                            <li class="hover:text-white hover:translate-x-1 transition-all">Friday: 8:00 AM - 5:00 PM</li>
                        </ul>
                    </div>

                    <!-- Socials -->
                    <div class="animate-on-scroll" style="animation-delay: 0.3s;">
                        <h4 class="section-heading text-lg font-semibold mb-6 text-purple-400">
                            <i class="fas fa-share-alt mr-2"></i>Follow Us
                        </h4>
                        <div class="flex space-x-4">
                            <a href="#" aria-label="Instagram" class="social-icon group">
                                <i class="fab fa-instagram text-2xl group-hover:scale-110 transition-transform"></i>
                            </a>
                            <a href="#" aria-label="Facebook" class="social-icon group">
                                <i class="fab fa-facebook text-2xl group-hover:scale-110 transition-transform"></i>
                            </a>
                            <a href="#" aria-label="TikTok" class="social-icon group">
                                <i class="fab fa-tiktok text-2xl group-hover:scale-110 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Main Menu -->
                    <div class="animate-on-scroll" style="animation-delay: 0.4s;">
                        <h4 class="section-heading text-lg font-semibold mb-6 text-sky-300">
                            <i class="fas fa-bars mr-2"></i>Main Menu
                        </h4>
                        <ul class="space-y-2">
                            @php
                                $user = auth()->user();
                                $homeRoute = $user ? match($user->user_type) {
                                    'applicant' => route('applicant.dashboard'),
                                    'consultant' => route('consultant.dashboard'),
                                    'admin' => route('admin.dashboard'),
                                    default => route('dashboard'),
                                } : route('home');
                            @endphp
                            <li><a href="{{ $homeRoute }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">{{ $user ? 'Dashboard' : 'Home' }}</a></li>
                            <li><a href="{{ route('for-parents') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">For Parents</a></li>
                            <li><a href="{{ route('for-educators') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">For Educators</a></li>
                            <li><a href="{{ route('about') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">About</a></li>
                            <li><a href="{{ route('resources') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Resources</a></li>
                            <li><a href="{{ route('contact') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Contact Us</a></li>
                        </ul>
                    </div>

                    <!-- Quick Links -->
                    <div class="animate-on-scroll" style="animation-delay: 0.5s;">
                        <h4 class="section-heading text-lg font-semibold mb-6 text-sky-300">
                            <i class="fas fa-link mr-2"></i>Quick Links
                        </h4>
                        <ul class="space-y-2 mb-6">
                            <li><a href="{{ route('faqs-parents') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">FAQs for Parents</a></li>
                            <li><a href="{{ route('faqs-educators') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">FAQs for Educators</a></li>
                            <li><a href="{{ route('agency-fees') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Agency Fees</a></li>
                        </ul>

                        <h4 class="section-heading text-lg font-semibold mb-6 text-sky-300">
                            <i class="fas fa-shield-alt mr-2"></i>Privacy Policy
                        </h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('privacy') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Privacy</a></li>
                            <li><a href="{{ route('terms') }}" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Terms of Use</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom -->
                <div class="border-t border-neutral-800 pt-8 text-center">
                    <p class="text-neutral-400 text-sm">
                        <i class="far fa-copyright mr-1"></i>
                        <span id="currentYear">{{ date('Y') }}</span> SPICE'd Childcare Services. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Toast Container -->
    <x-toast-container />
    
    @stack('scripts')
</body>
</html>
