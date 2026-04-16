<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Application Submitted - SPICE'd Dayhome</title>

    <link rel="icon" type="image/jpg" href="{{ asset('logo.jpeg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        'brand': {
                            'lavender': '#e3d4fc',
                            'cyan': '#d4f6ff', 
                            'purple': '#553e96'
                        }
                    },
                    animation: {
                        'bounce-slow': 'bounce 2s infinite',
                        'fade-in': 'fadeIn 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 py-4">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-3">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="SPICE'd" class="w-10 h-10 object-contain">
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        <span style="color: #553e96;">SPICE'd</span>
                        <span class="text-gray-800"> Dayhome</span>
                    </h1>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-2xl animate-fade-in">
                
                <!-- Success Header -->
                <div class="text-center mb-8">
                    <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Application Submitted!</h2>
                    <p class="text-lg text-gray-600 max-w-md mx-auto">
                        Thank you for taking the first step towards becoming a licensed dayhome provider.
                    </p>
                </div>

                <!-- Application Details Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1">Application Details</h3>
                            <p class="text-sm text-gray-600">Please save this information for your records</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Application Number</p>
                                <p class="font-mono font-bold text-lg text-gray-900">{{ $application->application_number ?? 'Pending' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Submitted On</p>
                                <p class="font-semibold text-gray-900">{{ $application->submitted_at ? $application->submitted_at->format('M j, Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm text-gray-600 mb-1">Applicant Name</p>
                            <p class="font-semibold text-gray-900">{{ $application->full_name }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm text-gray-600 mb-1">Contact Email</p>
                            <p class="font-semibold text-gray-900">{{ $application->email }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm text-gray-600 mb-1">Phone Number</p>
                            <p class="font-semibold text-gray-900">{{ $application->phone }}</p>
                        </div>

                        @if($application->consultant)
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm text-gray-600 mb-1">Assigned Consultant</p>
                            <p class="font-semibold text-gray-900">{{ $application->consultant->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">Your consultant will contact you soon</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- What Happens Next -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        What Happens Next?
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <span class="text-purple-600 font-bold text-sm">1</span>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Consultant Assignment</h4>
                                <p class="text-sm text-gray-600">A dedicated consultant will be assigned to your application within 2-3 business days.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <span class="text-purple-600 font-bold text-sm">2</span>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Initial Contact</h4>
                                <p class="text-sm text-gray-600">Your consultant will contact you to discuss the next steps and schedule a meet & greet.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <span class="text-purple-600 font-bold text-sm">3</span>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Home Inspection</h4>
                                <p class="text-sm text-gray-600">We'll schedule an inspection to ensure your home meets safety standards for childcare.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <span class="text-green-600 font-bold text-sm">4</span>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Account Creation</h4>
                                <p class="text-sm text-gray-600">After inspection, you'll create your online account to continue with document submission.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-blue-800 mb-2">Important Information</h3>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p>• A confirmation email has been sent to <strong>{{ $application->email }}</strong> (check spam folder if not received)</p>
                                <p>• Keep your phone available for our call</p>
                                <p>• Save your application number for future reference</p>
                                <p>• Questions? Contact us at <a href="mailto:executive@spicedchildcare.com" class="underline font-semibold">executive@spicedchildcare.com</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="/" class="flex-1">
                        <button class="w-full px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                            Return to Homepage
                        </button>
                    </a>
                    <button onclick="window.print()" class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Confirmation
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-scroll to top on load -->
    <script>
        window.scrollTo(0, 0);
    </script>
</body>
</html>