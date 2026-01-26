<section>
    <header>
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
                <h5 class="fw-semibold mb-1 text-danger">
                    <i class="ti ti-trash"></i> Hapus Akun
                </h5>
                <p class="text-muted small mb-0">
                    Setelah akun dihapus, semua resource dan data akan dihapus secara permanen.
                    Sebelum menghapus akun, silakan unduh data atau informasi yang ingin Anda simpan.
                </p>
            </div>

            <button 
                type="button" 
                class="btn btn-danger mx-3"
                data-bs-toggle="modal" 
                data-bs-target="#confirmDeleteModal"
            >
                <i class="ti ti-trash"></i> Hapus Akun
            </button>
        </div>
    </header>
</section>


{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        <i class="ti ti-alert-triangle"></i> Konfirmasi Hapus Akun
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-circle"></i>
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                    </div>

                    <p class="mb-3">
                        Apakah Anda yakin ingin menghapus akun Anda? Setelah akun dihapus, semua resource dan data akan dihapus secara permanen.
                    </p>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            Masukkan Password untuk Konfirmasi
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            placeholder="Masukkan password Anda"
                            required
                        >
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash"></i> Ya, Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->userDeletion->any())
    @push('scripts')
    <script>
        // Auto show modal if there are errors
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            myModal.show();
        });
    </script>
    @endpush
@endif