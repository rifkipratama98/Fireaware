<div class="panel p-4 h-100">
    <div class="d-flex align-items-center justify-content-between pb-2 border-bottom border-soft mb-3">
        <h2 class="h6 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-funnel text-primary"></i>
            <span>Kriteria &amp; Ambang Batas Peringatan</span>
        </h2>
        <span class="badge bg-mist text-primary border border-soft small">Grup 2</span>
    </div>

    <div class="d-flex flex-column gap-3">
        {{-- Kriteria 1: Deteksi Titik Panas Satelit (Hotspot) --}}
        <div class="p-3 rounded-3 border border-soft bg-light">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-check-label fw-bold text-dark d-flex align-items-center gap-2 mb-0" for="notify_hotspots">
                    <i class="bi bi-fire text-danger"></i>
                    <span>Deteksi Titik Panas Satelit (Hotspot)</span>
                </label>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="notify_hotspots" id="notify_hotspots" value="1" {{ old('notify_hotspots', $setting->notify_hotspots) ? 'checked' : '' }}>
                </div>
            </div>
            <p class="small text-secondary mb-2">
                Memicu notifikasi saat anomali termal satelit NASA FIRMS melampaui ambang batas kepercayaan dan intensitas daya api.
            </p>
            <div class="row g-2">
                <div class="col-6">
                    <label for="hotspot_min_confidence" class="form-label small fw-semibold text-dark mb-1">
                        Min. Kepercayaan (%) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="number"
                               name="hotspot_min_confidence"
                               id="hotspot_min_confidence"
                               min="0"
                               max="100"
                               class="form-control @error('hotspot_min_confidence') is-invalid @enderror"
                               value="{{ old('hotspot_min_confidence', $setting->hotspot_min_confidence) }}">
                        <span class="input-group-text bg-light text-secondary">%</span>
                    </div>
                    @error('hotspot_min_confidence')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="hotspot_min_frp" class="form-label small fw-semibold text-dark mb-1">
                        Min. Daya FRP (MW) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="number"
                               step="0.1"
                               name="hotspot_min_frp"
                               id="hotspot_min_frp"
                               min="0"
                               class="form-control @error('hotspot_min_frp') is-invalid @enderror"
                               value="{{ old('hotspot_min_frp', $setting->hotspot_min_frp) }}">
                        <span class="input-group-text bg-light text-secondary">MW</span>
                    </div>
                    @error('hotspot_min_frp')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-text small mt-1" style="font-size: .75rem;">
                Standar NASA FIRMS: Kepercayaan &ge; 80% &amp; FRP &ge; 50 MW menandakan kebakaran aktif intensitas tinggi.
            </div>
        </div>

        {{-- Kriteria 2: Penurunan Kualitas Udara (PM2.5) --}}
        <div class="p-3 rounded-3 border border-soft bg-light">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-check-label fw-bold text-dark d-flex align-items-center gap-2 mb-0" for="notify_air_quality">
                    <i class="bi bi-wind text-primary"></i>
                    <span>Penurunan Kualitas Udara (PM2.5)</span>
                </label>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="notify_air_quality" id="notify_air_quality" value="1" {{ old('notify_air_quality', $setting->notify_air_quality) ? 'checked' : '' }}>
                </div>
            </div>
            <p class="small text-secondary mb-2">
                Menyebarkan peringatan otomatis saat konsentrasi partikulat halus PM2.5 di stasiun pemantau memasuki kategori bahaya kesehatan.
            </p>
            <div>
                <label for="air_quality_min_pm25" class="form-label small fw-semibold text-dark mb-1">
                    Min. Konsentrasi PM2.5 (µg/m³) <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number"
                           step="0.1"
                           name="air_quality_min_pm25"
                           id="air_quality_min_pm25"
                           min="0"
                           class="form-control @error('air_quality_min_pm25') is-invalid @enderror"
                           value="{{ old('air_quality_min_pm25', $setting->air_quality_min_pm25) }}">
                    <span class="input-group-text bg-light text-secondary">µg/m³</span>
                </div>
                @error('air_quality_min_pm25')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text small mt-1" style="font-size: .75rem;">
                    Berdasarkan Standar ISPU KLHK: &gt; 55.4 µg/m³ (Tidak Sehat), &gt; 150.4 µg/m³ (Sangat Tidak Sehat).
                </div>
            </div>
        </div>

        {{-- Kriteria 3: Pengumuman & Peringatan Dini Resmi --}}
        <div class="p-3 rounded-3 border border-soft bg-light">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-check-label fw-bold text-dark d-flex align-items-center gap-2 mb-0" for="notify_announcements">
                    <i class="bi bi-megaphone text-warning"></i>
                    <span>Pengumuman &amp; Peringatan Dini Resmi</span>
                </label>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="notify_announcements" id="notify_announcements" value="1" {{ old('notify_announcements', $setting->notify_announcements) ? 'checked' : '' }}>
                </div>
            </div>
            <p class="small text-secondary mb-2">
                Menyiarkan warta resmi kebencanaan dari BPBD dan BMKG ke kanal aktif berdasarkan batas level darurat.
            </p>
            <div>
                <label for="announcement_min_level" class="form-label small fw-semibold text-dark mb-1">
                    Tingkat Bahaya Minimum <span class="text-danger">*</span>
                </label>
                <select name="announcement_min_level" id="announcement_min_level" class="form-select form-select-sm">
                    <option value="1" {{ old('announcement_min_level', $setting->announcement_min_level) == 1 ? 'selected' : '' }}>
                        Level 1: Semua (Informasi, Waspada, &amp; Siaga Darurat)
                    </option>
                    <option value="2" {{ old('announcement_min_level', $setting->announcement_min_level) == 2 ? 'selected' : '' }}>
                        Level 2: Hanya Waspada &amp; Siaga Darurat (Rekomendasi)
                    </option>
                    <option value="3" {{ old('announcement_min_level', $setting->announcement_min_level) == 3 ? 'selected' : '' }}>
                        Level 3: Khusus Siaga Darurat Saja
                    </option>
                </select>
                <div class="form-text small mt-1" style="font-size: .75rem;">
                    Tingkat yang lebih rendah akan disaring demi menjaga relevansi siaran publik.
                </div>
            </div>
        </div>
    </div>
</div>
