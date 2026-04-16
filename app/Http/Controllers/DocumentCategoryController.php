<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::withCount('documents')
            ->ordered()
            ->paginate(20);
        
        return view('admin.document-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.document-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7', // hex color
            'requires_expiry' => 'boolean',
            'default_validity_days' => 'nullable|integer|min:1|required_if:requires_expiry,true',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        DocumentCategory::create($validated);
        
        return redirect()
            ->route('admin.document-categories.index')
            ->with('success', 'Document category created successfully.');
    }

    public function edit(DocumentCategory $documentCategory)
    {
        return view('admin.document-categories.edit', compact('documentCategory'));
    }

    public function update(Request $request, DocumentCategory $documentCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'requires_expiry' => 'boolean',
            'default_validity_days' => 'nullable|integer|min:1|required_if:requires_expiry,true',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $documentCategory->update($validated);
        
        return redirect()
            ->route('admin.document-categories.index')
            ->with('success', 'Document category updated successfully.');
    }

    public function destroy(DocumentCategory $documentCategory)
    {
        // Check if category has documents
        if ($documentCategory->documents()->exists()) {
            return back()->with('error', 'Cannot delete category with existing documents.');
        }
        
        $documentCategory->delete();
        
        return redirect()
            ->route('admin.document-categories.index')
            ->with('success', 'Document category deleted successfully.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(DocumentCategory $documentCategory)
    {
        $documentCategory->update([
            'is_active' => !$documentCategory->is_active
        ]);

        return back()->with('success', 'Category status updated.');
    }
}

class DocumentTypeController extends Controller
{
    public function index()
    {
        $types = DocumentType::withCount('documents')
            ->ordered()
            ->paginate(20);
        
        return view('admin.document-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.document-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        DocumentType::create($validated);
        
        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Document type created successfully.');
    }

    public function edit(DocumentType $documentType)
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $documentType->update($validated);
        
        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Document type updated successfully.');
    }

    public function destroy(DocumentType $documentType)
    {
        if ($documentType->documents()->exists()) {
            return back()->with('error', 'Cannot delete type with existing documents.');
        }
        
        $documentType->delete();
        
        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Document type deleted successfully.');
    }
}