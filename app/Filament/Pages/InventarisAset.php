<?php

namespace App\Filament\Pages;

class InventarisAset extends ModulEkstensi
{
    protected static ?string $navigationLabel = 'Inventaris & Aset';

    protected static ?string $title = 'Inventaris & Aset';

    protected static ?string $slug = 'inventaris-aset';

    protected static ?int $navigationSort = 1;

    protected static string $deskripsi = 'Pencatatan barang milik masjid (karpet, sound system, AC, kendaraan, dll.), kondisi, lokasi penyimpanan, serta riwayat pemeliharaan dan peminjaman.';

    protected static array $fitur = [
        'Daftar aset dengan kode, kategori, tanggal perolehan, dan nilai',
        'Kondisi barang (baik / rusak ringan / rusak berat) dan lokasi',
        'Jadwal & riwayat pemeliharaan',
        'Peminjaman aset oleh jamaah/lembaga',
    ];

    protected static array $kolom = ['Kode', 'Nama Aset', 'Kategori', 'Kondisi', 'Lokasi'];
}
