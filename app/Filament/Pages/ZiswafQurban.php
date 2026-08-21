<?php

namespace App\Filament\Pages;

class ZiswafQurban extends ModulEkstensi
{
    protected static ?string $navigationLabel = 'ZISWAF & Qurban';

    protected static ?string $title = 'ZISWAF & Qurban';

    protected static ?string $slug = 'ziswaf-qurban';

    protected static ?int $navigationSort = 3;

    protected static string $deskripsi = 'Pengelolaan zakat, infaq, sedekah, wakaf, serta pendaftaran dan distribusi hewan qurban.';

    protected static array $fitur = [
        'Penerimaan zakat fitrah/mal, infaq, sedekah, wakaf (muzakki & mustahik)',
        'Pendaftaran peserta qurban, pembagian kelompok sapi/kambing',
        'Distribusi daging & laporan panitia',
        'Rekap ZISWAF per periode terhubung ke keuangan',
    ];

    protected static array $kolom = ['Tanggal', 'Nama', 'Jenis', 'Nominal / Hewan', 'Status'];
}
