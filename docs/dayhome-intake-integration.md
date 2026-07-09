# Dayhome Intake Integration — Internal Implementation Plan

## Overview

The SPICE'd Application Portal pushes approved/active dayhome data to an external operations system via a webhook. Once pushed, the external system becomes the primary system of record for ongoing operations. Status updates flow bidirectionally via HMAC-signed API calls.

**This portal is the application/onboarding system.** After activation, the external system handles daily operations, compliance tracking, and document lifecycle management. Status changes from the external system are pushed back to this portal via callback endpoints.

---

## Architecture

```
┌──────────────────────────────────────────────────────────┐
│  SPICE'd Portal (Laravel 12 + Sanctum)                   │
│                                                          │
│  Consultant clicks "Activate Dayhome"                    │
│       │                                                  │
│       ▼                                                  │
│  ApplicationController@activateDayhome()                 │
│       │                                                  │
│       ▼                                                  │
│  SendDayhomeIntakeJob  (queued, async — ShouldQueue)     │
│       │                                                  │
│       ▼                                                  │
│  DayhomeIntakeService                                    │
│  ┌──────────────────────────────────────────────────┐   │
│  │ 1. Load Application + relations eager-loaded      │   │
│  │ 2. Build payload JSON per defined schema          │   │
│  │ 3. HMAC-SHA256 sign the raw body                  │   │
│  │ 4. POST to INTAKE_WEBHOOK_URL                     │   │
│  │ 5. Handle: 201→mark synced_at                     │   │
│  │            409→log duplicate, ignore              │   │
│  │            422→log validation error, alert admin  │   │
│  │            5xx→retry with backoff                 │   │
│  └──────────────────────────────────────────────────┘   │
│                                                          │
│  Callback Endpoints (HMAC-verified inbound):             │
│  POST /api/v1/internal/status                           │
│  POST /api/v1/internal/compliance                       │
│  PUT  /api/v1/internal/educator-profile                  │
│  POST /api/v1/internal/documents                        │
└──────────────────────────────────────────────────────────┘
           │                              ▲
           │  Outbound (HMAC signed)      │  Inbound (HMAC verified)
           ▼                              │
┌──────────────────────────────────────────────────────────┐
│  External Operations System                              │
│  - Receives intake webhook                               │
│  - Fetches document files from signed download URLs       │
│  - Manages ongoing operations & compliance               │
│  - Pushes status/compliance/profile/doc updates back     │
└──────────────────────────────────────────────────────────┘
```

---

## Phase 1: Outbound Push (Portal → External System)

### 1.1 Environment Configuration

Add to `.env`:

```
INTAKE_WEBHOOK_URL=https://external-system.com/api/v1/dayhomes/intake
INTAKE_WEBHOOK_SECRET=  (64-char hex, generated via `openssl rand -hex 32`)
```

Add to `config/services.php`:

```php
'intake' => [
    'url' => env('INTAKE_WEBHOOK_URL'),
    'secret' => env('INTAKE_WEBHOOK_SECRET'),
],
```

### 1.2 Database Migrations

#### Migration 1: `add_synced_at_to_applications_table`

```php
Schema::table('applications', function (Blueprint $table) {
    $table->timestamp('synced_at')->nullable()->after('activated_at');
});
```

#### Migration 2: `create_dayhome_sync_logs_table`

```php
Schema::create('dayhome_sync_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('application_id')->constrained()->onDelete('cascade');
    $table->string('direction'); // 'outbound' or 'inbound'
    $table->string('endpoint');
    $table->integer('http_status')->nullable();
    $table->json('request_payload')->nullable();
    $table->json('response_body')->nullable();
    $table->string('error_message')->nullable();
    $table->timestamp('synced_at')->nullable();
    $table->timestamps();
});
```

### 1.3 DayhomeIntakeService

**File:** `app/Services/DayhomeIntakeService.php`

```php
<?php

namespace App\Services;

use App\Models\Application;
use App\Models\DayhomeSyncLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class DayhomeIntakeService
{
    protected string $webhookUrl;
    protected string $secret;

    public function __construct()
    {
        $this->webhookUrl = config('services.intake.url');
        $this->secret = config('services.intake.secret');
    }

    /**
     * Build the intake payload from an activated application.
     */
    public function buildPayload(Application $application): array
    {
        $application->loadMissing([
            'user.educatorProfile',
            'certificate',
            'documents' => fn($q) => $q->where('status', 'approved')->where('is_current_version', true),
            'inspections' => fn($q) => $q->where('type', 'final_inspection')->where('is_draft', false)->latest(),
            'user.educatorProfile.activeItems',
        ]);

        $profile = $application->user?->educatorProfile;
        $certificate = $application->certificate;
        $finalInspection = $application->inspections->first();
        $approvedDocs = $application->documents;

        return [
            'version' => '1.0',
            'externalId' => $application->application_number,

            // ── Educator ──
            'educator' => [
                'firstName' => $application->educator_first_name,
                'lastName'  => $application->educator_last_name,
                'fullName'  => $application->full_name,
                'email'     => $application->email,
                'phone'     => $application->phone,
            ],

            // ── Dayhome & Address ──
            'dayhome' => [
                'address' => [
                    'line1'      => $application->address_line_1,
                    'city'       => $application->city,
                    'province'   => $application->province,
                    'postalCode' => $application->postal_code,
                    'full'       => $application->full_address,
                ],
                'homeType'            => $application->home_type,
                'homeOwnership'       => $application->home_ownership,
                'fencedBackyard'      => (bool) $application->fenced_backyard,
                'smokingStatus'       => $application->smoking_status,
                'hasPets'             => (bool) $application->has_pets,
                'homeResidentsCount'  => $application->home_residents_count,
                'eveningOvernightCare' => (bool) $application->evening_overnight_care,
            ],

            // ── Operations ──
            'operations' => [
                'currentCapacity'   => $profile?->current_capacity ?? 0,
                'maximumCapacity'   => $profile?->maximum_capacity,
                'operatingHoursStart' => $profile?->operating_hours_start?->format('H:i:s'),
                'operatingHoursEnd'   => $profile?->operating_hours_end?->format('H:i:s'),
                'childcareLevel'    => $application->childcare_level,
                'languagesSpoken'   => $application->languages_spoken,
                'childcareEducation' => $application->childcare_education,
                'specializations'   => $profile?->specializations ?? [],
            ],

            // ── License ──
            'license' => [
                'certificateNumber' => $certificate?->certificate_number,
                'issueDate'         => $certificate?->issue_date?->toDateString(),
                'expiryDate'        => $certificate?->expiry_date?->toDateString(),
                'status'            => $certificate?->status ?? 'active',
            ],

            // ── Timeline ──
            'timeline' => [
                'submittedAt'         => $application->submitted_at?->toIso8601String(),
                'approvedAt'          => $application->approved_at?->toIso8601String(),
                'activatedAt'         => $application->activated_at?->toIso8601String(),
                'nextComplianceDue'   => $application->next_compliance_inspection_due?->toDateString(),
            ],

            // ── Final Inspection (last passed final inspection) ──
            'finalInspection' => $finalInspection ? [
                'conductedAt'   => $finalInspection->conducted_at?->toIso8601String(),
                'result'        => $finalInspection->overall_result,
                'score'         => $finalInspection->overall_score,
                'itemsPassed'   => $finalInspection->items_passed,
                'itemsFailed'   => $finalInspection->items_failed,
                'criticalFailures' => count($finalInspection->critical_failed_items ?? []),
                'summary'       => $finalInspection->summary ?? $finalInspection->observations,
                'inspectorName' => $finalInspection->consultant?->name,
            ] : null,

            // ── Approved Documents ──
            'documents' => $approvedDocs->map(fn($doc) => [
                'name'        => $doc->name,
                'fileName'    => $doc->original_filename,
                'category'    => $doc->documentCategory?->slug ?? $doc->documentType?->slug,
                'status'      => $doc->status,
                'issueDate'   => $doc->issue_date?->toDateString(),
                'expiryDate'  => $doc->expiry_date?->toDateString(),
                'downloadUrl' => URL::temporarySignedRoute(
                    'api.external.documents.download',
                    now()->addHours(24),
                    ['document' => $doc->id]
                ),
                'fileHash'    => $doc->file_hash,
            ])->toArray(),

            // ── Educator Profile Items (certifications, training) ──
            'profileItems' => $profile?->activeItems->map(fn($item) => [
                'title'     => $item->title,
                'type'      => $item->type,
                'expiryDate' => $item->expiry_date?->toDateString(),
                'fileName'  => $item->file_name,
            ])->toArray() ?? [],
        ];
    }

    /**
     * Generate HMAC-SHA256 signature for a given body.
     */
    public function signPayload(string $body): string
    {
        return 'sha256=' . hash_hmac('sha256', $body, $this->secret);
    }

    /**
     * Send the intake payload to the external system.
     * Returns ['status' => int, 'body' => array, 'error' => ?string].
     */
    public function send(array $payload): array
    {
        $body = json_encode($payload);
        $signature = $this->signPayload($body);

        try {
            $response = Http::withHeaders([
                'Signature'       => $signature,
                'Idempotency-Key' => $payload['externalId'],
                'Content-Type'    => 'application/json',
            ])->timeout(30)->post($this->webhookUrl, $payload);

            $status = $response->status();
            $responseBody = $response->json() ?? [];

            $this->logSync($payload['externalId'], 'outbound', $this->webhookUrl, $status, $payload, $responseBody);

            return [
                'status' => $status,
                'body'   => $responseBody,
                'error'  => null,
            ];
        } catch (\Exception $e) {
            Log::error('Dayhome intake request failed', [
                'externalId' => $payload['externalId'],
                'error'      => $e->getMessage(),
            ]);

            $this->logSync($payload['externalId'], 'outbound', $this->webhookUrl, 0, $payload, null, $e->getMessage());

            return [
                'status' => 0,
                'body'   => [],
                'error'  => $e->getMessage(),
            ];
        }
    }

    /**
     * Process the response and determine next action.
     * Returns true if the intake was accepted, false otherwise.
     */
    public function handleResponse(int $status, array $body, Application $application): bool
    {
        switch (true) {
            case $status === 201:
                $application->update(['synced_at' => now()]);
                Log::info('Dayhome intake accepted', [
                    'application' => $application->application_number,
                    'response'    => $body,
                ]);
                return true;

            case $status === 409:
                Log::info('Dayhome intake duplicate (already processed)', [
                    'application' => $application->application_number,
                    'response'    => $body,
                ]);
                $application->update(['synced_at' => now()]);
                return true;

            case $status === 422:
                Log::error('Dayhome intake validation failed', [
                    'application' => $application->application_number,
                    'response'    => $body,
                ]);
                return false;

            case $status === 401:
                Log::error('Dayhome intake auth failed — check HMAC secret', [
                    'application' => $application->application_number,
                    'response'    => $body,
                ]);
                return false;

            default:
                Log::warning('Dayhome intake unexpected response', [
                    'application' => $application->application_number,
                    'status'      => $status,
                    'response'    => $body,
                ]);
                return false;
        }
    }

    /**
     * Log the sync attempt to the database.
     */
    protected function logSync(
        string $externalId,
        string $direction,
        string $endpoint,
        int $httpStatus,
        ?array $requestPayload = null,
        ?array $responseBody = null,
        ?string $errorMessage = null
    ): void {
        try {
            $application = Application::where('application_number', $externalId)->first();
            if (!$application) return;

            DayhomeSyncLog::create([
                'application_id'  => $application->id,
                'direction'       => $direction,
                'endpoint'        => $endpoint,
                'http_status'     => $httpStatus,
                'request_payload' => $requestPayload,
                'response_body'   => $responseBody,
                'error_message'   => $errorMessage,
                'synced_at'       => $httpStatus === 201 || $httpStatus === 409 ? now() : null,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log sync attempt', [
                'externalId' => $externalId,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
```

### 1.4 SendDayhomeIntakeJob

**File:** `app/Jobs/SendDayhomeIntakeJob.php`

```php
<?php

namespace App\Jobs;

use App\Models\Application;
use App\Services\DayhomeIntakeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDayhomeIntakeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [300, 900, 1800]; // 5min, 15min, 30min

    public function __construct(public Application $application) {}

    public function handle(DayhomeIntakeService $service): void
    {
        // Only push active dayhomes
        if (!$this->application->isActiveDayhome()) {
            Log::warning('Attempted to push non-active dayhome', [
                'application' => $this->application->application_number,
                'status'      => $this->application->status,
            ]);
            return;
        }

        // Skip if already synced
        if ($this->application->synced_at) {
            Log::info('Dayhome already synced, skipping', [
                'application' => $this->application->application_number,
                'synced_at'   => $this->application->synced_at,
            ]);
            return;
        }

        $payload = $service->buildPayload($this->application);
        $response = $service->send($payload);
        $accepted = $service->handleResponse($response['status'], $response['body'], $this->application);

        if (!$accepted && $response['status'] >= 500) {
            // Server error — let the job retry with backoff
            $this->release($this->backoff[$this->attempts() - 1] ?? 3600);
        }

        if (!$accepted && $response['status'] < 500 && $response['status'] !== 0) {
            // Client error (422, 401, etc.) — no point retrying, fail permanently
            $this->fail(new \RuntimeException(
                "Intake rejected with status {$response['status']}: " . json_encode($response['body'])
            ));
        }

        if ($response['status'] === 0) {
            // Network/timeout error — retry
            $this->release($this->backoff[$this->attempts() - 1] ?? 3600);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Dayhome intake job failed permanently', [
            'application' => $this->application->application_number,
            'error'       => $e->getMessage(),
        ]);

        // Notify admins about the failure
        // TODO: Send admin notification
    }
}
```

### 1.5 Document Download Endpoint

Add to `routes/api.php`:

```php
use App\Http\Controllers\Api\DocumentDownloadController;

Route::middleware('signed')->prefix('external')->group(function () {
    Route::get('/documents/{document}/download', [DocumentDownloadController::class, 'download'])
        ->name('api.external.documents.download');
});
```

**File:** `app/Http/Controllers/Api/DocumentDownloadController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentDownloadController extends Controller
{
    /**
     * Provide a secure download for the external system.
     * The 'signed' middleware ensures the URL was generated by us and hasn't expired.
     */
    public function download(Document $document)
    {
        if (!$document->file_path || !Storage::exists($document->file_path)) {
            Log::warning('External document download failed — file not found', [
                'document_id' => $document->id,
                'path'        => $document->file_path,
            ]);
            return response()->json(['error' => 'File not found'], 404);
        }

        $document->increment('download_count');

        Log::info('External document downloaded', [
            'document_id'  => $document->id,
            'application'  => $document->application_id,
            'file_name'    => $document->original_filename,
        ]);

        return Storage::download($document->file_path, $document->original_filename);
    }
}
```

### 1.6 Hook into activateDayhome()

In `app/Http/Controllers/ApplicationController.php`, modify `activateDayhome()` to dispatch the job after commit:

```php
use App\Jobs\SendDayhomeIntakeJob;

// Inside activateDayhome(), after DB::commit():
DB::beginTransaction();
try {
    $this->statusService->transitionTo(
        $application,
        ApplicationStatus::ACTIVE,
        "Dayhome activated by " . auth()->user()->name
    );

    $application->update([
        'activated_at' => now(),
        'next_compliance_inspection_due' => now()->addMonths(6),
    ]);

    DB::commit();

    // Dispatch the intake push to the external system
    SendDayhomeIntakeJob::dispatch($application);

    return back()->with('success', 'Dayhome activated successfully!');

} catch (\Exception $e) {
    DB::rollback();
    // ...
}
```

---

## Phase 2: Inbound Callbacks (External System → Portal)

### 2.1 VerifyHmacSignature Middleware

**File:** `app/Http/Middleware/VerifyHmacSignature.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('Signature');
        $timestamp = $request->header('X-Timestamp');

        // Signature header is required
        if (!$signature || !str_starts_with($signature, 'sha256=')) {
            return response()->json([
                'error' => 'INTAKE_SIGNATURE_MISSING',
                'message' => 'Missing or malformed Signature header.',
            ], 401);
        }

        // Timestamp is required for replay protection
        if (!$timestamp) {
            return response()->json([
                'error' => 'INTAKE_TIMESTAMP_MISSING',
                'message' => 'Missing X-Timestamp header.',
            ], 401);
        }

        // Reject requests older than 5 minutes
        $requestTime = (int) $timestamp;
        if (abs(now()->timestamp - $requestTime) > 300) {
            return response()->json([
                'error' => 'INTAKE_TIMESTAMP_EXPIRED',
                'message' => 'Request timestamp is too old. Max 5 minute window.',
            ], 401);
        }

        $secret = config('services.intake.secret');
        $body = $request->getContent();
        $expected = 'sha256=' . hash_hmac('sha256', $body, $secret);

        if (!hash_equals($expected, $signature)) {
            return response()->json([
                'error' => 'INTAKE_SIGNATURE_INVALID',
                'message' => 'Signature does not match. Check shared secret.',
            ], 401);
        }

        return $next($request);
    }
}
```

Register in `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ... existing aliases ...
    'hmac' => \App\Http\Middleware\VerifyHmacSignature::class,
];
```

### 2.2 InternalApiController

**File:** `app/Http/Controllers/Api/InternalApiController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\EducatorProfile;
use App\Enums\ApplicationStatus;
use App\Services\ApplicationStatusService;
use App\Models\DayhomeSyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InternalApiController extends Controller
{
    protected ApplicationStatusService $statusService;

    public function __construct(ApplicationStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    /**
     * Find application by externalId (application_number).
     */
    protected function findApplication(string $externalId): ?Application
    {
        return Application::where('application_number', $externalId)->first();
    }

    /**
     * Log the incoming callback for audit trail.
     */
    protected function logCallback(Application $application, string $endpoint, int $status, array $payload, ?string $error = null): void
    {
        DayhomeSyncLog::create([
            'application_id'  => $application->id,
            'direction'       => 'inbound',
            'endpoint'        => $endpoint,
            'http_status'     => $status,
            'request_payload' => $payload,
            'error_message'   => $error,
            'synced_at'       => now(),
        ]);
    }

    /**
     * POST /api/v1/internal/status
     * Update dayhome status from the external system.
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId'    => 'required|string',
            'newStatus'     => 'required|string|in:' . implode(',', [
                'active', 'suspended', 'terminated',
                'compliance_inspection_due', 'compliance_inspection_scheduled',
                'compliance_inspection_completed', 'remediation_required', 'under_review',
            ]),
            'reason'        => 'nullable|string|max:1000',
            'effectiveDate' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => 'VALIDATION_FAILED',
                'message' => 'Invalid request body.',
                'details' => $validator->errors(),
            ], 422);
        }

        $application = $this->findApplication($request->input('externalId'));
        if (!$application) {
            return response()->json([
                'error'   => 'NOT_FOUND',
                'message' => 'No application found with that externalId.',
            ], 404);
        }

        $newStatus = ApplicationStatus::from($request->input('newStatus'));
        $reason = $request->input('reason');

        try {
            DB::beginTransaction();

            // Update status
            $this->statusService->transitionTo($application, $newStatus, $reason ?? "Status updated by external system");

            // Update additional fields based on status
            switch ($newStatus->value) {
                case 'active':
                    $application->update([
                        'suspended_at' => null,
                        'terminated_at' => null,
                        'activated_at' => $application->activated_at ?? now(),
                    ]);
                    break;
                case 'suspended':
                    $application->update(['suspended_at' => now()]);
                    break;
                case 'terminated':
                    $application->update(['terminated_at' => now()]);
                    break;
            }

            DB::commit();

            $this->logCallback($application, '/api/v1/internal/status', 200, $request->all());

            return response()->json([
                'status'  => 'ok',
                'message' => "Dayhome {$application->application_number} status updated to {$newStatus->value}.",
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update dayhome status from external system', [
                'externalId' => $request->input('externalId'),
                'error'      => $e->getMessage(),
            ]);

            $this->logCallback($application, '/api/v1/internal/status', 500, $request->all(), $e->getMessage());

            return response()->json([
                'error'   => 'INTERNAL_ERROR',
                'message' => 'Failed to update status.',
            ], 500);
        }
    }

    /**
     * POST /api/v1/internal/compliance
     * Record a compliance inspection result from the external system.
     */
    public function updateCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId'     => 'required|string',
            'inspectionDate' => 'required|date',
            'result'         => 'required|string|in:PASS,FAIL',
            'notes'          => 'nullable|string|max:2000',
            'nextDueDate'    => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'VALIDATION_FAILED', 'details' => $validator->errors()], 422);
        }

        $application = $this->findApplication($request->input('externalId'));
        if (!$application) {
            return response()->json(['error' => 'NOT_FOUND', 'message' => 'No application found.'], 404);
        }

        try {
            DB::beginTransaction();

            $application->update([
                'last_compliance_inspection_at' => now(),
                'next_compliance_inspection_due' => $request->input('nextDueDate')
                    ?: now()->addMonths(6),
            ]);

            // Set status back to active after compliance
            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::ACTIVE,
                "Compliance inspection passed — result: {$request->input('result')}"
            );

            DB::commit();

            $this->logCallback($application, '/api/v1/internal/compliance', 200, $request->all());

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to record compliance update', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'INTERNAL_ERROR', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT /api/v1/internal/educator-profile
     * Update educator profile fields from the external system.
     */
    public function updateEducatorProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId'         => 'required|string',
            'currentCapacity'    => 'nullable|integer|min:0',
            'maximumCapacity'    => 'nullable|integer|min:0',
            'operatingHoursStart' => 'nullable|date_format:H:i:s',
            'operatingHoursEnd'   => 'nullable|date_format:H:i:s',
            'specializations'    => 'nullable|array',
            'professionalBio'    => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'VALIDATION_FAILED', 'details' => $validator->errors()], 422);
        }

        $application = $this->findApplication($request->input('externalId'));
        if (!$application) {
            return response()->json(['error' => 'NOT_FOUND'], 404);
        }

        $profile = $application->user?->educatorProfile;
        if (!$profile) {
            return response()->json(['error' => 'NOT_FOUND', 'message' => 'No educator profile found.'], 404);
        }

        $updates = [];
        if ($request->has('currentCapacity'))    $updates['current_capacity'] = $request->input('currentCapacity');
        if ($request->has('maximumCapacity'))    $updates['maximum_capacity'] = $request->input('maximumCapacity');
        if ($request->has('operatingHoursStart')) $updates['operating_hours_start'] = $request->input('operatingHoursStart');
        if ($request->has('operatingHoursEnd'))   $updates['operating_hours_end'] = $request->input('operatingHoursEnd');
        if ($request->has('specializations'))    $updates['specializations'] = $request->input('specializations');
        if ($request->has('professionalBio'))    $updates['professional_bio'] = $request->input('professionalBio');
        $updates['last_updated_at'] = now();

        $profile->update($updates);
        $this->logCallback($application, '/api/v1/internal/educator-profile', 200, $request->all());

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * POST /api/v1/internal/documents
     * Update document statuses from the external system (expired, renewed, etc.).
     */
    public function updateDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId' => 'required|string',
            'documents'  => 'required|array',
            'documents.*.originalName'  => 'required|string',
            'documents.*.status'        => 'required|string|in:expired,renewed,replaced',
            'documents.*.expiryDate'    => 'nullable|date',
            'documents.*.replacedBy.name'      => 'nullable|string',
            'documents.*.replacedBy.fileName'  => 'nullable|string',
            'documents.*.replacedBy.expiryDate' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'VALIDATION_FAILED', 'details' => $validator->errors()], 422);
        }

        $application = $this->findApplication($request->input('externalId'));
        if (!$application) {
            return response()->json(['error' => 'NOT_FOUND'], 404);
        }

        try {
            DB::beginTransaction();

            foreach ($request->input('documents') as $docData) {
                // Find matching document by original filename
                $document = $application->documents()
                    ->where('original_filename', $docData['originalName'])
                    ->where('is_current_version', true)
                    ->first();

                if (!$document) continue;

                switch ($docData['status']) {
                    case 'expired':
                        $document->update([
                            'status' => 'expired',
                            'is_current_version' => false,
                        ]);
                        break;

                    case 'renewed':
                    case 'replaced':
                        $document->update([
                            'status' => 'replaced',
                            'is_current_version' => false,
                        ]);

                        // If replacedBy info is provided, create a placeholder
                        if (isset($docData['replacedBy'])) {
                            $application->documents()->create([
                                'uploaded_by'    => $application->user_id,
                                'name'           => $docData['replacedBy']['name'],
                                'original_filename' => $docData['replacedBy']['fileName'],
                                'file_path'      => null, // file lives in external system
                                'file_type'      => 'external',
                                'mime_type'      => 'application/octet-stream',
                                'file_size'      => 0,
                                'status'         => 'approved',
                                'expiry_date'    => $docData['replacedBy']['expiryDate'] ?? null,
                                'expires'        => (bool) ($docData['replacedBy']['expiryDate'] ?? false),
                                'is_current_version' => true,
                                'notes'          => "Managed by external system",
                            ]);
                        }
                        break;
                }
            }

            DB::commit();

            $this->logCallback($application, '/api/v1/internal/documents', 200, $request->all());

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update documents from external system', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'INTERNAL_ERROR', 'message' => $e->getMessage()], 500);
        }
    }
}
```

### 2.3 Callback Routes

Add to `routes/api.php`:

```php
use App\Http\Controllers\Api\InternalApiController;

Route::prefix('v1/internal')->middleware(['hmac', 'throttle:60,1'])->group(function () {
    Route::post('/status',              [InternalApiController::class, 'updateStatus']);
    Route::post('/compliance',          [InternalApiController::class, 'updateCompliance']);
    Route::put('/educator-profile',     [InternalApiController::class, 'updateEducatorProfile']);
    Route::post('/documents',           [InternalApiController::class, 'updateDocuments']);
});
```

---

## Phase 3: Sync Tracking & Monitoring

### DayhomeSyncLog Model

**File:** `app/Models/DayhomeSyncLog.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayhomeSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'direction',
        'endpoint',
        'http_status',
        'request_payload',
        'response_body',
        'error_message',
        'synced_at',
    ];

    protected $casts = [
        'request_payload' => 'json',
        'response_body'   => 'json',
        'synced_at'       => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
```

---

## Payload Schema (Phase 1 Outbound)

```json
{
  "version": "1.0",
  "externalId": "SPC-250T5K-0001",

  "educator": {
    "firstName": "Jane",
    "lastName": "Smith",
    "fullName": "Jane Smith",
    "email": "jane@example.com",
    "phone": "+14035551234"
  },

  "dayhome": {
    "address": {
      "line1": "123 Maple Street",
      "city": "Edmonton",
      "province": "AB",
      "postalCode": "T5K 0A1",
      "full": "123 Maple Street, Edmonton, AB T5K 0A1"
    },
    "homeType": "house",
    "homeOwnership": "own",
    "fencedBackyard": true,
    "smokingStatus": "no",
    "hasPets": false,
    "homeResidentsCount": 4,
    "eveningOvernightCare": false
  },

  "operations": {
    "currentCapacity": 2,
    "maximumCapacity": 8,
    "operatingHoursStart": "07:00:00",
    "operatingHoursEnd": "17:30:00",
    "childcareLevel": "Level 2",
    "languagesSpoken": "English, French",
    "childcareEducation": "Early Childhood Education Diploma",
    "specializations": ["Special Needs", "Infant Care"]
  },

  "license": {
    "certificateNumber": "SPICED-CERT-2026-0001",
    "issueDate": "2026-01-15",
    "expiryDate": "2027-01-15",
    "status": "active"
  },

  "timeline": {
    "submittedAt": "2025-09-20T10:30:00Z",
    "approvedAt": "2025-12-01T14:00:00Z",
    "activatedAt": "2025-12-15T09:00:00Z",
    "nextComplianceDue": "2026-06-15"
  },

  "finalInspection": {
    "conductedAt": "2025-11-10T11:00:00Z",
    "result": "pass",
    "score": 96.0,
    "itemsPassed": 24,
    "itemsFailed": 0,
    "criticalFailures": 0,
    "summary": "All requirements met.",
    "inspectorName": "Sarah Connor"
  },

  "documents": [
    {
      "name": "Home Insurance",
      "fileName": "home_insurance_2025.pdf",
      "category": "home_insurance",
      "status": "approved",
      "issueDate": "2025-01-01",
      "expiryDate": "2026-01-01",
      "downloadUrl": "https://portal.example.com/api/v1/external/documents/42/download?expires=...&signature=...",
      "fileHash": "sha256:e3b0c44298fc1c149afbf4c8996fb924..."
    }
  ],

  "profileItems": [
    {
      "title": "Standard First Aid",
      "type": "document",
      "expiryDate": "2027-03-15",
      "fileName": "first_aid_cert.pdf"
    }
  ]
}
```

---

## Security

| Concern | Mitigation |
|---|---|
| Secret exposure | Stored in `.env` only, never logged or displayed in responses |
| Replay attacks | `X-Timestamp` header + 5-minute acceptance window on inbound callbacks |
| Tampered body | HMAC-SHA256 — any modification invalidates the signature |
| Eavesdropping | HTTPS enforced at infrastructure level |
| Secret rotation | Manual rotation via `.env` change. Coordinated with external system out of band |
| Document access | Temporary signed URLs with 24-hour expiry (Laravel `signed` middleware) |
| Rate limiting | Inbound callbacks throttled at 60 req/min via Laravel `throttle` middleware |

---

## Status Mapping (Inbound Callbacks)

| External System Sends | Our Status (`applications.status`) | Side Effects |
|---|---|---|
| `active` | `active` | Clears `suspended_at`, `terminated_at` |
| `suspended` | `suspended` | Sets `suspended_at` |
| `terminated` | `terminated` | Sets `terminated_at` |
| `compliance_inspection_due` | `compliance_inspection_due` | Updates `next_compliance_inspection_due` |
| `compliance_inspection_scheduled` | `compliance_inspection_scheduled` | — |
| `compliance_inspection_completed` | `compliance_inspection_completed` | Updates `last_compliance_inspection_at` |
| `remediation_required` | `remediation_required` | Expects `remediationDeadline` in payload |
| `under_review` | `under_review` | — |

---

## Error Handling

| Scenario | Outbound Action | Inbound Action |
|---|---|---|
| HTTP 201 Created | Log success, update `synced_at` | N/A |
| HTTP 409 Conflict (duplicate) | Log, update `synced_at` (already processed) | N/A |
| HTTP 422 Validation | Log error, alert admin | Return 422 to caller with details |
| HTTP 401 Unauthorized | Log error (check secret), alert admin | Return 401 to caller |
| HTTP 5xx Server Error | Retry with backoff (5min/15min/30min) | Return 500 to caller |
| Network timeout | Retry with backoff | N/A |
| Invalid HMAC on inbound | N/A | Return 401 `INTAKE_SIGNATURE_INVALID` |

---

## Files to Create/Modify Summary

| File | Action |
|---|---|
| `.env` | Add `INTAKE_WEBHOOK_URL`, `INTAKE_WEBHOOK_SECRET` |
| `config/services.php` | Add `intake` config block |
| `app/Services/DayhomeIntakeService.php` | **Create** |
| `app/Jobs/SendDayhomeIntakeJob.php` | **Create** |
| `app/Http/Middleware/VerifyHmacSignature.php` | **Create** |
| `app/Http/Controllers/Api/InternalApiController.php` | **Create** |
| `app/Http/Controllers/Api/DocumentDownloadController.php` | **Create** |
| `app/Models/DayhomeSyncLog.php` | **Create** |
| `routes/api.php` | Add external download route + callback routes |
| `app/Http/Kernel.php` | Register `hmac` middleware alias |
| `app/Http/Controllers/ApplicationController.php` | Dispatch job in `activateDayhome()` |
| `database/migrations/YYYY_MM_DD_HHMMSS_create_dayhome_sync_logs_table.php` | **Create** |
| `database/migrations/YYYY_MM_DD_HHMMSS_add_synced_at_to_applications.php` | **Create** |

---

## Testing

1. **Unit:** `DayhomeIntakeService::buildPayload()` — assert correct structure with known test data
2. **Unit:** `DayhomeIntakeService::signPayload()` — assert HMAC matches expected value
3. **Feature:** Inbound callbacks — POST with valid/invalid HMAC, assert 200/401 responses
4. **Feature:** Document download — assert signed URL returns file, expired URL returns 403
5. **Queue:** Assert `SendDayhomeIntakeJob` is dispatched on `activateDayhome()`
6. **Integration:** Stub external HTTP endpoint, assert full flow creates sync log record
