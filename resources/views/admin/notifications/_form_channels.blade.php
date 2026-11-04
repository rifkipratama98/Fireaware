<div class="panel p-4 h-100">
    <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-toggle2-on text-primary"></i>
        <span>Sakelar Utama &amp; Kanal Pengiriman</span>
    </h2>

    {{-- Master Switch --}}
    <div class="p-3 bg-soft rounded-3 border border-soft mb-4">
        <div class="form-check form-switch mb-1">
            <input class="form-check-input" type="checkbox" role="switch" name="is_enabled" id="is_enabled" value="1" {{ old('is_enabled', $setting->is_enabled) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark" for="is_enabled">
                Sakelar Master Notifikasi Sistem
            </label>
        </div>
        <div class="small text-secondary-soft ps-4">
            Mengontrol seluruh aktivitas pengiriman notifikasi. Bila dinonaktifkan, seluruh proses evaluasi peringatan dini akan dihentikan sementara.
        </div>
    </div>

    {{-- Kanal 1: Browser (Masyarakat) --}}
    <div class="mb-3 pb-3 border-bottom border-soft">
        <div class="form-check form-switch mb-1">
            <input class="form-check-input" type="checkbox" role="switch" name="browser_enabled" id="browser_enabled" value="1" {{ old('browser_enabled', $setting->browser_enabled) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark" for="browser_enabled">
                Siaran Peramban Web (Browser Notification API)
            </label>
        </div>
        <div class="small text-secondary-soft ps-4">
            Menyiarkan peringatan bahaya ke tab peramban masyarakat di portal publik FireAware secara langsung tanpa memerlukan akun login.
        </div>
    </div>

    {{-- Kanal 2: Email Posko --}}
    <div class="mb-3 pb-3 border-bottom border-soft">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" role="switch" name="email_enabled" id="email_enabled" value="1" {{ old('email_enabled', $setting->email_enabled) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark" for="email_enabled">
                Notifikasi Email Posko Operasi
            </label>
        </div>
        <div class="ps-4">
            <label for="email_recipients" class="form-label small text-secondary-soft mb-1">Daftar Email Penerima Posko:</label>
            <input type="text"
                   name="email_recipients"
                   id="email_recipients"
                   class="form-control form-control-sm @error('email_recipients') is-invalid @enderror"
                   value="{{ old('email_recipients', $setting->email_recipients) }}"
                   placeholder="posko-siaga@kalsel.go.id, bpbd@kalteng.go.id">
            @error('email_recipients')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text small" style="font-size: .75rem;">Pisahkan beberapa alamat email dengan tanda koma (,).</div>
        </div>
    </div>

    {{-- Kanal 3: Webhook Koordinasi --}}
    <div class="mb-0">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" role="switch" name="webhook_enabled" id="webhook_enabled" value="1" {{ old('webhook_enabled', $setting->webhook_enabled) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark" for="webhook_enabled">
                Integrasi Webhook Eksternal (HTTP POST)
            </label>
        </div>
        <div class="ps-4">
            <label for="webhook_url" class="form-label small text-secondary-soft mb-1">URL Endpoint Webhook Posko:</label>
            <input type="url"
                   name="webhook_url"
                   id="webhook_url"
                   class="form-control form-control-sm @error('webhook_url') is-invalid @enderror"
                   value="{{ old('webhook_url', $setting->webhook_url) }}"
                   placeholder="https://posko.kalimantan.go.id/api/v1/alerts">
            @error('webhook_url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text small" style="font-size: .75rem;">Sistem akan mengirimkan payload JSON event peringatan dengan batas waktu 5 detik.</div>
        </div>
    </div>
</div>
