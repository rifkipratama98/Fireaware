<div class="row g-4">
    {{-- Kolom Kiri: Konten Utama --}}
    <div class="col-lg-8">
        <div class="panel p-4 mb-4">
            <h2 class="h6 fw-semibold mb-3 pb-2 border-bottom border-soft">Informasi Artikel Edukasi</h2>

            {{-- Judul --}}
            <div class="mb-3">
                <label for="title" class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $article->title) }}"
                       placeholder="Contoh: Mengapa Lahan Gambut Tropis Rentan Mengalami Ground Fire?"
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
                    <span class="input-group-text bg-light text-muted small">/edukasi/</span>
                    <input type="text"
                           name="slug"
                           id="slug"
                           class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $article->slug) }}"
                           placeholder="mengapa-lahan-gambut-rentan-kebakaran">
                </div>
                @error('slug')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Ringkasan --}}
            <div class="mb-3">
                <label for="summary" class="form-label">Ringkasan / Abstrak Artikel <span class="text-danger">*</span></label>
                <textarea name="summary"
                          id="summary"
                          rows="3"
                          class="form-control @error('summary') is-invalid @enderror"
                          placeholder="Jelaskan ringkasan materi edukasi dalam 2-3 kalimat pengantar yang menarik..."
                          required>{{ old('summary', $article->summary) }}</textarea>
                @error('summary')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Isi Lengkap Artikel --}}
            <div class="mb-0">
                <label for="content" class="form-label">Isi Lengkap Artikel Edukasi <span class="text-danger">*</span></label>
                <textarea name="content"
                          id="content"
                          rows="16"
                          class="form-control font-monospace small @error('content') is-invalid @enderror"
                          placeholder="Tuliskan materi edukasi ilmiah secara lengkap.&#10;&#10;Gunakan ### untuk judul bagian.&#10;Gunakan - untuk daftar butir.&#10;Gunakan > untuk kutipan penting."
                          required>{{ old('content', $article->content) }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text small">Mendukung format sub-heading (### Judul Bagian) dan pemisah paragraf ganda.</div>
            </div>
        </div>

        {{-- Panel Media Gambar --}}
        <div class="panel p-4">
            <h2 class="h6 fw-semibold mb-3 pb-2 border-bottom border-soft">Ilustrasi &amp; Gambar Artikel</h2>

            @if ($isEdit && $article->imageUrl())
                <div class="mb-3 p-3 bg-soft rounded-3 border border-soft d-flex align-items-center gap-3">
                    <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}" style="width: 100px; height: 70px; object-fit: cover; border-radius: .4rem;">
                    <div class="flex-grow-1">
                        <div class="small fw-semibold text-dark">Gambar Saat Ini</div>
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                            <label class="form-check-label text-danger small" for="remove_image">
                                Hapus gambar ini
                            </label>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <label for="image" class="form-label">Unggah File Gambar <small class="text-muted fw-normal">(JPG, PNG, WEBP, Maks. 2MB)</small></label>
                <input type="file"
                       name="image"
                       id="image"
                       class="form-control @error('image') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/webp">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-0">
                <label for="image_caption" class="form-label">Keterangan / Sumber Foto <small class="text-muted fw-normal">(Opsional)</small></label>
                <input type="text"
                       name="image_caption"
                       id="image_caption"
                       class="form-control @error('image_caption') is-invalid @enderror"
                       value="{{ old('image_caption', $article->image_caption) }}"
                       placeholder="Contoh: Dok. Balai Pengendalian Perubahan Iklim KLHK">
                @error('image_caption')
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
                    <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>
                        Published (Tayang untuk Publik)
                    </option>
                    <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>
                        Draft (Belum Ditampilkan)
                    </option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kategori --}}
            <div class="mb-3">
                <label for="category" class="form-label">Kategori Edukasi <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                    <option value="">Pilih Kategori...</option>
                    @foreach ($categories as $slug => $meta)
                        <option value="{{ $slug }}" {{ old('category', $article->category) === $slug ? 'selected' : '' }}>
                            {{ $meta['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Penulis --}}
            <div class="mb-3">
                <label for="author" class="form-label">Penulis / Narasumber <small class="text-muted fw-normal">(Opsional)</small></label>
                <input type="text"
                       name="author"
                       id="author"
                       class="form-control @error('author') is-invalid @enderror"
                       value="{{ old('author', $article->author) }}"
                       placeholder="Contoh: Tim Riset Gambut FireAware">
                @error('author')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tanggal Publikasi --}}
            <div class="mb-3">
                <label for="published_at" class="form-label">Tanggal Publikasi</label>
                <input type="datetime-local"
                       name="published_at"
                       id="published_at"
                       class="form-control @error('published_at') is-invalid @enderror"
                       value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                @error('published_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Estimasi Waktu Baca --}}
            <div class="mb-3">
                <label for="reading_time_minutes" class="form-label">Waktu Baca (Menit)</label>
                <input type="number"
                       name="reading_time_minutes"
                       id="reading_time_minutes"
                       min="1"
                       max="60"
                       class="form-control @error('reading_time_minutes') is-invalid @enderror"
                       value="{{ old('reading_time_minutes', $article->reading_time_minutes ?? 4) }}">
                @error('reading_time_minutes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Checkbox Demo --}}
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="is_demo" id="is_demo" value="1" {{ old('is_demo', $article->is_demo) ? 'checked' : '' }}>
                <label class="form-check-label small" for="is_demo">
                    Tandai sebagai <strong>Data DEMO</strong>
                </label>
                <div class="form-text small" style="font-size: .75rem;">Akan menampilkan label DEMO di halaman publik.</div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Terbitkan Artikel' }}
                </button>
                <a href="{{ route('admin.education.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>
            </div>
        </div>

        @if ($isEdit && $article->status === 'published')
            <div class="panel p-3 text-center">
                <span class="small text-secondary-soft d-block mb-2">Tinjau pada halaman publik:</span>
                <a href="{{ route('edukasi.show', $article->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Halaman Publik
                </a>
            </div>
        @endif
    </div>
</div>
