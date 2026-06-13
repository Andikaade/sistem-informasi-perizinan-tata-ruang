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
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40" class="me-2">
                <span class="mb-0 h5 fw-semibold">Pemerintahan Kab. Sijunjung</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#informasi">Informasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang_kami">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                </ul>
                <div class="d-flex user-logged ms-2">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-warning dropdown-toggle fw-semibold" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                👋 {{ Auth::user()->username }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-menu-item dropdown-item" href="{{ route('dashboard') }}">Dashboard Admin</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Keluar / Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="btn btn-primary btn-sm px-3 " href="{{ route('login') }}" role="button" style="background-color: #D97706; border: none;">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="banner" id="beranda">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11 col-12">
                    <div class="row">
                        <div class="col-lg-6 col-12 copywriting">
                            <p class="story">
                                Selamat Datang
                            </p>
                            <h1 class="header">
                                Di Website <span class="text-purple"> Perizinan Tata Ruang </span>Kab. <span class="text-purple"> Sijunjung </span>
                            </h1>
                            <p class="support">
                                Silahkan Masukan no antrian anda
                            </p>
                            <div class="col-12 col-md-10 col-lg-9"> 
                                <form action="{{ route('antrian.cari') }}" method="POST" class="d-flex gap-2" role="search">
                                    @csrf
                                    <input class="form-control px-4 py-2 input-antrian" type="text" name="no_antrian" placeholder="Masukan no antrian" aria-label="Search" value="{{ $keyword ?? '' }}" required>
                                    
                                    <button class="btn btn-warning px-4 btn-cari-antrian" type="submit">Cari</button>
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
                         <div class="col-lg-6 col-12 text-center">
                                <a href="#">
                                    <img src="{{asset('images/banner.png')}}" class="img-fluid" alt="">
                                </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="steps py-5 bg-white clearfix" id="informasi">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center text-lg-start">
                <h2 class="fw-bold text-dark" style="font-size: 2.2rem;">Informasi Alur Pengajuan</h2>
                <p class="text-muted">Ikuti 3 tahapan mudah berikut untuk mengurus kesesuaian tata ruang Anda di Kabupaten Sijunjung.</p>
            </div>
        </div>
        
        <div class="row align-items-center mb-5 g-4 text-lg-start text-center">
            <div class="col-lg-6 order-1">
                <div class="position-relative d-inline-block w-100">
                    <img src="{{ asset('images/step1.png') }}" class="img-fluid custom-step-img" alt="Langkah 1" style="max-height: 480px !important; object-fit: contain !important; width: auto !important; margin: 0 auto; display: block;">
                </div>
            </div>
            <div class="col-lg-6 order-2 ps-lg-5">
                <span class="d-inline-block fw-bold text-warning mb-2" style="letter-spacing: 2px; font-size: 0.85rem;">
                    ALUR PERIZINAN
                </span>
                <h3 class="fw-bold text-dark mb-3" style="font-size: 1.8rem; line-height: 1.3;">
                    Siapkan Dokumen & Berkas Anda
                </h3>
                <p class="text-muted mb-4" style="font-size: 1rem; line-height: 1.7; text-align: justify;">
                    Sebelum mengajukan permohonan tata ruang, pastikan Anda telah melengkapi seluruh persyaratan administrasi seperti KTP, KRK (Keterangan Rencana Kabupaten), serta sertifikat tanah yang sah untuk mempercepat proses verifikasi petugas.
                </p>
                <a href="#kontak" class="btn btn-secondary px-4 py-2 fw-semibold shadow-sm" style="border-radius: 8px;">
                    Tanya Persyaratan
                </a>
            </div>
        </div>

        <div class="row align-items-center mb-5 g-4 text-lg-start text-center">
            <div class="col-lg-6 order-2 order-lg-1 pe-lg-5">
                <span class="d-inline-block fw-bold text-warning mb-2" style="letter-spacing: 2px; font-size: 0.85rem;">
                    PROSES VERIFIKASI
                </span>
                <h3 class="fw-bold text-dark mb-3" style="font-size: 1.8rem; line-height: 1.3;">
                    Pantau Status Nomor Antrian
                </h3>
                <p class="text-muted mb-4" style="font-size: 1rem; line-height: 1.7; text-align: justify;">
                    Masukkan nomor antrian atau nomor berkas Anda pada kolom pencarian di bagian atas website untuk melihat sejauh mana perkembangan validasi pemetaan ruang dan dokumen teknis Anda oleh tim ahli Dinas.
                </p>
                <a href="#beranda" class="btn btn-secondary px-4 py-2 fw-semibold shadow-sm" style="border-radius: 8px;">
                    Cek Berkas
                </a>
            </div>
            <div class="col-lg-6 order-1 order-lg-2">
                <div class="position-relative d-inline-block w-100">
                    <img src="{{ asset('images/step2.png') }}" class="img-fluid custom-step-img" alt="Langkah 2" style="max-height: 480px !important; object-fit: contain !important; width: auto !important; margin: 0 auto; display: block;">
                </div>
            </div>
        </div>

        <div class="row align-items-center g-4 text-lg-start text-center">
            <div class="col-lg-6 order-1">
                <div class="position-relative d-inline-block w-100">
                    <img src="{{ asset('images/step3.png') }}" class="img-fluid custom-step-img" alt="Langkah 3" style="max-height: 480px !important; object-fit: contain !important; width: auto !important; margin: 0 auto; display: block;">
                </div>
            </div>
            <div class="col-lg-6 order-2 ps-lg-5">
                <span class="d-inline-block fw-bold text-warning mb-2" style="letter-spacing: 2px; font-size: 0.85rem;">
                    TAHAP AKHIR
                </span>
                <h3 class="fw-bold text-dark mb-3" style="font-size: 1.8rem; line-height: 1.3;">
                    Pengambilan SK Perizinan
                </h3>
                <p class="text-muted mb-4" style="font-size: 1rem; line-height: 1.7; text-align: justify;">
                    Setelah berkas dinyatakan lolos verifikasi kesesuaian tata ruang dan disetujui, Surat Keputusan (SK) resmi dapat langsung Anda ambil di loket pelayanan kantor Dinas Perizinan Kabupaten Sijunjung.
                </p>
                <a href="#tentang_kami" class="btn btn-secondary px-4 py-2 fw-semibold shadow-sm" style="border-radius: 8px;">
                    Lihat Lokasi Kantor
                </a>
            </div>
        </div>

    </div>
</section>

<section class="tentang_kami py-5" id="tentang_kami">
    <div class="container">
        <div class="row align-items-center justify-content-between g-4"> 
            
            <div class="col-lg-6">
                <h2 class="section-title mb-4">Tentang Kabupaten Sijunjung</h2>
                
                <p class="support mb-3">
                    <strong>Kabupaten Sijunjung</strong> (dijuluki <em>Ranah Lansek Manih</em>) adalah salah satu kabupaten di 
                    Provinsi Sumatera Barat yang beribu kota di Muaro Sijunjung. Wilayah ini dikenal 
                    sebagai jalur penghubung strategis antara provinsi Jambi dan Riau.
                </p>
                <p class="support mb-3">
                    <strong>Wilayah & Pemerintahan:</strong> Terdiri dari 8 kecamatan, 61 nagari, dan 1 desa. 
                    Kecamatan utamanya meliputi Kupitan, IV Nagari, Koto VII, Sumpur Kudus, Sijunjung, 
                    Lubuk Tarok, Tanjung Gadang, dan Kamang Baru.
                </p>
                <p class="support mb-4">
                    <strong>Potensi Ekonomi & Pariwisata:</strong> Sijunjung merupakan sentra penghasil karet terbesar di Sumatera Barat. 
                    Selain sektor pertanian, salah satu daya tarik utamanya adalah <em>Perkampungan Adat Sijunjung</em>, 
                    yang berhasil meraih penghargaan sebagai Desa Wisata Berkelas Dunia pada ajang Anugerah Desa Wisata Indonesia.
                </p>
            </div>

            <div class="col-lg-5">
                <div class="map-container shadow-sm rounded">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d255312.35520336215!2d101.14441499577782!3d-0.6698971485661605!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2b0051d9319e71%3A0x3039d80b220cc70!2sKabupaten%20Sijunjung%2C%20Sumatera%20Barat!5e0!3m2!1sid!2sid!4v1718131200000!5m2!1sid!2sid" 
                        width="100%" 
                        height="350" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</section>
<section class="kontak_kami py-5 bg-white" id="kontak">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-dark" style="position: relative; padding-bottom: 15px; width: fit-content; margin: 0 auto;">
                    Hubungi Kami
                    <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 4px; background-color: #f59e0b; border-radius: 2px;"></span>
                </h2>
                <p class="text-muted mt-3">Punya pertanyaan seputar perizinan tata ruang? Silakan hubungi kami melalui formulir atau WhatsApp.</p>
            </div>
        </div>

        <div class="row g-5 align-items-stretch">
            <div class="col-lg-7">
                <div class="card p-4 shadow-sm border-0 bg-light h-100">
                    <h4 class="fw-bold text-dark mb-4">Kirim Pesan</h4>
                    <form action="{{ route('kontak.kirim') }}" method="POST">
                        @csrf
                        @if(session('success_email'))
                            <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                                🎉 {{ session('success_email') }}
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control px-3 py-2 border-0 shadow-sm" placeholder="Masukkan nama Anda" required style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">Alamat Email</label>
                                <input type="email" name="email" class="form-control px-3 py-2 border-0 shadow-sm" placeholder="nama@email.com" required style="border-radius: 8px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark fw-medium">Subjek / Perihal</label>
                                <input type="text" name="subjek" class="form-control px-3 py-2 border-0 shadow-sm" placeholder="Contoh: Tanya Status Berkas" required style="border-radius: 8px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark fw-medium">Isi Pesan</label>
                                <textarea name="pesan" rows="4" class="form-control px-3 py-2 border-0 shadow-sm" placeholder="Tuliskan pesan atau pertanyaan Anda di sini..." required style="border-radius: 8px; resize: none;"></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-warning w-100 fw-bold py-2 text-dark shadow-sm" style="border-radius: 8px; background: #f59e0b; border: none; transition: 0.3s;">
                                    Kirim ke Email
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card p-4 shadow-sm border-0 text-white h-100" style="background: linear-gradient(135deg, #111827, #1f2937); border-radius: 12px;">
                    <h4 class="fw-bold mb-3 text-warning">Respon Cepat via WhatsApp</h4>
                    <p class="text-secondary small mb-4">Untuk konsultasi langsung yang lebih cepat dan interaktif dengan petugas, Anda dapat mengklik tombol WhatsApp di bawah ini:</p>
                    
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Perizinan%20Tata%20Ruang%20Kab.%20Sijunjung,%20saya%20ingin%20bertanya..." 
                       target="_blank" 
                       class="btn btn-success d-flex align-items-center justify-content-center gap-2 fw-bold py-3 mb-4 border-0 shadow" 
                       style="background-color: #25D366; border-radius: 8px; transition: 0.3s;">
                        <span style="font-size: 1.2rem;">💬</span> Hubungi via WhatsApp Chat
                    </a>

                    <hr class="border-secondary my-3">

                    <div class="mt-2">
                        <h5 class="fw-bold text-warning mb-3" style="font-size: 1rem;">Kantor Dinas Perizinan Tata Ruang</h5>
                        <p class="mb-2 small text-secondary">📍 Muaro Sijunjung, Kab. Sijunjung, Sumatera Barat</p>
                        <p class="mb-0 small text-secondary">🕒 Senin - Jumat: 08.00 - 16.00 WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <div class="clearfix"></div>

    <footer class="mt-5 py-4 bg-light border-top position-relative w-100" style="z-index: 10;">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-dark mb-1 fw-medium" style="font-size: 0.95rem;">
                        &copy; 2026 Pemerintah Kabupaten Sijunjung. All Rights Reserved.
                    </p>
                    <small class="text-muted" style="font-size: 0.85rem;">
                        Designed & Developed by <span class="text-warning fw-semibold">annovcoer</span>
                    </small>
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>