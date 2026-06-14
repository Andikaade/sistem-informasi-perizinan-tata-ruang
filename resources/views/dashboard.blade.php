<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Petaru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

   <nav class="navbar navbar-dark bg-dark px-4 py-2 d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold" href="#">Pemeritahan Kab. Sijunjung</a>
        
        <div class="d-flex align-items-center gap-2">
            <a href="#" class="btn btn-sm btn-secondary">Manajemen User</a>
            
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
            </form>
        </div> 
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between mb-3 mt-5">
            <h2>Selamat Datang {{ Auth::user()->username }}</h2>
        </div>

        <div class="card p-4 mt-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold">Daftar Perizinan</h4>
                <a href="#" class="btn btn-primary">+ Tambah Data</a>
            </div>

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Pemohon</th>
                        <th>No Surat</th>
                        <th>Status</th>
                        <th width="20%">Aksi</th>
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
                            <td>
                                <a href="#" class="btn btn-sm btn-info text-white">Lihat</a>
                                <a href="#" class="btn btn-sm btn-warning text-white">Edit</a>
                                <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada data perizinan di Google Sheets.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>