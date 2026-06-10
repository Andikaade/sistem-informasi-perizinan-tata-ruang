<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LacakPerizinanController;


Route::get('/', [LacakPerizinanController::class, 'index'])->name('antrian.index');

// Proses pencarian saat tombol diklik
Route::post('/cari', [LacakPerizinanController::class, 'cari'])->name('antrian.cari');