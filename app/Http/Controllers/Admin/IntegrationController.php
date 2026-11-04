<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Integrations\IntegrationManagerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('admin.integrations.index', [
            'integrations' => $integrations,
            'logs' => $logs,
            'stats' => $stats,
            'sourceFilter' => $sourceFilter,
            'statusFilter' => $statusFilter,
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
}
