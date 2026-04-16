<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment - SPICE'd Dayhome</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">
                    @if(request()->get('action') === 'reschedule')
                        Request Reschedule
                    @else
                        Confirm Your Appointment
                    @endif
                </h2>
                <p class="text-gray-600 mt-2">Please review and confirm your appointment details</p>
            </div>

            <!-- Appointment Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Appointment Details</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Type:</span>
                        <span class="font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $appointment->type)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date & Time:</span>
                        <span class="font-medium text-gray-900">{{ $appointment->scheduled_at->format('l, F j, Y \a\t g:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-medium text-gray-900">{{ $appointment->duration }} minutes</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Location:</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($appointment->location_type) }}</span>
                    </div>
                    @if($appointment->location_address)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Address/Link:</span>
                        <span class="font-medium text-gray-900 text-right">
                            @if($appointment->location_type === 'virtual')
                                <a href="{{ $appointment->location_address }}" target="_blank" class="text-purple-600 hover:text-purple-500">
                                    Join Meeting
                                </a>
                            @else
                                {{ $appointment->location_address }}
                            @endif
                        </span>
                    </div>
                    @endif
                    @if($appointment->consultant)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Consultant:</span>
                        <span class="font-medium text-gray-900">{{ $appointment->consultant->name }}</span>
                    </div>
                    @endif
                    @if($appointment->preparation_notes)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <span class="text-gray-600 block mb-2">Preparation Notes:</span>
                        <p class="text-gray-900">{{ $appointment->preparation_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Confirmation Form -->
            <form method="POST" action="{{ route('appointments.process-email-confirmation', ['appointment' => $appointment->id, 'token' => $appointment->confirmation_token]) }}" class="space-y-4">
                @csrf
                
                <!-- Display validation errors -->
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <ul class="text-sm text-red-800 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if(request()->get('action') === 'reschedule' || $errors->has('reschedule_reason'))
                    <!-- Reschedule Section -->
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <p class="text-sm text-orange-800">
                            <strong>Need to reschedule?</strong> Please provide a reason and we'll contact you to arrange a new time.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="reschedule_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for rescheduling <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="reschedule_reason" 
                                id="reschedule_reason" 
                                rows="4" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('reschedule_reason') border-red-500 @enderror"
                                placeholder="Please let us know why you need to reschedule and your preferred dates/times...">{{ old('reschedule_reason') }}</textarea>
                            @error('reschedule_reason')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" name="confirmation" value="reschedule"
                                    class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                Request Reschedule
                            </button>
                            <a href="{{ route('appointments.confirm-by-email', ['appointment' => $appointment->id, 'token' => $appointment->confirmation_token]) }}"
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-4 rounded-lg font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center">
                                Back to Confirm
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Confirmation Section -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Please confirm your availability:</strong> This helps us ensure the appointment proceeds smoothly.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" name="confirmation" value="confirm"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Confirm Appointment
                        </button>
                        <a href="{{ route('appointments.confirm-by-email', ['appointment' => $appointment->id, 'token' => $appointment->confirmation_token, 'action' => 'reschedule']) }}"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-4 rounded-lg font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Reschedule
                        </a>
                    </div>
                @endif
            </form>

            <!-- Footer Info -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Need help? Contact us at 
                    <a href="mailto:executive@spicedchildcare.com" class="text-purple-600 hover:text-purple-500 underline">
                        executive@spicedchildcare.com
                    </a>
                    <br>
                    <span class="text-gray-400 mt-1 block">or call (403) 123-4567</span>
                </p>
            </div>
        </div>
    </div>
</body>
</html>