@extends('layouts.app')

@section('title', $announcement->title . ' — Warta Resmi FireAware')

@section('content')
    <div class="py-4 bg-white border-bottom border-soft">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <a href="{{ route('announcements.index') }}" class="guide-back-nav mb-0">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pengumuman
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="section-block bg-soft">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">

                    {{-- Panel Utama Pengumuman --}}
                    <article class="bulletin-detail-panel mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                            <span class="badge {{ $announcement->levelBadgeClass() }} px-3 py-1 fs-6 fw-semibold">
                                <i class="bi {{ $announcement->levelIcon() }} me-1"></i>
                                Status: {{ $announcement->levelLabel() }}
                            </span>

                            @if ($announcement->province)
                                <span class="region-chip">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    Wilayah: {{ $announcement->province->name }}
                                </span>
                            @else
                                <span class="region-chip">
                                    <i class="bi bi-globe-asia-australia"></i>
                                    Cakupan: Seluruh Kalimantan
                                </span>
                            @endif

                            @if ($announcement->is_demo)
                                <span class="badge-demo ms-auto">DEMO SIMULASI</span>
                            @endif
                        </div>

                        <h1 class="bulletin-detail-title">
                            {{ $announcement->title }}
                        </h1>

                        {{-- Metadata Rilis Resmi --}}
                        <div class="bulletin-meta-grid">
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-3">
                                    <dt>Penerbit Resmi</dt>
                                    <dd>
                                        <i class="bi bi-patch-check text-success me-1"></i>
                                        {{ $announcement->source_name ?? 'Pemerintah Daerah' }}
                                    </dd>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <dt>Waktu Rilis</dt>
                                    <dd>{{ $announcement->effectiveDate()->isoFormat('D MMM Y, HH:mm') }}</dd>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <dt>Wilayah</dt>
                                    <dd>{{ $announcement->province?->name ?? 'Kalimantan (Umum)' }}</dd>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <dt>Status Rilis</dt>
                                    <dd>
                                        <span class="text-success fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Diterbitkan
                                        </span>
                                    </dd>
                                </div>
                            </div>
                        </div>

                        {{-- Isi Pengumuman --}}
                        <div class="guide-prose">
                            @if ($announcement->summary)
                                <div class="lead text-secondary-soft mb-4 pb-3 border-bottom border-soft">
                                    {{ $announcement->summary }}
                                </div>
                            @endif

                            @foreach ($announcement->bodyParagraphs() as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        {{-- Sumber & Tautan Eksternal --}}
                        @if ($announcement->source_url)
                            <div class="p-3 bg-soft rounded-3 border border-soft mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="small text-secondary">
                                    <i class="bi bi-link-45deg me-1"></i>
                                    Dokumen / pengumuman asli dapat ditinjau melalui portal resmi penerbit.
                                </div>
                                <a href="{{ $announcement->source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
                                    Buka Sumber Asli <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    </article>

                    {{-- Pengumuman Terbaru Lainnya --}}
                    @if ($recentAnnouncements->isNotEmpty())
                        <div class="mb-4">
                            <h2 class="h5 fw-bold mb-3">Pengumuman &amp; Peringatan Lainnya</h2>
                            <div class="d-grid gap-3">
                                @foreach ($recentAnnouncements as $recent)
                                    <div class="p-3 bg-white rounded-3 border border-soft d-flex align-items-center justify-content-between gap-3">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge {{ $recent->levelBadgeClass() }} py-0 px-2" style="font-size: .72rem;">
                                                    {{ $recent->levelLabel() }}
                                                </span>
                                                <span class="small text-secondary-soft">
                                                    {{ $recent->province?->name ?? 'Seluruh Kalimantan' }} &bull;
                                                    {{ $recent->effectiveDate()->isoFormat('D MMM Y') }}
                                                </span>
                                            </div>
                                            <h3 class="h6 mb-0">
                                                <a href="{{ route('announcements.show', $recent->slug) }}" class="text-dark text-decoration-none">
                                                    {{ $recent->title }}
                                                </a>
                                            </h3>
                                        </div>
                                        <a href="{{ route('announcements.show', $recent->slug) }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
                                            Rincian <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </main>
@endsection
