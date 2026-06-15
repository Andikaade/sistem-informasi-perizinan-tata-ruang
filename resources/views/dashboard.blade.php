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
                
                <a href="#" class="btn btn-primary btn-sm px-3 text-nowrap">+ Tambah Data</a>
            </div>

            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-striped align-middle m-0" id="perizinanTable">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Pemohon</th>
                            <th>No Surat</th>
                            <th>Status</th>
                            <th width="20%" class="text-center"><i class="fas fa-cog me-1"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perizinanData as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row[1] ?? '-' }}</td> 
                                <td>{{ $row[2] ?? '-' }}</td> 
                                <td>
                                    @php
                                        $status = strtolower($row[3] ?? '');
                                        $badgeColor = 'bg-secondary';
                                        if($status == 'selesai') $badgeColor = 'bg-success';
                                        elseif($status == 'dalam proses') $badgeColor = 'bg-warning text-dark';
                                        elseif($status == 'dikembalikan') $badgeColor = 'bg-danger';
                                    @endphp
                                    <span class="badge {{ $badgeColor }}">{{ ucfirst($row[3] ?? 'Pending') }}</span>
                                </td>
                               <td class="text-nowrap">
                                    <a href="#" class="btn btn-sm btn-info text-white" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button type="button" class="btn btn-sm btn-warning text-white btn-update-status" 
                                            data-bs-toggle="modal" data-bs-target="#updateStatusModal" 
                                            data-id="{{ $index }}" 
                                            data-nama="{{ $row[1] ?? '-' }}" 
                                            data-surat="{{ $row[2] ?? '-' }}" 
                                            data-status="{{ strtolower($row[3] ?? '') }}">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-primary btn-edit-data" 
                                            data-bs-toggle="modal" data-bs-target="#editDataModal" 
                                            data-id="{{ $index }}" 
                                            data-nama="{{ $row[1] ?? '-' }}" 
                                            data-surat="{{ $row[2] ?? '-' }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('perizinan.destroy', $index) }}" method="POST" class="d-inline m-0" onsubmit="return window.konfirmasiHapus(event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Hapus Data">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data perizinan di Google Sheets.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>