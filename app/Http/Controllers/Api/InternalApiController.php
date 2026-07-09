<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\EducatorProfile;
use App\Models\EducatorProfileItem;
use App\Enums\ApplicationStatus;
use App\Services\ApplicationStatusService;
use App\Models\DayhomeSyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InternalApiController extends Controller
{
    protected ApplicationStatusService $statusService;

    public function __construct(ApplicationStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    protected function findApplication(string $externalId): ?Application
    {
        return Application::where('application_number', $externalId)->first();
    }

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

    protected function downloadDocumentFile(string $url): ?array
    {
        try {
            $response = Http::timeout(30)->get($url);
            if (!$response->successful()) {
                Log::warning('Failed to download document from portal', ['url' => $url, 'status' => $response->status()]);
                return null;
            }
            $contents = $response->body();
            $headers = $response->headers();
            $extension = 'bin';
            if (isset($headers['Content-Type'][0])) {
                $mimeMap = [
                    'application/pdf' => 'pdf',
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'application/msword' => 'doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                ];
                $extension = $mimeMap[$headers['Content-Type'][0]] ?? 'bin';
            }
            $filename = 'external-docs/' . md5($url) . '.' . $extension;
            Storage::put($filename, $contents);
            return ['path' => $filename, 'mime' => $headers['Content-Type'][0] ?? 'application/octet-stream'];
        } catch (\Exception $e) {
            Log::error('Document download failed', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId' => 'required|string',
            'status'     => 'required|string|in:active,suspended,terminated,compliance_inspection_due',
            'reason'     => 'nullable|string|max:1000',
            'timestamp'  => 'nullable|date',
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

        $portalStatus = $request->input('status');
        $reason = $request->input('reason') ?? "Status updated by external system";

        $statusMap = [
            'active'                    => ApplicationStatus::ACTIVE,
            'suspended'                 => ApplicationStatus::SUSPENDED,
            'terminated'                => ApplicationStatus::TERMINATED,
            'compliance_inspection_due' => ApplicationStatus::COMPLIANCE_INSPECTION_DUE,
        ];

        $newStatus = $statusMap[$portalStatus];

        try {
            DB::beginTransaction();

            $this->statusService->transitionTo($application, $newStatus, $reason);

            $application->update(['portal_status' => $portalStatus]);

            switch ($portalStatus) {
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
                'message' => "Dayhome {$application->application_number} status updated to {$portalStatus}.",
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

    public function updateCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId'        => 'required|string',
            'result'            => 'required|string|in:pass,conditional,fail',
            'conductedAt'       => 'required|date',
            'nextComplianceDue' => 'required|date',
            'score'             => 'nullable|numeric|min:0|max:100',
            'itemsPassed'       => 'nullable|integer|min:0',
            'itemsFailed'       => 'nullable|integer|min:0',
            'criticalFailures'  => 'nullable|integer|min:0',
            'summary'           => 'nullable|string|max:2000',
            'inspectorName'     => 'nullable|string|max:255',
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
                'last_compliance_inspection_at' => $request->input('conductedAt'),
                'next_compliance_inspection_due' => $request->input('nextComplianceDue'),
            ]);

            $this->statusService->transitionTo(
                $application,
                ApplicationStatus::ACTIVE,
                "Compliance inspection result: {$request->input('result')}"
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

    public function updateEducatorProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId'          => 'required|string',
            'currentCapacity'     => 'nullable|integer|min:0',
            'maximumCapacity'     => 'nullable|integer|min:0',
            'operatingHoursStart' => 'nullable|date_format:H:i:s',
            'operatingHoursEnd'   => 'nullable|date_format:H:i:s',
            'childcareLevel'      => 'nullable|string|max:255',
            'languagesSpoken'     => 'nullable|string|max:500',
            'childcareEducation'  => 'nullable|string|max:1000',
            'specializations'     => 'nullable|array',
            'professionalBio'     => 'nullable|string|max:5000',
            'profileItems'        => 'nullable|array',
            'profileItems.*.title'      => 'required|string|max:255',
            'profileItems.*.type'       => 'required|string|in:document,certification,training',
            'profileItems.*.expiryDate' => 'nullable|date',
            'profileItems.*.fileName'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'VALIDATION_FAILED', 'details' => $validator->errors()], 422);
        }

        $application = $this->findApplication($request->input('externalId'));
        if (!$application) {
            return response()->json(['error' => 'NOT_FOUND'], 404);
        }

        $application->update([
            'childcare_level'    => $request->input('childcareLevel', $application->childcare_level),
            'languages_spoken'   => $request->input('languagesSpoken', $application->languages_spoken),
            'childcare_education' => $request->input('childcareEducation', $application->childcare_education),
        ]);

        $profile = $application->user?->educatorProfile;
        if ($profile) {
            $updates = [];
            if ($request->has('currentCapacity'))     $updates['current_capacity'] = $request->input('currentCapacity');
            if ($request->has('maximumCapacity'))     $updates['maximum_capacity'] = $request->input('maximumCapacity');
            if ($request->has('operatingHoursStart'))  $updates['operating_hours_start'] = $request->input('operatingHoursStart');
            if ($request->has('operatingHoursEnd'))    $updates['operating_hours_end'] = $request->input('operatingHoursEnd');
            if ($request->has('specializations'))     $updates['specializations'] = $request->input('specializations');
            if ($request->has('professionalBio'))     $updates['professional_bio'] = $request->input('professionalBio');
            if ($updates) {
                $updates['last_updated_at'] = now();
                $profile->update($updates);
            }

            if ($request->has('profileItems')) {
                foreach ($request->input('profileItems') as $itemData) {
                    $existingItem = $profile->items()
                        ->where('title', $itemData['title'])
                        ->where('type', $itemData['type'])
                        ->first();

                    if ($existingItem) {
                        $existingItem->update([
                            'expiry_date' => $itemData['expiryDate'] ?? $existingItem->expiry_date,
                            'file_name'   => $itemData['fileName'] ?? $existingItem->file_name,
                        ]);
                    } else {
                        $profile->items()->create([
                            'title'       => $itemData['title'],
                            'type'        => $itemData['type'],
                            'expiry_date' => $itemData['expiryDate'] ?? null,
                            'file_name'   => $itemData['fileName'] ?? null,
                            'sort_order'  => $profile->items()->max('sort_order') + 1,
                            'is_active'   => true,
                        ]);
                    }
                }
            }
        }

        $this->logCallback($application, '/api/v1/internal/educator-profile', 200, $request->all());

        return response()->json(['status' => 'ok'], 200);
    }

    public function updateDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId'                  => 'required|string',
            'documents'                   => 'required|array',
            'documents.*.name'            => 'required|string',
            'documents.*.fileName'        => 'required|string',
            'documents.*.category'        => 'required|string',
            'documents.*.action'          => 'required|string|in:expired,renewed,replaced,updated',
            'documents.*.downloadUrl'     => 'required_if:documents.*.action,renewed,replaced|nullable|string',
            'documents.*.expiryDate'      => 'nullable|date',
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
                $document = $application->documents()
                    ->where('original_filename', $docData['fileName'])
                    ->where('is_current_version', true)
                    ->first();

                switch ($docData['action']) {
                    case 'expired':
                        if ($document) {
                            $document->update([
                                'status' => 'expired',
                                'is_current_version' => false,
                            ]);
                        }
                        break;

                    case 'updated':
                        if ($document) {
                            $document->update([
                                'expiry_date' => $docData['expiryDate'] ?? $document->expiry_date,
                            ]);
                        }
                        break;

                    case 'renewed':
                    case 'replaced':
                        if ($document) {
                            $document->update([
                                'status' => 'replaced',
                                'is_current_version' => false,
                            ]);
                        }

                        $fileInfo = null;
                        if (!empty($docData['downloadUrl'])) {
                            $fileInfo = $this->downloadDocumentFile($docData['downloadUrl']);
                        }

                        $application->documents()->create([
                            'uploaded_by'      => $application->user_id,
                            'name'             => $docData['name'],
                            'original_filename' => $docData['fileName'],
                            'file_path'        => $fileInfo ? $fileInfo['path'] : null,
                            'file_type'        => 'external',
                            'mime_type'        => $fileInfo ? $fileInfo['mime'] : 'application/octet-stream',
                            'file_size'        => 0,
                            'status'           => 'approved',
                            'expiry_date'      => $docData['expiryDate'] ?? null,
                            'expires'          => (bool) ($docData['expiryDate'] ?? false),
                            'is_current_version' => true,
                            'notes'            => 'Managed by external system',
                        ]);
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
