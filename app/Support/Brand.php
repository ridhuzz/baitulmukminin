<?php

namespace App\Support;

/**
 * Identitas/merek aplikasi — satu tempat untuk semua caption
 * (sidebar, topbar, halaman publik, login, judul tab).
 */
class Brand
{
    /** Nama lengkap aplikasi. */
    public const NAMA = 'Sistem Manajemen Mesjid Baitul Mukminin - Cimone Permai';

    /** Dua baris untuk logo/brand di ruang sempit. */
    public const BARIS_1 = 'Sistem Manajemen Mesjid';

    public const BARIS_2 = 'Baitul Mukminin - Cimone Permai';

    /** Nama masjid (untuk teks Arab/kaligrafi di sidebar & header). */
    public const KALIGRAFI = 'بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ';

    public const NAMA_ARAB = 'مسجد بيت المؤمنين';
}
