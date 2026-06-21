<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Perizinan Tata Ruang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm w-100 mb-4">
        <div class="container py-2 d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40" class="me-2">
                <span class="mb-0 h5 fw-semibold text-dark">Pemerintahan Kab. Sijunjung</span>
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-warning btn-sm fw-medium px-2"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning px-3 rounded-2 fw-medium">
                        Logout
                    </button>
                </form>
            </div> 
        </div>
    </nav>

    <div class="container mb-5">
        
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted small">Dashboard</a></li>
                        <li class="breadcrumb-item active small" aria-current="page">Manajemen User</li>
                    </ol>
                </nav>
                <h4 class="fw-bold text-dark mb-0">Daftar Pengguna Sistem</h4>
            </div>
            
            <div class="gap-2 d-flex">
                <button class="btn btn-primary btn-sm px-3 rounded-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus me-1"></i> Tambah User Baru
                </button>
            </div>
        </div>

        {{-- Alert Flash Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tabel Manajemen User --}}
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80" class="text-center py-3">No</th>
                            <th class="py-3">Username</th>
                            <th class="py-3">Nama Lengkap</th>
                            <th class="py-3">NIP</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Jabatan</th>
                            <th class="py-3">No Kontak</th>
                            <th class="py-3">Role Akses</th>
                            <th class="py-3">Dibuat Pada</th>
                            <th class="py-3">Status</th>
                            <th width="150" class="text-center py-3"><i class="fas fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                <td class="text-secondary">{{ $user->username }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                    @if($user->id === auth()->user()->id)
                                        <span class="badge bg-success mt-1" style="font-size: 9px; padding: 3px 6px;">Administrator Aktif</span>
                                    @endif
                                </td>
                                <td class="text-secondary">{{ $user->nip ?? '-' }}</td>
                                <td class="text-secondary">{{ $user->email }}</td>
                                <td class="text-secondary">{{ $user->title ?? '-' }}</td>
                                <td class="text-secondary">{{ $user->phone ?? '-' }}</td>
                                <td class="text-secondary">
                                    @if ($user->is_admin == 1)
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-3 py-2" style="border-radius: 30px;">
                                            <i class="fas fa-shield-alt me-1"></i> Master Administrator
                                        </span>
                                    @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2" style="border-radius: 30px;">
                                            <i class="fas fa-user me-1"></i> Admin
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success" style="padding: 5px 10px; font-size: 11px; border-radius: 6px;">Aktif</span>
                                    @else
                                        <span class="badge bg-danger" style="padding: 5px 10px; font-size: 11px; border-radius: 6px;">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-warning rounded-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal{{ $user->id }}" 
                                                title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Hapus User" {{ $user->id === auth()->user()->id ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- MODAL EDIT USER --}}
                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit me-2 text-warning"></i>Edit Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body py-3">
                                                
                                                {{-- Baris 1: Nama Lengkap & NIP --}}
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Nama Lengkap</label>
                                                        <input type="text" name="name" class="form-control rounded-2" value="{{ $user->name }}" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">NIP (Nomor Induk Pegawai)</label>
                                                        <input type="text" name="nip" class="form-control rounded-2" value="{{ $user->nip }}" placeholder="Masukkan NIP jika ASN">
                                                    </div>
                                                </div>

                                                {{-- Baris 2: Username & Alamat Email --}}
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Username</label>
                                                        <input type="text" name="username" class="form-control rounded-2" value="{{ $user->username }}" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Alamat Email</label>
                                                        <input type="email" name="email" class="form-control rounded-2" value="{{ $user->email }}" required>
                                                    </div>
                                                </div>

                                                {{-- Baris 3: Jabatan, No HP, & Role Akses --}}
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Jabatan / Title</label>
                                                        <input type="text" name="title" class="form-control rounded-2" value="{{ $user->title }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">No. HP / WhatsApp</label>
                                                        <input type="text" name="phone" class="form-control rounded-2" value="{{ $user->phone }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Role Akses</label>
                                                        <select name="is_admin" class="form-select rounded-2" required>
                                                            <option value="0" {{ $user->is_admin == 0 ? 'selected' : '' }}>Admin</option>
                                                            <option value="1" {{ $user->is_admin == 1 ? 'selected' : '' }}>Master Administrator</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <hr class="text-muted opacity-25 my-3">
                                                
                                                <div class="form-text text-muted mb-3 bg-light p-2 rounded small">
                                                    <i class="fas fa-info-circle me-1 text-primary"></i> Kosongkan kolom di bawah jika tidak ingin mengubah password lama.
                                                </div>

                                                {{-- Baris 4: Password Baru & Konfirmasi --}}
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Password Baru</label>
                                                        <input type="password" name="password" class="form-control rounded-2" placeholder="Minimal 6 karakter">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Konfirmasi Password Baru</label>
                                                        <input type="password" name="password_confirmation" class="form-control rounded-2" placeholder="Ulangi password baru">
                                                    </div>
                                                </div>

                                                {{-- Baris 5: Status Akun --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Status Akun</label>
                                                    <select name="is_active" class="form-select rounded-2" required>
                                                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Non-Aktif (Blokir Akses)</option>
                                                    </select>
                                                </div>

                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light btn-sm px-3 rounded-2" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning btn-sm px-3 rounded-2 fw-medium text-dark">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="fas fa-user-slash d-block fs-2 mb-2 text-black-50"></i>
                                    Belum ada data user tambahan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH USER BARU --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-user-plus me-3 text-primary fs-4"></i>
                        Tambah User Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body py-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li class="fw-bold small">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Baris 1: Nama Lengkap & NIP --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">NIP (Nomor Induk Pegawai)</label>
                                <input type="text" name="nip" class="form-control" placeholder="Contoh: 19880102XXXXXXXXXX">
                            </div>
                        </div>

                        {{-- Baris 2: Username & Alamat Email --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Contoh: admin_sijunjung" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Alamat Email</label>
                                <input type="email" name="email" class="form-control" placeholder="contoh: user@sijunjung.go.id" required>
                            </div>
                        </div>

                        {{-- Baris 3: Jabatan, No. HP, & Role Akses --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Jabatan / Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Contoh: Kabid Tata Ruang">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">No. HP / WhatsApp</label>
                                <input type="text" name="phone" class="form-control" placeholder="Contoh: 0812758xxxx">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Role Akses</label>
                                <select name="is_admin" class="form-select" required>
                                    <option value="0" selected>Admin</option>
                                    <option value="1">Master Administrator</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr class="text-muted opacity-25 my-4">

                        {{-- Baris 4: Password & Konfirmasi --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-top-0 d-flex justify-content-end gap-2 pb-4">
                        <button type="button" class="btn btn-light px-4 rounded-3 text-muted" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-medium">Daftarkan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>