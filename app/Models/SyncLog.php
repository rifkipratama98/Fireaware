<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    use HasFactory;

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_TIMEOUT = 'timeout';
    public const STATUS_NOT_CONFIGURED = 'not_configured';
    public const STATUS_RUNNING = 'running';

    protected $fillable = [
        'source_key',
        'source_name',
        'status',
        'records_processed',
        'records_added',
        'records_updated',
        'observation_at',
        'started_at',
        'completed_at',
        'duration_ms',
        'error_message',
        'payload_summary',
        'trigger',
        'user_id',
    ];

    protected $casts = [
        'records_processed' => 'integer',
        'records_added' => 'integer',
        'records_updated' => 'integer',
        'duration_ms' => 'integer',
        'observation_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForSource(Builder $query, string $sourceKey): Builder
    {
        return $query->where('source_key', $sourceKey);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('id');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_SUCCESS => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
            self::STATUS_FAILED => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
            self::STATUS_TIMEOUT => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
            self::STATUS_NOT_CONFIGURED => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
            self::STATUS_RUNNING => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
            default => 'bg-light text-dark border',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SUCCESS => 'Berhasil',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_TIMEOUT => 'Batas Waktu (Timeout)',
            self::STATUS_NOT_CONFIGURED => 'Belum Dikonfigurasi',
            self::STATUS_RUNNING => 'Sedang Berjalan',
            default => ucfirst((string) $this->status),
        };
    }

    public function statusIcon(): string
    {
        return match ($this->status) {
            self::STATUS_SUCCESS => 'bi-check-circle-fill',
            self::STATUS_FAILED => 'bi-x-circle-fill',
            self::STATUS_TIMEOUT => 'bi-hourglass-bottom',
            self::STATUS_NOT_CONFIGURED => 'bi-gear',
            self::STATUS_RUNNING => 'bi-arrow-repeat',
            default => 'bi-question-circle',
        };
    }

    public function durationFormatted(): string
    {
        if ($this->duration_ms === null) {
            return '—';
        }

        if ($this->duration_ms < 1000) {
            return "{$this->duration_ms} ms";
        }

        $seconds = round($this->duration_ms / 1000, 2);

        return "{$seconds} detik";
    }
}
