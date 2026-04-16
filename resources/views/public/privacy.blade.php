@extends('layouts.app')

@section('title', 'Privacy Policy - SPICE\'d Dayhome Agency')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                Privacy Policy
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Last updated: {{ date('F d, Y') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 space-y-8">
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1. Information We Collect</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    We collect information that you provide directly to us, including personal information such as your name, email address, phone number, and address when you register for an account or submit an application.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">2. How We Use Your Information</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    We use the information we collect to:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                    <li>Process and manage your application</li>
                    <li>Provide approval and support services</li>
                    <li>Communicate with you about your account and services</li>
                    <li>Conduct inspections and compliance reviews</li>
                    <li>Improve our services and user experience</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">3. Information Sharing</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    We do not sell your personal information. We may share your information with consultants assigned to your application, regulatory authorities as required by law, and service providers who assist us in operating our platform.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">4. Data Security</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">5. Your Rights</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    You have the right to:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                    <li>Access your personal information</li>
                    <li>Correct inaccurate information</li>
                    <li>Request deletion of your information</li>
                    <li>Opt-out of certain communications</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">6. Contact Us</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    If you have questions about this Privacy Policy, please contact us at <a href="mailto:support@spiceddayhome.com" class="text-purple-600 hover:text-purple-700">support@spiceddayhome.com</a>.
                </p>
            </section>
        </div>
    </div>
</div>
@endsection

