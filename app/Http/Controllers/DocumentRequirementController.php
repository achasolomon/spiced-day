<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequirement;
use App\Models\DocumentCategory;
use App\Enums\ApplicationStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentRequirementController extends Controller
{
    /**
     * Display a listing of document requirements.
     */
    public function index()
    {
        $documentRequirements = DocumentRequirement::with('documentCategory')
            ->orderBy('sort_order')
            ->paginate(15);
        
        return view('admin.document-requirements.index', compact('documentRequirements'));
    }
    /**
     * Show the form for creating a new document requirement.
     */
  public function create()
    {
        $stages = ApplicationStage::cases();
        $acceptedFormats = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $categories = DocumentCategory::active()->ordered()->get();
        
        return view('admin.document-requirements.create', compact('stages', 'acceptedFormats', 'categories'));
    }
    /**
     * Store a newly created document requirement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'instructions' => 'nullable|string|max:2000',
            'document_category_id' => 'nullable|exists:document_categories,id',
            'stage' => 'required|in:' . implode(',', array_column(ApplicationStage::cases(), 'value')),
            'is_required' => 'boolean',
            'is_conditional' => 'boolean',
            'conditions' => 'nullable|json',
            'is_active' => 'boolean',
            'accepted_formats' => 'required|array|min:1',
            'accepted_formats.*' => 'in:pdf,jpg,jpeg,png,doc,docx',
            'max_file_size' => 'nullable|integer|min:1024|max:20480',
            'max_files' => 'required|integer|min:1|max:10',
            'has_expiry' => 'boolean',
            'validity_period' => 'nullable|integer|min:1|required_if:has_expiry,true',
            'requires_annual_renewal' => 'boolean',
            'requires_review' => 'boolean',
            'review_priority' => 'required|integer|min:1|max:10',
            'review_criteria' => 'nullable|json',
            'rejection_reasons' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
        ]);

        try {
            $validated['slug'] = Str::slug($validated['name']);
            $validated['accepted_formats'] = json_encode($validated['accepted_formats']);
           
            if (empty($request->document_category_id)) {
                $validated['document_category_id'] = null;
            }
                    
            DocumentRequirement::create($validated);
            
            return redirect()->route('admin.document-requirements.index')
                ->with('success', 'Document requirement created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create document requirement', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);
            return back()->withInput()->with('error', 'Failed to create document requirement.');
        }
    }

    /**
     * Show the form for editing a document requirement.
     */
    public function edit(DocumentRequirement $documentRequirement)
    {
        $stages = ApplicationStage::cases();
        $acceptedFormats = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        
        return view('admin.document-requirements.edit', compact('documentRequirement', 'stages', 'acceptedFormats'));
    }

    /**
     * Update the specified document requirement.
     */
    public function update(Request $request, DocumentRequirement $documentRequirement)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'stage' => 'required|in:' . implode(',', array_column(ApplicationStage::cases(), 'value')),
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'accepted_formats' => 'required|array|min:1',
            'accepted_formats.*' => 'in:pdf,jpg,jpeg,png,doc,docx',
            'max_file_size' => 'required|integer|min:1024|max:20480',
            'sort_order' => 'required|integer|min:0',
        ]);

        try {
            $validated['slug'] = Str::slug($validated['name']);
            $validated['accepted_formats'] = json_encode($validated['accepted_formats']);
            
            $documentRequirement->update($validated);
            
            return redirect()->route('admin.document-requirements.index')
                ->with('success', 'Document requirement updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update document requirement', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);
            return back()->withInput()->with('error', 'Failed to update document requirement.');
        }
    }

    /**
     * Remove the specified document requirement.
     */
    public function destroy(DocumentRequirement $documentRequirement)
    {
        try {
            $documentRequirement->delete();
            
            return redirect()->route('admin.document-requirements.index')
                ->with('success', 'Document requirement deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete document requirement', [
                'error' => $e->getMessage(),
                'id' => $documentRequirement->id,
            ]);
            return back()->with('error', 'Failed to delete document requirement.');
        }
    }
}
?>