<?php

namespace App\Filament\Widgets;

use App\Models\Masjid;
use App\Services\JadwalShalat;
use App\Support\Tanggal;
use Filament\Widgets\Widget;

/** Strip jam shalat hari ini (Kemenag) di atas tabel Jadwal Petugas. */
class JadwalShalatHariIniWidget extends Widget
{
    protected static string $view = 'filament.widgets.jadwal-shalat-hari-ini';

    protected static bool $isDiscovered = false;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $masjid = Masjid::first();
        $layanan = app(JadwalShalat::class);
        $waktu = $layanan->lengkap(today(), $masjid);
        $sumber = $layanan->sumber();
        $berikutnya = $layanan->berikutnya($waktu, now());

        return [
            'waktu' => $waktu,
            'sumber' => $sumber,
            'berikutnya' => $berikutnya,
            'tanggal' => Tanggal::lengkapDenganHijriah(today()),
            'lokasi' => trim(($masjid?->kota ?? '') . ($masjid?->provinsi ? ', ' . $masjid->provinsi : '')),
            'urlProfil' => \App\Filament\Resources\MasjidResource::canViewAny() ? \App\Filament\Resources\MasjidResource::getUrl() : null,
        ];
    }
}
