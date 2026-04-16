<!DOCTYPE html>
<html lang="en" x-data="{ mobileMenuOpen: false, darkMode: false }" x-init="darkMode = false">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPICE'd Dayhome Agency - Professional Childcare Approval</title>
    
    <link rel="icon" type="image/jpg" href="{{ asset('logo.jpeg') }}">


    <meta name="description" content="Alberta's premier dayhome approval agency. Expert guidance through the complete certification process with 98% approval rate and 15+ years experience.">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'serif': ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        accent: {
                            50: '#fefce8',
                            100: '#fef9c3',
                            200: '#fef08a',
                            300: '#fde047',
                            400: '#facc15',
                            500: '#eab308',
                            600: '#ca8a04',
                            700: '#a16207',
                            800: '#854d0e',
                            900: '#713f12',
                        },
                        neutral: {
                            50: '#fafafa',
                            100: '#f5f5f5',
                            200: '#e5e5e5',
                            300: '#d4d4d4',
                            400: '#a3a3a3',
                            500: '#737373',
                            600: '#525252',
                            700: '#404040',
                            800: '#262626',
                            900: '#171717',
                        }
                    },
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem',
                        '128': '32rem',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'scale-in': 'scaleIn 0.5s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.95)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
    /* Enhanced Footer Styles */
    .bg-clip-text {
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
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
 .land-acknowledgement p{
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


</head>

<body class="font-sans antialiased bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-100 transition-colors duration-300" 
      :class="{ 'dark': darkMode }">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 dark:bg-neutral-900/90 backdrop-blur-lg border-b border-neutral-200/50 dark:border-neutral-700/50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 from-brand-500 to-brand-600 rounded-xl flex items-center justify-center">
                       <img src="{{ asset('assets/images/logo.png') }}" alt="logo spiced dayhome agency" class="w-20 h-10  rounded-lg">
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl md:text-2xl font-bold tracking-tight">
                            <span class="text-purple-600 dark:text-orange-400">SPICE'd</span>
                            <span class="text-neutral-800 dark:text-neutral-200 hidden sm:inline"> Dayhome</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400 font-medium hidden sm:block">Professional Approval Agency</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium sm:hidden">Dayhome Agency</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-medium">Home</a>
                   
                    <!-- Dark Mode Toggle
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button> -->
                    
                    <div class="flex items-center space-x-4">
                        <a href="/login" class="text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-medium">Login</a>
                        <a href="/apply" class="bg-purple-600 hover:bg-brand-700 text-white px-6 py-2.5 rounded-lg font-semibold transition-all duration-200 hover:shadow-lg">
                            Get Started
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="md:hidden bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-700">
            <div class="px-6 py-4 space-y-4">
                <a href="#home" class="block text-neutral-700 dark:text-neutral-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors font-medium">Home</a>
                
                <div class="flex space-x-4 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                    <a href="/login" class="flex-1 text-center py-3 border border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-700 dark:text-neutral-300 font-medium">Login</a>
                    <a href="/apply" class="flex-1 text-center py-3 bg-brand-600 text-white rounded-lg font-semibold">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
        <section id="home" class="relative min-h-screen flex items-center overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-neutral-50 via-red-50/30 via-orange-50/30 via-yellow-50/30 via-green-50/30 via-blue-50/30 to-purple-50/30 dark:from-neutral-900 dark:via-neutral-800/50 dark:to-neutral-900/10 transition-all duration-700"></div>
            
            <!-- Floating Rainbow Elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-16 h-16 bg-red-400/20 rounded-full animate-float blur-sm transition-all duration-1000"></div>
                <div class="absolute top-40 right-20 w-20 h-20 bg-orange-400/20 rounded-full animate-float blur-sm transition-all duration-1000" style="animation-delay: 0.5s;"></div>
                <div class="absolute bottom-40 left-20 w-12 h-12 bg-yellow-400/20 rounded-full animate-float blur-sm transition-all duration-1000" style="animation-delay: 1s;"></div>
                <div class="absolute bottom-20 right-32 w-24 h-24 bg-green-400/20 rounded-full animate-float blur-sm transition-all duration-1000" style="animation-delay: 1.5s;"></div>
                <div class="absolute top-60 left-1/3 w-14 h-14 bg-blue-400/20 rounded-full animate-float blur-sm transition-all duration-1000" style="animation-delay: 2s;"></div>
                <div class="absolute bottom-32 right-10 w-18 h-18 bg-purple-400/20 rounded-full animate-float blur-sm transition-all duration-1000" style="animation-delay: 2.5s;"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-20 md:py-32">
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                    
                    <!-- Content -->
                    <div class="space-y-6 md:space-y-8 animate-fade-in order-2 lg:order-1">
                        <div class="space-y-4 md:space-y-6">
                            <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold leading-tight">
                                <span class="text-neutral-900 dark:text-white transition-colors duration-300">Professional</span><br>
                                <span class="bg-gradient-to-r from-red-500 via-orange-500 via-yellow-500 via-green-500 via-blue-500 to-purple-500 bg-clip-text text-transparent animate-pulse">Dayhome</span><br>
                                <span class="text-neutral-900 dark:text-white transition-colors duration-300">Approval</span>
                            </h1>
                            
                            <p class="text-lg md:text-xl text-neutral-600 dark:text-neutral-300 leading-relaxed max-w-xl transition-colors duration-300">
                                Expert guidance through SPICE'd dayhome certification process with our 
                                <span class="font-semibold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent" style="background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-image: linear-gradient(to right, #16a34a, #2563eb); display: inline-block;">98% approval rate</span> 
                                and personalized support from application to operation.
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-4 animate-slide-up">
                            <a href="/apply" class="group bg-gradient-to-r from-purple-500 via-purple-500 to-purple-500 hover:from-red-600 hover:via-orange-600 hover:to-yellow-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all duration-300 hover:shadow-2xl hover:scale-105 text-center relative overflow-hidden">
                                <span class="relative z-10">Start Application</span>
                                <div class="absolute inset-0 bg-purple-500 via-purple-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </a>
                            <a href="#process" class="group border-2 border-purple-300 dark:border-purple-600 hover:border-transparent hover:bg-gradient-to-r hover:from-purple-500 hover:to-purple-500 text-neutral-700 dark:text-neutral-300 hover:text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all duration-300 text-center hover:shadow-xl hover:scale-105">
                                <span class="group-hover:text-white transition-colors duration-300">View Process</span>
                            </a>
                        </div>
                        
                        <!-- Trust Indicators -->
                        <div class="grid grid-cols-3 gap-4 md:gap-8 pt-6 md:pt-8 border-t border-neutral-200 dark:border-neutral-700 animate-scale-in">
                            <div class="text-center group hover:scale-110 transition-transform duration-300">
                                <div class="text-xl md:text-2xl font-bold bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent">500+</div>
                                <div class="text-xs md:text-sm text-neutral-500 dark:text-neutral-400 font-medium group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors duration-300">Approved Homes</div>
                            </div>
                            <div class="text-center group hover:scale-110 transition-transform duration-300">
                                <div class="text-xl md:text-2xl font-bold bg-gradient-to-r from-green-500 to-blue-500 bg-clip-text text-transparent">15+</div>
                                <div class="text-xs md:text-sm text-neutral-500 dark:text-neutral-400 font-medium group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors duration-300">Years Experience</div>
                            </div>
                            <div class="text-center group hover:scale-110 transition-transform duration-300">
                                <div class="text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-500 to-purple-500 bg-clip-text text-transparent">98%</div>
                                <div class="text-xs md:text-sm text-neutral-500 dark:text-neutral-400 font-medium group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors duration-300">Approval Rate</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hero Images - Responsive -->
                    <div class="relative animate-slide-up order-1 lg:order-2">
                        <div class="relative">
                            <!-- Main Hero Image - Responsive Heights -->
                            <div class="relative h-64 sm:h-80 md:h-96 lg:h-128 w-full rounded-2xl overflow-hidden shadow-2xl group">
                            <img 
                                    src="{{ asset('assets/images/splash.webp') }}" 
                                    alt="Happy children learning and playing in a professional dayhome environment"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <!-- Gradient overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <!-- Floating overlay content -->
                                <div class="absolute bottom-4 md:bottom-24 left-4 md:left-6 right-4 md:right-6 transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                    <div class="bg-white/90 dark:bg-neutral-900/90 backdrop-blur-sm rounded-lg p-3 md:p-4 shadow-lg">
                                        <p class="text-xs md:text-sm font-semibold text-neutral-800 dark:text-neutral-200">Safe, nurturing environment</p>
                                        <p class="text-xs text-neutral-600 dark:text-neutral-400">Professional childcare approval</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Secondary floating images - Hidden on mobile, visible on md+ -->
                            <div class="hidden md:block absolute -top-8 -right-8 w-24 h-24 lg:w-32 lg:h-32 rounded-xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-110 hover:rotate-3">
                                <img 
                                    src="{{ asset('assets/images/kid1.jpeg') }}"
                                    alt="Children engaged in educational activities"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-gradient-to-br from-red-400/20 to-orange-400/20"></div>
                            </div>
                            
                            <div class="hidden md:block absolute -bottom-8 -left-8 w-28 h-20 lg:w-40 lg:h-28 rounded-xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-110 hover:-rotate-2">
                                <img 
                                    src="{{ asset('assets/images/play.jpg') }}"
                                    alt="Children engaged in educational activities"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-blue-400/20"></div>
                            </div>
                            
                            <!-- Rainbow accent elements - Hidden on mobile -->
                            <div class="hidden md:block absolute top-1/4 -left-4 w-6 h-12 lg:w-8 lg:h-16 bg-gradient-to-b from-red-400 via-orange-400 via-yellow-400 via-green-400 via-blue-400 to-purple-400 rounded-full opacity-70 animate-float blur-sm"></div>
                            <div class="hidden md:block absolute bottom-1/4 -right-4 w-5 h-14 lg:w-6 lg:h-20 bg-gradient-to-b from-purple-400 via-blue-400 via-green-400 via-yellow-400 via-orange-400 to-red-400 rounded-full opacity-70 animate-float blur-sm" style="animation-delay: 1.5s;"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <div class="w-6 h-10 border-2 border-neutral-400 dark:border-neutral-600 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-neutral-400 dark:bg-neutral-600 rounded-full animate-pulse mt-2"></div>
                </div>
            </div>
        </section>

    <!-- About/SPICE Section -->
    <section id="about" class="py-24 bg-white dark:bg-neutral-900">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
        <!-- Text Section -->
        <div class="space-y-10">
            <!-- Mission -->
            <div>
            <h2 class="text-4xl lg:text-5xl font-bold text-neutral-900 dark:text-white mb-4">
                Our <span class="text-brand-600 dark:text-brand-400">Vision</span>
            </h2>
            <p class="text-lg text-neutral-600 dark:text-neutral-300 leading-relaxed">
                We believe that every child deserves the opportunity to fully develop. Therefore, we work to ensure that every 
              child's basic developmental skills and needs are being improved, and met.
            </p>
            </div>

            <!-- Vision -->
            <div>
            <h2 class="text-4xl lg:text-5xl font-bold text-neutral-900 dark:text-white mb-4">
                Our <span class="text-brand-600 dark:text-brand-400">Mission</span>
            </h2>
            <p class="text-lg text-neutral-600 dark:text-neutral-300 leading-relaxed">
              Our mission is to assist children in developing their fundamental Social, Physical,
                Intellectual, Creative, and Emotional skills. This is achieved through child-led exploration,
                investigation, imagination, creativity, and problem solving.
            </p>
            </div>
        </div>

        <!-- Image Section -->
        <div class="flex justify-center">
            <img
             src="{{ asset('assets/images/art.jpg') }}" 
            alt="SPICE'd team or classroom"
            class="rounded-3xl shadow-xl object-cover w-full max-w-md h-[400px]"
            />
        </div>
        </div>

        <!-- Stats Section -->
        <div
        class="bg-gradient-to-r from-brand-50 to-accent-50 dark:from-brand-900/20 dark:to-accent-900/20 rounded-3xl p-12"
        >
        <div class="grid md:grid-cols-4 gap-8 text-center">
            <div>
            <div class="text-4xl font-bold text-brand-600 dark:text-brand-400 mb-2">500+</div>
            <div class="text-neutral-600 dark:text-neutral-300 font-medium">Approved Dayhomes</div>
            </div>
            <div>
            <div class="text-4xl font-bold text-brand-600 dark:text-brand-400 mb-2">2000+</div>
            <div class="text-neutral-600 dark:text-neutral-300 font-medium">Families Served</div>
            </div>
            <div>
            <div class="text-4xl font-bold text-brand-600 dark:text-brand-400 mb-2">15+</div>
            <div class="text-neutral-600 dark:text-neutral-300 font-medium">Years Experience</div>
            </div>
            <div>
            <div class="text-4xl font-bold text-brand-600 dark:text-brand-400 mb-2">98%</div>
            <div class="text-neutral-600 dark:text-neutral-300 font-medium">Success Rate</div>
            </div>
        </div>
        </div>
    </div>
    </section>


    <!-- Services Section -->
    <section id="services" class="py-24 bg-neutral-50 dark:bg-neutral-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16 space-y-4"
                x-data="{
                    inView: false,
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    this.inView = true;
                                }
                            });
                        }, { threshold: 0.3 });
                        
                        observer.observe(this.$el);
                    }
                }"
                x-init="init"
                :class="inView ? 'animate-fade-in' : 'opacity-0 translate-y-8'"
                class="transition-all duration-700 ease-out">
                <h2 class="text-4xl lg:text-5xl font-bold text-neutral-900 dark:text-white">
                    Our <span class="text-brand-600 dark:text-brand-400">Services</span>
                </h2>
                <p class="text-xl text-neutral-600 dark:text-neutral-300 max-w-3xl mx-auto">
                    Comprehensive support throughout your dayhome approval journey
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
                    x-data="{
                        inView: false,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        setTimeout(() => this.inView = true, 200);
                                    }
                                });
                            }, { threshold: 0.3 });
                            
                            observer.observe(this.$el);
                        }
                    }"
                    x-init="init"
                    :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-700 ease-out delay-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-600 rounded-xl flex items-center justify-center mb-6 transform hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-neutral-900 dark:text-white">Complete approval</h3>
                    <p class="text-neutral-600 dark:text-neutral-300 mb-6">End-to-end support from initial application through final approval and beyond.</p>
                    <ul class="space-y-2 text-sm text-neutral-500 dark:text-neutral-400">
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-brand-500 rounded-full mr-3"></span>Application assistance</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-brand-500 rounded-full mr-3"></span>Document preparation</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-brand-500 rounded-full mr-3"></span>Regulatory compliance</li>
                    </ul>
                </div>

                <!-- Service 2 -->
                <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
                    x-data="{
                        inView: false,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        setTimeout(() => this.inView = true, 300);
                                    }
                                });
                            }, { threshold: 0.3 });
                            
                            observer.observe(this.$el);
                        }
                    }"
                    x-init="init"
                    :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-700 ease-out delay-150">
                    <div class="w-16 h-16 bg-gradient-to-br from-accent-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 transform hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-neutral-900 dark:text-white">Safety Inspections</h3>
                    <p class="text-neutral-600 dark:text-neutral-300 mb-6">Thorough home safety assessments to ensure full compliance with Alberta regulations.</p>
                    <ul class="space-y-2 text-sm text-neutral-500 dark:text-neutral-400">
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-accent-500 rounded-full mr-3"></span>Pre-inspection consultation</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-accent-500 rounded-full mr-3"></span>Detailed safety reports</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-accent-500 rounded-full mr-3"></span>Correction guidance</li>
                    </ul>
                </div>

                <!-- Service 3 -->
                <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
                    x-data="{
                        inView: false,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        setTimeout(() => this.inView = true, 400);
                                    }
                                });
                            }, { threshold: 0.3 });
                            
                            observer.observe(this.$el);
                        }
                    }"
                    x-init="init"
                    :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-700 ease-out delay-200">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-6 transform hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm3.5 6L12 10.5 8.5 8 12 5.5 15.5 8zM12 19c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-neutral-900 dark:text-white">Training Programs</h3>
                    <p class="text-neutral-600 dark:text-neutral-300 mb-6">Comprehensive education to prepare you for successful dayhome operation.</p>
                    <ul class="space-y-2 text-sm text-neutral-500 dark:text-neutral-400">
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3"></span>Child development</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3"></span>Safety protocols</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3"></span>Business management</li>
                    </ul>
                </div>

                <!-- Service 4 -->
                <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
                    x-data="{
                        inView: false,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        setTimeout(() => this.inView = true, 500);
                                    }
                                });
                            }, { threshold: 0.3 });
                            
                            observer.observe(this.$el);
                        }
                    }"
                    x-init="init"
                    :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-700 ease-out delay-250">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 transform hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-neutral-900 dark:text-white">Ongoing Support</h3>
                    <p class="text-neutral-600 dark:text-neutral-300 mb-6">Continuous assistance to maintain compliance and grow your dayhome business.</p>
                    <ul class="space-y-2 text-sm text-neutral-500 dark:text-neutral-400">
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-3"></span>Approval renewals</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-3"></span>Policy updates</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-3"></span>Business coaching</li>
                    </ul>
                </div>

                <!-- Service 5 -->
                <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
                    x-data="{
                        inView: false,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        setTimeout(() => this.inView = true, 600);
                                    }
                                });
                            }, { threshold: 0.3 });
                            
                            observer.observe(this.$el);
                        }
                    }"
                    x-init="init"
                    :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-700 ease-out delay-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mb-6 transform hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-neutral-900 dark:text-white">Document Review</h3>
                    <p class="text-neutral-600 dark:text-neutral-300 mb-6">Expert review of all required documentation to ensure accuracy and compliance.</p>
                    <ul class="space-y-2 text-sm text-neutral-500 dark:text-neutral-400">
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-3"></span>Policy validation</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-3"></span>Legal compliance</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-3"></span>Error correction</li>
                    </ul>
                </div>

                <!-- Service 6 -->
                <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
                    x-data="{
                        inView: false,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        setTimeout(() => this.inView = true, 700);
                                    }
                                });
                            }, { threshold: 0.3 });
                            
                            observer.observe(this.$el);
                        }
                    }"
                    x-init="init"
                    :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-700 ease-out delay-350">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 transform hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2zm0 7.27L9.18 11l-2.09-.3L9.18 9L12 9.27zm0 4.19l.82 1.63 1.63.82-1.63.82L12 13.46z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-neutral-900 dark:text-white">Premium Support</h3>
                    <p class="text-neutral-600 dark:text-neutral-300 mb-6">24/7 priority support with dedicated consultation hours for urgent matters.</p>
                    <ul class="space-y-2 text-sm text-neutral-500 dark:text-neutral-400">
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3"></span>Priority response</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3"></span>Direct consultant access</li>
                        <li class="flex items-center transform hover:translate-x-1 transition-transform duration-200"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3"></span>Emergency support</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section id="process" class="py-24 bg-white dark:bg-neutral-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16 space-y-4" 
                x-data="{
                    inView: false,
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    this.inView = true;
                                }
                            });
                        }, { threshold: 0.3 });
                        
                        observer.observe(this.$el);
                    }
                }"
                x-init="init"
                :class="inView ? 'animate-fade-in' : 'opacity-0 translate-y-8'"
                class="transition-all duration-700 ease-out">
                <h2 class="text-4xl lg:text-5xl font-bold text-neutral-900 dark:text-white">
                    Our <span class="text-brand-600 dark:text-brand-400">Process</span>
                </h2>
                <p class="text-xl text-neutral-600 dark:text-neutral-300 max-w-3xl mx-auto">
                    A streamlined, step-by-step approach to getting your dayhome approval and operational
                </p>
            </div>

            <div class="relative">
                <!-- Timeline Line -->
                <div class="hidden lg:block absolute left-1/2 transform -translate-x-1/2 w-1 h-full bg-gradient-to-b from-brand-200 to-brand-400 rounded-full"
                    x-data="{
                        inView: false,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        setTimeout(() => this.inView = true, 200);
                                    }
                                });
                            }, { threshold: 0.3 });
                            
                            observer.observe(this.$el);
                        }
                    }"
                    x-init="init"
                    :class="inView ? 'animate-scale-in' : 'scale-y-0'"
                    class="transform origin-top transition-transform duration-1000 ease-out"></div>

                <div class="space-y-16">
                    <!-- Step 1 -->
                    <div class="flex flex-col lg:flex-row items-center"
                        x-data="{
                            inView: false,
                            init() {
                                const observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            setTimeout(() => this.inView = true, 300);
                                        }
                                    });
                                }, { threshold: 0.3 });
                                
                                observer.observe(this.$el);
                            }
                        }"
                        x-init="init"
                        :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                        class="transition-all duration-700 ease-out delay-100">
                        <div class="lg:w-1/2 lg:pr-16 mb-8 lg:mb-0">
                            <div class="bg-white dark:bg-neutral-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ml-auto lg:max-w-lg transform hover:-translate-y-2">
                                <div class="flex items-center mb-4">
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-3 py-1 rounded-full text-sm font-semibold">Step 1</span>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-neutral-900 dark:text-white">Initial Consultation</h3>
                                <p class="text-neutral-600 dark:text-neutral-300 mb-4">
                                    Schedule a comprehensive consultation to assess your readiness and create a personalized approval plan.
                                </p>
                                <div class="text-sm text-neutral-500 dark:text-neutral-400 space-y-1">
                                    <div class="flex items-center"><span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span><strong>Duration:</strong> 60-90 minutes</div>
                                    <div class="flex items-center"><span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span><strong>Format:</strong> In-person or virtual</div>
                                </div>
                            </div>
                        </div>
                        <div class="relative lg:w-1/2 lg:pl-16">
                            <div class="w-20 h-20 bg-gradient-to-br from-red-400 to-red-600 rounded-2xl flex items-center justify-center mx-auto lg:ml-0 relative z-10 shadow-lg transform hover:scale-110 transition-transform duration-300" style="color: #ffffff !important; font-weight: 700; font-size: 1.5rem; line-height: 1;">
                                <span style="color: #ffffff !important; display: block;">1</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex flex-col lg:flex-row-reverse items-center"
                        x-data="{
                            inView: false,
                            init() {
                                const observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            setTimeout(() => this.inView = true, 500);
                                        }
                                    });
                                }, { threshold: 0.3 });
                                
                                observer.observe(this.$el);
                            }
                        }"
                        x-init="init"
                        :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                        class="transition-all duration-700 ease-out delay-200">
                        <div class="lg:w-1/2 lg:pl-16 mb-8 lg:mb-0">
                            <div class="bg-white dark:bg-neutral-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 mr-auto lg:max-w-lg transform hover:-translate-y-2">
                                <div class="flex items-center mb-4">
                                    <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 px-3 py-1 rounded-full text-sm font-semibold">Step 2</span>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-neutral-900 dark:text-white">Application Preparation</h3>
                                <p class="text-neutral-600 dark:text-neutral-300 mb-4">
                                    Complete your official application with our guided assistance and document checklist.
                                </p>
                                <div class="text-sm text-neutral-500 dark:text-neutral-400 space-y-1">
                                    <div class="flex items-center"><span class="w-2 h-2 bg-orange-400 rounded-full mr-2"></span><strong>Duration:</strong> 2-3 days</div>
                                    <div class="flex items-center"><span class="w-2 h-2 bg-orange-400 rounded-full mr-2"></span><strong>Support:</strong> Document review included</div>
                                </div>
                            </div>
                        </div>
                        <div class="relative lg:w-1/2 lg:pr-16">
                            <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center mx-auto lg:mr-0 relative z-10 shadow-lg transform hover:scale-110 transition-transform duration-300" style="color: #ffffff !important; font-weight: 700; font-size: 1.5rem; line-height: 1;">
                                <span style="color: #ffffff !important; display: block;">2</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex flex-col lg:flex-row items-center"
                        x-data="{
                            inView: false,
                            init() {
                                const observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            setTimeout(() => this.inView = true, 700);
                                        }
                                    });
                                }, { threshold: 0.3 });
                                
                                observer.observe(this.$el);
                            }
                        }"
                        x-init="init"
                        :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                        class="transition-all duration-700 ease-out delay-300">
                        <div class="lg:w-1/2 lg:pr-16 mb-8 lg:mb-0">
                            <div class="bg-white dark:bg-neutral-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ml-auto lg:max-w-lg transform hover:-translate-y-2">
                                <div class="flex items-center mb-4">
                                    <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 px-3 py-1 rounded-full text-sm font-semibold">Step 3</span>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-neutral-900 dark:text-white">Safety Assessment</h3>
                                <p class="text-neutral-600 dark:text-neutral-300 mb-4">
                                    Professional safety inspection of your home to ensure full regulatory compliance.
                                </p>
                                <div class="text-sm text-neutral-500 dark:text-neutral-400 space-y-1">
                                    <div class="flex items-center"><span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span><strong>Duration:</strong> 2-3 hours</div>
                                    <div class="flex items-center"><span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span><strong>Report:</strong> Detailed findings provided</div>
                                </div>
                            </div>
                        </div>
                        <div class="relative lg:w-1/2 lg:pl-16">
                            <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-2xl flex items-center justify-center mx-auto lg:ml-0 relative z-10 shadow-lg transform hover:scale-110 transition-transform duration-300" style="color: #ffffff !important; font-weight: 700; font-size: 1.5rem; line-height: 1;">
                                <span style="color: #ffffff !important; display: block;">3</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex flex-col lg:flex-row-reverse items-center"
                        x-data="{
                            inView: false,
                            init() {
                                const observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            setTimeout(() => this.inView = true, 900);
                                        }
                                    });
                                }, { threshold: 0.3 });
                                
                                observer.observe(this.$el);
                            }
                        }"
                        x-init="init"
                        :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                        class="transition-all duration-700 ease-out delay-400">
                        <div class="lg:w-1/2 lg:pl-16 mb-8 lg:mb-0">
                            <div class="bg-white dark:bg-neutral-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 mr-auto lg:max-w-lg transform hover:-translate-y-2">
                                <div class="flex items-center mb-4">
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-3 py-1 rounded-full text-sm font-semibold">Step 4</span>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-neutral-900 dark:text-white">Uploading & Certification</h3>
                                <p class="text-neutral-600 dark:text-neutral-300 mb-4">
                                    Complete all required modules and obtain necessary certifications for operation.
                                </p>
                                <div class="text-sm text-neutral-500 dark:text-neutral-400 space-y-1">
                                    <div class="flex items-center"><span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span><strong>Duration:</strong> 1-2 weeks</div>
                                    <div class="flex items-center"><span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span><strong>Format:</strong> Online and practical</div>
                                </div>
                            </div>
                        </div>
                        <div class="relative lg:w-1/2 lg:pr-16">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto lg:mr-0 relative z-10 shadow-lg transform hover:scale-110 transition-transform duration-300" style="color: #ffffff !important; font-weight: 700; font-size: 1.5rem; line-height: 1;">
                                <span style="color: #ffffff !important; display: block;">4</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="flex flex-col lg:flex-row items-center"
                        x-data="{
                            inView: false,
                            init() {
                                const observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            setTimeout(() => this.inView = true, 1100);
                                        }
                                    });
                                }, { threshold: 0.3 });
                                
                                observer.observe(this.$el);
                            }
                        }"
                        x-init="init"
                        :class="inView ? 'animate-slide-up' : 'opacity-0 translate-y-12'"
                        class="transition-all duration-700 ease-out delay-500">
                        <div class="lg:w-1/2 lg:pr-16 mb-8 lg:mb-0">
                            <div class="bg-white dark:bg-neutral-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ml-auto lg:max-w-lg transform hover:-translate-y-2">
                                <div class="flex items-center mb-4">
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-sm font-semibold">Step 5</span>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-neutral-900 dark:text-white">Final Approval</h3>
                                <p class="text-neutral-600 dark:text-neutral-300 mb-4">
                                    Receive your official approval and begin operating your approved dayhome.
                                </p>
                                <div class="text-sm text-neutral-500 dark:text-neutral-400 space-y-1">
                                    <div class="flex items-center"><span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span><strong>Duration:</strong> 3-5 business days</div>
                                    <div class="flex items-center"><span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span><strong>Support:</strong> Launch assistance included</div>
                                </div>
                            </div>
                        </div>
                        <div class="relative lg:w-1/2 lg:pl-16">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto lg:ml-0 relative z-10 shadow-lg transform hover:scale-110 transition-transform duration-300" style="color: #ffffff !important; font-weight: 700; font-size: 1.5rem; line-height: 1;">
                                <span style="color: #ffffff !important; display: block;">✓</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-16"
                x-data="{
                    inView: false,
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    setTimeout(() => this.inView = true, 1300);
                                }
                            });
                        }, { threshold: 0.3 });
                        
                        observer.observe(this.$el);
                    }
                }"
                x-init="init"
                :class="inView ? 'animate-scale-in' : 'opacity-0 scale-95'"
                class="transition-all duration-700 ease-out">
                <a href="/apply" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all duration-200 hover:shadow-xl hover:scale-105 inline-block transform hover:-translate-y-1">
                    Begin Your Journey
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-neutral-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <!-- Top Row -->
            <div class="grid lg:grid-cols-3 gap-12 mb-12">
                <!-- Left: Business Info -->
                <div class="animate-on-scroll">
                    <h3 class="text-3xl font-bold mb-2">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400" style="background-clip: text; -webkit-background-clip: text; color: transparent; background-image: linear-gradient(to right, #a78bfa, #f472b6);">SPICE'd</span> 
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
                    <div class="land-acknowledgement  from-dark-800 to-dark-900 bg-white  text-dark-200 italic p-8 rounded-2xl shadow-lg border border-neutral-700 hover:border-purple-500/50">
                        <i class="fas fa-leaf text-purple-400 mb-3 text-2xl block"></i>
                        <p class="leading-relaxed ">
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
                        <li><a href="/" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Home</a></li>
                        <li><a href="/for-parents" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">For Parents</a></li>
                        <li><a href="/for-educators" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">For Educators</a></li>
                        <li><a href="/about" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">About</a></li>
                        <li><a href="/resources" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Resources</a></li>
                        <li><a href="/contact" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div class="animate-on-scroll" style="animation-delay: 0.5s;">
                    <h4 class="section-heading text-lg font-semibold mb-6 text-sky-300">
                        <i class="fas fa-link mr-2"></i>Quick Links
                    </h4>
                    <ul class="space-y-2 mb-6">
                        <li><a href="/faqs-parents" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">FAQs for Parents</a></li>
                        <li><a href="/faqs-educators" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">FAQs for Educators</a></li>
                        <li><a href="/agency-fees" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Agency Fees</a></li>
                    </ul>

                    <h4 class="section-heading text-lg font-semibold mb-6 text-sky-300">
                        <i class="fas fa-shield-alt mr-2"></i>Privacy Policy
                    </h4>
                    <ul class="space-y-2">
                        <li><a href="/privacy" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Privacy</a></li>
                        <li><a href="/terms" class="footer-link text-neutral-300 hover:text-white hover:translate-x-1 inline-block">Terms of Use</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom -->
            <div class="border-t border-neutral-800 pt-8 text-center">
                <p class="text-neutral-400 text-sm">
                    <i class="far fa-copyright mr-1"></i>
                    <span id="currentYear"></span> SPICE'd Childcare Services. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Floating CTA -->
    <div class="fixed bottom-8 right-8 z-50">
        <a href="/apply" class="w-16 h-16 bg-brand-600 hover:bg-brand-700 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-110 group">
            <svg class="w-8 h-8 text-white transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
        </a>
    </div>

    <!-- Scripts -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    if (window.Alpine) {
                        Alpine.store('mobileMenuOpen', false);
                    }
                }
            });
        });

        // Enhanced scroll effects for navigation
        let lastScrollY = window.scrollY;
        const nav = document.querySelector('nav');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            
            // Add/remove background opacity based on scroll position
            if (currentScrollY > 50) {
                nav.classList.add('bg-white/95', 'dark:bg-neutral-900/95', 'shadow-lg');
                nav.classList.remove('bg-white/90', 'dark:bg-neutral-900/90');
            } else {
                nav.classList.add('bg-white/90', 'dark:bg-neutral-900/90');
                nav.classList.remove('bg-white/95', 'dark:bg-neutral-900/95', 'shadow-lg');
            }

            // Hide/show nav on scroll direction (optional enhancement)
            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                nav.style.transform = 'translateY(-100%)';
            } else {
                nav.style.transform = 'translateY(0)';
            }
            
            lastScrollY = currentScrollY;
        });

        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                    // Optional: Add staggered animation for child elements
                    const children = entry.target.querySelectorAll('.stagger-animation');
                    children.forEach((child, index) => {
                        setTimeout(() => {
                            child.classList.add('animate-slide-up');
                        }, index * 100);
                    });
                }
            });
        }, observerOptions);

        // Observe sections for scroll animations
        document.querySelectorAll('section, .hover\\:shadow-xl').forEach(el => {
            observer.observe(el);
        });

        // Form validation and submission
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500');
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });
                
                if (isValid) {
                    // Show success message (you would typically send to server here)
                    const button = form.querySelector('button[type="submit"]');
                    const originalText = button.textContent;
                    button.textContent = 'Sending...';
                    button.disabled = true;
                    
                    setTimeout(() => {
                        button.textContent = 'Message Sent!';
                        button.classList.add('bg-green-600');
                        setTimeout(() => {
                            button.textContent = originalText;
                            button.disabled = false;
                            button.classList.remove('bg-green-600');
                            form.reset();
                        }, 2000);
                    }, 1000);
                }
            });
        });

        // Enhanced mobile menu
        document.addEventListener('alpine:init', () => {
            Alpine.store('ui', {
                mobileMenuOpen: false,
                darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches,
                
                toggleMobileMenu() {
                    this.mobileMenuOpen = !this.mobileMenuOpen;
                },
                
                closeMobileMenu() {
                    this.mobileMenuOpen = false;
                },
                
                // toggleDarkMode() {
                //     this.darkMode = !this.darkMode;
                //     localStorage.setItem('darkMode', this.darkMode);
                // }
            });
        });

        // Load saved dark mode preference
       localStorage.removeItem('darkMode');
        document.documentElement.classList.remove('dark');
    </script>
    <script>
    // Auto-update copyright year
    document.getElementById('currentYear').textContent = new Date().getFullYear();

    // Optional: Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        observer.observe(el);
    });
</script>
</body>
</html> 