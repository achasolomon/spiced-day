<!-- resources/views/components/consultants/show-modal.blade.php -->
<div 
    x-data="{
        show: false,
        loading: false,
        consultant: null,
        activeTab: 'details',
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
            this.activeTab = 'details';
            this.fetchConsultant(consultantId);
        },
        close() {
            this.show = false;
            this.consultant = null;
        }
    }"
    @open-show-consultant-modal.window="open($event.detail.consultantId)"
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
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-4xl w-full p-6" @click.stop>
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
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    <span x-text="consultant.user_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()"></span>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="consultant.user_name"></h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="consultant.user_email"></p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': consultant.employment_status === 'active',
                                                'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300': consultant.employment_status === 'inactive',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': consultant.employment_status === 'on_leave',
                                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': consultant.employment_status === 'terminated'
                                            }"
                                            x-text="consultant.employment_status.replace('_', ' ').charAt(0).toUpperCase() + consultant.employment_status.replace('_', ' ').slice(1)"
                                        ></span>
                                        <span 
                                            x-show="consultant.accepts_new_applications"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                                        >
                                            Accepting Applications
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Tabs -->
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                            <nav class="-mb-px flex space-x-8">
                                <button 
                                    @click="activeTab = 'details'"
                                    :class="activeTab === 'details' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                >
                                    Details
                                </button>
                                <button 
                                    @click="activeTab = 'workload'"
                                    :class="activeTab === 'workload' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                >
                                    Workload
                                </button>
                                <button 
                                    @click="activeTab = 'performance'"
                                    :class="activeTab === 'performance' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                >
                                    Performance
                                </button>
                                <button 
                                    @click="activeTab = 'activity'"
                                    :class="activeTab === 'activity' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                >
                                    Activity
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="max-h-[60vh] overflow-y-auto">
                            <!-- Details Tab -->
                            <div x-show="activeTab === 'details'" class="space-y-6">
                                <!-- Basic Information -->
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Employee ID</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="consultant.employee_id"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Position</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="consultant.position_title"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Department</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="consultant.department || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Hire Date</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="consultant.hire_date_formatted"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Regions -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assigned Regions</h4>
                                    <template x-if="consultant.regions && consultant.regions.length > 0">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="region in consultant.regions" :key="region.id">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300" x-text="region.name"></span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!consultant.regions || consultant.regions.length === 0">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">No regions assigned</p>
                                    </template>
                                </div>

                                <!-- Contact Information -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Work Phone</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="consultant.work_phone || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Emergency Contact</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="consultant.emergency_contact_name || 'N/A'"></p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="consultant.emergency_contact_phone"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Skills & Qualifications -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Skills & Qualifications</h4>
                                    <template x-if="consultant.certifications && consultant.certifications.length > 0">
                                        <div class="mb-4">
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Certifications</p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="cert in consultant.certifications" :key="cert">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300" x-text="cert"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="consultant.specializations && consultant.specializations.length > 0">
                                        <div class="mb-4">
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specializations</p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="spec in consultant.specializations" :key="spec">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300" x-text="spec"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="consultant.languages && consultant.languages.length > 0">
                                        <div class="mb-4">
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Languages</p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="lang in consultant.languages" :key="lang">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300" x-text="lang"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="consultant.bio">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400" x-text="consultant.bio"></p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Permissions -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Permissions</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <svg :class="consultant.can_approve_applications ? 'text-green-500' : 'text-gray-300'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Can Approve Applications</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg :class="consultant.can_conduct_inspections ? 'text-green-500' : 'text-gray-300'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Can Conduct Inspections</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg :class="consultant.can_view_all_applications ? 'text-green-500' : 'text-gray-300'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Can View All Applications</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Workload Tab -->
                            <div x-show="activeTab === 'workload'" class="space-y-6">
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                        <p class="text-sm text-blue-600 dark:text-blue-400">Total Applications</p>
                                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-300" x-text="consultant.stats.total_applications"></p>
                                    </div>
                                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                                        <p class="text-sm text-orange-600 dark:text-orange-400">Active</p>
                                        <p class="text-2xl font-bold text-orange-900 dark:text-orange-300" x-text="consultant.stats.active_applications"></p>
                                    </div>
                                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                        <p class="text-sm text-green-600 dark:text-green-400">Completed</p>
                                        <p class="text-2xl font-bold text-green-900 dark:text-green-300" x-text="consultant.stats.completed_applications"></p>
                                    </div>
                                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                        <p class="text-sm text-purple-600 dark:text-purple-400">Pending Inspections</p>
                                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-300" x-text="consultant.stats.pending_inspections"></p>
                                    </div>
                                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                                        <p class="text-sm text-indigo-600 dark:text-indigo-400">Completed Inspections</p>
                                        <p class="text-2xl font-bold text-indigo-900 dark:text-indigo-300" x-text="consultant.stats.completed_inspections"></p>
                                    </div>
                                    <div class="bg-pink-50 dark:bg-pink-900/20 rounded-lg p-4">
                                        <p class="text-sm text-pink-600 dark:text-pink-400">Appointments This Month</p>
                                        <p class="text-2xl font-bold text-pink-900 dark:text-pink-300" x-text="consultant.stats.appointments_this_month"></p>
                                    </div>
                                </div>

                                <!-- Workload Capacity -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Workload Capacity</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="flex justify-between text-sm mb-2">
                                                <span class="text-gray-600 dark:text-gray-400">Current Load</span>
                                                <span class="font-medium text-gray-900 dark:text-white">
                                                    <span x-text="consultant.active_applications"></span> / <span x-text="consultant.max_concurrent_applications"></span>
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                                <div 
                                                    class="h-3 rounded-full transition-all"
                                                    :class="{
                                                        'bg-green-600': (consultant.active_applications / consultant.max_concurrent_applications) < 0.5,
                                                        'bg-yellow-600': (consultant.active_applications / consultant.max_concurrent_applications) >= 0.5 && (consultant.active_applications / consultant.max_concurrent_applications) < 0.8,
                                                        'bg-red-600': (consultant.active_applications / consultant.max_concurrent_applications) >= 0.8
                                                    }"
                                                    :style="`width: ${Math.min((consultant.active_applications / consultant.max_concurrent_applications) * 100, 100)}%`"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Tab -->
                            <div x-show="activeTab === 'performance'" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 text-center">
                                        <div class="flex items-center justify-center gap-1 mb-2">
                                            <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400">Satisfaction Rating</p>
                                        <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-300" x-text="(consultant.client_satisfaction_rating || 0).toFixed(1)"></p>
                                    </div>
                                    
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                                        <p class="text-sm text-blue-600 dark:text-blue-400">Total Handled</p>
                                        <p class="text-3xl font-bold text-blue-900 dark:text-blue-300" x-text="consultant.total_applications_handled || 0"></p>
                                    </div>
                                    
                                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                                        <p class="text-sm text-green-600 dark:text-green-400">Approval Rate</p>
                                        <p class="text-3xl font-bold text-green-900 dark:text-green-300">
                                            <span x-text="(consultant.approval_rate || 0).toFixed(1)"></span>%
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Tab -->
                            <div x-show="activeTab === 'activity'" class="space-y-4">
                                <template x-if="consultant.recent_activity && consultant.recent_activity.length > 0">
                                    <div class="space-y-3">
                                        <template x-for="activity in consultant.recent_activity" :key="activity.id">
                                            <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                                <div class="flex-shrink-0 w-2 h-2 bg-orange-500 rounded-full mt-2"></div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="activity.description"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="activity.created_at"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!consultant.recent_activity || consultant.recent_activity.length === 0">
                                    <div class="text-center py-8">
                                        <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button 
                                type="button" 
                                @click="close(); setTimeout(() => $dispatch('open-edit-consultant-modal', { consultantId: consultant.id }), 300)"
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition"
                            >
                                Edit Consultant
                            </button>
                            
                            <form :action="`/admin/consultants/${consultant.id}/toggle-availability`" method="POST" class="inline">
                                @csrf
                                <button 
                                    type="submit"
                                    class="px-4 py-2 rounded-lg font-medium transition"
                                    :class="consultant.accepts_new_applications ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white'"
                                >
                                    <span x-text="consultant.accepts_new_applications ? 'Mark Unavailable' : 'Mark Available'"></span>
                                </button>
                            </form>

                            <button 
                                @click="close()"
                                class="ml-auto px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>