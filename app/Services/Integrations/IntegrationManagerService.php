<?php

namespace App\Services\Integrations;

use App\Models\SyncLog;
use App\Services\Integrations\Contracts\SyncServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class IntegrationManagerService
{
    /** @var array<string, SyncServiceInterface> */
    private array $services;

    public function __construct(
        FirmsHotspotSyncService $firmsService,
        BmkgKarhutlaSyncService $bmkgService,
        AirQualitySyncService $airQualityService,
    ) {
        $this->services = [
            $firmsService->getSourceKey() => $firmsService,
            $bmkgService->getSourceKey() => $bmkgService,
            $airQualityService->getSourceKey() => $airQualityService,
        ];
    }

    /**
     * @return array<string, SyncServiceInterface>
     */
    public function getServices(): array
    {
        return $this->services;
    }

    public function getService(string $key): ?SyncServiceInterface
    {
        return $this->services[$key] ?? null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getIntegrationStatuses(): array
    {
        $statuses = [];
        foreach ($this->services as $service) {
            $statuses[] = $service->getStatusSummary();
        }

        return $statuses;
    }

    public function sync(string $key, string $trigger = 'manual', ?int $userId = null): SyncResult
    {
        $service = $this->getService($key);
        if (! $service) {
            throw new InvalidArgumentException("Sumber data eksternal '{$key}' tidak dikenali.");
        }

        return $service->sync($trigger, $userId);
    }

    /**
     * @return array<string, SyncResult>
     */
    public function syncAll(string $trigger = 'manual', ?int $userId = null): array
    {
        $results = [];
        foreach ($this->services as $key => $service) {
            $results[$key] = $service->sync($trigger, $userId);
        }

        return $results;
    }

    public function getRecentLogs(int $perPage = 15, ?string $sourceFilter = null, ?string $statusFilter = null): LengthAwarePaginator
    {
        $query = SyncLog::query()->with('user')->latestFirst();

        if ($sourceFilter && array_key_exists($sourceFilter, $this->services)) {
            $query->where('source_key', $sourceFilter);
        }

        if ($statusFilter && in_array($statusFilter, [
            SyncLog::STATUS_SUCCESS,
            SyncLog::STATUS_FAILED,
            SyncLog::STATUS_TIMEOUT,
            SyncLog::STATUS_NOT_CONFIGURED,
        ], true)) {
            $query->where('status', $statusFilter);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getStats(): array
    {
        return [
            'total' => SyncLog::count(),
            'success' => SyncLog::where('status', SyncLog::STATUS_SUCCESS)->count(),
            'failed' => SyncLog::where('status', SyncLog::STATUS_FAILED)->count(),
            'timeout' => SyncLog::where('status', SyncLog::STATUS_TIMEOUT)->count(),
            'not_configured' => SyncLog::where('status', SyncLog::STATUS_NOT_CONFIGURED)->count(),
        ];
    }
}
