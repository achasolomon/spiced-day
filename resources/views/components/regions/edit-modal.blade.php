<!-- resources/views/components/regions/edit-modal.blade.php -->
<div 
    x-data="{
        show: false,
        loading: false,
        region: null,
        formData: {
            name: '',
            description: ''
        },
        errors: {},
        showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        },
        async fetchRegion(regionId) {
            this.loading = true;
            try {
                const response = await fetch(`/admin/regions/${regionId}/data`);
                const data = await response.json();
                
                console.log('Fetched data:', data); // Debug log
                
                if (data.success && data.region) {
                    this.region = data.region;
                    this.formData = {
                        name: data.region.name || '',
                        description: data.region.description || ''
                    };
                } else {
                    alert('Failed to load region data');
                    this.close();
                }
            } catch (error) {
                console.error('Error fetching region:', error);
                alert('Failed to load region data');
                this.close();
            } finally {
                this.loading = false;
            }
        },
        async submitForm() {
            this.loading = true;
            this.errors = {};
            
            try {
                const response = await fetch(`/admin/regions/${this.region.id}`, {
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
                    this.showToast('success', data.message || 'Region updated successfully');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.errors = data.errors || {};
                    if (data.message) {
                        alert(data.message);
                    }
                }
            } catch (error) {
                console.error('Error updating region:', error);
                alert('An error occurred. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        open(regionId) {
            this.show = true;
            this.fetchRegion(regionId);
        },
        close() {
            this.show = false;
            this.region = null;
            this.formData = { name: '', description: '' };
            this.errors = {};
        }
    }"
    @open-edit-region-modal.window="open($event.detail.regionId)"
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
                <template x-if="loading && !region">
                    <div class="text-center py-12">
                        <svg class="animate-spin h-12 w-12 text-orange-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading...</p>
                    </div>
                </template>

                <!-- Form -->
                <template x-if="region">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Region</h3>
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
                                        Region Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="formData.name"
                                        required 
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': errors.name }"
                                    >
                                    <template x-if="errors.name">
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="errors.name[0]"></p>
                                    </template>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Description
                                    </label>
                                    <textarea 
                                        x-model="formData.description"
                                        rows="4"
                                        class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': errors.description }"
                                    ></textarea>
                                    <template x-if="errors.description">
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="errors.description[0]"></p>
                                    </template>
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
                                    <span x-show="!loading">Update Region</span>
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