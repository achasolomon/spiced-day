<?php

use App\Http\Controllers\Api\ChecklistController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/checklists/{checklist}/items', [ChecklistController::class, 'getChecklistItems']);
    Route::get('/checklists/by-type/{type}', [ChecklistController::class, 'getChecklistsByType']);
});