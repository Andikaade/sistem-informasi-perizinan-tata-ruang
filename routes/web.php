<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LacakPerizinanController;
use App\Http\Controllers\KontakController;


Route::get('/', [LacakPerizinanController::class, 'index'])->name('antrian.index');

// Proses pencarian saat tombol diklik
Route::post('/cari', [LacakPerizinanController::class, 'cari'])->name('antrian.cari');
// Email
Route::post('/kontak-kirim', [KontakController::class, 'kirimPesan'])->name('kontak.kirim');