@extends('layouts.dashboard')

@section('title', 'Application Details')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Application Number</p>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $application->application_number }}
                </h1>
            </div>
            
            <div class="flex items-center gap-3">
                            <span class="px-4 py-2 rounded-full text-sm font-semibold inline-flex items-center gap-2
                    {{ $application->status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}
                    {{ in_array($application->status, ['submitted', 'phone_interview_scheduled', 'phone_interview_completed']) ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                    {{ in_array($application->status, ['meet_and_greet_scheduled', 'meet_and_greet_completed', 'initial_inspection_scheduled', 'initial_inspection_completed']) ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                    {{ in_array($application->status, ['documents_pending', 'documents_submitted', 'second_inspection_scheduled', 'second_inspection_completed']) ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                    {{ in_array($application->status, ['contract_signing_scheduled', 'contract_signed']) ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                    {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                    {{ in_array($application->status, ['rejected', 'cancelled']) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                    <span class="w-2 h-2 rounded-full 
                        {{ $application->status === 'draft' ? 'bg-gray-500' : '' }}
                        {{ in_array($application->status, ['submitted', 'phone_interview_scheduled', 'phone_interview_completed']) ? 'bg-blue-500' : '' }}
                        {{ in_array($application->status, ['meet_and_greet_scheduled', 'meet_and_greet_completed', 'initial_inspection_scheduled', 'initial_inspection_completed']) ? 'bg-yellow-500' : '' }}
                        {{ in_array($application->status, ['documents_pending', 'documents_submitted', 'second_inspection_scheduled', 'second_inspection_completed']) ? 'bg-orange-500' : '' }}
                        {{ in_array($application->status, ['contract_signing_scheduled', 'contract_signed']) ? 'bg-purple-500' : '' }}
                        {{ $application->status === 'approved' ? 'bg-green-500' : '' }}
                        {{ in_array($application->status, ['rejected', 'cancelled']) ? 'bg-red-500' : '' }}">
                    </span>
                    {{ $application->status_display }}
                </span>
                
                @if($application->canBeEdited())
                    <a href="{{ route('applicant.applications.edit', $application) }}" 
                       class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Application
                    </a>
                @endif
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="mt-6">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600 dark:text-gray-400 font-medium">Application Completion</span>
                <span class="text-gray-900 dark:text-white font-bold">{{ number_format($application->application_progress_percentage, 0) }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full transition-all duration-500" 
                     style="width: {{ $application->application_progress_percentage }}%; background: linear-gradient(90deg, #553e96 0%, #7c3aed 100%);">
                </div>
            </div>
        </div>

        {{-- Next Steps --}}
       @php
            $nextSteps = [
                'draft' => 'Complete all required fields and submit your application.',
                'submitted' => 'Your application is under review. We will contact you within 3-5 business days.',
                'phone_interview_scheduled' => 'Your phone interview is scheduled. Please be available at the scheduled time.',
                'phone_interview_completed' => 'Phone interview completed. Awaiting meet and greet scheduling.',
                'meet_and_greet_scheduled' => 'Your meet and greet is scheduled. We look forward to meeting you!',
                'meet_and_greet_completed' => 'Meet and greet completed. Preparing for home inspection.',
                'initial_inspection_scheduled' => 'Your initial home inspection has been scheduled. Please ensure your home meets all requirements.',
                'initial_inspection_completed' => 'Initial inspection completed. Please upload any required documents.',
                'documents_pending' => 'Please upload the required documents to proceed with your application.',
                'documents_submitted' => 'Documents received and under review. You will be notified once approved.',
                'second_inspection_scheduled' => 'Your second inspection is scheduled. Please ensure all previous requirements have been addressed.',
                'second_inspection_completed' => 'Second inspection completed successfully. Moving to finial inspection.',
                'final_inspection_scheduled' => 'Your final inspection is scheduled. This is the last step before contract signing.',
                'final_inspection_completed' => 'Final inspection completed. Preparing your contract for signing.',
                'contract_signing_scheduled' => 'Your contract signing appointment is scheduled. Please bring required identification.',
                'contract_signed' => 'Contract signed! Your application is in final review.',
                'approved' => 'Congratulations! Your dayhome has been approved and licensed.',
                'rejected' => 'Your application was not approved. Please contact us for more information.',
                'cancelled' => 'Your application has been cancelled.',
            ];
        @endphp
        
        @if(isset($nextSteps[$application->status]))
        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200">Next Steps</h4>
                    <p class="text-sm text-blue-800 dark:text-blue-300 mt-1">{{ $nextSteps[$application->status] }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Congratulations Message for Approved Applications --}}
@if($application->status === 'approved')
<div class="relative overflow-hidden bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl shadow-lg border-2 border-green-200 dark:border-green-700 p-8">
    {{-- Animated background sparkles --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="sparkle"></div>
        <div class="sparkle"></div>
        <div class="sparkle"></div>
        <div class="sparkle"></div>
        <div class="sparkle"></div>
    </div>
    
    <div class="relative z-10">
        <div class="flex items-start gap-4">

            <div class="flex-1">
                <h2 class="text-3xl font-bold text-green-800 dark:text-green-200 mb-2">
                    🎉 Congratulations!
                </h2>
                <p class="text-lg text-green-700 dark:text-green-300 mb-4">
                    Your dayhome application has been <span class="font-bold">approved</span>! You are now licensed to operate as a SPICE'd Childcare Services dayhome.
                </p>
                
                {{-- Certificate Information --}}
                @if($application->certificate)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4 border border-green-200 dark:border-green-700">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Certificate Number</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $application->certificate->certificate_number }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Issued: {{ $application->certificate->issue_date->format('F j, Y') }} • 
                                Valid Until: {{ $application->certificate->expiry_date->format('F j, Y') }}
                            </p>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('applicant.certificates.preview', $application->certificate) }}" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Certificate
                            </a>
                            <a href="{{ route('applicant.certificates.download', $application->certificate) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="space-y-2 text-sm text-green-700 dark:text-green-300">
                    <p class="flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Your certificate is valid for one year from the issue date
                    </p>
                    <p class="flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        We'll notify you before your certificate expires
                    </p>
                    <p class="flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Keep a copy of your certificate for your records
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes sparkle {
    0%, 100% {
        opacity: 0;
        transform: scale(0) rotate(0deg);
    }
    50% {
        opacity: 1;
        transform: scale(1) rotate(180deg);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    25% {
        transform: translateY(-20px) translateX(10px);
    }
    75% {
        transform: translateY(-10px) translateX(-10px);
    }
}

.sparkle {
    position: absolute;
    width: 20px;
    height: 20px;
    background: radial-gradient(circle, #ffd700 0%, transparent 70%);
    border-radius: 50%;
    animation: sparkle 3s infinite, float 4s infinite;
    pointer-events: none;
}

.sparkle:nth-child(1) {
    top: 10%;
    left: 15%;
    animation-delay: 0s, 0s;
}

.sparkle:nth-child(2) {
    top: 20%;
    right: 20%;
    animation-delay: 0.5s, 1s;
}

.sparkle:nth-child(3) {
    bottom: 30%;
    left: 25%;
    animation-delay: 1s, 0.5s;
}

.sparkle:nth-child(4) {
    bottom: 15%;
    right: 15%;
    animation-delay: 1.5s, 2s;
}

.sparkle:nth-child(5) {
    top: 50%;
    left: 50%;
    animation-delay: 2s, 1.5s;
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-bounce-slow {
    animation: bounce-slow 2s infinite;
}
</style>
@endif

{{-- Active Status Certificate Access --}}
@if($application->status === 'active' && $application->certificate)
<div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-700 p-6">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
        </div>
        
        <div class="flex-1">
            <h3 class="text-lg font-bold text-blue-900 dark:text-blue-200 mb-2">
                Your Certificate
            </h3>
            <p class="text-sm text-blue-800 dark:text-blue-300 mb-3">
                Certificate #{{ $application->certificate->certificate_number }} • 
                Valid until {{ $application->certificate->expiry_date->format('F j, Y') }}
            </p>
            <div class="flex gap-2">
                <a href="{{ route('applicant.certificates.preview', $application->certificate) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Certificate
                </a>
                <a href="{{ route('applicant.certificates.download', $application->certificate) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download
                </a>
            </div>
        </div>
    </div>
</div>
@endif

    {{-- Personal Information --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
                Personal Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">First Name</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->educator_first_name ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Last Name</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->educator_last_name ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Email</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->email ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Phone</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->phone ?: '—' }}</p>
                </div>
                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Full Address</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->full_address ?: '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Professional Qualifications --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"></path>
                </svg>
                Professional Qualifications
            </h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Childcare Level</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->childcare_level ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Referred By</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->referred_by ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Languages Spoken</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->languages_spoken ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Certifications</h3>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @if($application->has_criminal_record_check)
                            <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-xs font-medium">✓ Criminal Record Check</span>
                        @endif
                        @if($application->has_first_aid_cpr)
                            <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-xs font-medium">✓ First Aid & CPR</span>
                        @endif
                        @if(!$application->has_criminal_record_check && !$application->has_first_aid_cpr)
                            <span class="text-gray-500">—</span>
                        @endif
                    </div>
                </div>
                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Childcare Education & Training</h3>
                    <p class="text-base text-gray-900 dark:text-white">{{ $application->childcare_education ?: '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Home Environment --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                Home Environment
            </h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Home Type</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->home_type ? ucfirst($application->home_type) : '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Ownership</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->home_ownership ? ucfirst($application->home_ownership) : '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Number of Residents</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->home_residents_count ?? '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Home Features</h3>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @if($application->has_pets)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-xs font-medium">Has Pets</span>
                        @endif
                        @if($application->fenced_backyard)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-xs font-medium">Fenced Backyard</span>
                        @endif
                        @if(!$application->has_pets && !$application->fenced_backyard)
                            <span class="text-gray-500">—</span>
                        @endif
                    </div>
                </div>
                @if($application->home_residents_details)
                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Resident Details</h3>
                    <p class="text-base text-gray-900 dark:text-white">{{ $application->home_residents_details }}</p>
                </div>
                @endif
                @if($application->has_pets && $application->pets_details)
                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Pet Details</h3>
                    <p class="text-base text-gray-900 dark:text-white">{{ $application->pets_details }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Experience & Operation --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.951 22.951 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                Experience & Operation
            </h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Smoking Status</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->smoking_status === 'no' ? 'No Smoking' : 'Yes' }}</p>
                    @if($application->smoking_details)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $application->smoking_details }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Desired Start Date</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->desired_start_date ? $application->desired_start_date->format('F j, Y') : '—' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Special Needs</h3>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $application->comfortable_special_needs ? 'Comfortable with Special Needs' : 'Not specified' }}</p>
                </div>
                @if($application->current_operation_details)
                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Current Operation Details</h3>
                    <p class="text-base text-gray-900 dark:text-white">{{ $application->current_operation_details }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Philosophy & Approach --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                </svg>
                Philosophy & Approach
            </h2>
        </div>
        <div class="p-6 space-y-6">
            @if($application->motivation)
            <div>
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Why Become a Dayhome Educator?</h3>
                <p class="text-base text-gray-900 dark:text-white leading-relaxed">{{ $application->motivation }}</p>
            </div>
            @endif
            
            @if($application->why_spiced)
            <div>
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Why SPICE'd Dayhome Agency?</h3>
                <p class="text-base text-gray-900 dark:text-white leading-relaxed">{{ $application->why_spiced }}</p>
            </div>
            @endif
            
            @if($application->education_philosophy)
            <div>
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Early Childhood Education Philosophy</h3>
                <p class="text-base text-gray-900 dark:text-white leading-relaxed">{{ $application->education_philosophy }}</p>
            </div>
            @endif
            
            @if($application->program_planning_process)
            <div>
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Program Planning Process</h3>
                <p class="text-base text-gray-900 dark:text-white leading-relaxed">{{ $application->program_planning_process }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Inspections --}}
    @if($application->inspections()->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Inspection Reports
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($application->inspections()->latest('conducted_at')->get() as $inspection)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ ucwords(str_replace('_', ' ', $inspection->type)) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Conducted on {{ $inspection->conducted_at->format('M d, Y') }} by {{ $inspection->consultant->name ?? 'Consultant' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $inspection->overall_result === 'pass' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $inspection->overall_result === 'fail' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $inspection->overall_result === 'conditional_pass' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $inspection->overall_result)) }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Score: {{ $inspection->overall_score ?? 'N/A' }}%</p>
                            </div>
                            <a href="{{ route('applicant.inspections.show', $inspection) }}" 
                               class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors text-sm">
                                View Report
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <a href="{{ route('applicant.dashboard') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>

        @if($application->canBeEdited())
            <a href="{{ route('applicant.applications.edit', $application) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 text-white rounded-xl font-semibold transition-all hover:shadow-lg"
               style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Continue Editing
            </a>
        @endif
    </div>
</div>
@endsection