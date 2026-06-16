<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Petaru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm w-100">
        <div class="container py-2 d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40" class="me-2">
                <span class="mb-0 h5 fw-semibold text-dark">Pemerintahan Kab. Sijunjung</span>
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="btn btn-dark btn-sm fw-medium px-2">Manajemen User</a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger px-3 rounded-2 fw-medium">
                        Logout
                    </button>
                </form>
            </div> 
        </div>
    </nav>

    <div class="container mt-4">
        <div class="bg-white p-3 rounded-3 shadow-sm d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="text-muted d-block small">Selamat Datang,</span>
                <h5 class="fw-bold text-dark mb-0">
                    {{ Auth::user()->name ?? Auth::user()->username }}
                </h5>
            </div>
            <div>
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show small py-1 mb-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success px-3 py-2 rounded-pill">
                    Administrator Aktif
                </span>
            </div>
        </div>

        <div class="card p-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
                <h4 class="fw-bold mb-0 text-nowrap">Daftar Perizinan</h4>
                
                <div class="d-flex gap-2 w-100 justify-content-md-end" style="max-width: 600px;">
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari Antrian, Nama, atau No Surat...">
                    
                    <select id="statusFilter" class="form-select form-select-sm" style="max-width: 180px;">
                        <option value="">-- Semua Status --</option>
                        <option value="selesai">Selesai</option>
                        <option value="dalam proses">Dalam Proses</option>
                        <option value="dikembalikan">Dikembalikan</option>
                    </select>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-success btn-sm px-3 text-nowrap"><i class="fas fa-file-excel me-1"></i> Ekspor Excel</a>
                    <a href="#" class="btn btn-primary btn-sm px-3 text-nowrap">+ Tambah Data</a>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-striped align-middle m-0" id="perizinanTable">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th class="text-center">No Antrian</th>
                            <th>Nama Pemohon</th>
                            <th>No Surat</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 1%; text-nowrap: true;"><i class="fas fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perizinanData as $index => $row)
                            @php
                                // Pemetaan indeks array berdasarkan Google Sheets update (A-K)
                                $noAntrian = $row[1] ?? '-';
                                $namaPemohon = $row[2] ?? '-';
                                $noSurat = $row[3] ?? '-';
                                $deskripsi = $row[4] ?? '-';
                                $phone = $row[5] ?? '-';
                                $alamat = $row[6] ?? '-';
                                $tglPengajuan = $row[7] ?? '-';
                                $tglProses = $row[8] ?? '-';
                                $tglSelesai = $row[9] ?? '-';
                                $statusRaw = strtolower($row[10] ?? '');

                                // Logika Warna Badge Status
                                $badgeColor = 'bg-secondary';
                                if($statusRaw == 'selesai') $badgeColor = 'bg-success';
                                elseif($statusRaw == 'dalam proses') $badgeColor = 'bg-warning text-dark';
                                elseif($statusRaw == 'dikembalikan') $badgeColor = 'bg-danger';
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center fw-semibold">{{ $noAntrian }}</td> 
                                <td>{{ $namaPemohon }}</td> 
                                <td>{{ $noSurat }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeColor }}">{{ ucfirst($row[10] ?? 'Pending') }}</span>
                                </td>
                                <td class="text-nowrap text-center">
                                    <button class="btn-detail-data btn btn-info btn-sm text-white"
                                        data-antrian="{{ $row[1] ?? '-' }}"
                                        data-nama="{{ $row[2] ?? '-' }}"
                                        data-surat="{{ $row[3] ?? '-' }}"
                                        data-deskripsi="{{ $row[4] ?? '-' }}"
                                        data-phone="{{ $row[5] ?? '-' }}"
                                        data-alamat="{{ $row[6] ?? '-' }}"
                                        data-tgl-pengajuan="{{ $row[7] ?? '-' }}"
                                        data-tgl-proses="{{ $row[8] ?? '-' }}"
                                        data-tgl-selesai="{{ $row[9] ?? '-' }}"
                                        data-status="{{ $row[10] ?? 'Pending' }}">
                                        <i class="fas fa-eye"></i> 
                                    </button>

                                    <button type="button" 
                                            class="btn btn-sm btn-warning text-white btn-update-status" 
                                            title="Update Status"
                                            data-id="{{ $index }}" 
                                            data-nama="{{ $namaPemohon }}" 
                                            data-surat="{{ $noSurat }}" 
                                            data-status="{{ $statusRaw }}">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>

                                    <button type="button" 
                                            class="btn btn-sm btn-primary btn-edit-data" 
                                            title="Edit Data"
                                            data-id="{{ $index }}" 
                                            data-nama="{{ $namaPemohon }}" 
                                            data-surat="{{ $noSurat }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('perizinan.destroy', $index) }}" method="POST" class="d-inline m-0" onsubmit="return window.konfirmasiHapus(event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Belum ada data perizinan di Google Sheets.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="updateStatusModalLabel">Update Status Perizinan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('perizinan.update-status') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="row_index" id="modal_row_index">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Nama Pemohon</label>
                            <input type="text" id="modal_nama_pemohon" class="form-control bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">No Surat</label>
                            <input type="text" id="modal_no_surat" class="form-control bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Status Baru</label>
                            <select name="status" id="modal_status" class="form-select" required>
                                <option value="selesai">Selesai</option>
                                <option value="dalam proses">Dalam Proses</option>
                                <option value="dikembalikan">Dikembalikan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="editDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editDataModalLabel">Edit Data Perizinan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('perizinan.update-data') }}" method="POST">
                    @csrf 
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="row_index" id="edit_row_index">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pemohon</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">No Surat</label>
                            <input type="text" name="no_surat" id="edit_no_surat" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm px-3">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailDataModal" tabindex="-1" aria-labelledby="detailDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="detailDataModalLabel">
                        <i class="fas fa-info-circle me-2 text-info"></i>Detail Pengajuan Perizinan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-bordered table-striped m-0">
                        <tr>
                            <th style="width: 30%;" class="ps-3 bg-light text-muted small fw-bold text-uppercase">No Antrian</th>
                            <td id="detail_no_antrian" class="ps-3 fw-semibold">{{ $noAntrian }}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Nama Pemohon</th>
                            <td id="detail_nama_pemohon" class="ps-3">{{$namaPemohon}}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">No. Surat</th>
                            <td id="detail_no_surat" class="ps-3">{{$noSurat}}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Deskripsi Surat</th>
                            <td id="detail_deskripsi_surat" class="ps-3 text-wrap">{{$deskripsi}}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">No. HP / WhatsApp</th>
                            <td id="detail_phone" class="ps-3">{{$phone}}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Alamat</th>
                            <td id="detail_alamat" class="ps-3 text-wrap">{{$alamat}}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Tanggal Pengajuan</th>
                            <td id="detail_tgl_pengajuan" class="ps-3">{{$tglPengajuan}}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Tanggal Proses</th>
                            <td id="detail_tgl_proses" class="ps-3">{{$tglProses}}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Tanggal Selesai</th>
                            <td id="" class="ps-3">{{$tglSelesai}}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Status Perizinan</th>
                            <td class="ps-3">
                                <span id="detail_status" class="badge bg-secondary"></span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>