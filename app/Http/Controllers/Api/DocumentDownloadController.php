<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentDownloadController extends Controller
{
    public function download(Document $document)
    {
        if (!$document->file_path || !Storage::exists($document->file_path)) {
            Log::warning('External document download failed — file not found', [
                'document_id' => $document->id,
                'path' => $document->file_path,
            ]);
            return response()->json(['error' => 'FILE_NOT_FOUND', 'message' => 'The requested file was not found.'], 404);
        }

        $document->increment('download_count');

        Log::info('External document downloaded', [
            'document_id' => $document->id,
            'application_id' => $document->application_id,
            'file_name' => $document->original_filename,
        ]);

        return Storage::download($document->file_path, $document->original_filename);
    }
}
