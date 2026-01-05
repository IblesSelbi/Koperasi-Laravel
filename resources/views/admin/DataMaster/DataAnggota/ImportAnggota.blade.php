@extends('layouts.app')

@section('title', 'Import Data Anggota')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Import Data Anggota</h4>
                    <p class="text-muted fs-3 mb-0">Upload file Excel untuk import data anggota</p>
                </div>
                <div>
                    <a href="{{ route('master.data-anggota') }}" class="btn btn-success">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-circle-check fs-4 me-2"></i>
                <div>
                    <strong>Import Berhasil!</strong>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-circle fs-4 me-2"></i>
                <div>
                    <strong>Import Gagal!</strong>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Upload Form Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0"><i class="ti ti-upload"></i> Upload File Excel</h5>
        </div>
        <div class="card-body">
            <form id="formImport" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih File Excel <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="fileExcel" name="file_excel" accept=".xls,.xlsx" required>
                            <small class="text-muted">
                                <i class="ti ti-info-circle"></i> Format file: .xls atau .xlsx (Max: 5MB)
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="mb-3 w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-upload"></i> Upload & Import
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Info Box -->
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="ti ti-info-circle fs-5 me-2"></i>
                <div>
                    <strong>Informasi:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Pastikan format file sesuai dengan template</li>
                        <li>Kolom yang wajib diisi: Nama, Username, Jenis Kelamin, Alamat, Kota</li>
                        <li>Download template Excel: <a href="javascript:void(0)" onclick="downloadTemplate()"
                                class="fw-bold">Template_Import_Anggota.xlsx</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="card mb-3" style="display: none;">
        <div class="card-body text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 mb-0 text-muted">Sedang memproses file...</p>
        </div>
    </div>

    <!-- Success Alert (Dynamic) -->
    <div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-4 me-2"></i>
            <div>
                <strong>Import Berhasil!</strong>
                <p class="mb-0" id="successMessage">Data berhasil diimport.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Error Alert (Dynamic) -->
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-circle fs-4 me-2"></i>
            <div>
                <strong>Import Gagal!</strong>
                <p class="mb-0" id="errorMessage">Terjadi kesalahan saat import data.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Result Table Card -->
    <div id="resultTableCard" class="card" style="display: none;">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="ti ti-table"></i> Hasil Import Data</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge bg-success-subtle text-success fs-3 px-3 py-2">
                        <i class="ti ti-check"></i> Berhasil: <strong id="successCount">0</strong>
                    </span>
                    <span class="badge bg-danger-subtle text-danger fs-3 px-3 py-2 ms-2">
                        <i class="ti ti-x"></i> Gagal: <strong id="failCount">0</strong>
                    </span>
                </div>
                <button class="btn btn-sm btn-info" onclick="eksporHasil()">
                    <i class="ti ti-download"></i> Ekspor Hasil
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabelHasil" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center" width="50px">No</th>
                            <th class="text-center" width="80px">Status</th>
                            <th width="100px">ID Anggota</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Kelamin</th>
                            <th>Alamat</th>
                            <th>Kota</th>
                            <th>Jabatan</th>
                            <th class="text-center" width="150px">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTabelHasil">
                        @foreach($hasilImport as $item)
                            <tr>
                                <td class="text-center text-muted fw-medium">{{ $item->no }}</td>
                                <td class="text-center">
                                    @if($item->status === 'success')
                                        <span class="badge bg-success-subtle text-success fw-semibold px-3 py-1">
                                            <i class="ti ti-check"></i> Berhasil
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-1">
                                            <i class="ti ti-x"></i> Gagal
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">{{ $item->id_anggota }}</span>
                                </td>
                                <td>{{ $item->username }}</td>
                                <td><strong>{{ $item->nama }}</strong></td>
                                <td>{{ $item->jenis_kelamin }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>{{ $item->kota }}</td>
                                <td>{{ $item->jabatan }}</td>
                                <td class="text-center {{ $item->status === 'success' ? 'text-success' : 'text-danger' }} fw-semibold">
                                    {{ $item->keterangan }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="py-6 px-6 text-center mt-4">
        <p class="mb-0 fs-4">Sistem Koperasi <strong>Akeno</strong> &copy; 2025</p>
    </div>
@endsection

@push('scripts')
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- SheetJS untuk parse Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        // Data hasil import
        let hasilImport = [];
        let table;

        // Handle Form Submit
        document.getElementById('formImport').addEventListener('submit', function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('fileExcel');
            const file = fileInput.files[0];

            if (!file) {
                showError('Silakan pilih file terlebih dahulu!');
                return;
            }

            // Validasi ukuran file (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showError('Ukuran file terlalu besar! Maksimal 5MB.');
                return;
            }

            // Validasi ekstensi file
            const fileName = file.name;
            const fileExt = fileName.split('.').pop().toLowerCase();
            if (fileExt !== 'xls' && fileExt !== 'xlsx') {
                showError('Format file tidak valid! Gunakan file .xls atau .xlsx');
                return;
            }

            // Show loading
            document.getElementById('loadingIndicator').style.display = 'block';
            document.getElementById('resultTableCard').style.display = 'none';
            document.getElementById('successAlert').style.display = 'none';
            document.getElementById('errorAlert').style.display = 'none';

            // Read and parse Excel file
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {
                        type: 'array'
                    });

                    // Ambil sheet pertama
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    const jsonData = XLSX.utils.sheet_to_json(firstSheet);

                    // Process data
                    setTimeout(() => {
                        processImportData(jsonData);
                    }, 1500); // Simulasi delay untuk loading

                } catch (error) {
                    document.getElementById('loadingIndicator').style.display = 'none';
                    showError('Gagal membaca file Excel! Pastikan format file benar.');
                }
            };
            reader.readAsArrayBuffer(file);
        });

        // Process imported data
        function processImportData(data) {
            hasilImport = [];
            let successCount = 0;
            let failCount = 0;
            let idCounter = 12; // Start from AG0012

            data.forEach((row, index) => {
                const result = {
                    no: index + 1,
                    status: 'success',
                    idAnggota: `AG${String(idCounter).padStart(4, '0')}`,
                    username: row.Username || row.username || '',
                    nama: row['Nama Lengkap'] || row.Nama || row.nama || '',
                    jenisKelamin: row['Jenis Kelamin'] || row.JenisKelamin || '',
                    alamat: row.Alamat || row.alamat || '',
                    kota: row.Kota || row.kota || '',
                    jabatan: row.Jabatan || row.jabatan || 'Anggota',
                    keterangan: ''
                };

                // Validasi data wajib
                const errors = [];
                if (!result.nama) errors.push('Nama kosong');
                if (!result.username) errors.push('Username kosong');
                if (!result.jenisKelamin) errors.push('Jenis kelamin kosong');
                if (!result.alamat) errors.push('Alamat kosong');
                if (!result.kota) errors.push('Kota kosong');

                if (errors.length > 0) {
                    result.status = 'failed';
                    result.keterangan = errors.join(', ');
                    failCount++;
                } else {
                    result.status = 'success';
                    result.keterangan = 'Berhasil diimport';
                    successCount++;
                    idCounter++;
                }

                hasilImport.push(result);
            });

            // Hide loading
            document.getElementById('loadingIndicator').style.display = 'none';

            // Show success alert
            if (successCount > 0) {
                document.getElementById('successMessage').textContent =
                    `${successCount} data berhasil diimport${failCount > 0 ? `, ${failCount} data gagal` : ''}.`;
                document.getElementById('successAlert').style.display = 'block';
            }

            if (failCount > 0 && successCount === 0) {
                document.getElementById('errorMessage').textContent =
                    `Semua data gagal diimport! Silakan periksa format file.`;
                document.getElementById('errorAlert').style.display = 'block';
            }

            // Update counter
            document.getElementById('successCount').textContent = successCount;
            document.getElementById('failCount').textContent = failCount;

            // Show result table
            displayResultTable();
        }

        // Display result table
        function displayResultTable() {
            const tbody = document.getElementById('bodyTabelHasil');
            tbody.innerHTML = '';

            hasilImport.forEach(row => {
                const statusBadge = row.status === 'success' ?
                    '<span class="badge bg-success-subtle text-success fw-semibold px-3 py-1"><i class="ti ti-check"></i> Berhasil</span>' :
                    '<span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-1"><i class="ti ti-x"></i> Gagal</span>';

                const keteranganClass = row.status === 'success' ? 'text-success' : 'text-danger';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center text-muted fw-medium">${row.no}</td>
                    <td class="text-center">${statusBadge}</td>
                    <td><span class="badge bg-primary-subtle text-primary">${row.idAnggota}</span></td>
                    <td>${row.username}</td>
                    <td><strong>${row.nama}</strong></td>
                    <td>${row.jenisKelamin}</td>
                    <td>${row.alamat}</td>
                    <td>${row.kota}</td>
                    <td>${row.jabatan}</td>
                    <td class="text-center ${keteranganClass} fw-semibold">${row.keterangan}</td>
                `;
                tbody.appendChild(tr);
            });

            // Show table card
            document.getElementById('resultTableCard').style.display = 'block';

            // Init DataTable if not initialized
            if ($.fn.DataTable.isDataTable('#tabelHasil')) {
                $('#tabelHasil').DataTable().destroy();
            }

            table = $('#tabelHasil').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                scrollX: true
            });

            // Scroll to result
            setTimeout(() => {
                document.getElementById('resultTableCard').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 300);
        }

        // Show error message
        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorAlert').style.display = 'block';

            setTimeout(() => {
                document.getElementById('errorAlert').style.display = 'none';
            }, 5000);
        }

        // Download template Excel
        function downloadTemplate() {
            const template = [{
                'Username': 'member_contoh',
                'Nama Lengkap': 'Nama Contoh',
                'Jenis Kelamin': 'Laki-laki',
                'Tempat Lahir': 'Jakarta',
                'Tanggal Lahir': '01/01/1990',
                'Status': 'Belum Kawin',
                'Departement': 'Produksi BOPP',
                'Pekerjaan': 'Karyawan Swasta',
                'Agama': 'Islam',
                'Alamat': 'Jl. Contoh No. 123',
                'Kota': 'Jakarta',
                'No Telepon': '081234567890',
                'Jabatan': 'Anggota'
            }];

            const ws = XLSX.utils.json_to_sheet(template);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Template");

            const tanggal = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `Template_Import_Anggota_${tanggal}.xlsx`);
        }

        // Ekspor hasil import
        function eksporHasil() {
            if (hasilImport.length === 0) {
                alert('Tidak ada data untuk diekspor!');
                return;
            }

            const exportData = hasilImport.map(row => ({
                'No': row.no,
                'Status': row.status === 'success' ? 'Berhasil' : 'Gagal',
                'ID Anggota': row.idAnggota,
                'Username': row.username,
                'Nama Lengkap': row.nama,
                'Jenis Kelamin': row.jenisKelamin,
                'Alamat': row.alamat,
                'Kota': row.kota,
                'Jabatan': row.jabatan,
                'Keterangan': row.keterangan
            }));

            const ws = XLSX.utils.json_to_sheet(exportData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Hasil Import");

            const tanggal = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `Hasil_Import_Anggota_${tanggal}.xlsx`);
        }
    </script>
@endpush