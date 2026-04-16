<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionChecklist;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function getChecklistItems(InspectionChecklist $checklist)
    {
        $items = $checklist->items()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'checklist' => $checklist,
            'items' => $items
        ]);
    }

    public function getChecklistsByType($type)
    {
        $checklists = InspectionChecklist::where('inspection_type', $type)
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('version', 'desc')
            ->get();

        return response()->json($checklists);
    }
}