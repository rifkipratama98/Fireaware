<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SUPPRESSED = 'suppressed';
    public const STATUS_PENDING = 'pending';

    public const CHANNEL_BROWSER = 'browser';
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_WEBHOOK = 'webhook';
    public const CHANNEL_SYSTEM = 'system';

    protected $fillable = [
        'channel',
        'title',
        'message',
        'category',
        'level',
        'province_id',
        'recipient_target',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('id');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_SENT => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
            self::STATUS_FAILED => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
            self::STATUS_SUPPRESSED => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
            self::STATUS_PENDING => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
            default => 'bg-light text-dark border',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SENT => 'Terkirim',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_SUPPRESSED => 'Ditekan (Anti-Spam)',
            self::STATUS_PENDING => 'Menunggu',
            default => ucfirst((string) $this->status),
        };
    }

    public function channelLabel(): string
    {
        return match ($this->channel) {
            self::CHANNEL_BROWSER => 'Browser (Masyarakat)',
            self::CHANNEL_EMAIL => 'Email Posko',
            self::CHANNEL_WEBHOOK => 'Webhook Koordinasi',
            self::CHANNEL_SYSTEM => 'Sistem Internal',
            default => ucfirst((string) $this->channel),
        };
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'hotspot' => 'Titik Panas (Hotspot)',
            'air_quality' => 'Kualitas Udara (PM2.5)',
            'announcement' => 'Pengumuman / Rilis Resmi',
            'test' => 'Uji Coba Pengiriman',
            default => ucfirst((string) $this->category),
        };
    }

    public function levelBadgeClass(): string
    {
        return match ($this->level) {
            'danger' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
            'warning' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
            default => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
        };
    }
}
