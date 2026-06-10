<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  </head>
  <body>
    
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Kontak</a>
                </li>
            </ul>
            <div class="d-flex user-logged ms-2">
                <a class="btn btn-primary" href="#" role="button">Masuk</a>
            </div>
        </div>
    </div>
</nav>

    <section class="banner">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11 col-12">
                    <div class="row">
                        <div class="col-lg-6 col-12 copywriting">
                            <p class="story">
                                Selamat Datang
                            </p>
                            <h1 class="header">
                                Di Website <span class="text-purple"> Perizinan Tata Ruang </span> Kabupaten <span class="text-purple"> Sijunjung </span>
                            </h1>
                            <p class="support">
                                Silahkan Masukan no antrian anda
                            </p>
                            <div class="col-12 col-md-10 col-lg-9"> 
                                <form action="{{ route('antrian.cari') }}" method="POST" class="d-flex gap-2" role="search">
                                    @csrf
                                    <input class="form-control px-4 py-2" type="text" name="no_antrian" placeholder="Masukan no antrian" aria-label="Search" style="border-radius: 50px; border: 1px solid #E7E5F4;" value="{{ $keyword ?? '' }}" required>
                                    
                                    <button class="btn btn-warning px-4" style="color: #1a202c; font-weight: 600; border-radius: 50px;" type="submit">Cari</button>
                                </form>
                                <div class="mt-4">
                                    {{-- Jika Data Antrian Ditemukan --}}
                                        @if(isset($antrian))
                                            <div class="alert alert-success card shadow-sm p-4 text-dark mt-4">
                                                <h5 class="fw-bold mb-3"> Hasil Pencarian Antrian</h5>
                                                <p class="mb-2"><strong>Nomor Surat:</strong> {{ $antrian['no_surat'] }}</p>
                                                <p class="mb-2"><strong>Nama Pemohon:</strong> {{ $antrian['nama_pemohon'] ?? '-' }}</p>
                                                <p class="mb-0"><strong>Status Berkas:</strong> 
                                                    <span class="badge bg-primary text-white px-3 py-2">{{ $antrian['status'] ?? '-' }}</span>
                                                </p>
                                            </div>
                                        @endif
                                    {{-- Jika Terjadi Error / Data Tidak Ketemu --}}
                                    @if(isset($error))
                                        <div class="alert alert-danger card shadow-sm p-3">
                                            {{ $error }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row brands">
                <div class="col-lg-12 col-12 text-center">
                    <img src="/assets/images/brands.png" alt="">
                </div>
            </div>
        </div>
    </section>

    <section class="pricing">
        <div class="container">
            <div class="row pb-70">
                <div class="col-lg-5 col-12 header-wrap copywriting">
                    <p class="story">
                        GOOD INVESTMENT
                    </p>
                    <h2 class="primary-header text-white">
                        Start Your Journey
                    </h2>
                    <p class="support">
                        Learn how to speaking in public to demonstrate your <br> final project and receive the important feedbacks
                    </p>
                    <p class="mt-5">
                        <a href="#" class="btn btn-master btn-thirdty me-3">
                            View Syllabus
                        </a>
                    </p>
                </div>
                <div class="col-lg-7 col-12">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="table-pricing paket-biasa">
                                
                                </div>
                                <p>
                                    <a href="#" class="btn btn-master btn-secondary w-100 mt-3">
                                        Start With This Plan
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pb-70">
                <div class="col-lg-12 col-12 text-center">
                    <img src="/assets/images/brands.png" height="30" alt="">
                </div>
            </div>
        </div>
    </section>

    <div class="clearfix"></div>

    <footer class="mt-5 py-4" style="background-color: #ffffff; border-top: 1px solid #E7E5F4; position: relative; z-index: 10; width: 100%;">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0" style="font-size: 14px; font-weight: 500;">
                        2026 Copyright By Abdi Maulana
                    </p>              
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>