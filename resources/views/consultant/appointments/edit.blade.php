@extends('layouts.consultant')

@section('title', 'Edit Appointment')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 md:space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('consultant.appointments.show', $appointment) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Edit Appointment</h1>
            <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 mt-1">Update appointment details</p>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
        <form action="{{ route('consultant.appointments.update', $appointment) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4 md:space-y-6">
                <!-- Application (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Application</label>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-300 dark:border-gray-600 rounded-lg">
                        <p class="text-gray-900 dark:text-white font-medium">
                            {{ $appointment->application->full_name }} - {{ $appointment->application->application_number }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $appointment->applicant->email }}
                        </p>
                    </div>
                </div>

                <!-- Type & Title -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type*</label>
                        <select name="type" required class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            <option value="meet_and_greet" {{ $appointment->type === 'meet_and_greet' ? 'selected' : '' }}>Meet & Greet</option>
                            <option value="initial_inspection" {{ $appointment->type === 'initial_inspection' ? 'selected' : '' }}>Initial Inspection</option>
                            <option value="second_inspection" {{ $appointment->type === 'second_inspection' ? 'selected' : '' }}>Second Inspection</option>
                            <option value="final_inspection" {{ $appointment->type === 'final_inspection' ? 'selected' : '' }}>Final Inspection</option>
                            <option value="contract_signing" {{ $appointment->type === 'contract_signing' ? 'selected' : '' }}>Contract Signing</option>
                            <option value="follow_up" {{ $appointment->type === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consultant*</label>
                        <select name="consultant_id" required class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}" {{ $appointment->consultant_id === $consultant->id ? 'selected' : '' }}>
                                    {{ $consultant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('consultant_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title*</label>
                    <input 
                        type="text" 
                        name="title" 
                        value="{{ old('title', $appointment->title) }}"
                        required 
                        placeholder="e.g., Initial Home Inspection" 
                        class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date, Time & Duration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date & Time*</label>
                        <input 
                            type="datetime-local" 
                            name="scheduled_at" 
                            value="{{ old('scheduled_at', $appointment->scheduled_at->format('Y-m-d\TH:i')) }}"
                            required 
                            class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        @error('scheduled_at')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes)*</label>
                        <input 
                            type="number" 
                            name="duration" 
                            value="{{ old('duration', $appointment->duration) }}"
                            required 
                            min="30" 
                            max="480" 
                            class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Address*</label>
                        <input 
                            type="text" 
                            name="location_address" 
                            value="{{ old('location_address', $appointment->location_address) }}"
                            required 
                            placeholder="Address" 
                            class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        @error('location_address')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Type*</label>
                        <select name="location_type" required class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            <option value="home" {{ $appointment->location_type === 'home' ? 'selected' : '' }}>Home</option>
                            <option value="office" {{ $appointment->location_type === 'office' ? 'selected' : '' }}>Office</option>
                            <option value="other" {{ $appointment->location_type === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('location_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Notes</label>
                    <textarea 
                        name="location_notes" 
                        rows="2" 
                        placeholder="Any specific directions or location details..."
                        class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ old('location_notes', $appointment->location_notes) }}</textarea>
                    @error('location_notes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea 
                        name="description" 
                        rows="3" 
                        placeholder="Additional notes about the appointment..."
                        class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ old('description', $appointment->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preparation Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preparation Notes</label>
                    <textarea 
                        name="preparation_notes" 
                        rows="3" 
                        placeholder="What should the applicant prepare for this appointment..."
                        class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ old('preparation_notes', $appointment->preparation_notes) }}</textarea>
                    @error('preparation_notes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warning Message -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                Note: Updating this appointment will reset confirmation status
                            </p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                                Both you and the applicant will need to confirm the updated appointment details.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('consultant.appointments.show', $appointment) }}" 
                       class="w-full sm:flex-1 px-4 py-2.5 text-center bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm md:text-base">
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="w-full sm:flex-1 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors text-sm md:text-base">
                        Update Appointment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection