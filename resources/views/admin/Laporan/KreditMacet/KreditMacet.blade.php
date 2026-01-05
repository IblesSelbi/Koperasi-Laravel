@extends('layouts.app')

@section('title', 'Laporan Kredit Macet')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Kredit Macet</h4>
                    <p class="text-muted fs-3 mb-0">Data pinjaman yang mengalami kemacetan pembayaran</p>
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
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-2"></i>Filter Periode</h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- Filter Periode -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-calendar text-primary"></i> Pilih Periode
                    </label>
                    <input type="month" class="form-control" id="filterPeriode" value="{{ date('Y-m') }}">
                </div>

                <!-- Action Buttons -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary w-100" onclick="filterData()">
                            <i class="ti ti-search"></i> Tampilkan
                        </button>
                        <button class="btn btn-outline-secondary" onclick="resetFilter()"
                            data-bs-toggle="tooltip" title="Reset Filter">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>

                <!-- Info Summary -->
                <div class="col-12 col-lg-6">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                        <span class="badge bg-danger-subtle text-danger shadow-sm px-3 py-2">
                            <i class="ti ti-alert-triangle"></i> Total Tagihan:
                            <strong id="totalTagihan">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
                        </span>
                        <span class="badge bg-warning-subtle text-warning shadow-sm px-3 py-2">
                            <i class="ti ti-exclamation-circle"></i> Sisa Tagihan:
                            <strong id="sisaTagihan">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong>
                        </span>
                    </div>
                </div>

            </div>

            <!-- Secondary Actions -->
            <div class="row mt-3 pt-3 border-top">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                            <i class="ti ti-printer"></i> Cetak Laporan
                        </button>
                        <button class="btn btn-success btn-sm" onclick="exportExcel()">
                            <i class="ti ti-file-spreadsheet"></i> Export Excel
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="kirimPemanggilan()">
                            <i class="ti ti-mail"></i> Kirim Surat Pemanggilan
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="tindakLanjut()">
                            <i class="ti ti-clipboard-check"></i> Tindak Lanjut
                        </button>

                        <span class="badge bg-info-subtle text-info shadow-sm border-2 px-3 py-2 ms-auto">
                            <i class="ti ti-alert-octagon"></i> Total Macet:
                            <strong id="totalData">{{ $totalData }}</strong> Anggota
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Info -->
    <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
        <i class="ti ti-alert-triangle fs-5 me-2"></i>
        <div>
            <strong>Periode:</strong> <span id="periodeText">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }}</span> |
            <strong>Status:</strong> <span class="badge bg-danger ms-2">KREDIT MACET</span>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive shadow-sm">
                <table id="tabelKreditMacet"
                    class="table table-hover align-middle rounded-2 border-1 overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" style="width: 5%;">No</th>
                            <th class="text-center align-middle" style="width: 10%;">Kode Pinjam</th>
                            <th class="align-middle" style="width: 15%;">Nama Anggota</th>
                            <th class="text-center align-middle" style="width: 12%;">Tanggal Pinjam</th>
                            <th class="text-center align-middle" style="width: 12%;">Tanggal Tempo</th>
                            <th class="text-center align-middle" style="width: 10%;">Lama Pinjam</th>
                            <th class="text-end align-middle" style="width: 12%;">Jumlah Tagihan</th>
                            <th class="text-end align-middle" style="width: 12%;">Dibayar</th>
                            <th class="text-end align-middle" style="width: 15%;">Sisa Tagihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kreditMacet as $index => $item)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="fw-semibold">{{ $index + 1 }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-danger-subtle text-danger fw-semibold px-2 py-1">{{ $item->kode_pinjam }}</span>
                                </td>
                                <td class="align-middle">
                                    <div>
                                        <strong class="text-dark">{{ $item->nama }}</strong><br>
                                        <small class="text-muted">ID: {{ $item->id_anggota }}</small>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->locale('id')->translatedFormat('d M Y') }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-danger px-2 py-1">
                                        <i class="ti ti-alert-triangle"></i> {{ \Carbon\Carbon::parse($item->tanggal_tempo)->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $item->lama_pinjam }}</span>
                                </td>
                                <td class="text-end align-middle">
                                    <strong class="text-danger">Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end align-middle">
                                    @if($item->dibayar > 0)
                                        <span class="text-success">Rp {{ number_format($item->dibayar, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">Rp 0</span>
                                    @endif
                                </td>
                                <td class="text-end align-middle">
                                    <strong class="text-danger fs-5">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="6" class="text-center align-middle">
                                <strong>Jumlah Total</strong>
                            </td>
                            <td class="text-end align-middle">
                                <strong class="text-danger">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end align-middle">
                                <strong class="text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end align-middle">
                                <strong class="text-danger fs-5">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize DataTable
        let table;
        $(document).ready(function () {
            table = $('#tabelKreditMacet').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[4, 'asc']], // Sort by tanggal tempo
                columnDefs: [
                    { orderable: false, targets: [0] }
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // Helper function to parse currency
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\Rp\.,]/g, '') * 1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // Total over all pages
                    var totalTagihan = api.column(6).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    var totalDibayar = api.column(7).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    var totalSisa = api.column(8).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Update footer
                    $(api.column(6).footer()).html(
                        '<strong class="text-danger">' + formatRupiah(totalTagihan) + '</strong>'
                    );
                    $(api.column(7).footer()).html(
                        '<strong class="text-success">' + formatRupiah(totalDibayar) + '</strong>'
                    );
                    $(api.column(8).footer()).html(
                        '<strong class="text-danger fs-5">' + formatRupiah(totalSisa) + '</strong>'
                    );

                    // Update summary badges
                    $('#totalTagihan').text(formatRupiah(totalTagihan));
                    $('#sisaTagihan').text(formatRupiah(totalSisa));
                }
            });

            // Update periode text
            updatePeriodeText();

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Function: Format Rupiah
        function formatRupiah(angka) {
            var number_string = angka.toString().replace(/[^,\d]/g, '');
            var split = number_string.split(',');
            var sisa = split[0].length % 3;
            var rupiah = split[0].substr(0, sisa);
            var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return 'Rp ' + rupiah;
        }

        // Function: Update Periode Text
        function updatePeriodeText() {
            const periodeInput = document.getElementById('filterPeriode').value;
            if (periodeInput) {
                const [year, month] = periodeInput.split('-');
                const date = new Date(year, month - 1);
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const monthName = monthNames[date.getMonth()] + ' ' + year;
                document.getElementById('periodeText').textContent = monthName;
            }
        }

        // Function: Filter Data
        function filterData() {
            const periode = $('#filterPeriode').val();

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mengambil data kredit macet',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simulate processing
            setTimeout(() => {
                updatePeriodeText();

                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Data Ditampilkan!',
                    text: 'Data kredit macet berhasil dimuat',
                    timer: 1500,
                    showConfirmButton: false
                });

                // TODO: Reload dengan filter
                // location.href = `{{ route('laporan.kredit-macet') }}?periode=${periode}`;
            }, 1000);
        }

        // Function: Reset Filter
        function resetFilter() {
            const today = new Date();
            const currentMonth = today.toISOString().slice(0, 7);
            $('#filterPeriode').val(currentMonth);

            updatePeriodeText();

            Swal.fire({
                icon: 'info',
                title: 'Filter Direset',
                text: 'Filter dikembalikan ke bulan ini',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Function: Cetak Laporan
        function cetakLaporan() {
            const periode = $('#filterPeriode').val();

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                icon: 'info',
                title: 'Membuka Jendela Cetak',
                text: 'Laporan akan dibuka di tab baru',
                timer: 1500,
                showConfirmButton: false
            });

            const url = `{{ route('laporan.kredit-macet.cetak') }}?periode=${periode}`;
            window.open(url, '_blank');
        }

        // Function: Export Excel
        function exportExcel() {
            const periode = $('#filterPeriode').val();

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Export Excel',
                text: 'File akan segera diunduh...',
                timer: 1500,
                showConfirmButton: false
            });

            window.location.href = `{{ route('laporan.kredit-macet.export.excel') }}?periode=${periode}`;
        }

        // Function: Kirim Pemanggilan
        function kirimPemanggilan() {
            const periode = $('#filterPeriode').val();
            const totalData = $('#totalData').text();

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Kirim Surat Pemanggilan?',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Surat pemanggilan akan dikirim kepada <strong>${totalData} anggota</strong> yang memiliki kredit macet pada periode <strong>${document.getElementById('periodeText').textContent}</strong></p>
                        
                        <div class="alert alert-warning" role="alert">
                            <i class="ti ti-alert-triangle"></i> 
                            <strong>Perhatian:</strong> Pastikan data sudah benar sebelum mengirim surat pemanggilan
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metode Pengiriman <span class="text-danger">*</span></label>
                            <select id="swal-metode" class="form-select">
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="cetak">Cetak Surat</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea id="swal-catatan" class="form-control" rows="3" placeholder="Opsional - Tambahkan catatan khusus"></textarea>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-send"></i> Ya, Kirim',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#dc3545',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const metode = document.getElementById('swal-metode').value;
                    const catatan = document.getElementById('swal-catatan').value;

                    if (!metode) {
                        Swal.showValidationMessage('Metode pengiriman harus dipilih!');
                        return false;
                    }

                    return { metode, catatan };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengirim...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Simulate sending
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dikirim!',
                            html: `Surat pemanggilan telah dikirim melalui <strong>${result.value.metode}</strong> kepada ${totalData} anggota`,
                            timer: 3000,
                            showConfirmButton: false
                        });

                        // TODO: Implement AJAX
                        // $.ajax({
                        //     url: '{{ route("laporan.kredit-macet.kirim-pemanggilan") }}',
                        //     type: 'POST',
                        //     data: {
                        //         _token: '{{ csrf_token() }}',
                        //         periode: periode,
                        //         metode: result.value.metode,
                        //         catatan: result.value.catatan
                        //     },
                        //     dataType: 'json',
                        //     success: function(response) {
                        //         if(response.status === 'success') {
                        //             Swal.fire({
                        //                 icon: 'success',
                        //                 title: 'Berhasil!',
                        //                 text: response.message,
                        //                 timer: 3000
                        //             });
                        //         }
                        //     }
                        // });
                    }, 1500);
                }
            });
        }

        // Function: Tindak Lanjut
        function tindakLanjut() {
            const periode = $('#filterPeriode').val();

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Tindak Lanjut Kredit Macet',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Pilih tindak lanjut untuk kredit macet periode <strong>${document.getElementById('periodeText').textContent}</strong></p>
                        
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action" onclick="tindakLanjutDetail('penjadwalan')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="ti ti-calendar-event text-info"></i> Penjadwalan Ulang</h6>
                                </div>
                                <p class="mb-1 text-muted small">Atur jadwal pembayaran baru dengan anggota</p>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" onclick="tindakLanjutDetail('restrukturisasi')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="ti ti-refresh text-warning"></i> Restrukturisasi Pinjaman</h6>
                                </div>
                                <p class="mb-1 text-muted small">Ubah skema pembayaran pinjaman</p>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" onclick="tindakLanjutDetail('peringatan')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="ti ti-alert-triangle text-danger"></i> Surat Peringatan</h6>
                                </div>
                                <p class="mb-1 text-muted small">Kirim surat peringatan resmi</p>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" onclick="tindakLanjutDetail('pertemuan')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="ti ti-users text-primary"></i> Jadwalkan Pertemuan</h6>
                                </div>
                                <p class="mb-1 text-muted small">Atur pertemuan dengan anggota</p>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" onclick="tindakLanjutDetail('penghapusan')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="ti ti-trash text-danger"></i> Penghapusan Tagihan</h6>
                                </div>
                                <p class="mb-1 text-muted small">Hapus tagihan yang tidak dapat ditagih (write-off)</p>
                            </a>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: '<i class="ti ti-x"></i> Tutup',
                width: '600px',
                customClass: {
                    cancelButton: 'btn btn-secondary'
                }
            });
        }

        // Function: Tindak Lanjut Detail
        function tindakLanjutDetail(jenis) {
            Swal.close();

            let title = '';

            switch (jenis) {
                case 'penjadwalan':
                    title = 'Penjadwalan Ulang Pembayaran';
                    break;
                case 'restrukturisasi':
                    title = 'Restrukturisasi Pinjaman';
                    break;
                case 'peringatan':
                    title = 'Surat Peringatan';
                    break;
                case 'pertemuan':
                    title = 'Jadwalkan Pertemuan';
                    break;
                case 'penghapusan':
                    title = 'Penghapusan Tagihan (Write-off)';
                    break;
            }

            Swal.fire({
                icon: 'success',
                title: 'Fitur Segera Hadir',
                html: `<p>Fitur <strong>${title}</strong> sedang dalam pengembangan</p>`,
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Update periode text on change
        document.getElementById('filterPeriode').addEventListener('change', function () {
            updatePeriodeText();
        });
    </script>
@endpush