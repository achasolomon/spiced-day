<!-- resources/views/components/regions/create-modal.blade.php -->
<div 
    x-data="{
        show: false,
        loading: false,
        formData: {
            name: '',
            description: ''
        },
        errors: {},
        async submitForm() {
            this.loading = true;
            this.errors = {};
            
            try {
                const response = await fetch('{{ route('admin.regions.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
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
                console.error('Error creating region:', error);
                alert('An error occurred. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        open() {
            this.show = true;
            this.formData = { name: '', description: '' };
            this.errors = {};
        },
        close() {
            this.show = false;
        }
    }"
    @open-create-region-modal.window="open()"
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
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Region</h3>
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
                                placeholder="e.g., North Region"
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
                                placeholder="Brief description of the region..."
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
                            <span x-show="!loading">Create Region</span>
                            <span x-show="loading">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>