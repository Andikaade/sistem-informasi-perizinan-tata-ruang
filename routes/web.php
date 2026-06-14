<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LacakPerizinanController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// ==========================================
// 1. HALAMAN UMUM (GUEST / PUBLIC)
// ==========================================

// Halaman Utama (Pencarian / Tracking)
Route::get('/', [LacakPerizinanController::class, 'index'])->name('antrian.index');
Route::post('/cari', [LacakPerizinanController::class, 'cari'])->name('antrian.cari');

// Kirim Email / Kontak
Route::post('/kontak-kirim', [KontakController::class, 'kirimPesan'])->name('kontak.kirim');


// ==========================================
// 2. JALUR KHUSUS USER BELUM LOGIN (GUEST)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});


// ==========================================
// 3. JALUR KHUSUS USER SUDAH LOGIN (AUTH)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // Halaman Dashboard Utama (Memanggil Controller)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Jalur untuk Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});