<form id="mapFilterForm" class="map-filter" aria-label="Filter peta">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h6 fw-semibold mb-0">Filter</h2>
        <button type="button" id="mapFilterReset" class="btn btn-link btn-sm p-0 text-secondary">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
        </button>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold" for="filterProvince">Provinsi</label>
        <select class="form-select" id="filterProvince" name="provinsi">
            <option value="">Semua provinsi</option>
            @foreach ($provinces as $province)
                <option value="{{ $province->slug }}"
                    @if($filter['provinsi'] === $province->slug) selected @endif>
                    {{ $province->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold" for="filterFrom">Dari tanggal</label>
        <input type="date" class="form-control" id="filterFrom" name="dari"
               value="{{ $filter['dari']?->format('Y-m-d') ?? '' }}" max="{{ now()->format('Y-m-d') }}">
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold" for="filterTo">Sampai tanggal</label>
        <input type="date" class="form-control" id="filterTo" name="sampai"
               value="{{ $filter['sampai']?->format('Y-m-d') ?? '' }}" max="{{ now()->format('Y-m-d') }}">
    </div>

    <button type="submit" class="btn btn-primary w-100" id="mapFilterApply">
        <i class="bi bi-funnel me-1"></i>Terapkan Filter
    </button>
</form>
