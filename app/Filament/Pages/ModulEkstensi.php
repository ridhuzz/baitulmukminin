<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Halaman penampung untuk modul ekstensi yang ada di peta modul
 * (Inventaris & Aset, Donatur, ZISWAF & Qurban) namun belum
 * diimplementasikan pada tahap MVP. Menjaga struktur menu tetap
 * sama dengan mockup tanpa menampilkan data palsu.
 */
abstract class ModulEkstensi extends Page
{
    protected static ?string $navigationGroup = 'Ekstensi';

    protected static string $view = 'filament.pages.modul-ekstensi';

    /** Ringkasan singkat tujuan modul. */
    protected static string $deskripsi = '';

    /** Daftar fitur yang direncanakan. */
    protected static array $fitur = [];

    /** Kolom tabel yang akan dipakai (untuk gambaran tampilan). */
    protected static array $kolom = ['No', 'Keterangan', 'Kategori', 'Status'];

    /** Tampil di sidebar hanya bila MENU_EKSTENSI=true di .env (config/simasjid.php). */
    public static function shouldRegisterNavigation(): bool
    {
        return (bool) config('simasjid.menu_ekstensi', false);
    }

    public static function canAccess(): bool
    {
        return (bool) config('simasjid.menu_ekstensi', false);
    }

    public function getSubheading(): ?string
    {
        return 'Kelola data dan informasi terkait ' . mb_strtolower(static::getNavigationLabel()) . '.';
    }

    protected function getViewData(): array
    {
        return [
            'judul' => static::getNavigationLabel(),
            'deskripsi' => static::$deskripsi,
            'fitur' => static::$fitur,
            'kolom' => static::$kolom,
        ];
    }
}
