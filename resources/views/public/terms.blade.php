@extends('layouts.app')

@section('title', 'Terms of Use - SPICE\'d Dayhome Agency')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                Terms of Use
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Last updated: {{ date('F d, Y') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 space-y-8">
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1. Acceptance of Terms</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    By accessing and using the SPICE'd Dayhome Agency platform, you accept and agree to be bound by these Terms of Use. If you do not agree to these terms, please do not use our services.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">2. Use of Services</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    You agree to:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                    <li>Provide accurate and complete information</li>
                    <li>Maintain the security of your account</li>
                    <li>Use the platform only for lawful purposes</li>
                    <li>Comply with all applicable laws and regulations</li>
                    <li>Not share your account credentials with others</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">3. Approval Process</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    The approval process requires completion of all required steps, including application submission, inspections, and document approval. SPICE'd reserves the right to deny or revoke approvals that do not meet our standards.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">4. Fees and Payments</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    All fees are non-refundable unless otherwise stated. You are responsible for paying all applicable fees associated with the approval process and annual renewals.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">5. Intellectual Property</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    All content, features, and functionality of the platform are owned by SPICE'd Dayhome Agency and are protected by copyright, trademark, and other intellectual property laws.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">6. Limitation of Liability</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    SPICE'd Dayhome Agency shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the platform or services.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">7. Contact Information</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    For questions about these Terms of Use, please contact us at <a href="mailto:support@spiceddayhome.com" class="text-purple-600 hover:text-purple-700">support@spiceddayhome.com</a>.
                </p>
            </section>
        </div>
    </div>
</div>
@endsection

