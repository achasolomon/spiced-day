@extends('layouts.app')

@section('title', 'FAQ - SPICE\'d Dayhome Agency')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                Frequently Asked Questions
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Find answers to common questions
            </p>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">General Questions</h3>
                <div class="space-y-4 mt-4">
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">What is SPICE'd Dayhome Agency?</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            SPICE'd Dayhome Agency is an approved dayhome approval agency that helps aspiring educators become approved dayhome providers in Alberta. We provide comprehensive support throughout the approval process.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">How do I get started?</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Start by creating an account and submitting your application. Our team will review your application and assign a consultant to guide you through the process.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Quick Links</h3>
                <div class="grid md:grid-cols-2 gap-4 mt-4">
                    <a href="{{ route('faqs-parents') }}" class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">FAQs for Parents</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Questions about finding a dayhome</p>
                    </a>
                    <a href="{{ route('faqs-educators') }}" class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">FAQs for Educators</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Questions about becoming approved</p>
                    </a>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-2xl shadow-lg p-8 text-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Can't find what you're looking for?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Contact our support team for assistance.
                </p>
                <a href="{{ route('contact') }}" class="inline-block px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-colors">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

