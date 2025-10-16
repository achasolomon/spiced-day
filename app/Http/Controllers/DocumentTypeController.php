<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DocumentType;

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