<?php

namespace App\Filament\Resources\JadwalPetugasResource\Pages;

use App\Filament\Resources\JadwalPetugasResource;
use App\Filament\Widgets\JadwalShalatHariIniWidget;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListJadwalPetugas extends ListRecords
{
    protected static string $resource = JadwalPetugasResource::class;

    protected static ?string $title = 'Jadwal Ibadah & Petugas';

    public function getSubheading(): ?string
    {
        return 'Jam shalat diambil otomatis dari jadwal Kemenag RI (sesuai kab/kota di Profil Masjid); di sini pengurus mengatur imam, khotib, muadzin, dan petugas lainnya.';
    }

    protected function getHeaderWidgets(): array
    {
        return [JadwalShalatHariIniWidget::class];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publik')
                ->label('Halaman publik')
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(route('publik.jadwal'), shouldOpenInNewTab: true),
            Actions\CreateAction::make()->label('Tambah Jadwal')->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'mendatang' => Tab::make('Mendatang')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('tanggal_jadwal', '>=', today())->orderBy('tanggal_jadwal')),
            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('tanggal_jadwal', today())),
            'jumat' => Tab::make('Shalat Jumat')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('jenisIbadah', fn (Builder $j) => $j->where('kategori', 'jumat'))),
            'semua' => Tab::make('Semua'),
        ];
    }
}
