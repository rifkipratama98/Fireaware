<div class="panel p-4 h-100">
    <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-funnel text-primary"></i>
        <span>Kriteria Informasi Layak Beritahu &amp; Ambang Batas</span>
    </h2>

    {{-- Kriteria 1: Titik Panas --}}
    <div class="p-3 bg-soft rounded-3 border border-soft mb-3">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" role="switch" name="notify_hotspots" id="notify_hotspots" value="1" {{ old('notify_hotspots', $setting->notify_hotspots) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark" for="notify_hotspots">
                Deteksi Titik Panas Satelit (Hotspot)
            </label>
        </div>
        <div class="row g-2 ps-4">
            <div class="col-6">
                <label for="hotspot_min_confidence" class="form-label small text-secondary-soft mb-1">Min. Kepercayaan (%):</label>
                <input type="number"
                       name="hotspot_min_confidence"
                       id="hotspot_min_confidence"
                       min="0"
                       max="100"
                       class="form-control form-control-sm @error('hotspot_min_confidence') is-invalid @enderror"
                       value="{{ old('hotspot_min_confidence', $setting->hotspot_min_confidence) }}">
                @error('hotspot_min_confidence')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-6">
                <label for="hotspot_min_frp" class="form-label small text-secondary-soft mb-1">Min. Daya FRP (MW):</label>
                <input type="number"
                       step="0.1"
                       name="hotspot_min_frp"
                       id="hotspot_min_frp"
                       min="0"
                       class="form-control form-control-sm @error('hotspot_min_frp') is-invalid @enderror"
                       value="{{ old('hotspot_min_frp', $setting->hotspot_min_frp) }}">
                @error('hotspot_min_frp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Kriteria 2: Kualitas Udara --}}
    <div class="p-3 bg-soft rounded-3 border border-soft mb-3">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" role="switch" name="notify_air_quality" id="notify_air_quality" value="1" {{ old('notify_air_quality', $setting->notify_air_quality) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark" for="notify_air_quality">
                Penurunan Kualitas Udara (PM2.5)
            </label>
        </div>
        <div class="ps-4">
            <label for="air_quality_min_pm25" class="form-label small text-secondary-soft mb-1">Min. Konsentrasi PM2.5 (µg/m³):</label>
            <input type="number"
                   step="0.1"
                   name="air_quality_min_pm25"
                   id="air_quality_min_pm25"
                   min="0"
                   class="form-control form-control-sm @error('air_quality_min_pm25') is-invalid @enderror"
                   value="{{ old('air_quality_min_pm25', $setting->air_quality_min_pm25) }}">
            @error('air_quality_min_pm25')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text small" style="font-size: .75rem;">Standar ISPU Indonesia: &gt; 55.4 µg/m³ (Tidak Sehat), &gt; 150.4 µg/m³ (Sangat Tidak Sehat).</div>
        </div>
    </div>

    {{-- Kriteria 3: Pengumuman Resmi --}}
    <div class="p-3 bg-soft rounded-3 border border-soft mb-0">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" role="switch" name="notify_announcements" id="notify_announcements" value="1" {{ old('notify_announcements', $setting->notify_announcements) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark" for="notify_announcements">
                Pengumuman &amp; Peringatan Dini Resmi
            </label>
        </div>
        <div class="ps-4">
            <label for="announcement_min_level" class="form-label small text-secondary-soft mb-1">Tingkat Bahaya Minimum:</label>
            <select name="announcement_min_level" id="announcement_min_level" class="form-select form-select-sm">
                <option value="1" {{ old('announcement_min_level', $setting->announcement_min_level) == 1 ? 'selected' : '' }}>Level 1: Informasi, Waspada, &amp; Siaga Darurat</option>
                <option value="2" {{ old('announcement_min_level', $setting->announcement_min_level) == 2 ? 'selected' : '' }}>Level 2: Hanya Waspada &amp; Siaga Darurat (Direkomendasikan)</option>
                <option value="3" {{ old('announcement_min_level', $setting->announcement_min_level) == 3 ? 'selected' : '' }}>Level 3: Khusus Siaga Darurat Saja</option>
            </select>
        </div>
    </div>
</div>
