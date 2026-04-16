@extends('layouts.consultant')

@section('title', 'Edit Inspection')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('consultant.inspections.show', $inspection) }}"
           class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            ←
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Inspection</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                {{ $checklist->name }} · {{ ucfirst($inspection->type) }}
            </p>
        </div>
    </div>

    <form action="{{ route('consultant.inspections.update', $inspection) }}"
          method="POST"
          enctype="multipart/form-data"
          x-data="inspectionForm()">

        @csrf
        @method('PUT')

        <!-- 🔒 Correction Reason -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
            <label class="block text-sm font-semibold text-yellow-800 dark:text-yellow-300 mb-2">
                Reason for Editing Inspection (Required)
            </label>
            <textarea name="edit_reason" required rows="3"
                      placeholder="Explain what was corrected and why…"
                      class="w-full px-4 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:bg-gray-800 dark:text-white"></textarea>
        </div>

        <!-- Inspection Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-6">
            <h2 class="text-xl font-bold mb-6">Inspection Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium">Inspection Type</label>
                    <input type="text" readonly
                           value="{{ ucwords(str_replace('_', ' ', $inspection->type)) }}"
                           class="w-full px-4 py-2 bg-gray-100 rounded-lg">
                </div>

                <div>
                    <label class="text-sm font-medium">Inspection Date</label>
                    <input type="datetime-local" name="conducted_at"
                           value="{{ optional($inspection->conducted_at)->format('Y-m-d\TH:i') ?? '' }}"
                           class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
        </div>

        <!-- Inspection Checklist -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border p-6 mt-6">

            @foreach($checklistItems as $category => $items)
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4">
                    {{ ucwords(str_replace('_', ' ', $category)) }}
                </h3>

                @foreach($items as $item)
                @php
                    $checklistResults = is_array($inspection->checklist_results) 
                        ? $inspection->checklist_results 
                        : json_decode($inspection->checklist_results, true);
                    $result = $checklistResults[$item->code] ?? [];
                @endphp

                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg mb-4">
                    <h4 class="font-semibold">{{ $item->title }}</h4>

                    <!-- Yes / No / NA -->
                    @if(in_array($item->response_type, ['yes_no', 'yes_no_na']))
                    <div class="flex gap-4 mt-3">
                        @foreach(['pass' => 'Pass', 'fail' => 'Fail', 'n/a' => 'N/A'] as $val => $label)
                        <label class="flex items-center gap-2">
                            <input type="radio"
                                   name="checklist_results[{{ $item->code }}][status]"
                                   value="{{ $val }}"
                                   @checked(($result['status'] ?? null) === $val)>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                    @endif

                    <!-- Rating -->
                    @if($item->response_type === 'rating_scale')
                    <div class="flex gap-2 mt-3">
                        @for($i = 1; $i <= 5; $i++)
                        <label>
                            <input type="radio"
                                   name="checklist_results[{{ $item->code }}][status]"
                                   value="{{ $i }}"
                                   @checked(($result['status'] ?? null) == $i)>
                            {{ $i }}
                        </label>
                        @endfor
                    </div>
                    @endif

                    <!-- Notes -->
                    <textarea name="checklist_results[{{ $item->code }}][notes]"
                              rows="2"
                              class="mt-3 w-full border rounded-lg"
                              placeholder="Notes…">{{ $result['notes'] ?? '' }}</textarea>

                    <input type="hidden" name="checklist_results[{{ $item->code }}][item_id]"
                           value="{{ $item->id }}">
                </div>
                @endforeach
            </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border p-6 mt-6">
            <label class="font-medium">Overall Observations</label>
            <textarea name="observations" rows="4"
                      class="w-full mt-2 border rounded-lg">{{ $inspection->observations }}</textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-between mt-6">
            <a href="{{ route('consultant.inspections.show', $inspection) }}"
               class="px-6 py-3 bg-gray-200 rounded-lg">
                Cancel
            </a>

            <button type="submit"
                    class="px-6 py-3 bg-orange-600 text-white rounded-lg">
                Save Corrections
            </button>
        </div>

    </form>
</div>
@endsection
