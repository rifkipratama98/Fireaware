@extends('layouts.admin')

@section('title', 'Konfigurasi Notifikasi — Admin FireAware Kalimantan')
@section('page_title', 'Konfigurasi Notifikasi')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-semibold text-dark">Konfigurasi Notifikasi &amp; Peringatan Dini</h1>
            <p class="text-secondary-soft mb-0">
                Pengaturan sakelar kanal, kriteria informasi layak beritahu, wilayah pantauan, aturan frekuensi, dan riwayat pengiriman.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.notifications.test') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1 rounded-3">
                    <i class="bi bi-broadcast"></i>
                    <span>Kirim Notifikasi Uji Coba</span>
                </button>
            </form>
            <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 rounded-3 shadow-xs" onclick="document.getElementById('btnOpenSaveConfirmModal')?.click()">
                <i class="bi bi-check-lg"></i>
                <span>Simpan Konfigurasi</span>
            </button>
        </div>
    </div>

    {{-- Penjelasan Aturan Masyarakat Tanpa Login --}}
    <div class="alert alert-info py-2-5 px-3 small d-flex align-items-start gap-2 mb-4 rounded-3 border-info border-opacity-25 bg-soft" role="alert">
        <i class="bi bi-shield-check fs-5 text-primary mt-0-5 flex-shrink-0"></i>
        <div class="lh-sm">
            <strong class="text-dark d-block mb-1">Prinsip Notifikasi Masyarakat:</strong>
            <span class="text-secondary">Pengguna publik (Masyarakat Kalimantan) tidak memiliki akun login. Notifikasi publik disiarkan melalui peramban web (Web Notification API) saat membuka portal, tanpa menyimpan data pribadi warga di basis data.</span>
        </div>
    </div>

    {{-- Ringkasan Statistik Log Pengiriman --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary fs-5"><i class="bi bi-bell"></i></span>
                    <div>
                        <div class="stat-value">{{ number_format($stats['total']) }}</div>
                        <div class="stat-label">Total Evaluasi</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Event peringatan diproses</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-success bg-opacity-10 text-success fs-5"><i class="bi bi-send-check"></i></span>
                    <div>
                        <div class="stat-value text-success">{{ number_format($stats['sent']) }}</div>
                        <div class="stat-label">Terkirim</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Berhasil disiarkan ke kanal</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-warning bg-opacity-10 text-warning fs-5"><i class="bi bi-slash-circle"></i></span>
                    <div>
                        <div class="stat-value text-warning">{{ number_format($stats['suppressed']) }}</div>
                        <div class="stat-label">Ditekan Anti-Spam</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Jeda cooldown / jam hening</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-danger bg-opacity-10 text-danger fs-5"><i class="bi bi-exclamation-octagon"></i></span>
                    <div>
                        <div class="stat-value text-danger">{{ number_format($stats['failed']) }}</div>
                        <div class="stat-label">Gagal Kirim</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Kendala SMTP atau webhook</div>
            </div>
        </div>
    </div>

    {{-- Formulir Konfigurasi Notifikasi --}}
    <form id="notificationSettingForm" action="{{ route('admin.notifications.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                @include('admin.notifications._form_channels')
            </div>
            <div class="col-lg-6">
                @include('admin.notifications._form_thresholds')
            </div>
        </div>

        @include('admin.notifications._form_frequency_regions')
    </form>


    {{-- Bagian Riwayat Pengiriman Notifikasi --}}
    @include('admin.notifications._history_panel')

    {{-- Modal Konfirmasi Perubahan Konfigurasi Penting --}}
    <div class="modal fade" id="saveConfirmModal" tabindex="-1" aria-labelledby="saveConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-semibold text-dark d-flex align-items-center gap-2" id="saveConfirmModalLabel">
                        <i class="bi bi-shield-exclamation text-primary"></i>
                        <span>Konfirmasi Perubahan Konfigurasi Notifikasi</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Batal"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="mb-2 text-dark">
                        Apakah Anda yakin ingin menerapkan perubahan aturan notifikasi dan ambang batas keselamatan ini?
                    </p>
                    <div class="small text-secondary-soft p-3 bg-soft rounded-2 border border-soft">
                        <i class="bi bi-info-circle me-1"></i> Perubahan sakelar master, ambang batas pemicu, atau wilayah pantauan akan langsung berdampak pada siaran peringatan dini masyarakat dan koordinasi posko.
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Periksa Kembali
                    </button>
                    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" id="btnExecuteSave">
                        <i class="bi bi-check-lg"></i>
                        <span>Ya, Terapkan Konfigurasi</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('notificationSettingForm');
            const openModalBtn = document.getElementById('btnOpenSaveConfirmModal');
            const executeSaveBtn = document.getElementById('btnExecuteSave');
            const modalEl = document.getElementById('saveConfirmModal');

            if (openModalBtn && modalEl) {
                openModalBtn.addEventListener('click', function () {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        bsModal.show();
                    } else {
                        if (confirm('Terapkan perubahan konfigurasi notifikasi keselamatan?')) {
                            form.submit();
                        }
                    }
                });
            }

            if (executeSaveBtn && form) {
                executeSaveBtn.addEventListener('click', function () {
                    executeSaveBtn.setAttribute('disabled', 'disabled');
                    executeSaveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                    form.submit();
                });
            }
        });
    </script>
    @endpush
@endsection
