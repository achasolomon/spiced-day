<?php

use App\Http\Controllers\Api\ChecklistController;
use App\Http\Controllers\Api\DocumentDownloadController;
use App\Http\Controllers\Api\InternalApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/checklists/{checklist}/items', [ChecklistController::class, 'getChecklistItems']);
    Route::get('/checklists/by-type/{type}', [ChecklistController::class, 'getChecklistsByType']);
});

Route::middleware('signed')->prefix('external')->group(function () {
    Route::get('/documents/{document}/download', [DocumentDownloadController::class, 'download'])
        ->name('api.external.documents.download');
});

Route::prefix('v1/internal')->middleware(['hmac', 'throttle:60,1'])->group(function () {
    Route::post('/status',          [InternalApiController::class, 'updateStatus']);
    Route::post('/compliance',      [InternalApiController::class, 'updateCompliance']);
    Route::put('/educator-profile', [InternalApiController::class, 'updateEducatorProfile']);
    Route::post('/documents',       [InternalApiController::class, 'updateDocuments']);
});