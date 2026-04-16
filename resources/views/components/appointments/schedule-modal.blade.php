<!-- resources/views/components/appointments/schedule-modal.blade.php -->

<div 
    x-data="{ 
        showModal: false, 
        locationType: 'home',
        applicationId: null,
        applicantId: null,
        applicantAddress: '',
        showApplicationSelector: false,
        applications: @js($applications ?? []),
        appointmentType: '{{ old('type', '') }}', // Track appointment type for conditional fields
        inspectionType: '{{ old('inspection_type', '') }}' // Track inspection type
    }"
    @open-appointment-modal.window="
        showModal = true;
        applicationId = $event.detail.applicationId || null;
        applicantId = $event.detail.applicantId || null;
        applicantAddress = $event.detail.applicantAddress || '';
        showApplicationSelector = !applicationId;
    "
    @keydown.escape.window="showModal = false"
>
    <div 
        x-show="showModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow-xl max-w-2xl w-full p-4 md:p-6">
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Schedule Appointment</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" 
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 
                                  4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 
                                  4.293a1 1 0 01-1.414-1.414L8.586 10 
                                  4.293 5.707a1 1 0 010-1.414z" 
                                  clip-rule="evenodd">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    There were errors with your submission:
                                </h3>
                                <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Warning if no application selected -->
                <div x-show="!applicationId && !showApplicationSelector" class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Warning:</strong> No application selected. Please close this modal and select an application first.
                    </p>
                </div>

                <form action="{{ route('consultant.appointments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="consultant_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="application_id" x-model="applicationId">
                    <input type="hidden" name="applicant_id" x-model="applicantId">
                    <input type="hidden" name="user_timezone" id="schedule_modal_user_timezone" value="{{ old('user_timezone', auth()->user()?->timezone ?? config('app.timezone')) }}">

                    <div class="space-y-4 max-h-[70vh] overflow-y-auto px-1">

                        <!-- Application Selector -->
                        <div x-show="showApplicationSelector">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Application <span class="text-red-500">*</span>
                            </label>
                            <select 
                                @change="
                                    const selected = applications.find(app => app.id == $event.target.value);
                                    if (selected) {
                                        applicationId = selected.id;
                                        applicantId = selected.user_id;
                                        applicantAddress = selected.full_address;
                                    }
                                "
                                :required="showApplicationSelector"
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                       focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Select an Application --</option>
                                <template x-for="app in applications" :key="app.id">
                                    <option :value="app.id" x-text="`${app.full_name} - ${app.application_number}`"></option>
                                </template>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Choose the application for this appointment</p>
                        </div>

                        <!-- Selected Application Display -->
                        <div x-show="!showApplicationSelector && applicationId" class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-900 dark:text-blue-200">Appointment for selected application</p>
                                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">The appointment will be scheduled for the applicant of this application.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Appointment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" x-model="appointmentType" required 
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                       focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select type</option>
                                <option value="meet_and_greet">Meet and Greet</option>
                                <option value="initial_inspection">Initial Inspection</option>
                                <option value="second_inspection">Second Inspection</option>
                                <option value="final_inspection">Final Inspection</option>
                                <option value="contract_signing">Contract Signing</option>
                                <option value="follow_up">Follow Up</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Inspection Type (only if follow_up) -->
                       <div x-show="appointmentType === 'follow_up'">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Inspection Type <span class="text-red-500">*</span>
    </label>
    <select name="inspection_type" 
            x-model="inspectionType" 
            :required="appointmentType === 'follow_up'"
            class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                   focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
        <option value="">Select type</option>
        <option value="scheduled">Scheduled</option>
        <option value="unscheduled">Unscheduled</option>
    </select>
    @error('inspection_type')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title*</label>
                            <input type="text" name="title" required placeholder="e.g., Initial Home Visit"
                                value="{{ old('title') }}"
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                       focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea name="description" rows="3" placeholder="Purpose or agenda" 
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                       focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                         <!-- Date & Duration -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date & Time*</label>
                                <input type="datetime-local" name="scheduled_at" required 
                                    value="{{ old('scheduled_at') }}"
                                    min="{{ now(config('app.timezone'))->format('Y-m-d\TH:i') }}"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                           focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                                @php
                                    $timezoneLabel = auth()->user()?->timezone ?? config('app.timezone');
                                    $timezoneText = $timezoneLabel === 'America/Toronto' ? 'Eastern Time' : $timezoneLabel;
                                @endphp
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Time is in {{ $timezoneText }}
                                </p>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const browserTimezone = sessionStorage.getItem('userTimezone');
                                        const timezoneInput = document.getElementById('schedule_modal_user_timezone');
                                        if (timezoneInput && browserTimezone) {
                                            timezoneInput.value = browserTimezone;
                                        }
                                    });
                                </script>
                                @error('scheduled_at')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes)*</label>
                                <input type="number" name="duration" value="{{ old('duration', 120) }}" min="30" max="480" required 
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                           focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                                @error('duration')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Location Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Type*</label>
                            <select name="location_type" x-model="locationType" required
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                       focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                                <option value="home">Home</option>
                                <option value="office">Office</option>
                                <option value="virtual">Virtual</option>
                                <option value="other">Other</option>
                            </select>
                            @error('location_type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location Address -->
                        <div x-show="locationType !== 'virtual'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Address*</label>
                            <input type="text" name="location_address" x-model="applicantAddress"
                                :required="locationType !== 'virtual'"
                                placeholder="Enter address or it will auto-fill from application"
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                       focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            @error('location_address')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Virtual Meeting Link -->
                        <div x-show="locationType === 'virtual'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting Link*</label>
                            <input type="url" name="virtual_meeting_link" placeholder="https://zoom.us/j/..." value="{{ old('virtual_meeting_link') }}"
                                :required="locationType === 'virtual'"
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                       focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            @error('virtual_meeting_link')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Notes</label>
                            <input type="text" name="location_notes" placeholder="e.g., Ring the doorbell" value="{{ old('location_notes') }}"
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                       focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            @error('location_notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preparation Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preparation Notes</label>
                            <textarea name="preparation_notes" rows="2" placeholder="What to prepare or bring" class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ old('preparation_notes') }}</textarea>
                            @error('preparation_notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Auto-confirm -->
                        <div class="flex items-center">
                            <input type="checkbox" name="consultant_confirmed" value="1" id="autoConfirm" 
                                {{ old('consultant_confirmed', true) ? 'checked' : '' }}
                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <label for="autoConfirm" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Automatically confirm this appointment
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showModal = false"
                            class="w-full sm:flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 
                                   rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Cancel
                        </button>
                        <button type="submit" :disabled="!applicationId"
                            class="w-full sm:flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                            Schedule Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->any() || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.dispatchEvent(new CustomEvent('open-appointment-modal', {
                detail: {
                    applicationId: {{ $applicationId ?? 'null' }},
                    applicantId: {{ $applicantId ?? 'null' }},
                    applicantAddress: '{{ $address ?? '' }}'
                }
            }));

        });
    </script>
@endif

<style>
[x-cloak] { display: none !important; }
</style>