<?php

use App\Http\Controllers\EntitasController;
use App\Http\Controllers\PublikController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublikController::class, 'beranda'])->name('publik.beranda');
Route::get('/jadwal-ibadah', [PublikController::class, 'jadwalIbadah'])->name('publik.jadwal');
Route::get('/kegiatan', [PublikController::class, 'kegiatan'])->name('publik.kegiatan');
Route::get('/struktur-organisasi', [PublikController::class, 'struktur'])->name('publik.struktur');
Route::get('/pengumuman/{slug}', [PublikController::class, 'pengumuman'])->name('publik.pengumuman');
Route::get('/laporan-keuangan/{entitas?}', [PublikController::class, 'laporan'])->name('publik.laporan');
Route::get('/halaman/{slug}', [PublikController::class, 'halaman'])->name('publik.halaman');

// Pemilih entitas aktif (Masjid / Yayasan / Semua) di panel pengurus
Route::middleware('auth')
    ->get('/admin/ganti-entitas/{id}', [EntitasController::class, 'ganti'])
    ->name('entitas.ganti');
