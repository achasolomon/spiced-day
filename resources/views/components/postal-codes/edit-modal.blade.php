<!-- resources/views/components/postal-codes/edit-modal.blade.php -->
<div 
    x-data="{
        show: false,
        loading: false,
        postalCode: null,
        regions: @js($regions),
        formData: {
            region_id: '',
            prefix: '',
            full_postal_codes: ''
        },
        errors: {},
        async fetchPostalCode(postalCodeId) {
            this.loading = true;
            try {
                const response = await fetch(`/admin/postal-codes/${postalCodeId}/data`);
                const data = await response.json();
                
                if (data.success) {
                    this.postalCode = data.postalCode;
                    this.formData = {
                        region_id: data.postalCode.region_id,
                        prefix: data.postalCode.prefix,
                        full_postal_codes: data.postalCode.full_postal_codes || ''
                    };
                }
            } catch (error) {
                console.error('Error fetching postal code:', error);
                alert('Failed to load postal code data');
            } finally {
                this.loading = false;
            }
        },
        async submitForm() {
            this.loading = true;
            this.errors = {};
            
            try {
                const response = await fetch(`/admin/postal-codes/${this.postalCode.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.location.reload();
                } else {
                    this.errors = data.errors || {};
                    if (data.message) {
                        alert(data.message);
                    }
                }
            } catch (error) {
                console.error('Error updating postal code:', error);
                alert('An error occurred. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        open(postalCodeId) {
            this.show = true;
            this.fetchPostalCode(postalCodeId);
        },
        close() {
            this.show = false;
            this.postalCode = null;
            this.errors = {};
        }
    }"
    @open-edit-postal-code-modal.window="open($event.detail.postalCodeId)"
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
                <!-- Loading State -->
                <template x-if="loading">
                    <div class="text-center py-12">
                        <svg class="animate-spin h-12 w-12 text-orange-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading...</p>
                    </div>
                </template>

                <!-- Form -->
                <template x-if="!loading && postalCode">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Postal Code</h3>
                            <button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
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

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Postal Code Prefix <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="formData.prefix"
                                        required 
                                        placeholder="e.g., T2P, T3K"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white uppercase"
                                        :class="{ 'border-red-500': errors.prefix }"
                                    >
                                    <template x-if="errors.prefix">
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="errors.prefix[0]"></p>
                                    </template>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Full Postal Codes (Optional)
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="formData.full_postal_codes"
                                        placeholder="e.g., T2P 1A1, T2P 1A2, T2P 1A3"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': errors.full_postal_codes }"
                                    >
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Separate multiple codes with commas
                                    </p>
                                    <template x-if="errors.full_postal_codes">
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="errors.full_postal_codes[0]"></p>
                                    </template>
                                </div>
                            </div>

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
                                    :disabled="loading"
                                    class="w-full sm:flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span x-show="!loading">Update Postal Code</span>
                                    <span x-show="loading">Updating...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>