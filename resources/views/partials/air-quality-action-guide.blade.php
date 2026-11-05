{{-- Panduan Tindakan & Perlindungan Masyarakat --}}
<div class="panel p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h2 class="h5 fw-bold mb-1 text-dark">Panduan Perlindungan Diri &amp; Keluarga</h2>
            <p class="text-secondary small mb-0">
                Langkah praktis yang dapat dilakukan masyarakat untuk melindungi saluran pernapasan dari partikel debu halus dan asap karhutla:
            </p>
        </div>
        <a href="{{ route('guides.index') }}" class="btn btn-outline-primary btn-sm">
            Lihat Semua Panduan <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
        </a>
    </div>

    <div class="aq-action-grid">
        <div class="aq-action-card">
            <div class="aq-action-icon">
                <i class="bi bi-mask" aria-hidden="true"></i>
            </div>
            <div>
                <h3>Gunakan Masker Standar Partikulat</h3>
                <p>
                    Saat kualitas udara memburuk, gunakan masker respirator N95 atau KN95 yang mampu menyaring partikel mikroskopis 2,5 mikron. Masker kain biasa tidak cukup rapat menahan partikel asap pekat.
                </p>
            </div>
        </div>

        <div class="aq-action-card">
            <div class="aq-action-icon">
                <i class="bi bi-house-door" aria-hidden="true"></i>
            </div>
            <div>
                <h3>Jaga Kebersihan Udara Dalam Rumah</h3>
                <p>
                    Tutup jendela dan pintu saat kabut asap menebal di luar. Jika menggunakan pendingin ruangan (AC), atur ke mode resirkulasi internal dan nyalakan alat penyaring udara (air purifier) bila ada.
                </p>
            </div>
        </div>

        <div class="aq-action-card">
            <div class="aq-action-icon">
                <i class="bi bi-heart-pulse" aria-hidden="true"></i>
            </div>
            <div>
                <h3>Lindungi Kelompok Rentan</h3>
                <p>
                    Berikan perlindungan ekstra bagi balita, anak-anak, ibu hamil, lansia, dan warga yang memiliki riwayat penyakit asma, bronkitis, atau gangguan jantung.
                </p>
            </div>
        </div>

        <div class="aq-action-card">
            <div class="aq-action-icon">
                <i class="bi bi-droplet-half" aria-hidden="true"></i>
            </div>
            <div>
                <h3>Jaga Hidrasi &amp; Asupan Sehat</h3>
                <p>
                    Perbanyak konsumsi air putih hangat minimal 2 liter per hari untuk menjaga kelembapan selaput lendir tenggorokan dan membantu mengeluarkan partikel yang terhirup.
                </p>
            </div>
        </div>

        <div class="aq-action-card">
            <div class="aq-action-icon">
                <i class="bi bi-hospital" aria-hidden="true"></i>
            </div>
            <div>
                <h3>Waspada Gejala Darurat</h3>
                <p>
                    Segera periksakan diri ke Puskesmas atau rumah sakit terdekat bila mengalami sesak napas berat, nyeri dada, batuk terus menerus, pusing berlebih, atau iritasi mata parah.
                </p>
            </div>
        </div>

        <div class="aq-action-card bg-light border-primary-subtle">
            <div class="aq-action-icon bg-primary text-white">
                <i class="bi bi-telephone-outbound" aria-hidden="true"></i>
            </div>
            <div>
                <h3>Layanan Darurat &amp; Informasi Posko</h3>
                <p class="mb-2">
                    Butuh bantuan medis darurat, informasi posko oksigen gratis, atau laporan kabut asap pekat di wilayah Anda?
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="tel:112" class="btn btn-sm btn-primary">
                        <i class="bi bi-telephone-fill me-1" aria-hidden="true"></i>Panggilan Darurat 112
                    </a>
                    <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-megaphone me-1" aria-hidden="true"></i>Pengumuman Resmi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
