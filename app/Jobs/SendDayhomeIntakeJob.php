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
    public array $backoff = [300, 900, 1800];

    public function __construct(public Application $application) {}

    public function handle(DayhomeIntakeService $service): void
    {
        if (!$this->application->isActiveDayhome()) {
            Log::warning('SendDayhomeIntakeJob skipped — application is not active', [
                'application' => $this->application->application_number,
                'status' => $this->application->status,
            ]);
            return;
        }

        if ($this->application->synced_at) {
            Log::info('SendDayhomeIntakeJob skipped — already synced', [
                'application' => $this->application->application_number,
                'synced_at' => $this->application->synced_at,
            ]);
            return;
        }

        $payload = $service->buildPayload($this->application);
        $result = $service->send($payload);
        $accepted = $service->handleResponse($result['status'], $result['responseBody'], $this->application);

        if ($accepted) {
            return;
        }

        $status = $result['status'];

        if ($status >= 500 || $status === 0) {
            $delay = $this->backoff[$this->attempts() - 1] ?? 3600;
            $this->release($delay);
            return;
        }

        $this->fail(new \RuntimeException(
            "Intake permanently rejected. Status: {$status}, Response: " . json_encode($result['responseBody'])
        ));
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendDayhomeIntakeJob failed permanently', [
            'application' => $this->application->application_number,
            'error' => $e->getMessage(),
        ]);
    }
}
