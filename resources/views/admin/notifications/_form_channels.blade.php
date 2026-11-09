<div class="panel p-4 h-100">
    <div class="d-flex align-items-center justify-content-between pb-2 border-bottom border-soft mb-3">
        <h2 class="h6 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-toggle2-on text-primary"></i>
            <span>Sakelar Utama &amp; Kanal Pengiriman</span>
        </h2>
        <span class="badge bg-mist text-primary border border-soft small">Grup 1</span>
    </div>

    {{-- Master Switch --}}
    <div class="notify-switch-card mb-4 {{ old('is_enabled', $setting->is_enabled) ? 'is-active' : '' }}">
        <div class="d-flex align-items-center justify-content-between">
            <label class="form-check-label fw-bold text-dark d-flex align-items-center gap-2 cursor-pointer mb-0" for="is_enabled">
                <i class="bi bi-power text-primary fs-5"></i>
                <span>Sakelar Master Notifikasi Sistem</span>
            </label>
            <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_enabled" id="is_enabled" value="1" {{ old('is_enabled', $setting->is_enabled) ? 'checked' : '' }} style="width: 2.75rem; height: 1.4rem;">
            </div>
        </div>
        <p class="small text-secondary mb-0 mt-2">
            Mengontrol seluruh aktivitas pengiriman notifikasi sistem. Bila dinonaktifkan, seluruh proses evaluasi peringatan dini akan dihentikan sementara.
        </p>
    </div>

    <div class="d-flex flex-column gap-3">
        {{-- Kanal 1: Browser (Masyarakat) --}}
        <div class="p-3 rounded-3 border border-soft bg-light">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label class="form-check-label fw-bold text-dark d-flex align-items-center gap-2 mb-0" for="browser_enabled">
                    <i class="bi bi-broadcast text-primary"></i>
                    <span>Siaran Peramban Web (Browser Notification API)</span>
                </label>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="browser_enabled" id="browser_enabled" value="1" {{ old('browser_enabled', $setting->browser_enabled) ? 'checked' : '' }}>
                </div>
            </div>
            <p class="small text-secondary mb-0">
                Menyiarkan peringatan bahaya ke tab peramban masyarakat di portal publik FireAware secara langsung tanpa memerlukan akun login.
            </p>
        </div>

        {{-- Kanal 2: Email Posko --}}
        <div class="p-3 rounded-3 border border-soft bg-light">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-check-label fw-bold text-dark d-flex align-items-center gap-2 mb-0" for="email_enabled">
                    <i class="bi bi-envelope-at text-primary"></i>
                    <span>Notifikasi Email Posko Operasi</span>
                </label>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="email_enabled" id="email_enabled" value="1" {{ old('email_enabled', $setting->email_enabled) ? 'checked' : '' }}>
                </div>
            </div>
            <p class="small text-secondary mb-2">
                Mengirimkan surel peringatan dini langsung ke posko siaga karhutla dan instansi penanggulangan bencana daerah.
            </p>
            <div>
                <label for="email_recipients" class="form-label small fw-semibold text-dark mb-1">
                    Daftar Email Penerima Posko <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="email_recipients"
                       id="email_recipients"
                       class="form-control form-control-sm @error('email_recipients') is-invalid @enderror"
                       value="{{ old('email_recipients', $setting->email_recipients) }}"
                       placeholder="posko-siaga@fireaware.id, bpbd-kalsel@example.com">
                @error('email_recipients')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text small" style="font-size: .75rem;">
                    Pisahkan beberapa alamat email dengan tanda koma (,). Wajib diisi jika kanal email diaktifkan.
                </div>
            </div>
        </div>

        {{-- Kanal 3: Webhook Koordinasi --}}
        <div class="p-3 rounded-3 border border-soft bg-light">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-check-label fw-bold text-dark d-flex align-items-center gap-2 mb-0" for="webhook_enabled">
                    <i class="bi bi-hdd-network text-primary"></i>
                    <span>Integrasi Webhook Eksternal (HTTP POST)</span>
                </label>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="webhook_enabled" id="webhook_enabled" value="1" {{ old('webhook_enabled', $setting->webhook_enabled) ? 'checked' : '' }}>
                </div>
            </div>
            <p class="small text-secondary mb-2">
                Mengirimkan payload event JSON secara terenkripsi ke URL endpoint sistem koordinasi eksternal posko karhutla.
            </p>
            <div>
                <label for="webhook_url" class="form-label small fw-semibold text-dark mb-1">
                    URL Endpoint Webhook Posko <span class="text-danger">*</span>
                </label>
                <input type="url"
                       name="webhook_url"
                       id="webhook_url"
                       class="form-control form-control-sm font-monospace @error('webhook_url') is-invalid @enderror"
                       value="{{ old('webhook_url', $setting->webhook_url) }}"
                       placeholder="https://posko.kalimantan.go.id/api/v1/alerts">
                @error('webhook_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text small" style="font-size: .75rem;">
                    Sistem akan mengirimkan payload JSON event peringatan dengan batas waktu 5 detik. Wajib diisi jika kanal webhook diaktifkan.
                </div>
            </div>
        </div>
    </div>
</div>
