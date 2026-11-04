{{-- Modal Konfirmasi Penghapusan Terpadu --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-semibold text-danger d-flex align-items-center gap-2" id="deleteConfirmModalLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Batal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2 text-dark">
                    Apakah Anda yakin ingin menghapus <span id="deleteItemType" class="fw-semibold">data ini</span>?
                </p>
                <div class="p-3 bg-soft rounded-2 border border-soft text-break fw-semibold text-dark mb-2" id="deleteItemTitle">
                    -
                </div>
                <div class="small text-secondary-soft">
                    <i class="bi bi-info-circle me-1"></i> Data yang dihapus tidak dapat dipulihkan kembali. Seluruh berkas terkait akan dibersihkan dari penyimpanan.
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <form id="deleteConfirmForm" method="POST" action="" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2">
                        <i class="bi bi-trash"></i>
                        <span>Ya, Hapus Data</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('deleteConfirmModal');
        if (!modalEl) return;

        const deleteForm = document.getElementById('deleteConfirmForm');
        const titleEl = document.getElementById('deleteItemTitle');
        const typeEl = document.getElementById('deleteItemType');

        document.querySelectorAll('[data-delete-url]').forEach(function (button) {
            button.addEventListener('click', function () {
                const url = this.getAttribute('data-delete-url');
                const title = this.getAttribute('data-delete-title') || '';
                const type = this.getAttribute('data-delete-type') || 'konten';

                if (deleteForm) deleteForm.setAttribute('action', url);
                if (titleEl) titleEl.textContent = title;
                if (typeEl) typeEl.textContent = type;

                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    bsModal.show();
                } else {
                    // Fallback jika bootstrap JS belum siap
                    if (confirm('Yakin ingin menghapus ' + type + ': "' + title + '"?')) {
                        deleteForm.submit();
                    }
                }
            });
        });
    });
</script>
