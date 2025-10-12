<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{
    /**
     * Display a listing of the regions.
     */
    public function index()
    {
        $regions = Region::withCount('postalCodeRanges', 'consultants')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.regions.index', compact('regions'));
    }

    /**
     * Store a newly created region in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:regions,name',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $region = Region::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Region created successfully',
            'region' => $region
        ], 201);
    }

    /**
     * Display the specified region data.
     */
    public function showData(Region $region)
    {
        $region->load(['postalCodeRanges', 'consultants']);

        return response()->json([
            'success' => true,
            'region' => $region
        ]);
    }

/**
 * Get all regions as JSON (for AJAX requests in modals)
 */
public function getAllRegions()
{
    $regions = Region::orderBy('name')->get(['id', 'name']);
    
    return response()->json($regions);
}

    /**
     * Update the specified region in storage.
     */
    public function update(Request $request, Region $region)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:regions,name,' . $region->id,
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $region->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Region updated successfully',
            'region' => $region
        ]);
    }

    /**
     * Remove the specified region from storage.
     */
    public function destroy(Region $region)
    {
        // Check if region has postal codes or consultants
        $postalCodesCount = $region->postalCodeRanges()->count();
        $consultantsCount = $region->consultants()->count();

        if ($postalCodesCount > 0 || $consultantsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete region. It has ' . $postalCodesCount . ' postal code(s) and ' . $consultantsCount . ' consultant(s) assigned.'
            ], 422);
        }

        $region->delete();

        return response()->json([
            'success' => true,
            'message' => 'Region deleted successfully'
        ]);
    }
}