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

    public function buildPayload(Application $application): array
    {
        $application->loadMissing([
            'user.educatorProfile.activeItems',
            'certificate',
            'inspections' => fn($q) => $q
                ->where('type', 'final_inspection')
                ->where('is_draft', false)
                ->where('overall_result', 'pass')
                ->latest(),
        ]);

        $profile = $application->user?->educatorProfile;
        $certificate = $application->certificate;
        $finalInspection = $application->inspections->first();

        $approvedDocs = $application->documents()->where('is_current_version', true)
            ->whereIn('status', ['approved', 'uploaded'])
            ->get();

        return [
            'version' => '1.0',
            'externalId' => $application->application_number,

            'educator' => [
                'firstName' => $application->educator_first_name,
                'lastName' => $application->educator_last_name,
                'fullName' => $application->full_name,
                'email' => $application->email,
                'phone' => $application->phone,
            ],

            'dayhome' => [
                'address' => [
                    'line1' => $application->address_line_1,
                    'city' => $application->city,
                    'province' => $application->province,
                    'postalCode' => $application->postal_code,
                    'full' => $application->full_address,
                ],
                'homeType' => $application->home_type,
                'homeOwnership' => $application->home_ownership,
                'fencedBackyard' => (bool) $application->fenced_backyard,
                'smokingStatus' => $application->smoking_status,
                'hasPets' => (bool) $application->has_pets,
                'homeResidentsCount' => $application->home_residents_count,
                'eveningOvernightCare' => (bool) $application->evening_overnight_care,
            ],

            'operations' => [
                'currentCapacity' => (int) ($profile?->current_capacity ?? 0),
                'maximumCapacity' => (int) ($profile?->maximum_capacity ?? 0),
                'operatingHoursStart' => $profile?->operating_hours_start?->format('H:i:s'),
                'operatingHoursEnd' => $profile?->operating_hours_end?->format('H:i:s'),
                'childcareLevel' => $application->childcare_level,
                'languagesSpoken' => $application->languages_spoken,
                'childcareEducation' => $application->childcare_education,
                'specializations' => $profile?->specializations ?? [],
            ],

            'license' => [
                'certificateNumber' => $certificate?->certificate_number,
                'issueDate' => $certificate?->issue_date?->toDateString(),
                'expiryDate' => $certificate?->expiry_date?->toDateString(),
                'status' => $certificate?->status ?? 'active',
            ],

            'timeline' => [
                'submittedAt' => $application->submitted_at?->toIso8601String(),
                'approvedAt' => $application->approved_at?->toIso8601String(),
                'activatedAt' => $application->activated_at?->toIso8601String(),
                'nextComplianceDue' => $application->next_compliance_inspection_due?->toDateString(),
            ],

            'finalInspection' => $finalInspection ? [
                'conductedAt' => $finalInspection->conducted_at?->toIso8601String(),
                'result' => $finalInspection->overall_result,
                'score' => $finalInspection->overall_score,
                'itemsPassed' => $finalInspection->items_passed,
                'itemsFailed' => $finalInspection->items_failed,
                'criticalFailures' => $finalInspection->critical_failed_items ? count($finalInspection->critical_failed_items) : 0,
                'summary' => $finalInspection->summary ?? $finalInspection->observations,
                'inspectorName' => $finalInspection->consultant?->name,
            ] : null,

            'documents' => $approvedDocs->map(fn($doc) => [
                'name' => $doc->name,
                'fileName' => $doc->original_filename,
                'category' => $doc->documentCategory?->slug,
                'status' => $doc->status,
                'issueDate' => $doc->issue_date?->toDateString(),
                'expiryDate' => $doc->expiry_date?->toDateString(),
                'downloadUrl' => URL::temporarySignedRoute(
                    'api.external.documents.download',
                    now()->addHours(24),
                    ['document' => $doc->id]
                ),
                'fileHash' => $doc->file_hash,
            ])->values()->toArray(),

            'profileItems' => $profile?->activeItems->map(fn($item) => [
                'title' => $item->title,
                'type' => $item->type,
                'expiryDate' => $item->expiry_date?->toDateString(),
                'fileName' => $item->file_name,
            ])->values()->toArray() ?? [],
        ];
    }

    public function signPayload(string $body): string
    {
        return 'sha256=' . hash_hmac('sha256', $body, $this->secret);
    }

    public function send(array $payload): array
    {
        $body = json_encode($payload);
        $signature = $this->signPayload($body);

        try {
            $response = Http::withHeaders([
                'Signature' => $signature,
                'Idempotency-Key' => $payload['externalId'],
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->webhookUrl, $payload);

            $status = $response->status();
            $responseBody = $response->json() ?? [];

            $this->logSyncAttempt($payload['externalId'], 'outbound', $this->webhookUrl, $status, $payload, $responseBody);

            return compact('status', 'responseBody');
        } catch (\Exception $e) {
            Log::error('Dayhome intake request failed', [
                'externalId' => $payload['externalId'],
                'error' => $e->getMessage(),
            ]);

            $this->logSyncAttempt($payload['externalId'], 'outbound', $this->webhookUrl, 0, $payload, null, $e->getMessage());

            return [
                'status' => 0,
                'responseBody' => [],
            ];
        }
    }

    public function handleResponse(int $status, array $responseBody, Application $application): bool
    {
        if ($status === 201 || $status === 409) {
            $application->update(['synced_at' => now()]);
            Log::info('Dayhome intake processed', [
                'application' => $application->application_number,
                'status' => $status,
                'response' => $responseBody,
            ]);
            return true;
        }

        if ($status === 422) {
            Log::error('Dayhome intake validation failed', [
                'application' => $application->application_number,
                'response' => $responseBody,
            ]);
            return false;
        }

        if ($status === 401) {
            Log::error('Dayhome intake authentication failed — verify INTAKE_WEBHOOK_SECRET matches', [
                'application' => $application->application_number,
                'response' => $responseBody,
            ]);
            return false;
        }

        Log::warning('Dayhome intake unexpected response', [
            'application' => $application->application_number,
            'status' => $status,
            'response' => $responseBody,
        ]);
        return false;
    }

    protected function logSyncAttempt(
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
            if (!$application) {
                return;
            }

            DayhomeSyncLog::create([
                'application_id' => $application->id,
                'direction' => $direction,
                'endpoint' => $endpoint,
                'http_status' => $httpStatus,
                'request_payload' => $requestPayload,
                'response_body' => $responseBody,
                'error_message' => $errorMessage,
                'synced_at' => in_array($httpStatus, [201, 409]) ? now() : null,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to persist sync log', [
                'externalId' => $externalId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
