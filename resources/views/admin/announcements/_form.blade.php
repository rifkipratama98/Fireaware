<div class="row g-4">
    {{-- Kolom Kiri: Konten Utama --}}
    <div class="col-lg-8">
        <div class="panel p-4 mb-4">
            <h2 class="h6 fw-semibold mb-3 pb-2 border-bottom border-soft">Informasi Pengumuman / Rilis Resmi</h2>

            {{-- Judul --}}
            <div class="mb-3">
                <label for="title" class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $announcement->title) }}"
                       placeholder="Contoh: Waspada Kenaikan Titik Panas di Wilayah Pulang Pisau dan Kapuas"
                       required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Slug --}}
            <div class="mb-3">
                <label for="slug" class="form-label">
                    Slug URL <small class="text-muted fw-normal">(Biarkan kosong untuk dibuat otomatis)</small>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted small">/pengumuman/</span>
                    <input type="text"
                           name="slug"
                           id="slug"
                           class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $announcement->slug) }}"
                           placeholder="waspada-kenaikan-titik-panas">
                </div>
                @error('slug')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Ringkasan --}}
            <div class="mb-3">
                <label for="summary" class="form-label">Ringkasan Singkat</label>
                <textarea name="summary"
                          id="summary"
                          rows="3"
                          class="form-control @error('summary') is-invalid @enderror"
                          placeholder="Jelaskan ringkasan singkat dalam 1-2 kalimat untuk tampilan feed rilis...">{{ old('summary', $announcement->summary) }}</textarea>
                @error('summary')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Isi Teks Lengkap --}}
            <div class="mb-0">
                <label for="body" class="form-label">Isi Teks Lengkap Pengumuman <span class="text-danger">*</span></label>
                <textarea name="body"
                          id="body"
                          rows="12"
                          class="form-control @error('body') is-invalid @enderror"
                          placeholder="Tuliskan isi rilis resmi, poin imbauan keselamatan, atau detail status kebencanaan secara jelas..."
                          required>{{ old('body', $announcement->body) }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Panel Instansi & Sumber --}}
        <div class="panel p-4">
            <h2 class="h6 fw-semibold mb-3 pb-2 border-bottom border-soft">Instansi Penerbit / Sumber Rujukan</h2>

            <div class="mb-3">
                <label for="source_name" class="form-label">Nama Instansi / Penerbit Resmi <span class="text-danger">*</span></label>
                <input type="text"
                       name="source_name"
                       id="source_name"
                       class="form-control @error('source_name') is-invalid @enderror"
                       value="{{ old('source_name', $announcement->source_name) }}"
                       placeholder="Contoh: BPBD Provinsi Kalimantan Tengah, BMKG, atau KLHK"
                       required>
                @error('source_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-0">
                <label for="source_url" class="form-label">Tautan / URL Rilis Resmi <small class="text-muted fw-normal">(Opsional)</small></label>
                <input type="url"
                       name="source_url"
                       id="source_url"
                       class="form-control @error('source_url') is-invalid @enderror"
                       value="{{ old('source_url', $announcement->source_url) }}"
                       placeholder="https://bpbd.kalteng.go.id/rilis-kebakaran-terkini">
                @error('source_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Pengaturan & Publikasi --}}
    <div class="col-lg-4">
        <div class="panel p-4 mb-4">
            <h2 class="h6 fw-semibold mb-3 pb-2 border-bottom border-soft">Status &amp; Wilayah</h2>

            {{-- Status --}}
            <div class="mb-3">
                <label for="status" class="form-label">Status Publikasi <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="published" {{ old('status', $announcement->status) === 'published' ? 'selected' : '' }}>
                        Published (Tayang untuk Publik)
                    </option>
                    <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>
                        Draft (Belum Ditampilkan)
                    </option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Level Urgensi --}}
            <div class="mb-3">
                <label for="level" class="form-label">Tingkat Kewaspadaan <span class="text-danger">*</span></label>
                <select name="level" id="level" class="form-select @error('level') is-invalid @enderror" required>
                    @foreach ($levels as $lvlKey => $meta)
                        <option value="{{ $lvlKey }}" {{ (int)old('level', $announcement->level) === $lvlKey ? 'selected' : '' }}>
                            {{ $meta['label'] }} — {{ $meta['description'] }}
                        </option>
                    @endforeach
                </select>
                @error('level')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Wilayah Terkait --}}
            <div class="mb-3">
                <label for="province_id" class="form-label">Wilayah Terkait</label>
                <select name="province_id" id="province_id" class="form-select @error('province_id') is-invalid @enderror">
                    <option value="">Seluruh Wilayah Kalimantan (Umum)</option>
                    @foreach ($provinces as $prov)
                        <option value="{{ $prov->id }}" {{ (string)old('province_id', $announcement->province_id) === (string)$prov->id ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
                @error('province_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tanggal Rilis --}}
            <div class="mb-3">
                <label for="published_at" class="form-label">Waktu Rilis Resmi</label>
                <input type="datetime-local"
                       name="published_at"
                       id="published_at"
                       class="form-control @error('published_at') is-invalid @enderror"
                       value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                @error('published_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Checkbox Demo --}}
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="is_demo" id="is_demo" value="1" {{ old('is_demo', $announcement->is_demo) ? 'checked' : '' }}>
                <label class="form-check-label small" for="is_demo">
                    Tandai sebagai <strong>Data DEMO</strong>
                </label>
                <div class="form-text small" style="font-size: .75rem;">Akan menampilkan label DEMO di halaman publik.</div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Terbitkan Pengumuman' }}
                </button>
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>
            </div>
        </div>

        @if ($isEdit && $announcement->status === 'published')
            <div class="panel p-3 text-center">
                <span class="small text-secondary-soft d-block mb-2">Tinjau pada halaman publik:</span>
                <a href="{{ route('announcements.show', $announcement->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Rilis Publik
                </a>
            </div>
        @endif
    </div>
</div>
