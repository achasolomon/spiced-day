<!-- resources/views/components/postal-codes/create-modal.blade.php -->
<div 
    x-data="{
        show: false,
        loading: false,
        regions: @js($regions),
        formData: {
            region_id: '',
            prefixes: '',
            full_postal_codes: ''
        },
        errors: {},
        mode: 'multiple', // 'multiple' for comma-separated prefixes, 'single' for single prefix with full codes
        showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        },
        async submitForm() {
            this.loading = true;
            this.errors = {};
            
            try {
                if (this.mode === 'multiple') {
                    // Split prefixes by comma and submit each one
                    const prefixes = this.formData.prefixes
                        .split(',')
                        .map(p => p.trim().toUpperCase())
                        .filter(p => p.length > 0);

                    if (prefixes.length === 0) {
                        this.showToast('error', 'Please enter at least one prefix');
                        this.loading = false;
                        return;
                    }

                    let successCount = 0;
                    let failureCount = 0;

                    for (const prefix of prefixes) {
                        const response = await fetch('{{ route('admin.postal-codes.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                region_id: this.formData.region_id,
                                prefix: prefix,
                                full_postal_codes: ''
                            })
                        });

                        const data = await response.json();
                        if (response.ok && data.success) {
                            successCount++;
                        } else {
                            failureCount++;
                            console.error(`Failed to create prefix ${prefix}:`, data);
                        }
                    }

                    if (failureCount === 0) {
                        this.showToast('success', `${successCount} postal code prefix(es) created successfully`);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        this.showToast('error', `Created ${successCount}, but ${failureCount} failed`);
                    }
                } else {
                    // Single prefix with full postal codes
                    const response = await fetch('{{ route('admin.postal-codes.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            region_id: this.formData.region_id,
                            prefix: this.formData.prefixes.trim().toUpperCase(),
                            full_postal_codes: this.formData.full_postal_codes
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.showToast('success', data.message || 'Postal code created successfully');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        this.errors = data.errors || {};
                        if (data.message) {
                            this.showToast('error', data.message);
                        }
                    }
                }
            } catch (error) {
                console.error('Error creating postal code:', error);
                this.showToast('error', 'An error occurred. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        open() {
            this.show = true;
            this.formData = {
                region_id: '',
                prefixes: '',
                full_postal_codes: ''
            };
            this.errors = {};
            this.mode = 'multiple';
        },
        close() {
            this.show = false;
        }
    }"
    @open-create-postal-code-modal.window="open()"
    @keydown.escape.window="show && close()"
    x-cloak
>
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
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="close()"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6" @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Postal Code</h3>
                    <button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <!-- Mode Toggle -->
                <div class="mb-6 flex gap-2 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <button 
                        type="button"
                        @click="mode = 'multiple'"
                        :class="{
                            'bg-orange-600 text-white': mode === 'multiple',
                            'bg-transparent text-gray-700 dark:text-gray-300': mode !== 'multiple'
                        }"
                        class="flex-1 px-4 py-2 rounded-lg font-medium transition text-sm"
                    >
                        Multiple Prefixes
                    </button>
                    <button 
                        type="button"
                        @click="mode = 'single'"
                        :class="{
                            'bg-orange-600 text-white': mode === 'single',
                            'bg-transparent text-gray-700 dark:text-gray-300': mode !== 'single'
                        }"
                        class="flex-1 px-4 py-2 rounded-lg font-medium transition text-sm"
                    >
                        Single Prefix + Codes
                    </button>
                </div>

                <form @submit.prevent="submitForm">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Region <span class="text-red-500">*</span>
                            </label>
                            <select 
                                x-model="formData.region_id"
                                required
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': errors.region_id }"
                            >
                                <option value="">Select a region...</option>
                                <template x-for="region in regions" :key="region.id">
                                    <option :value="region.id" x-text="region.name"></option>
                                </template>
                            </select>
                            <template x-if="errors.region_id">
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="errors.region_id[0]"></p>
                            </template>
                        </div>

                        <!-- Multiple Prefixes Mode -->
                        <div x-show="mode === 'multiple'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Postal Code Prefixes <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                x-model="formData.prefixes"
                                required 
                                placeholder="e.g., T2P, T3K, T5J, T1X"
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': errors.prefixes }"
                            >
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Enter multiple postal code prefixes separated by commas. Each will be created as a separate entry.
                            </p>
                            <template x-if="errors.prefixes">
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="errors.prefixes[0]"></p>
                            </template>
                        </div>

                        <!-- Single Prefix + Full Codes Mode -->
                        <div x-show="mode === 'single'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Postal Code Prefix <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.prefixes"
                                    required 
                                    placeholder="e.g., T2P"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white uppercase"
                                    :class="{ 'border-red-500': errors.prefixes }"
                                    maxlength="10"
                                >
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Enter a single postal code prefix (e.g., T2P for Calgary)
                                </p>
                                <template x-if="errors.prefixes">
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="errors.prefixes[0]"></p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Postal Codes (Optional)
                                </label>
                                <textarea 
                                    x-model="formData.full_postal_codes"
                                    placeholder="e.g., T2P 1A1, T2P 1A2, T2P 1A3"
                                    rows="3"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': errors.full_postal_codes }"
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Separate multiple codes with commas. Leave empty if you only want the prefix.
                                </p>
                                <template x-if="errors.full_postal_codes">
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="errors.full_postal_codes[0]"></p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button 
                            type="button" 
                            @click="close()"
                            :disabled="loading"
                            class="w-full sm:flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition disabled:opacity-50"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            :disabled="loading"
                            class="w-full sm:flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span x-show="!loading">
                                <span x-show="mode === 'multiple'">Create Prefixes</span>
                                <span x-show="mode === 'single'">Create Postal Code</span>
                            </span>
                            <span x-show="loading">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>