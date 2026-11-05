@extends('layouts.app')

@section('title', $announcement->title . ' — Warta Resmi FireAware Kalimantan')

@section('content')
    <div class="py-3 bg-white border-bottom border-soft">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <a href="{{ route('announcements.index') }}" class="guide-back-nav mb-0">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengumuman
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="section-block bg-soft py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">

                    {{-- Panel Utama Pengumuman --}}
                    <article class="bulletin-detail-panel mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                            <span class="badge {{ $announcement->levelBadgeClass() }} px-3 py-1.5 fs-6 fw-bold">
                                <i class="bi {{ $announcement->levelIcon() }} me-1" aria-hidden="true"></i>
                                Status: {{ $announcement->levelLabel() }}
                            </span>

                            @if ($announcement->province)
                                <span class="region-chip">
                                    <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                                    Wilayah: {{ $announcement->province->name }}
                                </span>
                            @else
                                <span class="region-chip">
                                    <i class="bi bi-globe-asia-australia" aria-hidden="true"></i>
                                    Cakupan: Seluruh Kalimantan
                                </span>
                            @endif

                            <span class="badge bg-light text-primary border border-soft py-1 px-2.5 small fw-semibold">
                                <i class="bi bi-patch-check-fill text-success me-1" aria-hidden="true"></i> Sumber Terverifikasi
                            </span>

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
                                    <dt><i class="bi bi-patch-check text-success" aria-hidden="true"></i>Penerbit Resmi</dt>
                                    <dd>
                                        {{ $announcement->source_name ?? 'Pemerintah Daerah' }}
                                    </dd>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <dt><i class="bi bi-calendar-event text-primary" aria-hidden="true"></i>Waktu Rilis</dt>
                                    <dd>
                                        <time datetime="{{ $announcement->effectiveDate()->toIso8601String() }}">
                                            {{ $announcement->effectiveDate()->isoFormat('D MMM Y, HH:mm') }} WITA
                                        </time>
                                    </dd>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <dt><i class="bi bi-clock-history text-primary" aria-hidden="true"></i>Waktu Pembaruan</dt>
                                    <dd>
                                        {{ $announcement->updated_at->isoFormat('D MMM Y, HH:mm') }} WITA
                                    </dd>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <dt><i class="bi bi-shield-check text-primary" aria-hidden="true"></i>Tingkat Kedaruratan</dt>
                                    <dd class="fw-bold">
                                        {{ $announcement->levelLabel() }}
                                    </dd>
                                </div>
                            </div>
                        </div>

                        {{-- 8. Foto Dokumentasi Lingkungan / Kondisi Karhutla Pendukung --}}
                        <div class="bulletin-photo-card">
                            @if ($announcement->level == \App\Models\Announcement::LEVEL_DARURAT || $announcement->level == \App\Models\Announcement::LEVEL_WASPADA)
                                <img src="{{ asset('images/smoke-haze.jpg') }}"
                                     alt="Dokumentasi kondisi kabut asap dan potensi kebakaran lahan gambut di Kalimantan"
                                     loading="lazy"
                                     width="800"
                                     height="360">
                                <div class="photo-caption">
                                    <i class="bi bi-camera me-1" aria-hidden="true"></i>
                                    Dokumentasi lingkungan: Vegetasi lahan gambut dan kondisi atmosfer Kalimantan saat periode peningkatan kewaspadaan karhutla.
                                </div>
                            @else
                                <img src="{{ asset('images/conservation-action.jpg') }}"
                                     alt="Dokumentasi patroli kesiapsiagaan lingkungan di Kalimantan"
                                     loading="lazy"
                                     width="800"
                                     height="360">
                                <div class="photo-caption">
                                    <i class="bi bi-camera me-1" aria-hidden="true"></i>
                                    Dokumentasi lingkungan: Patroli lapangan satgas dan pemantauan berkala kawasan hutan dan lahan di Kalimantan.
                                </div>
                            @endif
                        </div>

                        {{-- Ringkasan Eksekutif --}}
                        @if ($announcement->summary)
                            <div class="bulletin-lead-box lead-{{ $announcement->level == 3 ? 'darurat' : ($announcement->level == 2 ? 'waspada' : 'info') }}">
                                <strong class="d-block small text-uppercase mb-1">
                                    <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Ringkasan Eksekutif:
                                </strong>
                                {{ $announcement->summary }}
                            </div>
                        @endif

                        {{-- Isi Teks Pengumuman --}}
                        <div class="bulletin-body-content">
                            @foreach ($announcement->bodyParagraphs() as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        {{-- 3 & 6. Panduan Tindakan Keselamatan Masyarakat Berdasarkan Level --}}
                        <div class="bulletin-safety-box">
                            <h3>
                                <i class="bi bi-shield-check" aria-hidden="true"></i>
                                Rekomendasi Kesiapsiagaan Masyarakat
                            </h3>
                            <ul>
                                @if ($announcement->level == \App\Models\Announcement::LEVEL_DARURAT)
                                    <li>Gunakan masker respirator N95 atau KN95 saat harus beraktivitas di luar rumah.</li>
                                    <li>Tutup rapat ventilasi ruangan dan nyalakan penjernih udara (air purifier) bila memungkinkan.</li>
                                    <li>Segera laporkan titik api baru ke posko darurat 112 atau posko BPBD terdekat.</li>
                                    <li>Kelompok rentan (anak-anak, ibu hamil, lansia) diimbau tetap berada di dalam ruangan.</li>
                                @elseif ($announcement->level == \App\Models\Announcement::LEVEL_WASPADA)
                                    <li>Pantau pembaruan indeks kualitas udara secara berkala sebelum beraktivitas di luar ruang.</li>
                                    <li>Dilarang keras membersihkan kebun atau membuka lahan pertanian dengan cara membakar.</li>
                                    <li>Sediakan persediaan masker standar partikulat dan obat-obatan rutin pernapasan di rumah.</li>
                                    <li>Laporkan asap mencurigakan di kawasan semak belukar atau lahan gambut kepada aparat desa.</li>
                                @else
                                    <li>Tingkatkan kewaspadaan terhadap potensi cuaca panas dan kekeringan lahan di sekitar lingkungan.</li>
                                    <li>Dukung tim Masyarakat Peduli Api (MPA) dalam menjaga kelestarian kawasan sekitar.</li>
                                    <li>Simpan nomor darurat posko penanggulangan bencana setempat.</li>
                                @endif
                            </ul>
                        </div>

                        {{-- 7. Sumber & Tautan Eksternal --}}
                        @if ($announcement->source_url)
                            <div class="p-3 bg-soft rounded-3 border border-soft mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="small text-secondary">
                                    <i class="bi bi-patch-check-fill text-success me-1"></i>
                                    Dokumen rilis resmi ini dapat ditinjau langsung melalui portal resmi penerbit (<strong>{{ $announcement->source_name }}</strong>).
                                </div>
                                <a href="{{ $announcement->source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
                                    Buka Sumber Asli <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    </article>

                    {{-- 9. Pengumuman Terbaru Lainnya --}}
                    @if ($recentAnnouncements->isNotEmpty())
                        <div class="mb-4">
                            <h2 class="h5 fw-bold mb-3 text-dark">
                                <i class="bi bi-newspaper me-1 text-primary" aria-hidden="true"></i>Pengumuman &amp; Peringatan Lainnya
                            </h2>
                            <div class="d-grid gap-3">
                                @foreach ($recentAnnouncements as $recent)
                                    <div class="p-3 bg-white rounded-3 border border-soft d-flex align-items-center justify-content-between gap-3 shadow-sm">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <span class="badge {{ $recent->levelBadgeClass() }} py-0.5 px-2 small fw-semibold">
                                                    {{ $recent->levelLabel() }}
                                                </span>
                                                <span class="small text-secondary">
                                                    {{ $recent->province?->name ?? 'Seluruh Kalimantan' }} &bull;
                                                    {{ $recent->effectiveDate()->isoFormat('D MMM Y, HH:mm') }} WITA
                                                </span>
                                            </div>
                                            <h3 class="h6 mb-0 fw-bold">
                                                <a href="{{ route('announcements.show', $recent->slug) }}" class="text-dark text-decoration-none">
                                                    {{ $recent->title }}
                                                </a>
                                            </h3>
                                        </div>
                                        <a href="{{ route('announcements.show', $recent->slug) }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
                                            Rincian <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
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
