<?php

namespace App\Services\Integrations;

use App\Models\SyncLog;
use Illuminate\Support\Carbon;

class SyncResult
{
    public function __construct(
        public readonly string $sourceKey,
        public readonly string $sourceName,
        public readonly string $status,
        public readonly int $recordsProcessed = 0,
        public readonly int $recordsAdded = 0,
        public readonly int $recordsUpdated = 0,
        public readonly ?Carbon $observationAt = null,
        public readonly ?string $errorMessage = null,
        public readonly ?SyncLog $log = null,
        public readonly ?string $payloadSummary = null,
    ) {
    }

    public static function success(
        string $sourceKey,
        string $sourceName,
        int $processed,
        int $added,
        int $updated,
        ?Carbon $observationAt = null,
        ?SyncLog $log = null,
        ?string $payloadSummary = null
    ): self {
        return new self(
            sourceKey: $sourceKey,
            sourceName: $sourceName,
            status: SyncLog::STATUS_SUCCESS,
            recordsProcessed: $processed,
            recordsAdded: $added,
            recordsUpdated: $updated,
            observationAt: $observationAt,
            log: $log,
            payloadSummary: $payloadSummary
        );
    }

    public static function failed(
        string $sourceKey,
        string $sourceName,
        string $errorMessage,
        ?SyncLog $log = null,
        int $processed = 0
    ): self {
        return new self(
            sourceKey: $sourceKey,
            sourceName: $sourceName,
            status: SyncLog::STATUS_FAILED,
            recordsProcessed: $processed,
            errorMessage: $errorMessage,
            log: $log
        );
    }

    public static function timeout(
        string $sourceKey,
        string $sourceName,
        string $errorMessage,
        ?SyncLog $log = null
    ): self {
        return new self(
            sourceKey: $sourceKey,
            sourceName: $sourceName,
            status: SyncLog::STATUS_TIMEOUT,
            errorMessage: $errorMessage,
            log: $log
        );
    }

    public static function notConfigured(
        string $sourceKey,
        string $sourceName,
        string $reason,
        ?SyncLog $log = null
    ): self {
        return new self(
            sourceKey: $sourceKey,
            sourceName: $sourceName,
            status: SyncLog::STATUS_NOT_CONFIGURED,
            errorMessage: $reason,
            log: $log
        );
    }

    public function isSuccess(): bool
    {
        return $this->status === SyncLog::STATUS_SUCCESS;
    }

    public function isFailed(): bool
    {
        return $this->status === SyncLog::STATUS_FAILED;
    }

    public function isTimeout(): bool
    {
        return $this->status === SyncLog::STATUS_TIMEOUT;
    }

    public function isNotConfigured(): bool
    {
        return $this->status === SyncLog::STATUS_NOT_CONFIGURED;
    }
}
