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

            $this->statusService->transitionTo($application, $newStatus, $reason ?? "Status updated by external system");

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

                        if (isset($docData['replacedBy'])) {
                            $application->documents()->create([
                                'uploaded_by'    => $application->user_id,
                                'name'           => $docData['replacedBy']['name'],
                                'original_filename' => $docData['replacedBy']['fileName'],
                                'file_path'      => null,
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
