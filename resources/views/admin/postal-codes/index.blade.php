@extends('layouts.admin')

@section('title', 'Postal Codes Management')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Postal Codes</h3>
                        <button 
                            @click="$dispatch('open-create-postal-code-modal')"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                        >
                            Add New Postal Code
                        </button>
                    </div>

                    <!-- Search and Filters -->
                    <div class="mb-6">
                        <form method="GET" class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('search') }}"
                                    placeholder="Search postal codes..."
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            <div class="flex-1">
                                <select 
                                    name="region_id"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="">All Regions</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                            >
                                Search
                            </button>
                        </form>
                    </div>

                    <!-- Postal Codes Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Prefix</th>
                                    <th scope="col" class="px-6 py-3">Region</th>
                                    <th scope="col" class="px-6 py-3">Full Postal Codes</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($postalCodes as $postalCode)
                                    <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700">
                                        <td class="px-6 py-4 font-semibold">{{ $postalCode->prefix }}</td>
                                        <td class="px-6 py-4">{{ $postalCode->region ? $postalCode->region->name : 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @if(is_array($postalCode->full_postal_codes) && count($postalCode->full_postal_codes) > 0)
                                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ implode(', ', array_slice($postalCode->full_postal_codes, 0, 3)) }}
                                                    @if(count($postalCode->full_postal_codes) > 3)
                                                        <span class="text-gray-500">... (+{{ count($postalCode->full_postal_codes) - 3 }} more)</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-gray-400 italic">None</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <button 
                                                @click="$dispatch('open-show-postal-code-modal', { postalCodeId: {{ $postalCode->id }} })"
                                                class="text-blue-600 dark:text-blue-400 hover:underline mr-4"
                                            >
                                                View
                                            </button>
                                            <button 
                                                @click="$dispatch('open-edit-postal-code-modal', { postalCodeId: {{ $postalCode->id }} })"
                                                class="text-orange-600 dark:text-orange-400 hover:underline mr-4"
                                            >
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.postal-codes.destroy', $postalCode) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit"
                                                    class="text-red-600 dark:text-red-400 hover:underline"
                                                    onclick="return confirm('Are you sure you want to delete this postal code?')"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No postal codes found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $postalCodes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modals -->
    @include('components.postal-codes.create-modal')
    @include('components.postal-codes.edit-modal')
    @include('components.postal-codes.show-modal')

@endsection