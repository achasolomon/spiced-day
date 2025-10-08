<!-- resources/views/components/consultants/edit-modal.blade.php -->

<div 
    x-data="{
        show: false,
        loading: false,
        consultant: null,
        async fetchConsultant(consultantId) {
            this.loading = true;
            try {
                const response = await fetch(`/admin/consultants/${consultantId}/data`);
                this.consultant = await response.json();
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },
        open(consultantId) {
            this.show = true;
            this.fetchConsultant(consultantId);
        },
        close() {
            this.show = false;
            this.consultant = null;
        }
    }"
    @open-edit-consultant-modal.window="open($event.detail.consultantId)"
    @keydown.escape.window="show && close()"
    x-cloak
>
    <!-- Modal Overlay -->
    <div 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="close()"></div>

            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-3xl w-full p-6" @click.stop>
                
                <!-- Loading State -->
                <template x-if="loading">
                    <div class="text-center py-12">
                        <svg class="animate-spin h-12 w-12 text-orange-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading consultant details...</p>
                    </div>
                </template>

                <!-- Content -->
                <template x-if="!loading && consultant">
                    <div>
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Consultant</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update information for <span x-text="consultant.user_name"></span></p>
                            </div>
                            <button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Errors:</h3>
                                        <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form :action="`/admin/consultants/${consultant.id}`" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4 max-h-[65vh] overflow-y-auto px-1">
                                
                                <!-- Basic Information -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Employee ID <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="employee_id" 
                                            required 
                                            :value="consultant.employee_id"
                                            class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Position Title <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="position_title" 
                                            required 
                                            :value="consultant.position_title"
                                            class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Department
                                        </label>
                                        <input 
                                            type="text" 
                                            name="department" 
                                            :value="consultant.department"
                                            class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Hire Date <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            name="hire_date" 
                                            required 
                                            :value="consultant.hire_date"
                                            class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Employment Status <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            name="employment_status" 
                                            required
                                            class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        >
                                            <option value="active" :selected="consultant.employment_status === 'active'">Active</option>
                                            <option value="inactive" :selected="consultant.employment_status === 'inactive'">Inactive</option>
                                            <option value="on_leave" :selected="consultant.employment_status === 'on_leave'">On Leave</option>
                                            <option value="terminated" :selected="consultant.employment_status === 'terminated'">Terminated</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Max Concurrent Applications <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            name="max_concurrent_applications" 
                                            required 
                                            min="1" 
                                            max="50"
                                            :value="consultant.max_concurrent_applications"
                                            class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        >
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Contact Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work Phone</label>
                                            <input 
                                                type="tel" 
                                                name="work_phone" 
                                                :value="consultant.work_phone"
                                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                            >
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Name</label>
                                            <input 
                                                type="text" 
                                                name="emergency_contact_name" 
                                                :value="consultant.emergency_contact_name"
                                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                            >
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Phone</label>
                                            <input 
                                                type="tel" 
                                                name="emergency_contact_phone" 
                                                :value="consultant.emergency_contact_phone"
                                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Bio -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                                    <textarea 
                                        name="bio" 
                                        rows="4"
                                        x-text="consultant.bio"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                    ></textarea>
                                </div>

                                <!-- Internal Notes (Admin Only) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Internal Notes</label>
                                    <textarea 
                                        name="internal_notes" 
                                        rows="3"
                                        x-text="consultant.internal_notes"
                                        placeholder="Internal notes (not visible to consultant)"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                    ></textarea>
                                </div>

                                <!-- Permissions -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Permissions</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="can_approve_applications" 
                                                value="1" 
                                                id="can_approve_edit"
                                                :checked="consultant.can_approve_applications"
                                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                            >
                                            <label for="can_approve_edit" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                Can approve applications
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="can_conduct_inspections" 
                                                value="1" 
                                                id="can_inspect_edit"
                                                :checked="consultant.can_conduct_inspections"
                                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                            >
                                            <label for="can_inspect_edit" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                Can conduct inspections
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="can_view_all_applications" 
                                                value="1" 
                                                id="can_view_all_edit"
                                                :checked="consultant.can_view_all_applications"
                                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                            >
                                            <label for="can_view_all_edit" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                Can view all applications
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="accepts_new_applications" 
                                                value="1" 
                                                id="accepts_new_edit"
                                                :checked="consultant.accepts_new_applications"
                                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                            >
                                            <label for="accepts_new_edit" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                Accepting new applications
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row items-center gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button 
                                    type="button" 
                                    @click="close()"
                                    class="w-full sm:flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="w-full sm:flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition"
                                >
                                    Update Consultant
                                </button>
                            </div>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@if($errors->any() && session('editConsultantId'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.dispatchEvent(new CustomEvent('open-edit-consultant-modal', {
                detail: { consultantId: {{ session('editConsultantId') }} }
            }));
        });
    </script>
@endif