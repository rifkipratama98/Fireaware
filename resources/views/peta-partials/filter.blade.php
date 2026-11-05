<div class="panel p-3 p-lg-4 mb-3 shadow-sm border border-soft">
    <form id="mapFilterForm" class="map-filter" aria-label="Filter peta">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-soft">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-sliders text-primary"></i>
                <h2 class="h6 fw-bold mb-0 text-dark">Filter Wilayah &amp; Waktu</h2>
            </div>
            <button type="button" id="mapFilterReset" class="btn btn-link btn-sm p-0 text-secondary text-decoration-none small">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
            </button>
        </div>

        {{-- Pilihan Wilayah Provinsi --}}
        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary-soft mb-1" for="filterProvince">
                <i class="bi bi-geo-alt me-1 text-primary"></i>Provinsi
            </label>
            <select class="form-select form-select-sm" id="filterProvince" name="provinsi">
                <option value="">Seluruh 5 Provinsi Kalimantan</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->slug }}"
                        @if($filter['provinsi'] === $province->slug) selected @endif>
                        {{ $province->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Shortcut Rentang Waktu Cepat --}}
        <div class="mb-2">
            <label class="form-label small fw-semibold text-secondary-soft mb-1 d-block">
                <i class="bi bi-lightning-charge me-1 text-warning"></i>Preset Cepat
            </label>
            <div class="d-flex gap-1 flex-wrap">
                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 small" data-date-preset="0" style="font-size: .75rem;">Hari Ini</button>
                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 small" data-date-preset="3" style="font-size: .75rem;">3 Hari</button>
                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 small" data-date-preset="7" style="font-size: .75rem;">7 Hari</button>
                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 small" data-date-preset="14" style="font-size: .75rem;">14 Hari</button>
            </div>
        </div>

        {{-- Rentang Tanggal Manual --}}
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label small fw-semibold text-secondary-soft mb-1" for="filterFrom">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" id="filterFrom" name="dari"
                       value="{{ $filter['dari']?->format('Y-m-d') ?? '' }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-6">
                <label class="form-label small fw-semibold text-secondary-soft mb-1" for="filterTo">Sampai</label>
                <input type="date" class="form-control form-control-sm" id="filterTo" name="sampai"
                       value="{{ $filter['sampai']?->format('Y-m-d') ?? '' }}" max="{{ now()->format('Y-m-d') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold py-2 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" id="mapFilterApply">
            <i class="bi bi-funnel-fill"></i>
            <span>Terapkan Filter Parameter</span>
        </button>
    </form>
</div>
