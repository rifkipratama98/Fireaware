@extends('layouts.admin')

@section('title', 'Dashboard — FireAware')
@section('page_title', 'Dashboard')

@section('content')
    <x-page-header title="Ringkasan"
                   description="Gambaran umum indikator kebakaran wilayah Kalimantan." />

    <div class="row g-4 mb-4">
        @foreach ([
            ['bi-fire', 'Titik Panas (30 hari)', '128', 'text-primary'],
            ['bi-clock-history', 'Kejadian Terverifikasi', '34', 'text-primary'],
            ['bi-map', 'Wilayah Risiko Tinggi', '23', 'text-warning'],
            ['bi-check2-circle', 'Respons Ditindaklanjuti', '19', 'text-success'],
        ] as [$icon, $label, $value, $color])
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="stat-icon bg-mist {{ $color }}"><i class="bi {{ $icon }}"></i></span>
                        <div>
                            <div class="stat-value">{{ $value }}</div>
                            <div class="stat-label text-secondary-soft">{{ $label }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="panel p-4">
                <h2 class="h6 fw-semibold mb-3">Tren Titik Panas</h2>
                <div class="bg-soft rounded-3 d-flex align-items-center justify-content-center" style="height:260px">
                    <span class="text-secondary-soft small">(Area grafik — siap diisi data)</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel p-4">
                <h2 class="h6 fw-semibold mb-3">Peringatan Terakhir</h2>
                <ul class="list-unstyled d-grid gap-3 mb-0">
                    @foreach ([
                        ['warning', 'Kadar udara ekstrem', 'Kota Banjarbaru — 2 jam lalu'],
                        ['info', 'Titik panas meningkat', 'Kab. Kotawaringin Timur — 5 jam lalu'],
                        ['success', 'Respons selesai', 'Kab. Barito Kuala — 1 hari lalu'],
                    ] as [$type, $title, $meta])
                        <li>
                            <x-alert :type="$type" :dismissible="false">
                                <div class="fw-semibold small">{{ $title }}</div>
                                <div class="small opacity-75">{{ $meta }}</div>
                            </x-alert>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection