<?php

namespace App\Filament\Resources\StrukturOrganisasiResource\Pages;

use App\Filament\Resources\StrukturOrganisasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStrukturOrganisasi extends ListRecords
{
    protected static string $resource = StrukturOrganisasiResource::class;

    protected static ?string $title = 'Struktur Organisasi';

    public function getSubheading(): ?string
    {
        return 'Periode kepengurusan Yayasan dan Masjid (DKM). Klik baris untuk melihat bagan struktur & daftar pengurusnya.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bagan_publik')
                ->label('Bagan versi publik')
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(route('publik.struktur'), shouldOpenInNewTab: true),
            Actions\CreateAction::make()
                ->label('Buat Struktur Organisasi')
                ->icon('heroicon-o-plus'),
        ];
    }
}
