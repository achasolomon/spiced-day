<div x-data="addItemModal()" 
     x-show="open" 
     @open-add-item-modal.window="open = true"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Background overlay -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
         @click="close()"></div>

    <!-- Modal panel -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Item</h3>
                    <button @click="close()" 
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="{{ route('applicant.profile.items.add') }}" enctype="multipart/form-data" @submit="submitForm">
                @csrf
                
                <div class="px-6 py-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    
                    <!-- Title -->
                    <div>
                        <label for="add_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="add_title" 
                               name="title" 
                               x-model="formData.title"
                               required
                               placeholder="e.g., CPR/First Aid Certification"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Give your item a clear, descriptive name</p>
                    </div>

                    <!-- Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="formData.type === 'document' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30' : 'border-gray-300 dark:border-gray-600 hover:border-purple-300'">
                                <input type="radio" 
                                       name="type" 
                                       value="document" 
                                       x-model="formData.type"
                                       class="sr-only">
                                <svg class="w-8 h-8 mb-2" :class="formData.type === 'document' ? 'text-purple-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium" :class="formData.type === 'document' ? 'text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300'">Document</span>
                            </label>

                            <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="formData.type === 'text' ? 'border-green-500 bg-green-50 dark:bg-green-900/30' : 'border-gray-300 dark:border-gray-600 hover:border-green-300'">
                                <input type="radio" 
                                       name="type" 
                                       value="text" 
                                       x-model="formData.type"
                                       class="sr-only">
                                <svg class="w-8 h-8 mb-2" :class="formData.type === 'text' ? 'text-green-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium" :class="formData.type === 'text' ? 'text-green-700 dark:text-green-300' : 'text-gray-700 dark:text-gray-300'">Text</span>
                            </label>

                            <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="formData.type === 'date' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30' : 'border-gray-300 dark:border-gray-600 hover:border-yellow-300'">
                                <input type="radio" 
                                       name="type" 
                                       value="date" 
                                       x-model="formData.type"
                                       class="sr-only">
                                <svg class="w-8 h-8 mb-2" :class="formData.type === 'date' ? 'text-yellow-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium" :class="formData.type === 'date' ? 'text-yellow-700 dark:text-yellow-300' : 'text-gray-700 dark:text-gray-300'">Date</span>
                            </label>

                            <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="formData.type === 'boolean' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-300'">
                                <input type="radio" 
                                       name="type" 
                                       value="boolean" 
                                       x-model="formData.type"
                                       class="sr-only">
                                <svg class="w-8 h-8 mb-2" :class="formData.type === 'boolean' ? 'text-indigo-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium" :class="formData.type === 'boolean' ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'">Yes/No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dynamic Fields Based on Type -->
                    
                    <!-- Document Upload -->
                    <div x-show="formData.type === 'document'">
                        <label for="add_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Upload Document <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               id="add_file" 
                               name="file" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               :required="formData.type === 'document'"
                               class="block w-full text-sm text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-purple-50 file:text-purple-700
                                      hover:file:bg-purple-100
                                      dark:file:bg-purple-900/30 dark:file:text-purple-300
                                      dark:hover:file:bg-purple-900/50">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PDF, DOC, DOCX, JPG, PNG (Max 5MB)</p>
                    </div>

                    <!-- Text Input -->
                    <div x-show="formData.type === 'text'">
                        <label for="add_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Text Value <span class="text-red-500">*</span>
                        </label>
                        <textarea id="add_value" 
                                  name="value" 
                                  rows="4"
                                  :required="formData.type === 'text'"
                                  placeholder="Enter details or information..."
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <!-- Date Input -->
                    <div x-show="formData.type === 'date'">
                        <label for="add_date_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="add_date_value" 
                               name="date_value" 
                               :required="formData.type === 'date'"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Boolean Input -->
                    <div x-show="formData.type === 'boolean'">
                        <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
                            <input type="checkbox" 
                                   name="boolean_value" 
                                   value="1"
                                   class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mark as Yes/True</span>
                        </label>
                    </div>

                    <!-- Expiry Date (Optional) -->
                    <div>
                        <label for="add_expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Expiry Date (Optional)
                        </label>
                        <input type="date" 
                               id="add_expiry_date" 
                               name="expiry_date" 
                               :min="new Date().toISOString().split('T')[0]"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">For certifications that expire</p>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="add_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="add_notes" 
                                  name="notes" 
                                  rows="3"
                                  placeholder="Additional information or context..."
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                    <button type="button" 
                            @click="close()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                        Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addItemModal() {
    return {
        open: false,
        formData: {
            title: '',
            type: 'document',
        },
        
        close() {
            this.open = false;
            this.resetForm();
        },
        
        resetForm() {
            this.formData = {
                title: '',
                type: 'document',
            };
        },
        
        submitForm(event) {
            // Form will submit normally
            // You can add client-side validation here if needed
        }
    };
}
</script>

<style>
[x-cloak] {
    display: none !important;
}
</style>