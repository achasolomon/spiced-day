@extends(match(auth()->user()->user_type) {
    'consultant' => 'layouts.consultant',
    'applicant' => 'layouts.dashboard',
    'admin' => 'layouts.admin',
    default => 'layouts.dashboard',
})

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your account information and preferences</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Basic Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h2>
        
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name*</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address*</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @if($user->email_verified_at === null)
                        <p class="mt-1 text-sm text-yellow-600 dark:text-yellow-400">Your email is not verified.</p>
                    @endif
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                    <input 
                        type="tel" 
                        name="phone" 
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                @if($user->isConsultant() && $user->consultant)
                    <!-- Consultant-Specific Fields -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Consultant Information</h3>
                        
                        <!-- Work Phone -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work Phone</label>
                            <input 
                                type="tel" 
                                name="work_phone" 
                                value="{{ old('work_phone', $user->consultant->work_phone) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Bio -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                            <textarea 
                                name="bio" 
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">{{ old('bio', $user->consultant->bio) }}</textarea>
                        </div>

                        <!-- Specializations -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specializations (comma-separated)</label>
                            <input 
                                type="text" 
                                name="specializations" 
                                value="{{ old('specializations', is_array($user->consultant->specializations) ? implode(', ', $user->consultant->specializations) : '') }}"
                                placeholder="e.g., Home Safety, Child Care, First Aid"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Languages -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Languages (comma-separated)</label>
                            <input 
                                type="text" 
                                name="languages" 
                                value="{{ old('languages', is_array($user->consultant->languages) ? implode(', ', $user->consultant->languages) : '') }}"
                                placeholder="e.g., English, French, Spanish"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Max Concurrent Applications -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Concurrent Applications</label>
                            <input 
                                type="number" 
                                name="max_concurrent_applications" 
                                value="{{ old('max_concurrent_applications', $user->consultant->max_concurrent_applications ?? 10) }}"
                                min="1"
                                max="50"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Accepts New Applications -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="accepts_new_applications" 
                                    value="1"
                                    {{ old('accepts_new_applications', $user->consultant->accepts_new_applications) ? 'checked' : '' }}
                                    class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Accept new applications</span>
                            </label>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Name</label>
                                <input 
                                    type="text" 
                                    name="emergency_contact_name" 
                                    value="{{ old('emergency_contact_name', $user->consultant->emergency_contact_name) }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Phone</label>
                                <input 
                                    type="tel" 
                                    name="emergency_contact_phone" 
                                    value="{{ old('emergency_contact_phone', $user->consultant->emergency_contact_phone) }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Change Password</h2>
        
        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password*</label>
                    <input 
                        type="password" 
                        name="current_password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password*</label>
                    <input 
                        type="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    @error('password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password*</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                </div>

                <button 
                    type="submit" 
                    class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm border border-red-200 dark:border-red-800 p-6">
        <h2 class="text-xl font-semibold text-red-900 dark:text-red-300 mb-2">Delete Account</h2>
        <p class="text-sm text-red-700 dark:text-red-400 mb-4">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
        
        <button 
            type="button"
            onclick="document.getElementById('deleteAccountModal').classList.remove('hidden')"
            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
            Delete Account
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Are you sure?</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">This action cannot be undone. Please enter your password to confirm.</p>
        
        <form action="{{ route('profile.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                @error('password', 'userDeletion')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex gap-3">
                <button 
                    type="button"
                    onclick="document.getElementById('deleteAccountModal').classList.add('hidden')"
                    class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection