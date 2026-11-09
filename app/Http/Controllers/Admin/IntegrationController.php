<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SyncLog;
use App\Services\Integrations\IntegrationManagerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IntegrationController extends Controller
{
    public function __construct(
        private readonly IntegrationManagerService $manager
    ) {
    }

    public function index(Request $request): View
    {
        $sourceFilter = trim((string) $request->query('sumber', ''));
        $statusFilter = trim((string) $request->query('status', ''));

        $integrations = $this->manager->getIntegrationStatuses();
        $logs = $this->manager->getRecentLogs(
            perPage: 10,
            sourceFilter: $sourceFilter !== '' ? $sourceFilter : null,
            statusFilter: $statusFilter !== '' ? $statusFilter : null
        );
        $stats = $this->manager->getStats();
        $chartData = $this->getSyncActivityChartData();
        $healthMetrics = $this->getHealthMetrics($stats);

        return view('admin.integrations.index', [
            'integrations'   => $integrations,
            'logs'           => $logs,
            'stats'          => $stats,
            'sourceFilter'   => $sourceFilter,
            'statusFilter'   => $statusFilter,
            'chartData'      => $chartData,
            'healthMetrics'  => $healthMetrics,
        ]);
    }

    public function sync(Request $request, string $source): RedirectResponse
    {
        $userId = auth()->id();

        if ($source === 'all') {
            $results = $this->manager->syncAll(trigger: 'manual', userId: $userId);
            $totalAdded = 0;
            $totalUpdated = 0;
            $hasFailure = false;

            foreach ($results as $res) {
                $totalAdded += $res->recordsAdded;
                $totalUpdated += $res->recordsUpdated;
                if ($res->isFailed() || $res->isTimeout()) {
                    $hasFailure = true;
                }
            }

            if ($hasFailure) {
                return redirect()->route('admin.integrations.index')
                    ->with('warning', "Sinkronisasi selesai sebagian. {$totalAdded} rekaman baru ditambahkan, namun beberapa sumber mengalami kendala koneksi.");
            }

            return redirect()->route('admin.integrations.index')
                ->with('success', "Sinkronisasi seluruh sumber data berhasil diproses ({$totalAdded} data baru, {$totalUpdated} diperbarui).");
        }

        $service = $this->manager->getService($source);
        if (! $service) {
            return redirect()->route('admin.integrations.index')
                ->with('error', "Sumber data '{$source}' tidak dikenali oleh sistem.");
        }

        $result = $this->manager->sync($source, trigger: 'manual', userId: $userId);

        if ($result->isNotConfigured()) {
            return redirect()->route('admin.integrations.index')
                ->with('warning', "<strong>{$service->getSourceName()}</strong> belum dapat disinkronkan karena parameter konfigurasi API belum disetel pada server.");
        }

        if ($result->isTimeout()) {
            return redirect()->route('admin.integrations.index')
                ->with('error', "Sinkronisasi <strong>{$service->getSourceName()}</strong> gagal: {$result->errorMessage}");
        }

        if ($result->isFailed()) {
            return redirect()->route('admin.integrations.index')
                ->with('error', "Sinkronisasi <strong>{$service->getSourceName()}</strong> mengalami kendala: {$result->errorMessage}");
        }

        $feedback = "Sinkronisasi <strong>{$service->getSourceName()}</strong> berhasil dijalankan.";
        if ($result->recordsProcessed > 0) {
            $feedback .= " Diproses {$result->recordsProcessed} rekaman ({$result->recordsAdded} baru, {$result->recordsUpdated} diperbarui).";
        } else {
            $feedback .= " Tidak ada data baru pada periode observasi saat ini.";
        }

        return redirect()->route('admin.integrations.index')->with('success', $feedback);
    }

    /**
     * Hitung agregasi volume aktivitas sinkronisasi selama 7 hari terakhir.
     */
    private function getSyncActivityChartData(): array
    {
        $startDate = now()->subDays(6)->startOfDay();

        $logs = SyncLog::query()
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw("SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count"),
                DB::raw("SUM(CASE WHEN status IN ('failed', 'timeout') THEN 1 ELSE 0 END) as failed_count"),
                DB::raw("SUM(records_processed) as total_records")
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $successValues = [];
        $failedValues = [];
        $recordValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $dateStr = $d->format('Y-m-d');
            $labels[] = $d->translatedFormat('d M');
            $row = $logs->get($dateStr);
            $successValues[] = (int) ($row?->success_count ?? 0);
            $failedValues[] = (int) ($row?->failed_count ?? 0);
            $recordValues[] = (int) ($row?->total_records ?? 0);
        }

        return [
            'labels'   => $labels,
            'success'  => $successValues,
            'failed'   => $failedValues,
            'records'  => $recordValues,
        ];
    }

    /**
     * Hitung metrik kesehatan infrastruktur telemetri.
     */
    private function getHealthMetrics(array $stats): array
    {
        $total = $stats['total'] ?? 0;
        $success = $stats['success'] ?? 0;
        $successRate = $total > 0 ? round(($success / $total) * 100, 1) : 100.0;
        $avgDurationMs = (int) round((float) (SyncLog::whereNotNull('duration_ms')->avg('duration_ms') ?? 0));

        return [
            'success_rate'    => $successRate,
            'avg_duration_ms' => $avgDurationMs,
            'last_success_at' => SyncLog::where('status', SyncLog::STATUS_SUCCESS)->max('completed_at'),
            'last_failure_at' => SyncLog::whereIn('status', [SyncLog::STATUS_FAILED, SyncLog::STATUS_TIMEOUT])->max('created_at'),
        ];
    }

}
