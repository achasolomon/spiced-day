@extends('layouts.app')

@section('title', 'FAQs for Educators - SPICE\'d Dayhome Agency')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                FAQs for Educators
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Common questions about becoming an approved dayhome educator
            </p>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">How long does the approval process take?</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    The approval process typically takes several weeks to a few months, depending on how quickly you complete each step. Our consultants will guide you through the process and help ensure all requirements are met efficiently.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">What are the requirements to become approved?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-3">
                    Requirements include:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                    <li>Appropriate childcare education and training</li>
                    <li>Current First Aid and CPR certification</li>
                    <li>Clear criminal record check</li>
                    <li>Safe and suitable home environment</li>
                    <li>Completion of all required inspections</li>
                    <li>Submission and approval of all required documents</li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">What support do you provide during the process?</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    You'll be assigned a dedicated consultant who will guide you through every step, answer your questions, help prepare for inspections, review your documents, and provide ongoing support throughout the approval process.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">What happens after I'm approved?</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    After receiving your approval, you'll continue to receive support from SPICE'd. We conduct regular compliance inspections and provide ongoing assistance to help you maintain your approval and operate a successful dayhome.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">What are the agency fees?</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Please see our <a href="{{ route('agency-fees') }}" class="text-purple-600 hover:text-purple-700 underline">Agency Fees</a> page for detailed information about approval fees and ongoing costs.
                </p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-2xl shadow-lg p-8 text-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Ready to get started?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
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

