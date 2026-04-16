<?php

namespace App\Http\Controllers;

use App\Models\PostalCodeRange;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostalCodeController extends Controller
{
    /**
     * Display a listing of the postal code ranges.
     */
   /**
 * Display a listing of the postal code ranges.
 */
public function index(Request $request)
{
    try {
        $query = PostalCodeRange::query();

        // Filter by search (prefix)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('prefix', 'like', "%{$search}%");
        }

        // Filter by region
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->input('region_id'));
        }

        // Get paginated results with region loaded
        $postalCodes = $query->with('region')
            ->orderBy('region_id')
            ->orderBy('prefix')
            ->paginate(20);

        // Get all regions for the filter dropdown
        $regions = Region::orderBy('name')->get();

        return view('admin.postal-codes.index', compact('postalCodes', 'regions'));
    } catch (\Exception $e) {
        Log::error('Error loading postal codes: ' . $e->getMessage());
        return back()->with('error', 'Failed to load postal codes.');
    }
}

    /**
     * Store a newly created postal code range in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required|exists:regions,id',
            'prefix' => 'required|string|max:10|alpha_num',
            'full_postal_codes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Check for duplicate prefix in the same region
            $exists = PostalCodeRange::where('region_id', $request->region_id)
                ->where('prefix', $request->prefix)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This postal code prefix already exists for the selected region.'
                ], 422);
            }

            // Process full postal codes if provided
            $fullPostalCodes = null;
            if ($request->filled('full_postal_codes')) {
                $codes = array_map('trim', explode(',', $request->full_postal_codes));
                $codes = array_filter($codes); // Remove empty values
                $fullPostalCodes = !empty($codes) ? $codes : null;
            }

            $postalCode = PostalCodeRange::create([
                'region_id' => $request->region_id,
                'prefix' => strtoupper($request->prefix),
                'full_postal_codes' => $fullPostalCodes,
            ]);

            $postalCode->load('region');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Postal code range created successfully',
                'postalCode' => $postalCode
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating postal code: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create postal code range. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified postal code range data.
     */
    public function showData(PostalCodeRange $postalCode)
    {
        try {
            $postalCode->load('region');

            // Format full_postal_codes for display
            $formattedCodes = '';
            if (is_array($postalCode->full_postal_codes)) {
                $formattedCodes = implode(', ', $postalCode->full_postal_codes);
            }

            return response()->json([
                'success' => true,
                'postalCode' => [
                    'id' => $postalCode->id,
                    'region_id' => $postalCode->region_id,
                    'prefix' => $postalCode->prefix,
                    'full_postal_codes' => $formattedCodes,
                    'region' => $postalCode->region,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching postal code data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch postal code data.'
            ], 500);
        }
    }

    /**
     * Update the specified postal code range in storage.
     */
    public function update(Request $request, PostalCodeRange $postalCode)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required|exists:regions,id',
            'prefix' => 'required|string|max:10|alpha_num',
            'full_postal_codes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Check for duplicate prefix in the same region (excluding current record)
            $exists = PostalCodeRange::where('region_id', $request->region_id)
                ->where('prefix', $request->prefix)
                ->where('id', '!=', $postalCode->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This postal code prefix already exists for the selected region.'
                ], 422);
            }

            // Process full postal codes if provided
            $fullPostalCodes = null;
            if ($request->filled('full_postal_codes')) {
                $codes = array_map('trim', explode(',', $request->full_postal_codes));
                $codes = array_filter($codes); // Remove empty values
                $fullPostalCodes = !empty($codes) ? $codes : null;
            }

            $postalCode->update([
                'region_id' => $request->region_id,
                'prefix' => strtoupper($request->prefix),
                'full_postal_codes' => $fullPostalCodes,
            ]);

            $postalCode->load('region');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Postal code range updated successfully',
                'postalCode' => $postalCode
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating postal code: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update postal code range. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified postal code range from storage.
     */
    public function destroy(PostalCodeRange $postalCode)
    {
        try {
            DB::beginTransaction();

            $region = $postalCode->region->name;
            $prefix = $postalCode->prefix;

            $postalCode->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Postal code range '{$prefix}' from region '{$region}' deleted successfully"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting postal code: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete postal code range. Please try again.'
            ], 500);
        }
    }

    /**
     * Get all postal codes for a specific region (for AJAX requests)
     */
    public function getByRegion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required|exists:regions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $postalCodes = PostalCodeRange::where('region_id', $request->region_id)
                ->orderBy('prefix')
                ->get();

            return response()->json([
                'success' => true,
                'postalCodes' => $postalCodes
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching postal codes by region: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch postal codes.'
            ], 500);
        }
    }
}