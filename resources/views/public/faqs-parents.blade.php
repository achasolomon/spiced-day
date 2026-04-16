@extends('layouts.app')

@section('title', 'FAQs for Parents - SPICE\'d Dayhome Agency')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                FAQs for Parents
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Common questions about finding and choosing an approved dayhome
            </p>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">What is an approved dayhome?</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    An approved dayhome is a private residence that has been approved by SPICE'd Childcare Services to provide childcare. All approved dayhomes meet strict safety, health, and educational standards and are regularly inspected to ensure compliance.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">How do I find an approved dayhome in my area?</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Contact SPICE'd Childcare Services directly, and we can help match you with approved dayhome educators in your area based on your location and childcare needs.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">What qualifications do SPICE'd educators have?</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    All SPICE'd approved educators have appropriate childcare training, current First Aid and CPR certifications, criminal record checks, and meet all Alberta childcare standards.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">How often are dayhomes inspected?</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Approved dayhomes undergo regular compliance inspections to ensure they continue to meet safety and quality standards. Initial inspections are conducted before approval, and ongoing inspections occur throughout the year.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">What should I look for when choosing a dayhome?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-3">
                    When choosing a dayhome, consider:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                    <li>Approval status and current certifications</li>
                    <li>Safety and cleanliness of the home environment</li>
                    <li>Educator qualifications and experience</li>
                    <li>Program and activities offered</li>
                    <li>Location and hours of operation</li>
                    <li>References from other parents</li>
                </ul>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-2xl shadow-lg p-8 text-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Still have questions?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Contact us for more information about finding the right dayhome for your family.
                </p>
                <a href="{{ route('contact') }}" class="inline-block px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-colors">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

