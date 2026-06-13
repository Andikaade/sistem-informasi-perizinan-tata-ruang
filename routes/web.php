<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LacakPerizinanController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\Auth\LoginController;


// Halaman Utama (Pencarian / Tracking)
Route::get('/', [LacakPerizinanController::class, 'index'])->name('antrian.index');
Route::post('/cari', [LacakPerizinanController::class, 'cari'])->name('antrian.cari');

// Kirim Email / Kontak
Route::post('/kontak-kirim', [KontakController::class, 'kirimPesan'])->name('kontak.kirim');

// [Grup 1] Jalur untuk user yang BELUM Login (Guest)
Route::middleware('guest')->group(function () {
    // Berikan ->name('login') di method GET agar dikenali oleh route('login')
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});


// [Grup 2] Jalur untuk user yang SUDAH Login (Authenticated)
Route::middleware('auth')->group(function () {
    
    // Halaman Dashboard Utama
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Jalur untuk Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});