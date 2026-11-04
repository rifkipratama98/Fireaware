@extends('layouts.admin')

@section('title', 'Konfigurasi Notifikasi — Admin FireAware Kalimantan')
@section('page_title', 'Konfigurasi Notifikasi')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">Konfigurasi Peringatan &amp; Notifikasi</h1>
            <p class="text-secondary-soft mb-0">
                Ambang batas deteksi dini bahaya asap, titik panas, dan kanal penyebaran informasi kewaspadaan.
            </p>
        </div>
    </div>

    {{-- Ambang Batas Otomatis --}}
    <div class="panel p-0 overflow-hidden mb-4">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <h2 class="h6 fw-bold mb-0">Aturan &amp; Ambang Batas Peringatan Dini</h2>
            <span class="badge bg-mist text-primary border">Parameter Aktif</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3 py-3">Parameter Indikator</th>
                        <th class="py-3">Tingkat Bahaya</th>
                        <th class="py-3">Aturan Pemicu</th>
                        <th class="py-3">Tindakan Sistem</th>
                        <th class="py-3 pe-3 text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($thresholds as $t)
                        <tr>
                            <td class="ps-3 fw-semibold">
                                <i class="bi bi-bell me-1 text-primary"></i>
                                {{ $t['parameter'] }}
                            </td>
                            <td>
                                <span class="badge {{ str_contains($t['level'], 'Darurat') ? 'bg-danger' : 'bg-warning' }} bg-opacity-10 {{ str_contains($t['level'], 'Darurat') ? 'text-danger' : 'text-warning' }} border">
                                    {{ $t['level'] }}
                                </span>
                            </td>
                            <td class="font-monospace small text-secondary-soft">{{ $t['rule'] }}</td>
                            <td class="small">{{ $t['action'] }}</td>
                            <td class="pe-3 text-end">
                                <span class="badge bg-success bg-opacity-10 text-success border">{{ $t['status'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kanal Pengiriman Notifikasi --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="panel p-3 p-md-4 h-100">
                <h2 class="h6 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-broadcast text-primary"></i>
                    <span>Kanal Pengiriman Peringatan</span>
                </h2>
                <div class="list-group list-group-flush small">
                    @foreach ($channels as $c)
                        <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-3">
                            <div>
                                <div class="fw-semibold">{{ $c['name'] }}</div>
                                <div class="text-secondary-soft font-monospace">{{ $c['target'] }}</div>
                                <div class="text-secondary-soft opacity-75">{{ $c['type'] }}</div>
                            </div>
                            <span class="badge {{ $c['is_active'] ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} border">
                                {{ $c['is_active'] ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Log Riwayat Peringatan Terakhir --}}
        <div class="col-lg-6">
            <div class="panel p-3 p-md-4 h-100">
                <h2 class="h6 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>
                    <span>Peringatan Resmi Terakhir Yang Diterbitkan</span>
                </h2>
                @if ($recentTriggers->isNotEmpty())
                    <div class="list-group list-group-flush small">
                        @foreach ($recentTriggers as $trig)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge {{ $trig->levelBadgeClass() }}">{{ $trig->levelLabel() }}</span>
                                    <span class="text-secondary-soft">{{ $trig->effectiveDate()->diffForHumans() }}</span>
                                </div>
                                <div class="fw-semibold">{{ $trig->title }}</div>
                                <div class="text-secondary-soft text-truncate">{{ $trig->excerpt(100) }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-secondary-soft small py-4 text-center">
                        Tidak ada peringatan darurat/waspada yang aktif saat ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
