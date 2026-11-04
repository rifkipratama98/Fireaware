<?php

namespace App\Console\Commands;

use App\Services\Integrations\AirQualitySyncService;
use App\Services\Integrations\BmkgKarhutlaSyncService;
use App\Services\Integrations\FirmsHotspotSyncService;
use App\Services\Integrations\IntegrationManagerService;
use App\Services\Integrations\SyncResult;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncExternalDataCommand extends Command
{
    /**
     * Nama dan tanda tangan perintah console.
     *
     * @var string
     */
    protected $signature = 'fireaware:sync
                            {source? : Sumber yang disinkronkan (firms|bmkg|airquality|all)}
                            {--all : Sinkronkan seluruh sumber data eksternal}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data eksternal (NASA FIRMS, BMKG Karhutla, Kualitas Udara) ke FireAware';

    public function handle(IntegrationManagerService $manager): int
    {
        $sourceInput = strtolower((string) ($this->argument('source') ?? ''));
        $syncAll = $this->option('all') || $sourceInput === 'all' || $sourceInput === '';

        $sourceMap = [
            'firms' => FirmsHotspotSyncService::SOURCE_KEY,
            'nasa_firms' => FirmsHotspotSyncService::SOURCE_KEY,
            'hotspot' => FirmsHotspotSyncService::SOURCE_KEY,
            'bmkg' => BmkgKarhutlaSyncService::SOURCE_KEY,
            'bmkg_karhutla' => BmkgKarhutlaSyncService::SOURCE_KEY,
            'airquality' => AirQualitySyncService::SOURCE_KEY,
            'air_quality' => AirQualitySyncService::SOURCE_KEY,
            'openaq' => AirQualitySyncService::SOURCE_KEY,
        ];

        $keysToSync = [];
        if ($syncAll) {
            $keysToSync = array_keys($manager->getServices());
            $this->info('Memulai sinkronisasi seluruh sumber data eksternal FireAware...');
        } else {
            if (! isset($sourceMap[$sourceInput])) {
                $this->error("Sumber '{$sourceInput}' tidak dikenali. Pilihan: firms, bmkg, airquality, all.");
                return Command::FAILURE;
            }
            $keysToSync = [$sourceMap[$sourceInput]];
        }

        $hasFailure = false;
        $summaryTable = [];

        foreach ($keysToSync as $key) {
            $service = $manager->getService($key);
            if (! $service) {
                continue;
            }

            $this->line("-> Memproses sinkronisasi: <comment>{$service->getSourceName()}</comment>...");
            $result = $service->sync('command');

            $statusText = match ($result->status) {
                'success' => '<info>BERHASIL</info>',
                'failed' => '<error>GAGAL</error>',
                'timeout' => '<comment>TIMEOUT</comment>',
                'not_configured' => '<fg=gray>BELUM DIKONFIGURASI</fg=gray>',
                default => $result->status,
            };

            if ($result->isFailed() || $result->isTimeout()) {
                $hasFailure = true;
            }

            $summaryTable[] = [
                $service->getSourceName(),
                strip_tags($statusText),
                $result->recordsProcessed,
                $result->recordsAdded,
                $result->recordsUpdated,
                $result->observationAt ? $result->observationAt->format('Y-m-d H:i') : '—',
                $result->errorMessage ? Str::limit($result->errorMessage, 40) : ($result->payloadSummary ?? '—'),
            ];
        }

        $this->newLine();
        $this->table(
            ['Sumber Data', 'Status', 'Diproses', 'Baru', 'Diperbarui', 'Waktu Observasi', 'Keterangan'],
            $summaryTable
        );

        if ($hasFailure) {
            $this->warn('Satu atau lebih sinkronisasi data mengalami kendala. Periksa log detail pada admin.');
            return Command::FAILURE;
        }

        $this->info('Proses sinkronisasi data eksternal selesai dengan aman.');
        return Command::SUCCESS;
    }
}
