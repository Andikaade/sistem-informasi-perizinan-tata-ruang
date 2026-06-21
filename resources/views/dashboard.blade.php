<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Petaru</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                @if(auth()->user()->is_admin == 1)
                    <a href="{{ route('users.index') }}" class="btn btn-warning btn-sm text-dark d-inline-flex align-items-center fw-bold">
                        <i class="fas fa-users me-2"></i>
                        Manajemen User
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning px-3 rounded-2 fw-medium">
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPerizinan">
                        <i class="fas fa-plus me-1"></i> Tambah Perizinan
                    </button>
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
                                $noAntrian = $row[1] ?? '-';
                                $namaPemohon = $row[2] ?? '-';
                                $noSurat = $row[3] ?? '-';
                                $deskripsi = $row[4] ?? '-';
                                $phone = $row[5] ?? '-';
                                $alamat = $row[6] ?? '-';
                                $tglPengajuan = $row[8] ?? '-';
                                $tglProses = $row[9] ?? '-';
                                $tglSelesai = $row[10] ?? '-';
                                $statusRaw = trim(strtolower($row[11] ?? ''));
                                
                                $createBy = $row[7] ?? '-';
                                $tglUpdate = $row[12] ?? '-';
                                $updateBy = $row[13] ?? '-';

                                $badgeColor = 'bg-secondary';
                                if ($statusRaw == 'selesai') {
                                    $badgeColor = 'bg-success';
                                } elseif ($statusRaw == 'dalam proses' || $statusRaw == 'proses') { 
                                    $badgeColor = 'bg-warning text-dark';
                                } elseif ($statusRaw == 'dikembalikan') {
                                    $badgeColor = 'bg-danger';
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center fw-semibold">{{ $noAntrian }}</td> 
                                <td>{{ $namaPemohon }}</td> 
                                <td>{{ $noSurat }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeColor }}">{{ ucfirst($row[11] ?? 'Pending') }}</span>
                                </td>
                                <td class="text-nowrap text-center">
                                    <button class="btn-detail-data btn btn-info btn-sm text-white"
                                        data-antrian="{{ $noAntrian }}"
                                        data-nama="{{ $namaPemohon }}"
                                        data-surat="{{ $noSurat }}"
                                        data-deskripsi="{{ $deskripsi }}"
                                        data-phone="{{ $phone }}"
                                        data-alamat="{{ $alamat }}"
                                        data-tgl-pengajuan="{{ $tglPengajuan }}"
                                        data-tgl-proses="{{ $tglProses }}"
                                        data-tgl-selesai="{{ $tglSelesai }}"
                                        data-status="{{ ucfirst($row[11] ?? 'Pending') }}"
                                        data-created-by="{{ $createBy }}"
                                        data-tgl-update="{{ $tglUpdate }}"
                                        data-update-by="{{ $updateBy }}"
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        data-bs-title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button class="btn-update-status btn btn-warning btn-sm text-white"
                                        data-index="{{ $index + 2 }}" 
                                        data-antrian="{{ $row[1] ?? '-' }}"
                                        data-nama="{{ $row[2] ?? '-' }}"
                                        data-surat="{{ $row[3] ?? '-' }}"
                                        data-status="{{ $row[11] ?? 'Pending' }}"
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        data-bs-title="Perbaharui Data">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>

                                    <button type="button" 
                                            class="btn btn-sm btn-primary btn-edit-data" 
                                            title="Edit Data"
                                            data-id="{{ $index }}" 
                                            data-antrian="{{ $noAntrian }}"
                                            data-nama="{{ $namaPemohon }}" 
                                            data-surat="{{ $noSurat }}"
                                            data-deskripsi="{{ $deskripsi }}"
                                            data-phone="{{ $phone }}"
                                            data-alamat="{{ $alamat }}"
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top" 
                                            data-bs-title="Ubah Data">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('perizinan.destroy', $index) }}" method="POST" class="d-inline m-0" onsubmit="return window.konfirmasiHapus(event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                data-bs-title="Hapus Data">
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

    {{-- MODAL 1: UPDATE STATUS PERIZINAN --}}
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
                            <label class="form-label bg-light text-muted small fw-bold text-uppercase d-block p-1 ps-2 rounded">No Antrian</label>
                            <input type="text" id="status_no_antrian" class="form-control bg-light fw-semibold" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label bg-light text-muted small fw-bold text-uppercase d-block p-1 ps-2 rounded">Nama Pemohon</label>
                            <input type="text" id="modal_nama_pemohon" class="form-control bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label bg-light text-muted small fw-bold text-uppercase d-block p-1 ps-2 rounded">No Surat</label>
                            <input type="text" id="modal_no_surat" class="form-control bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Pilih Status Baru</label>
                            <select name="status" id="modal_select_status" class="form-select" required>
                                <option value="proses">Proses</option>
                                <option value="selesai">Selesai</option>
                                <option value="dikembalikan">Dilanjutkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: EDIT DATA UTAMA --}}
    <div class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="editDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editDataModalLabel">Edit Data Perizinan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('perizinan.update-data') }}" method="POST" id="formEditData">
                    @csrf 
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="row_index" id="edit_row_index">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">No Antrian</label>
                            <input type="text" id="edit_no_antrian" class="form-control bg-light text-muted fw-bold" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pemohon</label>
                            <input type="text" name="nama_pemohon" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">No Surat</label>
                            <input type="text" name="no_surat" id="edit_no_surat" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Surat</label>
                            <textarea name="deskripsi_surat" id="edit_deskripsi" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone / WhatsApp</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control" rows="2" required></textarea>
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

    {{-- MODAL 3: DETAIL DATA --}}
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
                            <td id="detail_no_antrian" class="ps-3 fw-semibold"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Nama Pemohon</th>
                            <td id="detail_nama_pemohon" class="ps-3"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">No. Surat</th>
                            <td id="detail_no_surat" class="ps-3"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Deskripsi Surat</th>
                            <td id="detail_deskripsi_surat" class="ps-3 text-wrap"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">No. HP / WhatsApp</th>
                            <td id="detail_phone" class="ps-3"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Alamat</th>
                            <td id="detail_alamat" class="ps-3 text-wrap"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Tanggal Pengajuan</th>
                            <td id="detail_tgl_pengajuan" class="ps-3"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Tanggal Proses</th>
                            <td id="detail_tgl_proses" class="ps-3"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Tanggal Selesai</th>
                            <td id="detail_tgl_selesai" class="ps-3"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Status Perizinan</th>
                            <td class="ps-3">
                                <span id="detail_status" class="badge bg-secondary"></span>
                            </td>
                        </tr>
                        <tr class="table-info">
                            <th colspan="2" class="ps-3 text-dark small fw-bold text-uppercase text-right">Log Riwayat Data</th>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Dibuat oleh</th>
                            <td id="detail_created_by" class="ps-3 text-primary fw-semibold"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Tanggal Perubahan Data</th>
                            <td id="detail_tgl_update" class="ps-3"></td>
                        </tr>
                        <tr>
                            <th class="ps-3 bg-light text-muted small fw-bold text-uppercase">Perubahan Data oleh</th>
                            <td id="detail_updated_by" class="ps-3 text-primary fw-semibold"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 4: TAMBAH DATA (Sudah Diperbaiki) --}}
    <div class="modal fade" id="modalTambahPerizinan" tabindex="-1" aria-labelledby="modalTambahPerizinanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="modalTambahPerizinanLabel" style="color: #1a1a1a;">
                        <i class="fas fa-folder-plus text-primary me-2 fs-4"></i> Tambah Data Perizinan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form id="formTambahPerizinan" action="/dashboard/perizinan/store" method="POST" novalidate>
                    @csrf
                    <div class="modal-body px-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_no_antrian" class="form-label fw-semibold text-secondary small text-uppercase">Nomor Antrian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-list-ol"></i></span>
                                    <input type="text" class="form-control bg-light" id="add_no_antrian" name="no_antrian" placeholder="Memuat nomor..." readonly required>
                                    <div class="invalid-feedback">Nomor antrian wajib diisi otomatis oleh sistem.</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_nama_pemohon" class="form-label fw-semibold text-secondary small text-uppercase">Nama Pemohon</label>
                                <input type="text" class="form-control" id="add_nama_pemohon" name="nama_pemohon" placeholder="Masukkan nama lengkap pemohon" required>
                                <div class="invalid-feedback">Nama pemohon wajib diisi.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_no_surat" class="form-label fw-semibold text-secondary small text-uppercase">Nomor Surat</label>
                                <input type="text" class="form-control" id="add_no_surat" name="no_surat" placeholder="Masukkan nomor surat resmi" required>
                                <div class="invalid-feedback">Nomor surat wajib diisi.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_phone" class="form-label fw-semibold text-secondary small text-uppercase">No. HP / WhatsApp Pemohon</label>
                                <input type="text" class="form-control" id="add_phone" name="phone" placeholder="Contoh: 0812XXXXXXXX" required>
                                <div class="invalid-feedback">Nomor kontak wajib diisi untuk tracking.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="add_deskripsi" class="form-label fw-semibold text-secondary small text-uppercase">Deskripsi / Perihal Perizinan</label>
                            <textarea class="form-control" id="add_deskripsi" name="deskripsi_surat" rows="3" placeholder="Jelaskan perihal atau detail perizinan secara singkat..." required></textarea>
                            <div class="invalid-feedback">Deskripsi perizinan wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label for="add_alamat" class="form-label fw-semibold text-secondary small text-uppercase">Alamat Pemohon</label>
                            <textarea class="form-control" id="add_alamat" name="alamat" rows="2" placeholder="Masukkan alamat lengkap pemohon..." required></textarea>
                            <div class="invalid-feedback">Alamat wajib diisi.</div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 8px; background-color: #0061ff;">Simpan Data Perizinan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>