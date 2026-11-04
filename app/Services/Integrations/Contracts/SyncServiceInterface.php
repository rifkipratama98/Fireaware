<?php

namespace App\Services\Integrations\Contracts;

use App\Services\Integrations\SyncResult;

interface SyncServiceInterface
{
    /** Kunci unik pengenal sumber (mis. 'nasa_firms', 'bmkg_karhutla', 'air_quality'). */
    public function getSourceKey(): string;

    /** Nama manusiawi sumber data. */
    public function getSourceName(): string;

    /** Nama penyedia / otoritas data. */
    public function getProviderName(): string;

    /** Ikon bootstrap untuk sumber data. */
    public function getIcon(): string;

    /** Memeriksa apakah kredensial atau endpoint telah dikonfigurasi pada server. */
    public function isConfigured(): bool;

    /**
     * Menjalankan proses sinkronisasi data eksternal.
     *
     * @param string $trigger manual | scheduled | command
     * @param int|null $userId ID admin jika dipicu manual
     */
    public function sync(string $trigger = 'manual', ?int $userId = null): SyncResult;

    /**
     * Mengambil ringkasan status integrasi dan metrik terkini.
     *
     * @return array<string, mixed>
     */
    public function getStatusSummary(): array;
}
