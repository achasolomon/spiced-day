<!-- resources/views/components/users/show-modal.blade.php -->

<div 
    x-data="{
        show: false,
        loading: false,
        user: null,
        activeTab: 'details',
        async fetchUser(userId) {
            this.loading = true;
            try {
                const response = await fetch(`/admin/users/${userId}/data`);
                this.user = await response.json();
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },
        open(userId) {
            this.show = true;
            this.activeTab = 'details';
            this.fetchUser(userId);
        },
        close() {
            this.show = false;
            this.user = null;
        }
    }"
    @open-show-user-modal.window="open($event.detail.userId)"
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
            <div 
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                @click="close()"
            ></div>

            <!-- Modal Content -->
            <div 
                class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-4xl w-full p-6"
                @click.stop
            >
                <!-- Loading State -->
                <template x-if="loading">
                    <div class="text-center py-12">
                        <svg class="animate-spin h-12 w-12 text-purple-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading user details...</p>
                    </div>
                </template>

                <!-- Content -->
                <template x-if="!loading && user">
                    <div>
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    <span x-text="user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()"></span>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="user.name"></h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="user.email"></p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': user.user_type === 'applicant',
                                                'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300': user.user_type === 'consultant',
                                                'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300': user.user_type === 'admin'
                                            }"
                                            x-text="user.user_type.charAt(0).toUpperCase() + user.user_type.slice(1)"
                                        ></span>
                                        <span 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="user.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'"
                                            x-text="user.is_active ? 'Active' : 'Inactive'"
                                        ></span>
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
                                    :class="activeTab === 'details' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                >
                                    Details
                                </button>
                                <button 
                                    @click="activeTab = 'activity'"
                                    :class="activeTab === 'activity' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                >
                                    Activity
                                </button>
                                <button 
                                    @click="activeTab = 'stats'"
                                    :class="activeTab === 'stats' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                >
                                    Statistics
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="max-h-[60vh] overflow-y-auto">
                            <!-- Details Tab -->
                            <div x-show="activeTab === 'details'" class="space-y-6">
                                <!-- Personal Information -->
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Full Name</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.name || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.email || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Phone</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.phone || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">User Type</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.user_type ? user.user_type.charAt(0).toUpperCase() + user.user_type.slice(1) : 'N/A'"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Street Address</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.address || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">City</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.city || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Province</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.province || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Postal Code</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.postal_code || 'N/A'"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Information -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                                            <p class="text-base font-medium" 
                                               :class="user.is_active ? 'text-green-600' : 'text-red-600'"
                                               x-text="user.is_active ? 'Active' : 'Inactive'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Email Verified</p>
                                            <p class="text-base font-medium"
                                               :class="user.email_verified_at ? 'text-green-600' : 'text-red-600'"
                                               x-text="user.email_verified_at ? 'Yes' : 'No'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Member Since</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.created_at_formatted || 'N/A'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Last Login</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.last_login_formatted || 'Never'"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Consultant Info -->
                                <template x-if="user.user_type === 'consultant' && user.consultant">
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Consultant Information</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Employment Status</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.consultant.employment_status || 'N/A'"></p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Assigned Applications</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white" x-text="user.stats.assigned_applications || 0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Activity Tab -->
                            <div x-show="activeTab === 'activity'" class="space-y-4">
                                <template x-if="user.recent_activity && user.recent_activity.length > 0">
                                    <div class="space-y-3">
                                        <template x-for="activity in user.recent_activity" :key="activity.id">
                                            <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                                <div class="flex-shrink-0 w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="activity.description"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="activity.created_at"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!user.recent_activity || user.recent_activity.length === 0">
                                    <div class="text-center py-8">
                                        <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
                                    </div>
                                </template>
                            </div>

                            <!-- Statistics Tab -->
                            <div x-show="activeTab === 'stats'">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <template x-if="user.user_type === 'applicant'">
                                        <div class="contents">
                                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                                <p class="text-sm text-blue-600 dark:text-blue-400">Applications</p>
                                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-300" x-text="user.stats.applications || 0"></p>
                                            </div>
                                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                                <p class="text-sm text-green-600 dark:text-green-400">Documents</p>
                                                <p class="text-2xl font-bold text-green-900 dark:text-green-300" x-text="user.stats.documents || 0"></p>
                                            </div>
                                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                                <p class="text-sm text-purple-600 dark:text-purple-400">Appointments</p>
                                                <p class="text-2xl font-bold text-purple-900 dark:text-purple-300" x-text="user.stats.appointments || 0"></p>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="user.user_type === 'consultant'">
                                        <div class="contents">
                                            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                                                <p class="text-sm text-orange-600 dark:text-orange-400">Assigned</p>
                                                <p class="text-2xl font-bold text-orange-900 dark:text-orange-300" x-text="user.stats.assigned_applications || 0"></p>
                                            </div>
                                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                                <p class="text-sm text-green-600 dark:text-green-400">Completed</p>
                                                <p class="text-2xl font-bold text-green-900 dark:text-green-300" x-text="user.stats.completed_applications || 0"></p>
                                            </div>
                                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                                <p class="text-sm text-purple-600 dark:text-purple-400">Inspections</p>
                                                <p class="text-2xl font-bold text-purple-900 dark:text-purple-300" x-text="user.stats.inspections || 0"></p>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Login Count</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="user.stats.login_count || 0"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <!-- <button 
                                type="button" 
                                @click="close(); setTimeout(() => $dispatch('open-edit-user-modal', { userId: user.id }), 3000)"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition"
                            >
                                Edit User
                            </button> -->
                            
                            <template x-if="user.is_active">
                                <form :action="`/admin/users/${user.id}/deactivate`" method="POST" class="inline">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        onclick="return confirm('Are you sure you want to deactivate this user?')"
                                        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition"
                                    >
                                        Deactivate
                                    </button>
                                </form>
                            </template>
                            
                            <template x-if="!user.is_active">
                                <form :action="`/admin/users/${user.id}/activate`" method="POST" class="inline">
                                    @csrf
                                    <button 
                                        type="submit"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition"
                                    >
                                        Activate
                                    </button>
                                </form>
                            </template>

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