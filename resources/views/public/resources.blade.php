@extends('layouts.app')

@section('title', 'Resources - SPICE\'d Dayhome Agency')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                Resources
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Helpful resources for parents and educators
            </p>
        </div>

        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">For Parents</h3>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('faqs-parents') }}" class="hover:text-purple-600">FAQs for Parents</a></li>
                    <li><a href="{{ route('for-parents') }}" class="hover:text-purple-600">Finding a Dayhome</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-purple-600">Contact Support</a></li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">For Educators</h3>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('faqs-educators') }}" class="hover:text-purple-600">FAQs for Educators</a></li>
                    <li><a href="{{ route('for-educators') }}" class="hover:text-purple-600">Becoming Approved</a></li>
                    <li><a href="{{ route('agency-fees') }}" class="hover:text-purple-600">Agency Fees</a></li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Documentation</h3>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('privacy') }}" class="hover:text-purple-600">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-purple-600">Terms of Service</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-purple-600">About Us</a></li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Support</h3>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('contact') }}" class="hover:text-purple-600">Contact Us</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-purple-600">General FAQ</a></li>
                    <li><a href="https://support.algosoftwarelabs.com/" target="_blank" class="hover:text-purple-600">Help Center</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

