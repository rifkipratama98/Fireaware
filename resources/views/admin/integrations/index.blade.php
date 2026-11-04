@extends('layouts.admin')

@section('title', 'Integrasi Data — Admin FireAware Kalimantan')
@section('page_title', 'Integrasi Data')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">Integrasi Sumber Data</h1>
            <p class="text-secondary-soft mb-0">
                Status koneksi, ketepatan sinkronisasi, dan ketersediaan data aktual dari penyedia eksternal.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-mist text-primary border p-2">
                <i class="bi bi-shield-lock me-1"></i>Kredensial API Tersimpan Aman di Server
            </span>
        </div>
    </div>

    {{-- Penjelasan Aturan Status --}}
    <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div>
            <strong>Pedoman Status Data:</strong> Status integrasi membedakan <em>Koneksi</em> (apakah konfigurasi/stasiun terdaftar), <em>Sinkronisasi</em> (apakah data mutakhir), dan <em>Ketersediaan</em> (apakah data ada di basis data). Keberadaan konfigurasi tidak disamakan dengan keberhasilan integrasi.
        </div>
    </div>

    {{-- Kartu Integrasi Rinci --}}
    <div class="row g-4 mb-4">
        @foreach ($integrations as $src)
            <div class="col-lg-4">
                @include('admin.partials.integration-card', ['src' => $src])
            </div>
        @endforeach
    </div>

    {{-- Parameter Spasial & Batas Wilayah Aman --}}
    <div class="panel p-3 p-md-4 mb-4">
        <h2 class="h6 fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-gear text-primary"></i>
            <span>Parameter Wilayah Ingesti Telemetri (Kalimantan Bounding Box)</span>
        </h2>
        <div class="row g-3 small">
            <div class="col-sm-6 col-md-3">
                <div class="text-secondary-soft">Batas Lintang Selatan</div>
                <div class="fw-bold font-monospace">-4.500000° S</div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="text-secondary-soft">Batas Lintang Utara</div>
                <div class="fw-bold font-monospace">+4.500000° N</div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="text-secondary-soft">Batas Bujur Barat</div>
                <div class="fw-bold font-monospace">+108.500000° E</div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="text-secondary-soft">Batas Bujur Timur</div>
                <div class="fw-bold font-monospace">+119.500000° E</div>
            </div>
        </div>
    </div>
@endsection
