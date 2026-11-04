{{-- Modal Notifikasi Peramban untuk Masyarakat (Tanpa Akun) --}}
<div class="modal fade" id="browserNotificationModal" tabindex="-1" aria-labelledby="browserNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark d-flex align-items-center gap-2" id="browserNotificationModalLabel">
                    <i class="bi bi-bell text-primary"></i>
                    <span>Peringatan Dini Peramban Web</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body py-3">
                <p class="small text-dark mb-3">
                    Dapatkan pemberitahuan langsung di peramban web saat terdeteksi lonjakan titik panas ekstrem atau penurunan kualitas udara berbahaya di wilayah Kalimantan.
                </p>

                {{-- Status Izin Peramban --}}
                <div class="p-3 bg-soft rounded-3 border border-soft mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-secondary-soft">Status Izin Peramban:</span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" id="browserNotifyStatusBadge">
                            Memeriksa...
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small text-secondary-soft">Preferensi Pengguna:</span>
                        <span class="small fw-semibold text-dark" id="browserNotifyPrefText">Belum Diatur</span>
                    </div>
                </div>

                {{-- Penjelasan Batasan Teknis Jujur --}}
                <div class="alert alert-info py-2 px-3 small border-info border-opacity-25 mb-0" role="alert">
                    <div class="fw-semibold text-primary mb-1 d-flex align-items-center gap-1">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Informasi Privasi &amp; Batasan Teknis:</span>
                    </div>
                    <ul class="mb-0 ps-3 text-secondary-soft" style="font-size: .8rem;">
                        <li>Tidak memerlukan akun atau pengisian data pribadi warga.</li>
                        <li>Menggunakan <em>Web Notification API</em> bawaan peramban Anda.</li>
                        <li>Notifikasi aktif saat tab FireAware terbuka di jendela peramban.</li>
                        <li>Berbeda dengan push notification SMS/Aplikasi native yang selalu berjalan di latar belakang perangkat.</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-danger btn-sm d-none" id="btnDisableBrowserNotify">
                    <i class="bi bi-bell-slash me-1"></i>Nonaktifkan
                </button>
                <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" id="btnEnableBrowserNotify">
                    <i class="bi bi-bell me-1"></i>Aktifkan Notifikasi
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
<script>
    (function () {
        const badge = document.getElementById('browserNotifyStatusBadge');
        const prefText = document.getElementById('browserNotifyPrefText');
        const btnEnable = document.getElementById('btnEnableBrowserNotify');
        const btnDisable = document.getElementById('btnDisableBrowserNotify');

        function updateUI() {
            if (!('Notification' in window)) {
                if (badge) {
                    badge.className = 'badge bg-secondary bg-opacity-10 text-secondary border';
                    badge.textContent = 'Tidak Didukung Peramban';
                }
                if (btnEnable) btnEnable.classList.add('d-none');
                if (btnDisable) btnDisable.classList.add('d-none');
                return;
            }

            const permission = Notification.permission;
            const userPref = localStorage.getItem('fireaware_browser_notify');

            if (permission === 'granted' && userPref === 'enabled') {
                if (badge) {
                    badge.className = 'badge bg-success bg-opacity-10 text-success border';
                    badge.textContent = 'Aktif (Diizinkan)';
                }
                if (prefText) prefText.textContent = 'Aktif';
                if (btnEnable) btnEnable.classList.add('d-none');
                if (btnDisable) btnDisable.classList.remove('d-none');
            } else if (permission === 'denied') {
                if (badge) {
                    badge.className = 'badge bg-danger bg-opacity-10 text-danger border';
                    badge.textContent = 'Diblokir oleh Peramban';
                }
                if (prefText) prefText.textContent = 'Diblokir di Pengaturan Browser';
                if (btnEnable) btnEnable.classList.add('d-none');
                if (btnDisable) btnDisable.classList.add('d-none');
            } else {
                if (badge) {
                    badge.className = 'badge bg-warning bg-opacity-10 text-warning border';
                    badge.textContent = 'Belum Diaktifkan';
                }
                if (prefText) prefText.textContent = userPref === 'disabled' ? 'Dinonaktifkan' : 'Belum Ditentukan';
                if (btnEnable) btnEnable.classList.remove('d-none');
                if (btnDisable) btnDisable.classList.add('d-none');
            }
        }

        if (btnEnable) {
            btnEnable.addEventListener('click', function () {
                if (!('Notification' in window)) {
                    alert('Peramban Anda tidak mendukung Web Notification API.');
                    return;
                }

                Notification.requestPermission().then(function (permission) {
                    if (permission === 'granted') {
                        localStorage.setItem('fireaware_browser_notify', 'enabled');
                        updateUI();
                        new Notification('Peringatan Dini FireAware Aktif', {
                            body: 'Anda akan menerima pemberitahuan otomatis saat terjadi lonjakan bahaya Karhutla di Kalimantan.',
                            icon: '/favicon.ico'
                        });
                    } else {
                        updateUI();
                    }
                });
            });
        }

        if (btnDisable) {
            btnDisable.addEventListener('click', function () {
                localStorage.setItem('fireaware_browser_notify', 'disabled');
                updateUI();
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateUI();

            if (window.Notification && Notification.permission === 'granted' && localStorage.getItem('fireaware_browser_notify') === 'enabled') {
                fetch("{{ route('notifications.active') }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.count > 0 && data.data) {
                            const lastAlertId = localStorage.getItem('fireaware_last_shown_alert_id');
                            const newest = data.data[0];
                            if (newest && String(newest.id) !== String(lastAlertId)) {
                                new Notification(newest.title, {
                                    body: newest.message + ' (' + newest.province + ')',
                                    icon: '/favicon.ico'
                                });
                                localStorage.setItem('fireaware_last_shown_alert_id', String(newest.id));
                            }
                        }
                    })
                    .catch(() => {});
            }
        });
    })();
</script>

</div>
