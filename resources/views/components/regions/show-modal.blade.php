<!-- resources/views/components/regions/show-modal.blade.php -->
<div 
    x-data="{
        show: false,
        loading: false,
        region: null,
        async fetchRegion(regionId) {
            this.loading = true;
            try {
                const response = await fetch(`/admin/regions/${regionId}/data`);
                const data = await response.json();
                
                console.log('Show modal - Fetched data:', data); // Debug log
                
                if (data.success && data.region) {
                    this.region = data.region;
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
        open(regionId) {
            this.show = true;
            this.fetchRegion(regionId);
        },
        close() {
            this.show = false;
            this.region = null;
        }
    }"
    @open-show-region-modal.window="open($event.detail.regionId)"
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
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading region details...</p>
                    </div>
                </template>

                <!-- Region Details -->
                <template x-if="!loading && region">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="region.name"></h3>
                            <button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Details</h4>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Name</p>
                                        <p class="text-base font-medium text-gray-900 dark:text-white" x-text="region.name"></p>
                                    </div>
                                    
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Description</p>
                                        <p class="text-base font-medium text-gray-900 dark:text-white" x-text="region.description || 'No description provided'"></p>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Consultants</p>
                                            <p class="text-2xl font-bold text-orange-600" x-text="region.consultants?.length || 0"></p>
                                        </div>
                                        
                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Postal Codes</p>
                                            <p class="text-2xl font-bold text-orange-600" x-text="region.postal_code_ranges?.length || 0"></p>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Created</p>
                                        <p class="text-base font-medium text-gray-900 dark:text-white" x-text="new Date(region.created_at).toLocaleDateString()"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button 
                                @click="close(); setTimeout(() => $dispatch('open-edit-region-modal', { regionId: region.id }), 300)"
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition"
                            >
                                Edit Region
                            </button>
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