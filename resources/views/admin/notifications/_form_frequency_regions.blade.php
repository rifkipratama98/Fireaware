<div class="row g-4 mb-4">
    {{-- Cakupan Wilayah Pemantauan --}}
    <div class="col-lg-6">
        <div class="panel p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between pb-2 border-bottom border-soft mb-3">
                    <h2 class="h6 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt text-primary"></i>
                        <span>Cakupan Wilayah Pemantauan</span>
                    </h2>
                    <span class="badge bg-mist text-primary border border-soft small">Grup 3</span>
                </div>
                <p class="small text-secondary mb-3">
                    Tentukan provinsi target penyiaran notifikasi. Bila tidak ada provinsi yang dicentang, sistem otomatis memantau <strong>seluruh wilayah Pulau Kalimantan</strong>.
                </p>

                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" id="btnSelectAllProvinces" style="font-size: 0.78rem;">
                        <i class="bi bi-check-all me-1"></i>Pilih Semua
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" id="btnClearAllProvinces" style="font-size: 0.78rem;">
                        <i class="bi bi-x-circle me-1"></i>Kosongkan
                    </button>
                </div>

                <div class="notify-region-grid mb-3">
                    @php
                        $monitored = $setting->monitored_provinces ?? [];
                    @endphp
                    @foreach ($provinces as $prov)
                        @php
                            $isChecked = in_array($prov->id, $monitored) || in_array((string)$prov->id, $monitored);
                        @endphp
                        <label class="notify-region-item {{ $isChecked ? 'is-selected' : '' }}" for="prov_{{ $prov->id }}">
                            <input class="form-check-input province-checkbox"
                                   type="checkbox"
                                   name="monitored_provinces[]"
                                   value="{{ $prov->id }}"
                                   id="prov_{{ $prov->id }}"
                                   {{ $isChecked ? 'checked' : '' }}>
                            <div class="min-w-0">
                                <span class="d-block fw-semibold text-dark small text-truncate">{{ $prov->name }}</span>
                                <span class="badge bg-light text-secondary border border-soft" style="font-size: 0.68rem;">{{ $prov->code ?? $prov->slug }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="p-2-5 bg-soft rounded-3 border border-soft small text-secondary">
                <i class="bi bi-info-circle text-primary me-1"></i>
                Pemberitahuan darurat lintas batas provinsi akan tetap diprioritaskan demi keselamatan umum.
            </div>
        </div>
    </div>

    {{-- Aturan Frekuensi Anti-Spam & Jam Hening --}}
    <div class="col-lg-6">
        <div class="panel p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between pb-2 border-bottom border-soft mb-3">
                    <h2 class="h6 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock text-primary"></i>
                        <span>Aturan Frekuensi Anti-Spam &amp; Jam Hening</span>
                    </h2>
                    <span class="badge bg-mist text-primary border border-soft small">Grup 4</span>
                </div>

                {{-- Frekuensi Limit --}}
                <div class="p-3 bg-light rounded-3 border border-soft mb-3">
                    <label for="frequency_limit_minutes" class="form-label small fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split text-primary"></i>
                        <span>Batas Minimum Jeda Antar Notifikasi Sejenis (Menit) <span class="text-danger">*</span></span>
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="number"
                               name="frequency_limit_minutes"
                               id="frequency_limit_minutes"
                               min="5"
                               max="1440"
                               class="form-control @error('frequency_limit_minutes') is-invalid @enderror"
                               value="{{ old('frequency_limit_minutes', $setting->frequency_limit_minutes) }}"
                               required>
                        <span class="input-group-text bg-light text-secondary fw-medium">Menit</span>
                    </div>
                    @error('frequency_limit_minutes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="form-text small mt-1" style="font-size: .75rem;">
                        Mencegah lonjakan spam jika anomali termal titik panas satelit terdeteksi berulang kali dalam jeda singkat.
                    </div>
                </div>

                {{-- Jam Hening (Quiet Hours) --}}
                <div class="p-3 bg-light rounded-3 border border-soft mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-check-label fw-bold text-dark d-flex align-items-center gap-2 mb-0" for="quiet_hours_enabled">
                            <i class="bi bi-moon-stars text-primary"></i>
                            <span>Aktifkan Jam Hening (Quiet Hours)</span>
                        </label>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="quiet_hours_enabled" id="quiet_hours_enabled" value="1" {{ old('quiet_hours_enabled', $setting->quiet_hours_enabled) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <p class="small text-secondary mb-2">
                        Notifikasi tingkat non-darurat akan ditekan secara otomatis selama rentang waktu malam hari.
                    </p>
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="quiet_hours_start" class="form-label small fw-semibold text-dark mb-1">Mulai Jam:</label>
                            <input type="time"
                                   name="quiet_hours_start"
                                   id="quiet_hours_start"
                                   class="form-control form-control-sm @error('quiet_hours_start') is-invalid @enderror"
                                   value="{{ old('quiet_hours_start', $setting->quiet_hours_start ?? '22:00') }}">
                        </div>
                        <div class="col-6">
                            <label for="quiet_hours_end" class="form-label small fw-semibold text-dark mb-1">Selesai Jam:</label>
                            <input type="time"
                                   name="quiet_hours_end"
                                   id="quiet_hours_end"
                                   class="form-control form-control-sm @error('quiet_hours_end') is-invalid @enderror"
                                   value="{{ old('quiet_hours_end', $setting->quiet_hours_end ?? '06:00') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan Konfigurasi Utama --}}
            <div class="pt-3 border-top border-soft d-flex justify-content-end">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 shadow-xs" id="btnOpenSaveConfirmModal">
                    <i class="bi bi-check-lg"></i>
                    <span>Simpan Perubahan Konfigurasi</span>
                </button>
            </div>
        </div>
    </div>
</div>
