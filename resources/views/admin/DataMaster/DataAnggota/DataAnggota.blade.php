@extends('layouts.app')

@section('title', 'Master Data - Data Anggota')

@push('styles')
    <style>
        #tabelDataAnggota thead th {
            white-space: nowrap;
            vertical-align: middle;
        }
        
        /* Fix untuk tombol aksi agar horizontal */
        #tabelDataAnggota tbody td:last-child {
            white-space: nowrap !important;
        }
        
        /* Pastikan kolom aksi tidak membungkus */
        #tabelDataAnggota tbody td {
            vertical-align: middle;
        }
        
        /* Fix untuk responsive tanpa merusak layout tombol */
        .dataTables_wrapper .dataTables_scroll {
            overflow-x: auto;
        }
        
        /* Tombol dalam satu baris */
        .action-buttons {
            display: inline-flex;
            gap: 4px;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Master Data - Data Anggota</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data anggota koperasi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Toolbar Card -->
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-lg-auto">
                    <button class="btn btn-primary btn-sm" onclick="tambahData()">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                    <button class="btn btn-success btn-sm" onclick="eksporData()">
                        <i class="ti ti-download"></i> Ekspor
                    </button>
                    <a href="{{ route('master.data-anggota.import') }}" class="btn btn-success btn-sm">
                        <i class="ti ti-upload"></i> Impor
                    </a>
                    <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                        <i class="ti ti-printer"></i> Cetak
                    </button>
                </div>
                <div class="col-lg-auto ms-auto">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Anggota">
                        <button class="btn btn-primary" onclick="cariData()">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-auto">
                    <button class="btn btn-secondary btn-sm" onclick="resetFilter()">
                        <i class="ti ti-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelDataAnggota" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" width="60px">Photo</th>
                            <th class="text-center" width="100px">ID Anggota</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th class="text-center" width="100px">Jenis Kelamin</th>
                            <th>Alamat</th>
                            <th>Kota</th>
                            <th>Jabatan</th>
                            <th>Departement</th>
                            <th class="text-center" width="120px">Tanggal Registrasi</th>
                            <th class="text-center" width="100px">Status</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataAnggota as $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-center">
                                    <img src="{{ asset($item->photo_url) }}" class="rounded-circle" width="40" height="40">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $item->id_anggota }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->username }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->nama }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info fw-semibold">{{ $item->jenis_kelamin }}</span>
                                </td>
                                <td>{{ $item->alamat }}</td>
                                <td>{{ $item->kota }}</td>
                                <td>{{ $item->jabatan }}</td>
                                <td>{{ $item->departement ?: '-' }}</td>
                                <td class="text-center">{{ $item->tanggal_registrasi->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $item->aktif == 'Aktif' ? 'success' : 'danger' }}-subtle text-{{ $item->aktif == 'Aktif' ? 'success' : 'danger' }} fw-semibold">
                                        {{ $item->aktif }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="editData({{ $item->id }})" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="hapusData({{ $item->id }})" title="Hapus">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Data Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formDataAnggota" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="editId" value="">
                        <input type="hidden" id="editMethod" value="">

                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namaLengkap" placeholder="Masukkan nama lengkap"
                                maxlength="255" required>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" placeholder="Masukkan username"
                                maxlength="255" required>
                        </div>

                        <div class="row">
                            <!-- Jenis Kelamin -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenisKelamin" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            <!-- Tempat Lahir -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tempatLahir" placeholder="Masukkan tempat lahir"
                                    maxlength="225" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tanggal Lahir -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggalLahir" required>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="status">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Belum Kawin">Belum Kawin</option>
                                    <option value="Kawin">Kawin</option>
                                    <option value="Cerai Hidup">Cerai Hidup</option>
                                    <option value="Cerai Mati">Cerai Mati</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Departement -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departement</label>
                                <select class="form-select" id="departement">
                                    <option value="">-- Pilih Departement --</option>
                                    <option value="Produksi BOPP">Produksi BOPP</option>
                                    <option value="Produksi Slitting">Produksi Slitting</option>
                                    <option value="WH">WH</option>
                                    <option value="QA">QA</option>
                                    <option value="HRD">HRD</option>
                                    <option value="GA">GA</option>
                                    <option value="Purchasing">Purchasing</option>
                                    <option value="Accounting">Accounting</option>
                                    <option value="Engineering">Engineering</option>
                                </select>
                            </div>

                            <!-- Pekerjaan -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pekerjaan</label>
                                <select class="form-select" id="pekerjaan">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    <option value="TNI">TNI</option>
                                    <option value="PNS">PNS</option>
                                    <option value="Karyawan Swasta">Karyawan Swasta</option>
                                    <option value="Guru">Guru</option>
                                    <option value="Buruh">Buruh</option>
                                    <option value="Tani">Tani</option>
                                    <option value="Pedagang">Pedagang</option>
                                    <option value="Wiraswasta">Wiraswasta</option>
                                    <option value="Mengurus Rumah Tangga">Mengurus Rumah Tangga</option>
                                    <option value="Lainnya">Lainnya</option>
                                    <option value="Pensiunan">Pensiunan</option>
                                    <option value="Penjahit">Penjahit</option>
                                </select>
                            </div>
                        </div>

                        <!-- Agama -->
                        <div class="mb-3">
                            <label class="form-label">Agama</label>
                            <select class="form-select" id="agama">
                                <option value="">-- Pilih Agama --</option>
                                <option value="Islam">Islam</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Protestan">Protestan</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Budha">Budha</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-3">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat" rows="2" placeholder="Masukkan alamat lengkap"
                                required></textarea>
                        </div>

                        <div class="row">
                            <!-- Kota -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kota" placeholder="Masukkan kota"
                                    maxlength="255" required>
                            </div>

                            <!-- No Telepon -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Telepon / HP</label>
                                <input type="text" class="form-control" id="noTelp" placeholder="Masukkan nomor telepon"
                                    maxlength="12">
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tanggal Registrasi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Registrasi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggalRegistrasi" required>
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jabatan" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Anggota">Anggota</option>
                                    <option value="Pengurus">Pengurus</option>
                                </select>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger" id="reqPassword">*</span></label>
                            <input type="password" class="form-control" id="password"
                                placeholder="Masukkan password (minimal 8 karakter)" minlength="8" required>
                            <small class="text-muted">Minimal 8 karakter. Kosongkan jika tidak ingin ubah (saat
                                edit)</small>
                        </div>

                        <!-- Status Aktif -->
                        <div class="mb-3">
                            <label class="form-label">Aktif Keanggotaan <span class="text-danger">*</span></label>
                            <select class="form-select" id="aktif" required>
                                <option value="">-- Pilih Status Aktif --</option>
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Non Aktif">Non Aktif</option>
                            </select>
                        </div>

                        <!-- Photo Upload -->
                        <div class="mb-3">
                            <label class="form-label">Photo</label>
                            <input type="file" class="form-control" id="photo" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="simpanData()">
                        <i class="ti ti-device-floppy"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let table;

        // Init DataTable
        $(document).ready(function () {
            table = $('#tabelDataAnggota').DataTable({
                language: {
                    url: "{{ asset('assets/datatables/i18n/id.json') }}"
                },
                pageLength: 10,
                order: [],
                scrollX: true,
                autoWidth: false,
                columnDefs: [
                    { 
                        orderable: false, 
                        targets: '_all' 
                    },
                    {
                        // Fix width untuk kolom aksi
                        width: '150px',
                        targets: -1
                    }
                ]
            });
        });

        // Tambah Data
        function tambahData() {
            document.getElementById('modalTitle').textContent = 'Tambah Data Anggota';
            document.getElementById('formDataAnggota').reset();
            document.getElementById('editId').value = '';
            document.getElementById('editMethod').value = 'POST';
            document.getElementById('password').required = true;
            document.getElementById('reqPassword').style.display = 'inline';

            // Set tanggal hari ini
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggalRegistrasi').value = today;
            document.getElementById('aktif').value = 'Aktif';

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Edit Data
        function editData(id) {
            fetch(`/admin/data-anggota/${id}/edit`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Ubah Data Anggota';
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editMethod').value = 'PUT';
                    document.getElementById('namaLengkap').value = data.nama;
                    document.getElementById('username').value = data.username;
                    document.getElementById('jenisKelamin').value = data.jenis_kelamin;
                    document.getElementById('tempatLahir').value = data.tempat_lahir;
                    document.getElementById('tanggalLahir').value = data.tanggal_lahir || '';
                    document.getElementById('status').value = data.status || '';
                    document.getElementById('departement').value = data.departement || '';
                    document.getElementById('pekerjaan').value = data.pekerjaan || '';
                    document.getElementById('agama').value = data.agama || '';
                    document.getElementById('alamat').value = data.alamat;
                    document.getElementById('kota').value = data.kota;
                    document.getElementById('noTelp').value = data.no_telp || '';
                    document.getElementById('tanggalRegistrasi').value = data.tanggal_registrasi || '';
                    document.getElementById('jabatan').value = data.jabatan;
                    document.getElementById('aktif').value = data.aktif;

                    // Password tidak required saat edit
                    document.getElementById('password').required = false;
                    document.getElementById('password').value = '';
                    document.getElementById('reqPassword').style.display = 'none';

                    const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modal.show();
                });
        }

        // Simpan Data
        function simpanData() {
            const form = document.getElementById('formDataAnggota');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const id = document.getElementById('editId').value;
            const method = document.getElementById('editMethod').value || 'POST';
            const url = id ? `/admin/data-anggota/${id}` : `/admin/data-anggota`;
            const password = document.getElementById('password').value;

            // Validasi password saat tambah data
            if (!id && password.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Password minimal 8 karakter'
                });
                return;
            }

            // Validasi password saat edit (jika diisi)
            if (id && password && password.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Password minimal 8 karakter'
                });
                return;
            }

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            formData.append('nama', document.getElementById('namaLengkap').value);
            formData.append('username', document.getElementById('username').value);

            if (password) {
                formData.append('password', password);
            }

            formData.append('jenis_kelamin', document.getElementById('jenisKelamin').value);
            formData.append('tempat_lahir', document.getElementById('tempatLahir').value);
            formData.append('tanggal_lahir', document.getElementById('tanggalLahir').value);
            formData.append('status', document.getElementById('status').value);
            formData.append('departement', document.getElementById('departement').value);
            formData.append('pekerjaan', document.getElementById('pekerjaan').value);
            formData.append('agama', document.getElementById('agama').value);
            formData.append('alamat', document.getElementById('alamat').value);
            formData.append('kota', document.getElementById('kota').value);
            formData.append('no_telp', document.getElementById('noTelp').value);
            formData.append('tanggal_registrasi', document.getElementById('tanggalRegistrasi').value);
            formData.append('jabatan', document.getElementById('jabatan').value);
            formData.append('aktif', document.getElementById('aktif').value);

            const photoFile = document.getElementById('photo').files[0];
            if (photoFile) {
                formData.append('photo', photoFile);
            }

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        throw {
                            status: res.status,
                            data: data
                        };
                    }

                    return data;
                })
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        let errorMessage = res.message || 'Gagal menyimpan data';
                        if (res.errors) {
                            errorMessage = Object.values(res.errors).flat().join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: errorMessage
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';

                    if (error.status === 422 && error.data.errors) {
                        errorMessage = Object.values(error.data.errors).flat().join('\n');
                    } else if (error.data && error.data.message) {
                        errorMessage = error.data.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                });
        }

        // Hapus Data
        function hapusData(id) {
            Swal.fire({
                title: 'Yakin hapus?',
                text: 'Data tidak bisa dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/admin/data-anggota/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(res => res.json())
                        .then(res => {
                            Swal.fire('Terhapus!', res.message, 'success')
                                .then(() => location.reload());
                        });
                }
            });
        }

        // Cari Data
        function cariData() {
            table.search(document.getElementById('searchInput').value).draw();
        }

        // Reset Filter
        function resetFilter() {
            document.getElementById('searchInput').value = '';
            table.search('').draw();

            Swal.fire({
                icon: 'info',
                title: 'Filter direset',
                timer: 1200,
                showConfirmButton: false
            });
        }

        // Cetak & Export
        function cetakLaporan() {
            window.location.href = "{{ route('master.data-anggota.cetak') }}";
        }

        function eksporData() {
            window.location.href = "{{ route('master.data-anggota.export') }}";
        }
    </script>
@endpush