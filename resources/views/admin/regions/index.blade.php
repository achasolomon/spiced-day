@extends('layouts.admin')

@section('title', 'Regions Management')

@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Regions</h3>
                    <button 
                        @click="$dispatch('open-create-region-modal')"
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                    >
                        Add New Region
                    </button>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Search and Filters -->
                <div class="mb-6">
                    <form method="GET" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="Search regions..."
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                            >
                        </div>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                        >
                            Search
                        </button>
                    </form>
                </div>

                <!-- Regions Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Consultants</th>
                                <th scope="col" class="px-6 py-3">Postal Codes</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($regions as $region)
                                <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $region->name }}</td>
                                    <td class="px-6 py-4">{{ $region->consultants_count }}</td>
                                    <td class="px-6 py-4">{{ $region->postal_code_ranges_count ?? 0 }}</td>
                                    <td class="px-6 py-4">
                                        <button 
                                            @click="$dispatch('open-show-region-modal', { regionId: {{ $region->id }} })"
                                            class="text-blue-600 dark:text-blue-400 hover:underline mr-4"
                                        >
                                            View
                                        </button>
                                        <button 
                                            @click="$dispatch('open-edit-region-modal', { regionId: {{ $region->id }} })"
                                            class="text-orange-600 dark:text-orange-400 hover:underline mr-4"
                                        >
                                            Edit
                                        </button>
                                        <button 
                                            @click="deleteRegion({{ $region->id }}, '{{ $region->name }}')"
                                            class="text-red-600 dark:text-red-400 hover:underline"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No regions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $regions->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modals -->
    @include('components.regions.create-modal')
    @include('components.regions.edit-modal')
    @include('components.regions.show-modal')
</div>

<script>
    function deleteRegion(regionId, regionName) {
        if (confirm(`Are you sure you want to delete "${regionName}"?`)) {
            fetch(`/admin/regions/${regionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to delete region');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the region');
            });
        }
    }
</script>

@endsection