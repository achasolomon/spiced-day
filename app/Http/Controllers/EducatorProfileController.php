<?php

namespace App\Http\Controllers;

use App\Models\EducatorProfile;
use App\Models\EducatorProfileItem;
use App\Models\Application;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EducatorProfileController extends Controller
{
    /**
     * Display the educator's profile
     */
   public function index()
{
    $user = auth()->user();
    $profile = $user->getOrCreateEducatorProfile();
    $profile->load(['activeItems', 'expiringItems', 'expiredItems']);
    
    // Get application documents
    $applicationDocuments = $profile->getDocumentsByApplication();
    
    return view('applicant.profile.index', compact('profile', 'applicationDocuments'));
}
    /**
     * Show the form for editing the profile
     */
    // public function edit()
    // {
    //     $user = auth()->user();
    //     $profile = $user->getOrCreateEducatorProfile();
        
    //     return view('applicant.profile.edit', compact('profile'));
    // }
        public function edit()
    {
        $user = auth()->user();
        $profile = $user->getOrCreateEducatorProfile();
        
        // Get application data to pre-fill fields
        $application = $user->applications()->latest()->first();
        
        return view('applicant.profile.edit', compact('profile', 'application'));
    }

    /**
     * Update the educator profile
     */
    public function update(Request $request)
    {
               $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_hire' => 'nullable|date',
            'sin_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'marital_status' => 'nullable|string|max:255',
            'religious_beliefs' => 'nullable|string|max:255',
            'ethnicity_nationality' => 'nullable|string|max:255',
            'allergies' => 'nullable|string|max:1000',
            'dietary_restrictions' => 'nullable|string|max:1000',
            'medical_conditions' => 'nullable|string|max:1000',
            'activity_restrictions' => 'nullable|string|max:1000',
            'emergency_contact_1_first_name' => 'nullable|string|max:255',
            'emergency_contact_1_last_name' => 'nullable|string|max:255',
            'emergency_contact_1_relationship' => 'nullable|string|max:255',
            'emergency_contact_1_phone' => 'nullable|string|max:20',
            'emergency_contact_1_address_line_1' => 'nullable|string|max:255',
            'emergency_contact_1_city' => 'nullable|string|max:255',
            'emergency_contact_1_province' => 'nullable|string|max:255',
            'emergency_contact_1_postal_code' => 'nullable|string|max:10',
            'emergency_contact_2_first_name' => 'nullable|string|max:255',
            'emergency_contact_2_last_name' => 'nullable|string|max:255',
            'emergency_contact_2_relationship' => 'nullable|string|max:255',
            'emergency_contact_2_phone' => 'nullable|string|max:20',
            'emergency_contact_2_address_line_1' => 'nullable|string|max:255',
            'emergency_contact_2_city' => 'nullable|string|max:255',
            'emergency_contact_2_province' => 'nullable|string|max:255',
            'emergency_contact_2_postal_code' => 'nullable|string|max:10',
            'professional_bio' => 'nullable|string|max:2000',
            'operating_hours_start' => 'nullable|date_format:H:i',
            'operating_hours_end' => 'nullable|date_format:H:i',
            'current_capacity' => 'nullable|integer|min:0',
            'maximum_capacity' => 'nullable|integer|min:0',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:255',
            'professional_goals' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $profile = $user->getOrCreateEducatorProfile();

            // Handle profile photo upload - SAVE TO PRIVATE DISK
            if ($request->hasFile('profile_photo')) {
                // Delete old photo from private disk
                if ($profile->profile_photo && Storage::disk('private')->exists($profile->profile_photo)) {
                    Storage::disk('private')->delete($profile->profile_photo);
                }

                // Store in private disk
                $path = $request->file('profile_photo')->store('profile-photos', 'private');
                $validated['profile_photo'] = $path;
            }

            $profile->update($validated);
            $profile->update(['last_updated_at' => now()]);
            $profile->checkCompleteness();

            DB::commit();

            return redirect()->route('applicant.profile.index')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update educator profile', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Display the profile photo (for authenticated viewing)
     */
    public function showProfilePhoto(EducatorProfile $profile)
    {
        // Check ownership or authorization
        if ($profile->user_id != auth()->id() && !auth()->user()->isAdmin() && !auth()->user()->isConsultant()) {
            abort(403, 'Unauthorized access');
        }

        if (!$profile->profile_photo || !Storage::disk('private')->exists($profile->profile_photo)) {
            abort(404, 'Profile photo not found');
        }

        return Storage::disk('private')->response($profile->profile_photo);
    }

    /**
     * Add a new profile item
     */
    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:document,text,date,boolean',
            'value' => 'nullable|string|max:2000',
            'file' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png', // 5MB max
            'date_value' => 'nullable|date',
            'boolean_value' => 'nullable|boolean',
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $profile = $user->getOrCreateEducatorProfile();

            // Get the highest sort order
            $maxSortOrder = $profile->items()->max('sort_order') ?? 0;

            $itemData = [
                'educator_profile_id' => $profile->id,
                'title' => $validated['title'],
                'type' => $validated['type'],
                'notes' => $validated['notes'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'sort_order' => $maxSortOrder + 1,
                'is_active' => true,
            ];

            // Handle based on type
            switch ($validated['type']) {
                case 'document':
                    if ($request->hasFile('file')) {
                        $file = $request->file('file');
                        $path = $file->store('educator-documents', 'private');
                        
                        $itemData['file_path'] = $path;
                        $itemData['file_name'] = $file->getClientOriginalName();
                    } else {
                        return back()->with('error', 'Document file is required for document type.');
                    }
                    break;

                case 'text':
                    $itemData['value'] = $validated['value'];
                    break;

                case 'date':
                    $itemData['date_value'] = $validated['date_value'];
                    break;

                case 'boolean':
                    $itemData['boolean_value'] = $validated['boolean_value'] ?? false;
                    break;
            }

            EducatorProfileItem::create($itemData);
            
            $profile->update(['last_updated_at' => now()]);
            $profile->checkCompleteness();

            DB::commit();

            Log::info('Educator profile item added', [
                'user_id' => auth()->id(),
                'profile_id' => $profile->id,
                'item_title' => $validated['title']
            ]);

            return back()->with('success', 'Item added successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to add profile item', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to add item. Please try again.');
        }
    }

    /**
     * Update a profile item
     */
    public function updateItem(Request $request, EducatorProfileItem $item)
    {
        // Check ownership
        if ($item->educatorProfile->user_id != auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:document,text,date,boolean',
            'value' => 'nullable|string|max:2000',
            'file' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
            'date_value' => 'nullable|date',
            'boolean_value' => 'nullable|boolean',
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'title' => $validated['title'],
                'type' => $validated['type'],
                'notes' => $validated['notes'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
            ];

            // Handle based on type
            switch ($validated['type']) {
                case 'document':
                    if ($request->hasFile('file')) {
                        // Delete old file from private disk
                        if ($item->file_path && Storage::disk('private')->exists($item->file_path)) {
                            Storage::disk('private')->delete($item->file_path);
                        }

                        $file = $request->file('file');
                        $path = $file->store('educator-documents', 'private');
                        
                        $updateData['file_path'] = $path;
                        $updateData['file_name'] = $file->getClientOriginalName();
                    }
                    break;

                case 'text':
                    $updateData['value'] = $validated['value'];
                    // Clear other fields
                    $updateData['file_path'] = null;
                    $updateData['file_name'] = null;
                    $updateData['date_value'] = null;
                    $updateData['boolean_value'] = null;
                    break;

                case 'date':
                    $updateData['date_value'] = $validated['date_value'];
                    // Clear other fields
                    $updateData['value'] = null;
                    $updateData['file_path'] = null;
                    $updateData['file_name'] = null;
                    $updateData['boolean_value'] = null;
                    break;

                case 'boolean':
                    $updateData['boolean_value'] = $validated['boolean_value'] ?? false;
                    // Clear other fields
                    $updateData['value'] = null;
                    $updateData['file_path'] = null;
                    $updateData['file_name'] = null;
                    $updateData['date_value'] = null;
                    break;
            }

            $item->update($updateData);
            $item->educatorProfile->update(['last_updated_at' => now()]);

            DB::commit();

            Log::info('Educator profile item updated', [
                'user_id' => auth()->id(),
                'item_id' => $item->id
            ]);

            return back()->with('success', 'Item updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update profile item', [
                'item_id' => $item->id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update item. Please try again.');
        }
    }

    /**
     * Delete a profile item
     */
    public function deleteItem(EducatorProfileItem $item)
    {
        // Check ownership
        if ($item->educatorProfile->user_id != auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        DB::beginTransaction();
        try {
            $profile = $item->educatorProfile;
            
            // Delete file from private disk if exists
            if ($item->file_path && Storage::disk('private')->exists($item->file_path)) {
                Storage::disk('private')->delete($item->file_path);
            }

            $item->delete();
            
            $profile->update(['last_updated_at' => now()]);
            $profile->checkCompleteness();

            DB::commit();

            Log::info('Educator profile item deleted', [
                'user_id' => auth()->id(),
                'item_id' => $item->id
            ]);

            return back()->with('success', 'Item deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete profile item', [
                'item_id' => $item->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete item. Please try again.');
        }
    }

    /**
     * Download a profile item document
     */
    public function downloadItem(EducatorProfileItem $item)
    {
        // Check ownership
        if ($item->educatorProfile->user_id != auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($item->type !== 'document' || !$item->file_path) {
            abort(404, 'Document not found');
        }

        if (!Storage::disk('private')->exists($item->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download($item->file_path, $item->file_name);
    }

    /**
     * View a profile item document
     */
    public function viewItem(EducatorProfileItem $item)
    {
        // Check ownership
        if ($item->educatorProfile->user_id != auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($item->type !== 'document' || !$item->file_path) {
            abort(404, 'Document not found');
        }

        if (!Storage::disk('private')->exists($item->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->response($item->file_path);
    }

    /**
     * Reorder profile items
     */
    public function reorderItems(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:educator_profile_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $profile = $user->educatorProfile;

            if (!$profile) {
                return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
            }

            foreach ($validated['items'] as $itemData) {
                $item = EducatorProfileItem::find($itemData['id']);
                
                // Check ownership
                if ($item->educator_profile_id != $profile->id) {
                    continue;
                }

                $item->update(['sort_order' => $itemData['sort_order']]);
            }

            $profile->update(['last_updated_at' => now()]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Items reordered successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to reorder profile items', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to reorder items'], 500);
        }
    }
    /**
 * Consultant view of educator profile
 */
public function consultantView(EducatorProfile $profile)
{
logger()->info('consultantView reached for profile ID: ' . $profile->id);

    $user = auth()->user();
    
    if (!$user->isConsultant() && !$user->isAdmin()) {
        abort(403, 'Unauthorized access');
    }
    
    // For consultants, check if they have any applications assigned for this educator
    if ($user->isConsultant()) {
        $hasAccess = Application::where('user_id', $profile->user_id)
            ->where('consultant_id', $user->id)
            ->exists();
            
        if (!$hasAccess) {
            abort(403, 'You do not have access to this educator profile.');
        }
    }
    
    $profile->load(['activeItems', 'expiringItems', 'expiredItems']);
    
    // Get application documents grouped by application
    $applicationDocuments = $profile->getDocumentsByApplication();
    
    // Get all applications for this educator (for context)
    $applications = Application::where('user_id', $profile->user_id)
        ->when($user->isConsultant(), function($query) use ($user) {
            $query->where('consultant_id', $user->id);
        })
        ->with('consultant')
        ->latest()
        ->get();
    
    return view('consultant.educators.view', compact('profile', 'applicationDocuments', 'applications'));
}

/**
 * View application document from profile
 */
    public function viewApplicationDocument(EducatorProfile $profile, Document $document)
    {
        $user = auth()->user();
        
        // Check authorization
        if ($user->isApplicant()) {
            if ($profile->user_id != $user->id) {
                abort(403, 'Unauthorized access');
            }
        } elseif ($user->isConsultant()) {
            // Check if consultant has access to the application
            if ($document->application->consultant_id != $user->id) {
                abort(403, 'You do not have access to this document.');
            }
        } elseif (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->response($document->file_path);
    }

    /**
     * Download application document from profile
     */
    public function downloadApplicationDocument(EducatorProfile $profile, Document $document)
    {
        $user = auth()->user();
        
        // Check authorization
        if ($user->isApplicant()) {
            if ($profile->user_id != $user->id) {
                abort(403, 'Unauthorized access');
            }
        } elseif ($user->isConsultant()) {
            if ($document->application->consultant_id != $user->id) {
                abort(403, 'You do not have access to this document.');
            }
        } elseif (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $document->increment('download_count');
        
        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_filename
        );
    }
}