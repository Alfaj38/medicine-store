<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyDocument;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        $file = $request->file('file');
        $path = $file->store('company_documents', 'public');

        CompanyDocument::create([
            'company_id' => auth()->user()->company_id,
            'document_name' => $file->getClientOriginalName(),
            'document_type' => $validated['document_type'],
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(CompanyDocument $document)
    {
        if ($document->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
