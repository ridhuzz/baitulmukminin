<?php

namespace App\Filament\Resources\GaleriResource\Pages;

use App\Filament\Resources\GaleriResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGaleri extends ManageRecords
{
    protected static string $resource = GaleriResource::class;

    public function getSubheading(): ?string
    {
        return 'Foto di sini tampil pada bagian "Suasana & Fasilitas Masjid" di beranda situs publik. Seret baris untuk mengatur urutan.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('beranda')
                ->label('Lihat di beranda')
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(route('publik.beranda') . '#galeri', shouldOpenInNewTab: true),
            Actions\CreateAction::make()->label('Tambah Foto')->icon('heroicon-o-plus'),
        ];
    }
}
