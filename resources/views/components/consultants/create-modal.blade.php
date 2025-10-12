<!-- resources/views/components/consultants/create-modal.blade.php -->
<div 
    x-data="{
        show: false,
        users: [],
        regions: [],
        selectedRegions: [],
        loading: false,
        async fetchUsers() {
            this.loading = true;
            try {
                const response = await fetch('/admin/consultants/available-users');
                this.users = await response.json();
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        },
        async fetchRegions() {
            try {
                const response = await fetch('/admin/regions/all');
                this.regions = await response.json();
            } catch (error) {
                console.error('Error fetching regions:', error);
            }
        },
        open() {
            this.show = true;
            this.fetchUsers();
            this.fetchRegions();
        },
        close() {
            this.show = false;
            this.users = [];
            this.regions = [];
            this.selectedRegions = [];
        }
    }"
    @open-create-consultant-modal.window="open()"
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
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Consultant</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new consultant profile</p>
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

                <form action="{{ route('admin.consultants.store') }}" method="POST">
                    @csrf

                    <div class="space-y-4 max-h-[65vh] overflow-y-auto px-1">
                        <!-- User Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select User <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="user_id" 
                                required
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">Choose a user...</option>
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="`${user.name} (${user.email})`"></option>
                                </template>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Only users with consultant type and no existing profile are shown</p>
                        </div>

                        <!-- Regions Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assigned Regions <span class="text-red-500">*</span>
                            </label>
                            
                            <!-- Selected Regions Display (Tags) -->
                            <div x-show="selectedRegions.length > 0" class="flex flex-wrap gap-2 mb-3">
                                <template x-for="regionId in selectedRegions" :key="regionId">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200 text-sm rounded-full border border-orange-200 dark:border-orange-700">
                                        <span x-text="regions.find(r => r.id == regionId)?.name || 'Region ' + regionId"></span>
                                        <button 
                                            type="button"
                                            @click="selectedRegions = selectedRegions.filter(id => id != regionId)"
                                            class="hover:bg-orange-200 dark:hover:bg-orange-800 rounded-full p-0.5 transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                            </div>

                            <!-- Dropdown Button -->
                            <div x-data="{ open: false, search: '' }" class="relative">
                                <button 
                                    type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="w-full px-4 py-2.5 text-left text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white bg-white flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition"
                                >
                                    <span x-text="selectedRegions.length > 0 ? `${selectedRegions.length} region(s) selected` : 'Select regions...'" 
                                        class="text-gray-700 dark:text-gray-300"
                                        :class="selectedRegions.length === 0 && 'text-gray-500 dark:text-gray-400'">
                                    </span>
                                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>

                                <!-- Dropdown Content -->
                                <div 
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg overflow-hidden"
                                    style="display: none;"
                                >
                                    <!-- Search Box -->
                                    <div class="p-3 border-b border-gray-200 dark:border-gray-600">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                            </svg>
                                            <input 
                                                type="text"
                                                x-model="search"
                                                placeholder="Search regions..."
                                                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-600 dark:text-white"
                                                @click.stop
                                            >
                                        </div>
                                    </div>

                                    <!-- Regions List -->
                                    <div class="max-h-60 overflow-y-auto">
                                        <template x-for="region in regions.filter(r => r.name.toLowerCase().includes(search.toLowerCase()))" :key="region.id">
                                            <label 
                                                class="flex items-center px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition"
                                                :class="selectedRegions.includes(region.id) && 'bg-orange-50 dark:bg-orange-900/20'"
                                            >
                                                <input 
                                                    type="checkbox"
                                                    :value="region.id"
                                                    x-model="selectedRegions"
                                                    class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                                >
                                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-200" x-text="region.name"></span>
                                                <svg 
                                                    x-show="selectedRegions.includes(region.id)"
                                                    class="ml-auto w-5 h-5 text-orange-600 dark:text-orange-400"
                                                    fill="currentColor" 
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </label>
                                        </template>
                                        
                                        <!-- No Results -->
                                        <div 
                                            x-show="search && regions.filter(r => r.name.toLowerCase().includes(search.toLowerCase())).length === 0"
                                            class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            No regions found matching "<span x-text="search"></span>"
                                        </div>
                                    </div>

                                    <!-- Footer Actions -->
                                    <div class="p-3 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between bg-gray-50 dark:bg-gray-800">
                                        <button 
                                            type="button"
                                            @click="selectedRegions = []"
                                            class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium"
                                        >
                                            Clear all
                                        </button>
                                        <button 
                                            type="button"
                                            @click="selectedRegions = regions.map(r => r.id)"
                                            class="text-xs text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium"
                                        >
                                            Select all
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden inputs for form submission -->
                            <template x-for="regionId in selectedRegions" :key="regionId">
                                <input type="hidden" name="regions[]" :value="regionId">
                            </template>

                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Select one or more regions this consultant will cover
                            </p>
                        </div>
                        
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
                                    value="{{ old('employee_id') }}"
                                    placeholder="EMP-001234"
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
                                    value="{{ old('position_title', 'Licensing Consultant') }}"
                                    placeholder="Licensing Consultant"
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
                                    value="{{ old('department', 'Licensing') }}"
                                    placeholder="Licensing"
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
                                    value="{{ old('hire_date', date('Y-m-d')) }}"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                >
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
                                    value="{{ old('max_concurrent_applications', 10) }}"
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
                                        value="{{ old('work_phone') }}"
                                        placeholder="+1 (555) 123-4567"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Name</label>
                                    <input 
                                        type="text" 
                                        name="emergency_contact_name" 
                                        value="{{ old('emergency_contact_name') }}"
                                        placeholder="John Doe"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Phone</label>
                                    <input 
                                        type="tel" 
                                        name="emergency_contact_phone" 
                                        value="{{ old('emergency_contact_phone') }}"
                                        placeholder="+1 (555) 987-6543"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Skills (Optional) -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Skills & Qualifications (Optional)</h4>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Certifications</label>
                                <input 
                                    type="text" 
                                    name="certifications[]" 
                                    value="{{ old('certifications.0') }}"
                                    placeholder="Enter certification (e.g., Early Childhood Education Certificate)"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                >
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add certifications one at a time</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specializations</label>
                                <input 
                                    type="text" 
                                    name="specializations[]" 
                                    value="{{ old('specializations.0') }}"
                                    placeholder="Enter specialization (e.g., Home Inspections)"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                >
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Languages</label>
                                <input 
                                    type="text" 
                                    name="languages[]" 
                                    value="{{ old('languages.0') }}"
                                    placeholder="Enter language (e.g., English)"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                        </div>

                        <!-- Bio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                            <textarea 
                                name="bio" 
                                rows="4"
                                placeholder="Brief professional biography..."
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                            >{{ old('bio') }}</textarea>
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
                                        id="can_approve"
                                        {{ old('can_approve_applications') ? 'checked' : '' }}
                                        class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                    >
                                    <label for="can_approve" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Can approve applications
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="can_conduct_inspections" 
                                        value="1" 
                                        id="can_inspect"
                                        checked
                                        class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                    >
                                    <label for="can_inspect" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Can conduct inspections
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="can_view_all_applications" 
                                        value="1" 
                                        id="can_view_all"
                                        {{ old('can_view_all_applications') ? 'checked' : '' }}
                                        class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                    >
                                    <label for="can_view_all" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Can view all applications
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Info Note -->
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-xs text-blue-800 dark:text-blue-200">
                                    <p class="font-medium">Note:</p>
                                    <p class="mt-1">The consultant will be created with "Active" status and will be able to accept new applications immediately.</p>
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
                            Create Consultant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.dispatchEvent(new CustomEvent('open-create-consultant-modal'));
        });
    </script>
@endif