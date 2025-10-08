<!-- resources/views/components/users/edit-modal.blade.php -->

<div 
    x-data="{ 
        showModal: false,
        userId: null,
        userData: null,
        loading: false,
        showPassword: false,
        showConfirmPassword: false,
        async fetchUserData(userId) {
            this.loading = true;
            try {
                const response = await fetch(`/admin/users/${userId}/data`);
                this.userData = await response.json();
            } catch (error) {
                console.error('Error fetching user data:', error);
            } finally {
                this.loading = false;
            }
        }
    }"
    @open-edit-user-modal.window="
        userId = $event.detail.userId;
        showModal = true;
        fetchUserData(userId);
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
                
                <!-- Loading State -->
                <div x-show="loading" class="text-center py-12">
                    <svg class="animate-spin h-12 w-12 text-purple-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Loading user details...</p>
                </div>

                <!-- Content -->
                <div x-show="!loading && userData" style="display: none;">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4 md:mb-6">
                        <div>
                            <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Edit User</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="`Update information for ${userData?.name}`"></p>
                        </div>
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

                    <form :action="`/admin/users/${userData?.id}`" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4 max-h-[65vh] overflow-y-auto px-1">
                            
                            <!-- User Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    User Type <span class="text-red-500">*</span>
                                </label>
                                <select name="user_type" required
                                        :value="userData?.user_type"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                               focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                    <option value="applicant" :selected="userData?.user_type === 'applicant'">Applicant</option>
                                    <option value="consultant" :selected="userData?.user_type === 'consultant'">Consultant</option>
                                    <option value="admin" :selected="userData?.user_type === 'admin'">Admin</option>
                                </select>
                                @error('user_type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" required 
                                       :value="userData?.name"
                                       placeholder="John Doe"
                                       class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                              focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" required 
                                       :value="userData?.email"
                                       placeholder="john@example.com"
                                       class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                              focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number
                                </label>
                                <input type="tel" name="phone" 
                                       :value="userData?.phone"
                                       placeholder="+1 (555) 123-4567"
                                       class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                              focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Section -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                                    Change Password (Optional)
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    Leave blank to keep current password
                                </p>

                                <div class="space-y-3">
                                    <!-- New Password -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            New Password
                                        </label>
                                        <div class="relative">
                                            <input :type="showPassword ? 'text' : 'password'" name="password"
                                                placeholder="Minimum 8 characters"
                                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                       focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white pr-10">
                                            <button type="button" @click="showPassword = !showPassword"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Confirm Password -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Confirm New Password
                                        </label>
                                        <div class="relative">
                                            <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation"
                                                placeholder="Re-enter new password"
                                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                       focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white pr-10">
                                            <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address (Optional) -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Address Information</h4>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Street Address</label>
                                        <input type="text" name="address" 
                                               :value="userData?.address"
                                               placeholder="123 Main Street"
                                               class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                      focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                                            <input type="text" name="city" 
                                                   :value="userData?.city"
                                                   placeholder="Calgary"
                                                   class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                          focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Province</label>
                                            <input type="text" name="province" 
                                                   :value="userData?.province"
                                                   placeholder="Alberta"
                                                   class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                          focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                                        <input type="text" name="postal_code" 
                                               :value="userData?.postal_code"
                                               placeholder="T2P 1J9"
                                               class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                      focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" id="is_active_edit" 
                                       :checked="userData?.is_active"
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <label for="is_active_edit" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    User is active
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
                            <button type="submit"
                                class="w-full sm:flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('editUserId'))
                window.dispatchEvent(new CustomEvent('open-edit-user-modal', {
                    detail: { userId: {{ session('editUserId') }} }
                }));
            @endif
        });
    </script>
@endif

<style>
[x-cloak] { display: none !important; }
</style>