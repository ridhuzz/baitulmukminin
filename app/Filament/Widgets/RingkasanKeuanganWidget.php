<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiKeuangan;
use App\Support\Tanggal;
use Filament\Widgets\Widget;

/**
 * Tiga kartu ringkasan di atas tabel transaksi (Total Saldo Aktif,
 * Pemasukan & Pengeluaran bulan ini) — mengikuti mockup modul Keuangan.
 */
class RingkasanKeuanganWidget extends Widget
{
    protected static string $view = 'filament.widgets.ringkasan-keuangan';

    protected static bool $isDiscovered = false;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $entitas = \App\Support\EntitasAktif::id();

        return [
            'saldo' => TransaksiKeuangan::saldoTotal($entitas),
            'masuk' => TransaksiKeuangan::totalBulanBerjalan('pemasukan', $entitas),
            'keluar' => TransaksiKeuangan::totalBulanBerjalan('pengeluaran', $entitas),
            'bulan' => Tanggal::BULAN[now()->month] . ' ' . now()->year,
            'labelEntitas' => \App\Support\EntitasAktif::label(),
        ];
    }
}
