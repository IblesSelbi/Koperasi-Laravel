@extends('layouts.app')

@section('title', 'Master Data - Data Anggota')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <style>
        #tabelDataAnggota thead th {
            white-space: nowrap;
            vertical-align: middle;
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
                            <tr data-id="{{ $item->id }}" data-photo="{{ asset($item->photo) }}"
                                data-id-anggota="{{ $item->id_anggota }}" data-username="{{ $item->username }}"
                                data-nama="{{ $item->nama }}" data-jk="{{ $item->jenis_kelamin }}"
                                data-tempat-lahir="{{ $item->tempat_lahir }}" data-tgl-lahir="{{ $item->tanggal_lahir }}"
                                data-status="{{ $item->status }}" data-dept="{{ $item->departement }}"
                                data-pekerjaan="{{ $item->pekerjaan }}" data-agama="{{ $item->agama }}"
                                data-alamat="{{ $item->alamat }}" data-kota="{{ $item->kota }}" data-telp="{{ $item->no_telp }}"
                                data-tgl-reg="{{ $item->tanggal_registrasi }}" data-jabatan="{{ $item->jabatan }}"
                                data-aktif="{{ $item->aktif }}">
                                <td class="text-center">
                                    <img src="{{ asset($item->photo) }}" class="rounded-circle" width="40" height="40">
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-primary-subtle text-primary fw-semibold">{{ $item->id_anggota }}</span>
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
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_registrasi)->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->aktif == 'Aktif' ? 'success' : 'danger' }}-subtle text-{{ $item->aktif == 'Aktif' ? 'success' : 'danger' }} fw-semibold">
                                        {{ $item->aktif }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-1" onclick="editData(this)" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="hapusData(this)" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
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
                    <form id="formDataAnggota">
                        <input type="hidden" id="editId" value="">

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
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="password"
                                placeholder="Kosongkan jika tidak ingin ubah">
                            <small class="text-muted">Kosongkan password jika tidak ingin ubah/isi</small>
                        </div>

                        <!-- Status Aktif -->
                        <div class="mb-3">
                            <label class="form-label">Aktif Keanggotaan <span class="text-danger">*</span></label>
                            <select class="form-select" id="aktif" required>
                                <option value="">-- Pilih Status Aktif --</option>
                                <option value="Aktif">Aktif</option>
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
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Initialize DataTable
    let table;
    $(document).ready(function () {
        table = $('#tabelDataAnggota').DataTable({
            ordering: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            pageLength: 10,
            scrollX: true,
            columnDefs: [
                { orderable: false, targets: '_all' }
            ]
        });
    });

    // Format tanggal dari YYYY-MM-DD ke DD/MM/YYYY
    function formatTanggal(tanggal) {
        const [year, month, day] = tanggal.split('-');
        return `${day}/${month}/${year}`;
    }

    // Generate ID Anggota otomatis
    function generateIdAnggota() {
        const rows = document.querySelectorAll('#tabelDataAnggota tbody tr');
        let lastId = 0;
        rows.forEach(row => {
            const idAnggota = row.getAttribute('data-id-anggota');
            if (idAnggota) {
                const numId = parseInt(idAnggota.replace('AG', ''));
                if (numId > lastId) lastId = numId;
            }
        });
        const newId = (lastId + 1).toString().padStart(4, '0');
        return 'AG' + newId;
    }

    // Tambah Data
    function tambahData() {
        document.getElementById('modalTitle').textContent = 'Tambah Data Anggota';
        document.getElementById('formDataAnggota').reset();
        document.getElementById('editId').value = '';

        // Set tanggal hari ini
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggalRegistrasi').value = today;

        const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }

    // Edit Data
    function editData(btn) {
        const row = btn.closest('tr');

        document.getElementById('modalTitle').textContent = 'Ubah Data Anggota';
        document.getElementById('editId').value = row.getAttribute('data-id');
        document.getElementById('namaLengkap').value = row.getAttribute('data-nama');
        document.getElementById('username').value = row.getAttribute('data-username');
        document.getElementById('jenisKelamin').value = row.getAttribute('data-jk');
        document.getElementById('tempatLahir').value = row.getAttribute('data-tempat-lahir');
        document.getElementById('tanggalLahir').value = row.getAttribute('data-tgl-lahir');
        document.getElementById('status').value = row.getAttribute('data-status');
        document.getElementById('departement').value = row.getAttribute('data-dept');
        document.getElementById('pekerjaan').value = row.getAttribute('data-pekerjaan');
        document.getElementById('agama').value = row.getAttribute('data-agama');
        document.getElementById('alamat').value = row.getAttribute('data-alamat');
        document.getElementById('kota').value = row.getAttribute('data-kota');
        document.getElementById('noTelp').value = row.getAttribute('data-telp');
        document.getElementById('tanggalRegistrasi').value = row.getAttribute('data-tgl-reg');
        document.getElementById('jabatan').value = row.getAttribute('data-jabatan');
        document.getElementById('aktif').value = row.getAttribute('data-aktif');

        const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }

    // Simpan Data
    function simpanData() {
        const form = document.getElementById('formDataAnggota');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const editId = document.getElementById('editId').value;
        const namaLengkap = document.getElementById('namaLengkap').value;
        const username = document.getElementById('username').value;
        const jenisKelamin = document.getElementById('jenisKelamin').value;
        const tempatLahir = document.getElementById('tempatLahir').value;
        const tanggalLahir = document.getElementById('tanggalLahir').value;
        const status = document.getElementById('status').value;
        const departement = document.getElementById('departement').value;
        const pekerjaan = document.getElementById('pekerjaan').value;
        const agama = document.getElementById('agama').value;
        const alamat = document.getElementById('alamat').value;
        const kota = document.getElementById('kota').value;
        const noTelp = document.getElementById('noTelp').value;
        const tanggalRegistrasi = document.getElementById('tanggalRegistrasi').value;
        const jabatan = document.getElementById('jabatan').value;
        const aktif = document.getElementById('aktif').value;
        const photoFile = document.getElementById('photo').files[0];

        // Handle photo upload (simulasi)
        let photoUrl = '{{ asset("assets/images/profile/user-1.jpg") }}';
        if (photoFile) {
            photoUrl = URL.createObjectURL(photoFile);
        }

        if (editId) {
            // Update existing row
            const rows = document.querySelectorAll('#tabelDataAnggota tbody tr');
            rows.forEach(row => {
                if (row.getAttribute('data-id') === editId) {
                    const oldPhoto = row.getAttribute('data-photo');
                    const newPhoto = photoFile ? photoUrl : oldPhoto;
                    const idAnggota = row.getAttribute('data-id-anggota');

                    // Update data attributes
                    row.setAttribute('data-photo', newPhoto);
                    row.setAttribute('data-username', username);
                    row.setAttribute('data-nama', namaLengkap);
                    row.setAttribute('data-jk', jenisKelamin);
                    row.setAttribute('data-tempat-lahir', tempatLahir);
                    row.setAttribute('data-tgl-lahir', tanggalLahir);
                    row.setAttribute('data-status', status);
                    row.setAttribute('data-dept', departement);
                    row.setAttribute('data-pekerjaan', pekerjaan);
                    row.setAttribute('data-agama', agama);
                    row.setAttribute('data-alamat', alamat);
                    row.setAttribute('data-kota', kota);
                    row.setAttribute('data-telp', noTelp);
                    row.setAttribute('data-tgl-reg', tanggalRegistrasi);
                    row.setAttribute('data-jabatan', jabatan);
                    row.setAttribute('data-aktif', aktif);

                    // Update table cells
                    row.cells[0].innerHTML = `<img src="${newPhoto}" class="rounded-circle" width="40" height="40">`;
                    row.cells[2].innerHTML = `<div class="fw-semibold text-dark">${username}</div>`;
                    row.cells[3].innerHTML = `<div class="fw-semibold text-dark">${namaLengkap}</div>`;
                    row.cells[4].innerHTML = `<span class="badge bg-info-subtle text-info fw-semibold">${jenisKelamin}</span>`;
                    row.cells[5].innerHTML = alamat;
                    row.cells[6].innerHTML = kota;
                    row.cells[7].innerHTML = jabatan;
                    row.cells[8].innerHTML = departement || '-';
                    row.cells[9].innerHTML = formatTanggal(tanggalRegistrasi);
                    row.cells[10].innerHTML = `<span class="badge bg-${aktif === 'Aktif' ? 'success' : 'danger'}-subtle text-${aktif === 'Aktif' ? 'success' : 'danger'} fw-semibold">${aktif}</span>`;
                }
            });

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil diubah',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            // Add new row
            const newId = Date.now();
            const idAnggota = generateIdAnggota();

            const newRow = `
                    <tr data-id="${newId}"
                        data-photo="${photoUrl}"
                        data-id-anggota="${idAnggota}"
                        data-username="${username}"
                        data-nama="${namaLengkap}"
                        data-jk="${jenisKelamin}"
                        data-tempat-lahir="${tempatLahir}"
                        data-tgl-lahir="${tanggalLahir}"
                        data-status="${status}"
                        data-dept="${departement}"
                        data-pekerjaan="${pekerjaan}"
                        data-agama="${agama}"
                        data-alamat="${alamat}"
                        data-kota="${kota}"
                        data-telp="${noTelp}"
                        data-tgl-reg="${tanggalRegistrasi}"
                        data-jabatan="${jabatan}"
                        data-aktif="${aktif}">
                        <td class="text-center">
                            <img src="${photoUrl}" class="rounded-circle" width="40" height="40">
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary fw-semibold">${idAnggota}</span>
                        </td>
                        <td><div class="fw-semibold text-dark">${username}</div></td>
                        <td><div class="fw-semibold text-dark">${namaLengkap}</div></td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info fw-semibold">${jenisKelamin}</span>
                        </td>
                        <td>${alamat}</td>
                        <td>${kota}</td>
                        <td>${jabatan}</td>
                        <td>${departement || '-'}</td>
                        <td class="text-center">${formatTanggal(tanggalRegistrasi)}</td>
                        <td class="text-center">
                            <span class="badge bg-${aktif === 'Aktif' ? 'success' : 'danger'}-subtle text-${aktif === 'Aktif' ? 'success' : 'danger'} fw-semibold">${aktif}</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning me-1" onclick="editData(this)" title="Edit">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="hapusData(this)" title="Hapus">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            table.row.add($(newRow)).draw();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            });

            // reset form
            document.getElementById('formDataAnggota').reset();

            // reset editId
            document.getElementById('editId').value = '';

            // tutup modal
            $('#modalDataAnggota').modal('hide');
        }

        return false;
    }

</script>
@endpush