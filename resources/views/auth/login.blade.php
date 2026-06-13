<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Perizinan Tata Ruang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="d-flex flex-column" style="min-height: 100vh;">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm w-100 position-absolute top-0 start-0">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40" class="me-2">
                <span class="mb-0 h5 fw-semibold">Pemerintahan Kab. Sijunjung</span>
            </a>
            
            <div class="ms-auto">
                <a href="{{ route('antrian.index') }}" class="btn btn-warning btn-sm px-3" style="border-radius: 8px;">
                    ← Kembali
                </a>
            </div>
        </div>
    </nav>

    <div class="container d-flex flex-grow-1 align-items-center justify-content-center" style="padding-top: 80px;">
        <div class="login-card">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark">Halaman Login</h4>
                <p class="text-muted small">Silakan masuk menggunakan akun admin Anda</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf 
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Username</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                           value="{{ old('username') }}" placeholder="Masukkan username" required autofocus
                           style="border-radius: 8px; padding: 11px;">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Password</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Masukkan password" required
                           style="border-radius: 8px; padding: 11px;">
                </div>
                <div class="row justify-content-evenly">
                    <div class="col-4 ">
                        <button type="submit" class="btn btn-login">Masuk</button>
                    </div>
                    <div class="col-4 ">
                        <a href="#">Lupa pasword</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>