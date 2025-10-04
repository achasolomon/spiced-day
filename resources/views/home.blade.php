<!-- resources/views/home.blade.php -->
@extends('layouts.guest')

@section('title', 'Welcome to SPICE\'d Dayhome Agency')

@section('content')
<div class="relative z-10 flex flex-col items-center justify-center min-h-screen px-6 py-12 text-center">
    
    <!-- Hero Section -->
    <div class="glass-card rounded-2xl p-10 max-w-3xl w-full shadow-xl hover-lift">
        <h1 class="text-4xl sm:text-5xl font-extrabold mb-6 text-gray-800 dark:text-white">
            Welcome to <span class="rainbow-gradient bg-clip-text text-transparent">SPICE'd Dayhome Agency</span>
        </h1>
        
        <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-300 mb-8">
            Streamline your dayhome applications, manage documents, and connect with our agency.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @guest
                <a href="{{ route('login') }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold shadow-md hover:bg-blue-700 transition">
                    Log In
                </a>
                <a href="{{ route('register') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-800 rounded-xl font-semibold shadow-md hover:bg-gray-200 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition">
                    Register
                </a>
            @else
                <a href="{{ route('dashboard') }}" 
                   class="px-6 py-3 bg-green-600 text-white rounded-xl font-semibold shadow-md hover:bg-green-700 transition">
                    Go to Dashboard
                </a>
            @endguest
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-12 text-sm text-gray-500 dark:text-gray-400">
        &copy; {{ date('Y') }} SPICE'd Dayhome Agency. All rights reserved.
    </div>
</div>
@endsection
