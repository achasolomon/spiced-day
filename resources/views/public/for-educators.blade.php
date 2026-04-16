@extends('layouts.app')

@section('title', 'For Educators - SPICE\'d Dayhome Agency')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                For Educators
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Start your journey to becoming an approved dayhome educator
            </p>
        </div>

        <div class="max-w-4xl mx-auto space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Why Become a SPICE'd Approved Dayhome Educator?</h2>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong class="text-gray-900 dark:text-white">Expert Support:</strong> Comprehensive guidance through every step of the approval process.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong class="text-gray-900 dark:text-white">98% Approval Rate:</strong> Our proven process ensures your success.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong class="text-gray-900 dark:text-white">Dedicated Consultant:</strong> Personal consultant assigned to guide you through the process.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong class="text-gray-900 dark:text-white">Ongoing Support:</strong> Continued assistance even after certification.</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">The Approval Process</h2>
                <div class="space-y-4 text-gray-600 dark:text-gray-400">
                    <p>Our streamlined process includes:</p>
                    <ol class="list-decimal list-inside space-y-2 ml-4">
                        <li>Application submission and review</li>
                        <li>Meet & Greet with your consultant</li>
                        <li>Initial home inspection</li>
                        <li>Document submission and approval</li>
                        <li>Second and final inspections</li>
                        <li>Certification and activation</li>
                    </ol>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-2xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Ready to Get Started?</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Begin your application today and take the first step toward becoming an approved dayhome educator.
                </p>
                <a href="/apply" class="inline-block px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold text-lg transition-colors shadow-lg">
                    Apply Now
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

