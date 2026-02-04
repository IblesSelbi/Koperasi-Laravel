@extends('layouts.app')

@section('title', 'Import Data Anggota')

@push('styles')
    <!-- DataTables CSS (CDN) -->
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
                    <a href="{{ route('master.data-anggota') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0"><i class="ti ti-upload"></i> Upload File Excel</h5>
        </div>
        <div class="card-body">
            <form id="formImport" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Pilih File Excel <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="fileExcel" name="file_excel" accept=".xls,.xlsx"
                            required>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-upload"></i> Upload & Import
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <small class="text-muted">
                            <i class="ti ti-info-circle"></i>
                            Format file: .xls atau .xlsx (Max: 5MB)
                        </small>
                    </div>
                </div>
            </form>

            <!-- Info Box -->
            <button type="button" id="btnPetunjuk" class="btn btn-info mb-3 mt-3" onclick="togglePetunjuk()">
                <i class="ti ti-info-circle me-1"></i> Petunjuk Import Anggota
            </button>


            <div id="petunjukImport" class="alert alert-info d-none align-items-start mt-1" role="alert">
                <i class="ti ti-info-circle fs-4 me-2 mt-1"></i>
                <div>
                    <strong>Petunjuk Pengisian Data Anggota</strong>
                    <ul class="mb-0 mt-2">
                        <li>Gunakan <strong>file Excel sesuai template</strong> agar data bisa dibaca sistem dengan benar.
                        </li>
                        <li>Kolom yang <strong>wajib diisi</strong>:
                            <strong>Username, Nama Lengkap, Jenis Kelamin, Alamat, dan Kota</strong>.
                        </li>
                        <li><strong>Cara kerja sistem saat impor:</strong>
                            <ul class="mt-1">
                                <li>• Jika <strong>Username belum pernah ada</strong>,
                                    data akan <span class="fw-semibold text-success">ditambahkan sebagai anggota
                                        baru</span>.
                                </li>
                                <li>• Jika <strong>Username sudah ada</strong> dan <strong>datanya berbeda</strong>,
                                    data akan <span class="fw-semibold text-secondary">diperbarui</span>.
                                </li>
                                <li>• Jika <strong>Username sudah ada</strong> dan <strong>tidak ada perubahan
                                        data</strong>,
                                    data akan <span class="fw-semibold text-warning">dilewati</span>.
                                </li>
                            </ul>
                        </li>
                        <li>
                            <strong>• Tabel hasil impor</strong> hanya menampilkan
                            <strong>data baru atau data yang berubah</strong>.
                        </li>
                        <li>
                            <strong>• Password awal</strong> untuk anggota baru adalah
                            <strong class="text-primary">UserKoperasi</strong>.
                            <br>
                            <small class="text-muted">(Jika data diperbarui, password tidak ikut berubah)</small>
                        </li>
                        <li>
                            Unduh file contoh (berisi data anggota saat ini):
                            <a href="{{ route('master.data-anggota.export') }}" class="fw-bold text-decoration-underline"
                                target="_blank">
                                <i class="ti ti-download"></i> Template Import Anggota (Excel)
                            </a>
                            <br>
                            <small class="text-muted">
                                💡 File ini berisi data anggota yang sudah ada. Anda dapat mengedit dan import kembali untuk
                                update data.
                            </small>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Password Info Box -->
            <div class="alert alert-warning d-flex align-items-center mt-2" role="alert">
                <i class="ti ti-lock fs-4 me-2"></i>
                <div>
                    <strong>Keamanan Password:</strong> Semua anggota yang diimport akan mendapatkan password default
                    <strong>"UserKoperasi"</strong>. Mohon segera informasikan kepada anggota untuk mengubah password
                    setelah login pertama kali demi keamanan akun.
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
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0"><i class="ti ti-table"></i> Hasil Import Data</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge bg-success-subtle text-success fs-3 px-3 py-2">
                        <i class="ti ti-check"></i> Berhasil: <strong id="successCount">0</strong>
                    </span>
                    <span class="badge bg-info-subtle text-info fs-3 px-3 py-2 ms-2">
                        <i class="ti ti-plus"></i> Tambah Baru: <strong id="insertCount">0</strong>
                    </span>
                    <span class="badge bg-warning-subtle text-warning fs-3 px-3 py-2 ms-2">
                        <i class="ti ti-edit"></i> Update: <strong id="updateCount">0</strong>
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary fs-3 px-3 py-2 ms-2" id="skippedBadge"
                        style="display: none;">
                        <i class="ti ti-minus"></i> Tidak Berubah: <strong id="skippedCount">0</strong>
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
                    <thead class="table-primary">
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
                            <th class="text-center" width="250px">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTabelHasil">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables (CDN) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- SheetJS untuk parse Excel (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        let hasilImport = [];
        let table;

        // Handle Form Submit
        document.getElementById('formImport').addEventListener('submit', function (e) {
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
            reader.onload = function (e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });

                    // Ambil sheet pertama
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                    // Deteksi format file: Template atau Export
                    // Parse tanpa header dulu untuk cek struktur
                    const rawData = XLSX.utils.sheet_to_json(firstSheet, {
                        header: 1, // Ambil sebagai array 2D
                        defval: '',
                        raw: false
                    });

                    console.log('📊 RAW DATA (5 rows pertama):', rawData.slice(0, 10));

                    let jsonData = [];
                    let headerRow = -1;

                    // Cari row yang berisi header "Username" atau "Nama Lengkap"
                    for (let i = 0; i < Math.min(rawData.length, 15); i++) {
                        const row = rawData[i];
                        const rowStr = row.join('|').toLowerCase();

                        if (rowStr.includes('username') && rowStr.includes('nama lengkap')) {
                            headerRow = i;
                            console.log(`✅ Header ditemukan di row ${i + 1}:`, row);
                            break;
                        }
                    }

                    if (headerRow === -1) {
                        throw new Error('Header tidak ditemukan! Pastikan file Excel memiliki kolom "Username" dan "Nama Lengkap"');
                    }

                    // Parse ulang dengan header yang benar
                    jsonData = XLSX.utils.sheet_to_json(firstSheet, {
                        range: headerRow, // Mulai dari row header
                        defval: '',
                        raw: false
                    });

                    console.log('📊 Total rows setelah parsing:', jsonData.length);
                    console.log('📊 Kolom yang tersedia:', jsonData.length > 0 ? Object.keys(jsonData[0]) : []);
                    console.log('📊 Sample data (3 rows):', jsonData.slice(0, 3));

                    // Filter: ambil hanya row yang merupakan data anggota valid
                    jsonData = jsonData.filter((row, index) => {
                        const values = Object.values(row);

                        // Skip row kosong
                        if (values.every(v => !v || v === '')) {
                            console.log(`❌ Skip row ${index}: kosong`);
                            return false;
                        }

                        // Cari username dari berbagai kemungkinan nama kolom
                        let username = row['Username'] || row['username'] || '';

                        if (!username || username === '') {
                            console.log(`❌ Skip row ${index}: username kosong`);
                            return false;
                        }

                        // Skip jika username masih berisi keyword header
                        if (username.toLowerCase().includes('username')) {
                            console.log(`❌ Skip row ${index}: username adalah header`);
                            return false;
                        }

                        // Skip row ringkasan/footer berdasarkan pattern spesifik
                        const firstCol = String(values[0] || '').toLowerCase();

                        // Skip jika kolom pertama berisi bullet/ringkasan
                        if (firstCol.startsWith('•') ||
                            firstCol.includes('total anggota') ||
                            firstCol.includes('status aktif') ||
                            firstCol.includes('status non aktif') ||
                            firstCol.includes('jabatan anggota') ||
                            firstCol.includes('jabatan pengurus') ||
                            firstCol.includes('laki-laki:') ||
                            firstCol.includes('perempuan:')) {
                            console.log(`❌ Skip row ${index}: ringkasan (first col: "${firstCol}")`);
                            return false;
                        }

                        // Skip jika ada keyword footer di username
                        const usernameLower = username.toLowerCase();
                        if (usernameLower.includes('dicetak pada') ||
                            usernameLower.includes('catatan:') ||
                            usernameLower.includes('koperasi -') ||
                            usernameLower.includes('© 2') ||
                            usernameLower.includes('ringkasan')) {
                            console.log(`❌ Skip row ${index}: footer keyword di username`);
                            return false;
                        }

                        console.log(`✅ Valid row ${index}:`, {
                            username: username,
                            nama: row['Nama Lengkap'] || row['Nama'] || row['nama']
                        });

                        return true;
                    });

                    console.log('✅ Data setelah filter:', jsonData);
                    console.log('✅ Jumlah data valid:', jsonData.length);

                    // Validasi dan format data
                    processAndSendData(jsonData);

                } catch (error) {
                    document.getElementById('loadingIndicator').style.display = 'none';
                    showError('Gagal membaca file Excel! Pastikan format file benar. Error: ' + error.message);
                    console.error('Parse Error:', error);
                }
            };
            reader.readAsArrayBuffer(file);
        });

        // Process and send data to server
        function processAndSendData(jsonData) {
            const validData = [];

            jsonData.forEach((row) => {
                // Fungsi helper untuk ambil value dengan berbagai variasi nama kolom
                const getValue = (row, ...keys) => {
                    for (let key of keys) {
                        // Coba exact match dulu
                        if (row[key] !== undefined && row[key] !== null && row[key] !== '') {
                            return String(row[key]).trim();
                        }

                        // Coba case-insensitive match
                        const foundKey = Object.keys(row).find(k =>
                            k.toLowerCase().trim() === key.toLowerCase().trim()
                        );
                        if (foundKey && row[foundKey] !== undefined && row[foundKey] !== null && row[foundKey] !== '') {
                            return String(row[foundKey]).trim();
                        }
                    }
                    return '';
                };

                // Format data sesuai dengan struktur database
                const formattedRow = {
                    username: getValue(row, 'Username', 'username'),
                    nama: getValue(row, 'Nama Lengkap', 'Nama lengkap', 'Nama', 'nama'),
                    jenis_kelamin: getValue(row, 'Jenis Kelamin', 'Jenis kelamin', 'JenisKelamin', 'jenis_kelamin'),
                    tempat_lahir: getValue(row, 'Tempat Lahir', 'Tempat lahir', 'TempatLahir', 'tempat_lahir'),
                    tanggal_lahir: formatTanggalExcel(getValue(row, 'Tanggal Lahir', 'Tanggal lahir', 'TanggalLahir', 'tanggal_lahir')),
                    status: getValue(row, 'Status', 'status'),
                    departement: getValue(row, 'Departement', 'departement'),
                    pekerjaan: getValue(row, 'Pekerjaan', 'pekerjaan'),
                    agama: getValue(row, 'Agama', 'agama'),
                    alamat: getValue(row, 'Alamat', 'alamat'),
                    kota: getValue(row, 'Kota', 'kota'),
                    no_telp: getValue(row, 'No. Telepon', 'No Telepon', 'NoTelepon', 'no_telp', 'No Telp'),
                    jabatan: getValue(row, 'Jabatan', 'jabatan') || 'Anggota'
                };

                console.log('📝 Formatted row:', formattedRow);

                // Skip jika username kosong (data tidak valid)
                if (!formattedRow.username) {
                    console.warn('⚠️ Skip row karena username kosong:', row);
                    return;
                }

                validData.push(formattedRow);
            });

            // Debug: Log data yang akan dikirim
            console.log('✅ Data yang akan dikirim ke server:', validData);
            console.log('✅ Jumlah data valid:', validData.length);

            if (validData.length === 0) {
                document.getElementById('loadingIndicator').style.display = 'none';
                showError('Tidak ada data valid yang dapat diimport. Pastikan file Excel sesuai template.');
                return;
            }

            // Send to server
            fetch("{{ route('master.data-anggota.import.process') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ data: validData })
            })
                .then(async res => {
                    const data = await res.json();

                    // Debug: Log response
                    console.log('Response Status:', res.status);
                    console.log('Response Data:', data);

                    if (!res.ok) {
                        // Jika status bukan 200-299
                        throw {
                            status: res.status,
                            data: data
                        };
                    }

                    return data;
                })
                .then(response => {
                    document.getElementById('loadingIndicator').style.display = 'none';

                    if (response.success) {
                        hasilImport = response.results;

                        // Show success alert dengan info detail
                        let message = response.message;

                        document.getElementById('successMessage').textContent = message;
                        document.getElementById('successAlert').style.display = 'block';

                        // Update counter
                        document.getElementById('successCount').textContent = response.successCount || 0;
                        document.getElementById('insertCount').textContent = response.insertCount || 0;
                        document.getElementById('updateCount').textContent = response.updateCount || 0;
                        document.getElementById('failCount').textContent = response.failCount || 0;

                        // Show/hide skipped badge
                        const skippedCount = response.skippedCount || 0;
                        if (skippedCount > 0) {
                            document.getElementById('skippedCount').textContent = skippedCount;
                            document.getElementById('skippedBadge').style.display = 'inline-block';
                        } else {
                            document.getElementById('skippedBadge').style.display = 'none';
                        }

                        // Display result table (hanya tampilkan data yang berubah atau baru)
                        if (hasilImport.length > 0) {
                            displayResultTable();
                        } else {
                            // Semua data tidak berubah
                            document.getElementById('resultTableCard').style.display = 'none';
                        }

                        // Reset form
                        document.getElementById('formImport').reset();
                    } else {
                        showError(response.message || 'Gagal import data');
                    }
                })
                .catch(error => {
                    document.getElementById('loadingIndicator').style.display = 'none';

                    console.error('Error Detail:', error);

                    let errorMessage = 'Terjadi kesalahan saat mengirim data ke server';

                    if (error.status === 422 && error.data) {
                        // Validation error
                        if (error.data.errors) {
                            errorMessage = 'Validasi gagal:\n';
                            Object.keys(error.data.errors).forEach(key => {
                                errorMessage += `- ${error.data.errors[key].join(', ')}\n`;
                            });
                        } else if (error.data.message) {
                            errorMessage = error.data.message;
                        }
                    } else if (error.data && error.data.message) {
                        errorMessage = error.data.message;
                    }

                    showError(errorMessage);
                });
        }

        // Format tanggal dari Excel
        function formatTanggalExcel(tanggal) {
            if (!tanggal) return null;

            try {
                // Jika sudah format YYYY-MM-DD
                if (typeof tanggal === 'string' && tanggal.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    return tanggal;
                }

                // Jika format DD/MM/YYYY
                if (typeof tanggal === 'string' && tanggal.match(/^\d{1,2}\/\d{1,2}\/\d{4}$/)) {
                    const parts = tanggal.split('/');
                    if (parts.length === 3) {
                        const day = parts[0].padStart(2, '0');
                        const month = parts[1].padStart(2, '0');
                        const year = parts[2];
                        return `${year}-${month}-${day}`;
                    }
                }

                // Jika format MM/DD/YYYY (US format)
                if (typeof tanggal === 'string' && tanggal.match(/^\d{1,2}\/\d{1,2}\/\d{4}$/)) {
                    // Coba parse dengan Date object
                    const date = new Date(tanggal);
                    if (!isNaN(date.getTime())) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    }
                }

                // Jika Excel date serial number
                if (typeof tanggal === 'number') {
                    const date = XLSX.SSF.parse_date_code(tanggal);
                    return `${date.y}-${String(date.m).padStart(2, '0')}-${String(date.d).padStart(2, '0')}`;
                }

                // Jika format string lain, coba parse
                if (typeof tanggal === 'string') {
                    const date = new Date(tanggal);
                    if (!isNaN(date.getTime())) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    }
                }
            } catch (error) {
                console.warn('Error parsing date:', tanggal, error);
            }

            return null;
        }

        // Display result table
        function displayResultTable() {
            const tbody = document.getElementById('bodyTabelHasil');
            tbody.innerHTML = '';

            hasilImport.forEach((row, index) => {
                const statusBadge = row.status === 'success' ?
                    '<span class="badge bg-success-subtle text-success fw-semibold px-3 py-1"><i class="ti ti-check"></i> Berhasil</span>' :
                    '<span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-1"><i class="ti ti-x"></i> Gagal</span>';

                const keteranganClass = row.status === 'success' ? 'text-success' : 'text-danger';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                                                                    <td class="text-center text-muted fw-medium">${index + 1}</td>
                                                                    <td class="text-center">${statusBadge}</td>
                                                                    <td><span class="badge bg-primary-subtle text-primary">${row.id_anggota || '-'}</span></td>
                                                                    <td>${row.username || '-'}</td>
                                                                    <td><strong>${row.nama || '-'}</strong></td>
                                                                    <td>${row.jenis_kelamin || '-'}</td>
                                                                    <td>${row.alamat || '-'}</td>
                                                                    <td>${row.kota || '-'}</td>
                                                                    <td>${row.jabatan || '-'}</td>
                                                                    <td class="text-center ${keteranganClass} fw-semibold">${row.keterangan || '-'}</td>
                                                                `;
                tbody.appendChild(tr);
            });

            // Show table card
            document.getElementById('resultTableCard').style.display = 'block';

            // Init DataTable
            if ($.fn.DataTable.isDataTable('#tabelHasil')) {
                $('#tabelHasil').DataTable().destroy();
            }

            table = $('#tabelHasil').DataTable({
                language: {
                    url: '{{ asset("assets/datatables/i18n/id.json") }}' // Gunakan local file
                },
                pageLength: 10,
                order: [[0, 'asc']],
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
            }, 8000);
        }


        // Ekspor hasil import
        function eksporHasil() {
            if (hasilImport.length === 0) {
                alert('Tidak ada data untuk diekspor!');
                return;
            }

            const exportData = hasilImport.map((row, index) => ({
                'No': index + 1,
                'Status': row.status === 'success' ? 'Berhasil' : 'Gagal',
                'ID Anggota': row.id_anggota || '-',
                'Username': row.username || '-',
                'Nama Lengkap': row.nama || '-',
                'Keterangan': row.keterangan || '-'
            }));

            const ws = XLSX.utils.json_to_sheet(exportData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Hasil Import");

            const tanggal = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `Hasil_Import_Anggota_${tanggal}.xlsx`);
        }

        function togglePetunjuk() {
            const el = document.getElementById('petunjukImport');
            const btn = document.getElementById('btnPetunjuk');

            const isHidden = el.classList.contains('d-none');

            if (isHidden) {
                el.classList.remove('d-none');
                el.classList.add('d-flex');
                btn.innerHTML = '<i class="ti ti-eye-off me-1"></i> Sembunyikan Petunjuk';
            } else {
                el.classList.remove('d-flex');
                el.classList.add('d-none');
                btn.innerHTML = '<i class="ti ti-info-circle me-1"></i> Petunjuk Import Anggota';
            }
        }

    </script>
@endpush