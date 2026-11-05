{{-- Panduan Kategori ISPU Bahasa Sederhana --}}
<div class="panel p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h2 class="h5 fw-bold mb-1 text-dark">Panduan Kategori Kualitas Udara (ISPU)</h2>
            <p class="text-secondary small mb-0">
                Baku mutu Indeks Standar Pencemar Udara (ISPU) berdasarkan Peraturan Kementerian Lingkungan Hidup RI untuk konsentrasi PM2.5:
            </p>
        </div>
        <span class="badge bg-light text-primary border border-soft px-2.5 py-1.5 small fw-semibold">
            <i class="bi bi-patch-check me-1" aria-hidden="true"></i>Standar Nasional ISPU
        </span>
    </div>

    <div class="aq-category-grid">
        @foreach ($allCategories as $cat)
            @php
                $isCurrent = isset($category) && $category && strcasecmp($category['label'], $cat['label']) === 0;
            @endphp
            <div class="aq-category-item {{ $isCurrent ? 'is-current' : '' }}">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="aq-chip {{ $cat['chip'] }}">
                            <i class="bi {{ $cat['icon'] }}" aria-hidden="true"></i>
                            {{ $cat['label'] }}
                        </span>
                        <span class="small fw-bold text-dark">
                            {{ $cat['range'] }} µg/m³
                        </span>
                    </div>

                    @if ($isCurrent)
                        <span class="badge bg-primary text-white small px-2 py-1">
                            <i class="bi bi-geo-alt-fill me-1" aria-hidden="true"></i>Status Wilayah Ini
                        </span>
                    @else
                        <span class="small text-secondary fw-semibold">
                            {{ $cat['risk'] }}
                        </span>
                    @endif
                </div>

                <p class="aq-cat-desc mb-2">
                    {{ $cat['simple_desc'] }}
                </p>

                <div class="aq-cat-advice d-flex align-items-start gap-2 bg-light p-2 rounded border border-soft">
                    <i class="bi bi-check-circle-fill text-primary fs-6 mt-0.5" aria-hidden="true"></i>
                    <div>
                        <strong class="text-dark d-block small">Tindakan yang disarankan:</strong>
                        <span class="small text-secondary">{{ $cat['advice'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3 pt-3 border-top border-soft text-secondary small d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
            Nilai acuan di atas mengikuti Keputusan Menteri LHK No. 14 Tahun 2020 tentang ISPU.
        </span>
        <span class="fw-medium">Kategori dari sumber resmi selalu diutamakan.</span>
    </div>
</div>
