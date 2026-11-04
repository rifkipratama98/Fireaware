<div class="row g-4 mb-4">
    {{-- Cakupan Wilayah Pemantauan --}}
    <div class="col-lg-6">
        <div class="panel p-4 h-100">
            <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt text-primary"></i>
                <span>Cakupan Wilayah Pemantauan</span>
            </h2>
            <p class="small text-secondary-soft mb-3">
                Pilih provinsi yang akan dipantau. Bila tidak ada provinsi yang dipilih, sistem otomatis memantau <strong>seluruh wilayah Pulau Kalimantan</strong>.
            </p>

            <div class="d-flex flex-column gap-2">
                @php
                    $monitored = $setting->monitored_provinces ?? [];
                @endphp
                @foreach ($provinces as $prov)
                    <div class="form-check p-2 rounded-2 border border-soft bg-soft">
                        <input class="form-check-input ms-0 me-2"
                               type="checkbox"
                               name="monitored_provinces[]"
                               value="{{ $prov->id }}"
                               id="prov_{{ $prov->id }}"
                               {{ in_array($prov->id, $monitored) || in_array((string)$prov->id, $monitored) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small" for="prov_{{ $prov->id }}">
                            {{ $prov->name }}
                            <span class="text-secondary-soft fw-normal">({{ $prov->code ?? $prov->slug }})</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Aturan Frekuensi Anti-Spam & Jam Hening --}}
    <div class="col-lg-6">
        <div class="panel p-4 h-100">
            <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock text-primary"></i>
                <span>Aturan Frekuensi Anti-Spam &amp; Jam Hening</span>
            </h2>

            {{-- Frekuensi Limit --}}
            <div class="mb-4">
                <label for="frequency_limit_minutes" class="form-label small fw-semibold text-dark mb-1">
                    Batas Minimum Jeda Antar Notifikasi Sejenis (Menit):
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
                    <span class="input-group-text bg-light text-secondary-soft">Menit</span>
                </div>
                @error('frequency_limit_minutes')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text small" style="font-size: .75rem;">Mencegah lonjakan spam notifikasi jika anomali titik panas berulang dalam waktu singkat.</div>
            </div>

            {{-- Jam Hening (Quiet Hours) --}}
            <div class="p-3 bg-soft rounded-3 border border-soft mb-4">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" name="quiet_hours_enabled" id="quiet_hours_enabled" value="1" {{ old('quiet_hours_enabled', $setting->quiet_hours_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark" for="quiet_hours_enabled">
                        Aktifkan Jam Hening (Quiet Hours)
                    </label>
                </div>
                <div class="row g-2 ps-4">
                    <div class="col-6">
                        <label for="quiet_hours_start" class="form-label small text-secondary-soft mb-1">Mulai Jam:</label>
                        <input type="time"
                               name="quiet_hours_start"
                               id="quiet_hours_start"
                               class="form-control form-control-sm @error('quiet_hours_start') is-invalid @enderror"
                               value="{{ old('quiet_hours_start', $setting->quiet_hours_start ?? '22:00') }}">
                    </div>
                    <div class="col-6">
                        <label for="quiet_hours_end" class="form-label small text-secondary-soft mb-1">Selesai Jam:</label>
                        <input type="time"
                               name="quiet_hours_end"
                               id="quiet_hours_end"
                               class="form-control form-control-sm @error('quiet_hours_end') is-invalid @enderror"
                               value="{{ old('quiet_hours_end', $setting->quiet_hours_end ?? '06:00') }}">
                    </div>
                </div>
                <div class="form-text small ps-4 mt-1" style="font-size: .75rem;">Peringatan tingkat non-darurat akan ditekan selama jam hening aktif.</div>
            </div>

            {{-- Tombol Simpan Konfigurasi --}}
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" id="btnOpenSaveConfirmModal">
                    <i class="bi bi-check-lg"></i>
                    <span>Simpan Perubahan Konfigurasi</span>
                </button>
            </div>
        </div>
    </div>
</div>
