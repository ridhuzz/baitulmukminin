<?php

namespace App\Filament\Pages;

class Donatur extends ModulEkstensi
{
    protected static ?string $navigationLabel = 'Donatur';

    protected static ?string $title = 'Donatur';

    protected static ?string $slug = 'donatur';

    protected static ?int $navigationSort = 2;

    protected static string $deskripsi = 'Database donatur tetap dan insidental, riwayat donasi, serta pengingat/ucapan terima kasih otomatis.';

    protected static array $fitur = [
        'Profil donatur (perorangan / lembaga) dan kontak',
        'Riwayat donasi terhubung ke transaksi keuangan',
        'Kategori donatur: tetap, insidental, wakif',
        'Laporan donasi per donatur & per periode',
    ];

    protected static array $kolom = ['Nama Donatur', 'Jenis', 'Kontak', 'Total Donasi', 'Status'];
}
