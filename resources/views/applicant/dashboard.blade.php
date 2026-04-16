{{-- resources/views/applicant/dashboard.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'My Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Here's what's happening with your dayhome application
                </p>
            </div>
            <div class="hidden md:block">
                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    

    @if($activeApplication && $activeApplication->status === 'approved')
    {{-- Congratulations Banner with Sparkles Effect --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-green-400 via-green-400 to-tgreeneal-400 rounded-2xl shadow-2xl p-8 mb-6">
        {{-- Animated confetti/sparkles background --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            @for($i = 0; $i < 30; $i++)
                <div class="confetti" style="
                    left: {{ rand(0, 100) }}%;
                    animation-delay: {{ rand(0, 3000) / 1000 }}s;
                    animation-duration: {{ rand(2000, 4000) / 1000 }}s;
                "></div>
            @endfor
        </div>
        
        {{-- Floating sparkles --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="sparkle-big"></div>
            <div class="sparkle-big"></div>
            <div class="sparkle-big"></div>
            <div class="sparkle-big"></div>
            <div class="sparkle-big"></div>
            <div class="sparkle-big"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-6">
                {{-- Animated Trophy Icon --}}
                <div class="flex-shrink-0">
                    <div class="relative">
                       
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full animate-ping"></div>
                    </div>
                </div>
                
                {{-- Content --}}
                <div class="flex-1 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                        <span class="text-4xl">🎉</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-white">
                            Congratulations!
                        </h2>
                    </div>
                    
                    <p class="text-xl text-white/95 mb-4 font-medium">
                        Your dayhome has been officially approved! You're now approved with SPICE'd Childcare Services.
                    </p>
                    
                    @if($activeApplication->certificate)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mb-4 border border-white/20">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="text-center sm:text-left">
                                <p class="text-sm text-white/80 mb-1">Your Certificate Number</p>
                                <p class="text-2xl font-bold text-white tracking-wide">
                                    {{ $activeApplication->certificate->certificate_number }}
                                </p>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="{{ route('applicant.certificates.preview', $activeApplication->certificate) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-100 text-green-600 rounded-lg font-bold transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span class="hidden sm:inline">View Certificate</span>
                                    <span class="sm:hidden">View</span>
                                </a>
                                <a href="{{ route('applicant.certificates.download', $activeApplication->certificate) }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-green-900 rounded-lg font-bold transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Download PDF</span>
                                    <span class="sm:hidden">Download</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex flex-wrap gap-4 text-sm text-white/90">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Approval & Ready</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>All Requirements Met</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Certificate Issued</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    /* Confetti Animation */
    @keyframes confetti-fall {
        0% {
            transform: translateY(-100%) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
    
    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        background: linear-gradient(45deg, #ffd700, #ff69b4, #00bfff, #7fff00);
        animation: confetti-fall linear infinite;
        border-radius: 50%;
    }
    
    /* Big Sparkles Animation */
    @keyframes sparkle-float {
        0%, 100% {
            opacity: 0;
            transform: translateY(0) scale(0);
        }
        50% {
            opacity: 1;
            transform: translateY(-30px) scale(1);
        }
    }
    
    @keyframes twinkle {
        0%, 100% {
            opacity: 0.3;
        }
        50% {
            opacity: 1;
        }
    }
    
    .sparkle-big {
        position: absolute;
        width: 30px;
        height: 30px;
        animation: sparkle-float 3s infinite, twinkle 1.5s infinite;
    }
    
    .sparkle-big::before,
    .sparkle-big::after {
        content: '';
        position: absolute;
        background: white;
    }
    
    .sparkle-big::before {
        width: 100%;
        height: 2px;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
    }
    
    .sparkle-big::after {
        width: 2px;
        height: 100%;
        left: 50%;
        top: 0;
        transform: translateX(-50%);
    }
    
    .sparkle-big:nth-child(1) {
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }
    
    .sparkle-big:nth-child(2) {
        top: 20%;
        right: 15%;
        animation-delay: 0.5s;
    }
    
    .sparkle-big:nth-child(3) {
        bottom: 20%;
        left: 20%;
        animation-delay: 1s;
    }
    
    .sparkle-big:nth-child(4) {
        bottom: 30%;
        right: 20%;
        animation-delay: 1.5s;
    }
    
    .sparkle-big:nth-child(5) {
        top: 50%;
        left: 50%;
        animation-delay: 2s;
    }
    
    .sparkle-big:nth-child(6) {
        top: 30%;
        right: 40%;
        animation-delay: 2.5s;
    }
    
    /* Bounce Animation */
    @keyframes bounce-slow {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-15px);
        }
    }
    
    .animate-bounce-slow {
        animation: bounce-slow 2s ease-in-out infinite;
    }
    </style>
    
    @endif

    @if($activeApplication)

    {{-- Application Status Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div class="min-w-0 flex-1">
                    <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm font-medium truncate">
                        Application #{{ $activeApplication->application_number }}
                    </p>
                    <h2 class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ $activeApplication->full_name }}
                    </h2>
                </div>
                <span class="px-2 sm:px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap self-start"
                    style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%); color: white;">
                    {{ ucfirst(str_replace('_', ' ', $activeApplication->status)) }}
                </span>
            </div>

            @php
                // Define the main application stages (up to activation)
                $stages = [
                    ['name' => 'Submitted', 'status' => ['draft', 'submitted']],
                    ['name' => 'Meet & Greet', 'status' => ['meet_and_greet_scheduled', 'meet_and_greet_completed']],
                    ['name' => 'Initial Inspection', 'status' => ['initial_inspection_scheduled', 'initial_inspection_completed']],
                    ['name' => 'Documents', 'status' => ['documents_pending', 'documents_submitted', 'documents_approved']],
                    ['name' => 'Second Inspection', 'status' => ['second_inspection_scheduled', 'second_inspection_completed']],
                    ['name' => 'Final Inspection', 'status' => ['final_inspection_scheduled', 'final_inspection_completed', 'final_inspection_passed', 'final_inspection_failed']],
                    ['name' => 'Contract', 'status' => ['contract_signing_scheduled', 'contract_signed']],
                    ['name' => 'Approved', 'status' => ['approved']],
                    ['name' => 'Active', 'status' => ['active']],
                ];
                
                // Map each status to its stage index
                $statusToStageMap = [
                    'draft' => 0,
                    'submitted' => 0,
                    'meet_and_greet_scheduled' => 1,
                    'meet_and_greet_completed' => 1,
                    'initial_inspection_scheduled' => 2,
                    'initial_inspection_completed' => 2,
                    'documents_pending' => 3,
                    'documents_submitted' => 3,
                    'documents_approved' => 3,
                    'second_inspection_scheduled' => 4,
                    'second_inspection_completed' => 4,
                    'final_inspection_scheduled' => 5,
                    'final_inspection_completed' => 5,
                    'final_inspection_passed' => 5,
                    'final_inspection_failed' => 5,
                    'contract_signing_scheduled' => 6,
                    'contract_signed' => 6,
                    'approved' => 7,
                    'active' => 8,
                    // Post-activation statuses are not shown in progress bar
                    'compliance_inspection_due' => 8,
                    'compliance_inspection_scheduled' => 8,
                    'compliance_inspection_completed' => 8,
                    'suspended' => 8,
                    'under_review' => 8,
                    'remediation_required' => 8,
                    'terminated' => 8,
                    'rejected' => 0, // Shows as first stage but with different styling
                    'cancelled' => 0,
                ];
                
                $currentStatus = $activeApplication->status;
                $currentStageIndex = $statusToStageMap[$currentStatus] ?? 0;
                
                // Calculate progress percentage
                $progressPercentage = $currentStageIndex > 0 ? (($currentStageIndex) / (count($stages) - 1)) * 100 : 0;
                
                // Check if application is in a terminal or post-activation state
                $isPostActivation = in_array($currentStatus, [
                    'compliance_inspection_due', 
                    'compliance_inspection_scheduled', 
                    'compliance_inspection_completed',
                    'suspended', 
                    'under_review', 
                    'remediation_required'
                ]);
                
                $isTerminal = in_array($currentStatus, ['rejected', 'cancelled', 'terminated']);
                
                // If active or post-activation, show 100% progress
                if ($currentStatus === 'active' || $isPostActivation) {
                    $progressPercentage = 100;
                    $currentStageIndex = count($stages) - 1; // Show all stages as complete
                }
            @endphp

            {{-- Show alert for special statuses --}}
            @if($isTerminal)
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                            Application Status: {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                        </p>
                    </div>
                </div>
            @elseif($isPostActivation)
                <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Your dayhome is active. Current status: {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Mobile: Vertical Progress Stepper --}}
            <div class="block md:hidden mb-6">
                <div class="space-y-3">
                    @foreach($stages as $index => $stage)
                        <div class="flex items-center gap-3">
                            {{-- Circle --}}
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 border-2
                                {{ $index <= $currentStageIndex ? 'border-purple-600 bg-white dark:bg-gray-800 shadow-md' : 'border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700' }}
                                {{ $index < $currentStageIndex ? 'text-green-600' : ($index === $currentStageIndex ? 'text-purple-600' : 'text-gray-400 dark:text-gray-500') }}">
                                
                                @if($index < $currentStageIndex)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            
                            {{-- Label --}}
                            <div class="flex-1">
                                <p class="text-sm font-semibold {{ $index <= $currentStageIndex ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ $stage['name'] }}
                                </p>
                                {{-- Show status detail for current stage --}}
                                @if($index === $currentStageIndex && !$isTerminal)
                                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-0.5">
                                        Current
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Connector Line (except for last item) --}}
                        @if($index < count($stages) - 1)
                            <div class="flex items-center gap-3 -my-1">
                                <div class="w-10 flex justify-center">
                                    <div class="w-0.5 h-6 {{ $index < $currentStageIndex ? 'bg-purple-600' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Desktop: Horizontal Progress Stepper --}}
            <div class="hidden md:block mb-8">
                <div class="relative px-4">
                    <div class="flex items-center justify-between">
                        {{-- Background Line --}}
                        <div class="absolute top-8 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-700"></div>
                        
            {{-- Progress Line --}}
            <div class="absolute top-8 left-0 h-1 transition-all duration-500" 
            style="width: {{ $activeApplication->application_progress_percentage }}%; background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);"></div>
                        {{-- Stage Circles --}}
                        @foreach($stages as $index => $stage)
                            <div class="relative flex flex-col items-center" style="width: {{ 100 / count($stages) }}%;">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center font-bold text-lg transition-all duration-300 z-10 border-2
                                    {{ $index <= $currentStageIndex ? 'border-purple-600 bg-white dark:bg-gray-800 shadow-lg' : 'border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700' }}
                                    {{ $index < $currentStageIndex ? 'text-green-600' : ($index === $currentStageIndex ? 'text-purple-600' : 'text-gray-400 dark:text-gray-500') }}">
                                    
                                    @if($index < $currentStageIndex)
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                
                                <p class="mt-3 text-xs font-semibold text-center leading-tight px-1
                                    {{ $index <= $currentStageIndex ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ $stage['name'] }}
                                </p>
                                
                                {{-- Current indicator --}}
                                @if($index === $currentStageIndex && !$isTerminal)
                                    <span class="absolute -bottom-6 text-xs font-medium text-purple-600 dark:text-purple-400">
                                        Current
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Progress Summary --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Progress</span>
                        <span class="text-sm font-bold text-purple-600 dark:text-purple-400">{{ number_format($activeApplication->application_progress_percentage, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500" 
                            style="width: {{ $activeApplication->application_progress_percentage }}%; background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);"></div>
                    </div>
               </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <a href="{{ route('applicant.applications.show', $activeApplication) }}" 
                class="px-4 py-2.5 text-white rounded-lg text-sm font-medium hover:opacity-90 transition-opacity text-center"
                style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                    View Application
                </a>
                @if(in_array($activeApplication->status, ['draft']))
                    <a href="{{ route('applicant.applications.edit', $activeApplication) }}"
                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition-colors text-center">
                        Continue Editing
                    </a>
                @endif
            </div>
        </div>
    </div>
    

    @if($activeApplication && $activeApplication->status === 'active' && $activeApplication->certificate)
    {{-- Active Dayhome Certificate Quick Access --}}
    <div class="rounded-xl shadow-lg p-6 text-white" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-1">Your Approved application</h3>
                    <p class="text-blue-100 text-sm">
                        Certificate #{{ $activeApplication->certificate->certificate_number }}
                    </p>
                    <p class="text-blue-200 text-xs mt-1">
                        Valid until {{ $activeApplication->certificate->expiry_date->format('M j, Y') }}
                    </p>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('applicant.certificates.preview', $activeApplication->certificate) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-all shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View
                </a>
                <a href="{{ route('applicant.certificates.download', $activeApplication->certificate) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-400 text-blue-900 rounded-lg font-semibold hover:bg-yellow-300 transition-all shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download
                </a>
            </div>
        </div>
    </div>
    @endif

        {{-- Action Items --}}
        @if($pendingDocuments && count($pendingDocuments) > 0)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        Action Required: Missing Documents
                    </h3>
                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                        You have {{ count($pendingDocuments) }} required document(s) pending upload.
                    </p>
                    <div class="mt-2">
                        <a href="{{ route('applicant.documents.index', $activeApplication) }}" 
                           class="text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:text-yellow-900 dark:hover:text-yellow-100">
                            Upload Documents →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @else
        {{-- No Application CTA --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Start Your Dayhome Application
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Ready to begin your licensing journey? Let's get started with your application.
            </p>
            <a href="{{ route('applicant.applications.create') }}"
               class="inline-flex items-center px-6 py-3 text-white font-medium rounded-lg hover:opacity-90 transition-opacity"
               style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                Create New Application
            </a>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Upcoming Appointments --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Appointments</h3>
                <a href="{{ route('applicant.appointments.index') }}" class="text-sm font-medium hover:underline" style="color: #553e96;">
                    View All
                </a>
            </div>

            @if($upcomingAppointments && $upcomingAppointments->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingAppointments as $appointment)
                        <div class="flex items-start space-x-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-600 transition-colors">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg flex flex-col items-center justify-center text-white" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                                <span class="text-xs font-semibold">{{ $appointment->scheduled_at->format('M') }}</span>
                                <span class="text-lg font-bold">{{ $appointment->scheduled_at->format('d') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $appointment->title ?? ucfirst($appointment->type) }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $appointment->scheduled_at->format('g:i A') }}
                                    @if($appointment->consultant)
                                        • with {{ $appointment->consultant->name }}
                                    @endif
                                </p>
                                @if($appointment->status === 'scheduled' && !$appointment->applicant_confirmed)
                                    <form action="{{ route('applicant.appointments.confirm', $appointment) }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="text-xs px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded font-medium transition-colors">
                                            Confirm Appointment
                                        </button>
                                    </form>
                                @elseif($appointment->applicant_confirmed)
                                    <span class="inline-block mt-2 text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded">
                                        Confirmed
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming appointments</p>
                </div>
            @endif
        </div>

        {{-- Recent Documents --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Documents</h3>
                @if($activeApplication)
                    <a href="{{ route('applicant.documents.index', $activeApplication) }}" class="text-sm font-medium hover:underline" style="color: #553e96;">
                        View All
                    </a>
                @endif
            </div>

            @if($recentDocuments && $recentDocuments->count() > 0)
                <div class="space-y-3">
                    @foreach($recentDocuments as $document)
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 max-w-[200px]">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $document->name }}">
                                        {{ $document->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $document->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full 
                                {{ $document->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $document->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $document->status === 'under_review' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $document->status === 'uploaded' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($document->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No documents uploaded yet</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Notifications --}}
    @if($notifications && $notifications->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Notifications</h3>
            <a href="{{ route('notifications.index') }}" class="text-sm font-medium hover:underline" style="color: #553e96;">
                View All
            </a>
        </div>
        <div class="space-y-3">
            @foreach($notifications->take(3) as $notification)
                <div class="flex items-start space-x-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" style="background: #553e96;">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $notification->title }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Help Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #553e96 0%, #7c3aed 100%);">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Need Help?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Our support team is here to assist you through every step of the licensing process.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="mailto:support@spiceddayhome.com" 
                       class="inline-flex items-center text-sm font-medium hover:underline" style="color: #553e96;">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        Email Support
                    </a>
                    <a href="https://support.algosoftwarelabs.com/" 
                       class="inline-flex items-center text-sm font-medium hover:underline" style="color: #553e96;">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Help Center
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection