<?php

namespace App\Filament\Resources\PengurusResource\Pages;

use App\Filament\Resources\PengurusResource;
use App\Support\EntitasAktif;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPengurus extends ListRecords
{
    protected static string $resource = PengurusResource::class;

    protected static ?string $title = 'Struktur & Pengurus';

    public function getSubheading(): ?string
    {
        $entitas = EntitasAktif::entitas();

        return $entitas
            ? 'Pengurus ' . $entitas->nama . ' beserta struktur organisasi periode berjalan.'
            : 'Kelola data pengurus Yayasan dan DKM beserta struktur organisasi periode berjalan.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pengurus')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['semua' => Tab::make('Semua')];

        if (EntitasAktif::id() === null) {
            // Mode "Semua Entitas": satu tab per entitas (Yayasan / Masjid-DKM).
            foreach (EntitasAktif::daftar() as $entitas) {
                $tabs['entitas-' . $entitas->id] = Tab::make($entitas->label)
                    ->modifyQueryUsing(fn (Builder $query) => $query->whereHas(
                        'kepengurusan',
                        fn (Builder $q) => $q->where('status_aktif', true)
                            ->whereHas('struktur', fn (Builder $s) => $s->where('entitas_id', $entitas->id))
                    ));
            }
        }

        $tabs['tanpa-jabatan'] = Tab::make('Belum Ada Jabatan')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave(
                'kepengurusan',
                fn (Builder $q) => $q->where('status_aktif', true)
            ));

        $tabs['nonaktif'] = Tab::make('Nonaktif')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status_aktif', false));

        return $tabs;
    }
}
