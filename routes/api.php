<?php

use App\Http\Controllers\Api\ChecklistController;
use App\Http\Controllers\Api\DocumentDownloadController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/checklists/{checklist}/items', [ChecklistController::class, 'getChecklistItems']);
    Route::get('/checklists/by-type/{type}', [ChecklistController::class, 'getChecklistsByType']);
});

Route::middleware('signed')->prefix('external')->group(function () {
    Route::get('/documents/{document}/download', [DocumentDownloadController::class, 'download'])
        ->name('api.external.documents.download');
});