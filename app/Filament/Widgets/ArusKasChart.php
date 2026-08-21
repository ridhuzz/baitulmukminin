<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiKeuangan;
use App\Support\EntitasAktif;
use App\Support\Tanggal;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

/**
 * Grafik arus kas (juta rupiah) — area chart pemasukan vs pengeluaran
 * dari transaksi yang sudah disetujui. Dipasang di dashboard kustom.
 */
class ArusKasChart extends ChartWidget
{
    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        $entitas = EntitasAktif::entitas();

        return 'Grafik Arus Kas (Juta Rp)' . ($entitas ? ' — ' . $entitas->label : ' — Gabungan');
    }

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '6bulan';

    protected function getFilters(): ?array
    {
        return [
            '6bulan' => '6 Bulan Terakhir',
            '12bulan' => '12 Bulan Terakhir',
            'tahun' => 'Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        $bulan = match ($this->filter) {
            '12bulan' => $this->daftarBulan(12),
            'tahun' => $this->daftarBulanTahunIni(),
            default => $this->daftarBulan(6),
        };

        $mulai = $bulan->first()->copy()->startOfMonth()->toDateString();
        $selesai = $bulan->last()->copy()->endOfMonth()->toDateString();

        $rekap = EntitasAktif::terapkan(TransaksiKeuangan::disetujui())
            ->whereBetween('tanggal_transaksi', [$mulai, $selesai])
            ->selectRaw("DATE_FORMAT(tanggal_transaksi, '%Y-%m') as periode, jenis_transaksi, SUM(nominal) as total")
            ->groupBy('periode', 'jenis_transaksi')
            ->get()
            ->groupBy('periode');

        $masuk = [];
        $keluar = [];
        $label = [];

        foreach ($bulan as $b) {
            $key = $b->format('Y-m');
            $baris = $rekap->get($key, collect());

            $masuk[] = round(((float) $baris->firstWhere('jenis_transaksi', 'pemasukan')?->total) / 1_000_000, 2);
            $keluar[] = round(((float) $baris->firstWhere('jenis_transaksi', 'pengeluaran')?->total) / 1_000_000, 2);
            $label[] = Tanggal::BULAN_PENDEK[$b->month] . ($b->year !== now()->year ? ' ' . $b->format('y') : '');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Masuk',
                    'data' => $masuk,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.18)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                    'pointBackgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Keluar',
                    'data' => $keluar,
                    'borderColor' => '#f43f5e',
                    'backgroundColor' => 'rgba(244, 63, 94, 0.15)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                    'pointBackgroundColor' => '#f43f5e',
                ],
            ],
            'labels' => $label,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'bottom', 'labels' => ['usePointStyle' => true, 'boxWidth' => 8]],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['color' => '#64748b', 'font' => ['size' => 12]],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => '#e2e8f0', 'borderDash' => [3, 3]],
                    'border' => ['display' => false, 'dash' => [3, 3]],
                    'ticks' => ['color' => '#64748b', 'font' => ['size' => 12]],
                ],
            ],
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'maintainAspectRatio' => false,
        ];
    }

    /** @return \Illuminate\Support\Collection<int, Carbon> */
    protected function daftarBulan(int $jumlah)
    {
        return collect(range($jumlah - 1, 0))
            ->map(fn (int $i) => now()->startOfMonth()->subMonthsNoOverflow($i));
    }

    /** @return \Illuminate\Support\Collection<int, Carbon> */
    protected function daftarBulanTahunIni()
    {
        return collect(range(1, (int) now()->month))
            ->map(fn (int $m) => Carbon::create(now()->year, $m, 1));
    }
}
