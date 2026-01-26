<section>
    <header class="mb-4">
        <h5 class="fw-semibold mb-1 text-danger">
            Hapus Akun
        </h5>
        <p class="text-muted small mb-0">
            Setelah akun dihapus, semua data akan hilang secara permanen.
        </p>
    </header>

    <button 
        type="button" 
        class="btn btn-danger"
        data-bs-toggle="modal" 
        data-bs-target="#confirmDeleteModal"
    >
        Hapus Akun
    </button>
</section>

{{-- MODAL KONFIRMASI --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ auth()->user()->role === 'admin' ? route('admin.profile.destroy') : route('user.profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="modal-header border-0">
                    <h5 class="modal-title">Konfirmasi Hapus Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Apakah Anda yakin ingin menghapus akun? Semua data akan hilang permanen.
                    </p>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            placeholder="Masukkan password untuk konfirmasi"
                            required
                        >
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>