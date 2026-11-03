{{-- Kotak Pedoman Redaksi / Editor Safety Rules --}}
<div class="alert alert-info border-info border-opacity-25 bg-soft p-3 mb-4 rounded-3">
    <div class="d-flex gap-2">
        <i class="bi bi-shield-check text-primary fs-5 mt-1"></i>
        <div class="small">
            <strong class="d-block text-primary-dark mb-1">Pedoman Redaksi Panduan Keselamatan FireAware:</strong>
            <ul class="mb-0 ps-3 text-secondary-soft">
                <li><strong>Wajib Berdasar Rujukan:</strong> Setiap panduan wajib mencantumkan sumber rujukan resmi/kredibel (mis. Kemenkes, BMKG, KLHK SIPONGI, BNPB, WHO). Jangan membuat konten tanpa rujukan.</li>
                <li><strong>Larangan Tindakan Berbahaya:</strong> Jangan menyarankan masyarakat mendekati api atau mencoba memadamkan kebakaran lahan secara mandiri. Utamakan evakuasi diri dan pelaporan ke nomor darurat (112, BPBD, atau Manggala Agni).</li>
                <li><strong>Kejelasan Bahasa:</strong> Gunakan bahasa yang mudah dipahami warga awam dan kelompok rentan.</li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Kolom Kiri: Konten Utama --}}
    <div class="col-lg-8">
        <div class="panel p-4 mb-4">
            <h2 class="h6 fw-semibold mb-3 pb-2 border-bottom border-soft">Informasi Artikel</h2>

            {{-- Judul --}}
            <div class="mb-3">
                <label for="title" class="form-label">Judul Panduan <span class="text-danger">*</span></label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $guide->title) }}"
                       placeholder="Contoh: Prosedur Perlindungan Diri Saat Kabut Asap Memasuki Pemukiman"
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
                    <span class="input-group-text bg-light text-muted small">/panduan/</span>
                    <input type="text"
                           name="slug"
                           id="slug"
                           class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $guide->slug) }}"
                           placeholder="prosedur-perlindungan-diri">
                </div>
                @error('slug')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Ringkasan / Excerpt --}}
            <div class="mb-3">
                <label for="summary" class="form-label">Ringkasan / Pengantar Singkat <span class="text-danger">*</span></label>
                <textarea name="summary"
                          id="summary"
                          rows="3"
                          class="form-control @error('summary') is-invalid @enderror"
                          placeholder="Jelaskan secara ringkas poin inti panduan ini dalam 2-3 kalimat agar memudahkan pembaca..."
                          required>{{ old('summary', $guide->summary) }}</textarea>
                @error('summary')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text small">Tampil di halaman daftar panduan dan sebagai pengantar di halaman detail.</div>
            </div>

            {{-- Isi Lengkap Panduan --}}
            <div class="mb-0">
                <label for="content" class="form-label">Isi Lengkap Panduan <span class="text-danger">*</span></label>
                <textarea name="content"
                          id="content"
                          rows="16"
                          class="form-control font-monospace small @error('content') is-invalid @enderror"
                          placeholder="Tuliskan langkah-langkah panduan secara terstruktur.&#10;&#10;Gunakan baris baru ganda untuk memisahkan paragraf.&#10;Gunakan ### untuk sub-heading.&#10;Gunakan tanda - untuk butir poin."
                          required>{{ old('content', $guide->content) }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text small">Mendukung format sub-heading (### Judul Bagian) dan daftar butir (- Poin).</div>
            </div>
        </div>

        {{-- Panel Sumber Rujukan --}}
        <div class="panel p-4">
            <h2 class="h6 fw-semibold mb-3 pb-2 border-bottom border-soft">Dasar Sumber Rujukan</h2>

            {{-- Nama Sumber --}}
            <div class="mb-3">
                <label for="source_name" class="form-label">Nama Lembaga / Otoritas Sumber <span class="text-danger">*</span></label>
                <input type="text"
                       name="source_name"
                       id="source_name"
                       class="form-control @error('source_name') is-invalid @enderror"
                       value="{{ old('source_name', $guide->source_name) }}"
                       placeholder="Contoh: Kementerian Kesehatan Republik Indonesia (Kemenkes RI)"
                       required>
                @error('source_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- URL Sumber --}}
            <div class="mb-3">
                <label for="source_url" class="form-label">
                    Tautan / URL Rujukan Resmi <small class="text-muted fw-normal">(Opsional)</small>
                </label>
                <input type="url"
                       name="source_url"
                       id="source_url"
                       class="form-control @error('source_url') is-invalid @enderror"
                       value="{{ old('source_url', $guide->source_url) }}"
                       placeholder="https://p2ptm.kemkes.go.id">
                @error('source_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Catatan / Dokumen Rujukan --}}
            <div class="mb-0">
                <label for="source_notes" class="form-label">
                    Catatan Dokumen / Nomor Edaran <small class="text-muted fw-normal">(Opsional)</small>
                </label>
                <textarea name="source_notes"
                          id="source_notes"
                          rows="2"
                          class="form-control @error('source_notes') is-invalid @enderror"
                          placeholder="Contoh: Berdasarkan Buku Pedoman Kesiapsiagaan Dampak Kesehatan Akibat Asap Karhutla.">{{ old('source_notes', $guide->source_notes) }}</textarea>
                @error('source_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Pengaturan & Publikasi --}}
    <div class="col-lg-4">
        <div class="panel p-4 mb-4">
            <h2 class="h6 fw-semibold mb-3 pb-2 border-bottom border-soft">Publikasi</h2>

            {{-- Status --}}
            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="published" {{ old('status', $guide->status) === 'published' ? 'selected' : '' }}>
                        Published (Tayang untuk Publik)
                    </option>
                    <option value="draft" {{ old('status', $guide->status) === 'draft' ? 'selected' : '' }}>
                        Draft (Belum Ditampilkan)
                    </option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kategori --}}
            <div class="mb-3">
                <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                    <option value="">Pilih Kategori...</option>
                    @foreach ($categories as $slug => $meta)
                        <option value="{{ $slug }}" {{ old('category', $guide->category) === $slug ? 'selected' : '' }}>
                            {{ $meta['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tanggal Publikasi --}}
            <div class="mb-3">
                <label for="published_at" class="form-label">
                    Tanggal Publikasi <small class="text-muted fw-normal">(Opsional)</small>
                </label>
                <input type="datetime-local"
                       name="published_at"
                       id="published_at"
                       class="form-control @error('published_at') is-invalid @enderror"
                       value="{{ old('published_at', $guide->published_at ? $guide->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                @error('published_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Waktu Baca --}}
            <div class="mb-4">
                <label for="reading_time_minutes" class="form-label">
                    Estimasi Waktu Baca (Menit) <small class="text-muted fw-normal">(Opsional)</small>
                </label>
                <input type="number"
                       name="reading_time_minutes"
                       id="reading_time_minutes"
                       min="1"
                       max="60"
                       class="form-control @error('reading_time_minutes') is-invalid @enderror"
                       value="{{ old('reading_time_minutes', $guide->reading_time_minutes ?? 3) }}">
                @error('reading_time_minutes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text small">Dihitung otomatis bila dikosongkan.</div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Terbitkan Panduan' }}
                </button>
                <a href="{{ route('admin.guides.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>
            </div>
        </div>

        @if ($isEdit && $guide->status === 'published')
            <div class="panel p-3 text-center">
                <span class="small text-secondary-soft d-block mb-2">Lihat tampilan pada halaman publik:</span>
                <a href="{{ route('guides.show', $guide->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Halaman Publik
                </a>
            </div>
        @endif
    </div>
</div>
