<?php

namespace App\Filament\Resources\MenuNavigasiResource\Pages;

use App\Filament\Resources\MenuNavigasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMenuNavigasi extends ManageRecords
{
    protected static string $resource = MenuNavigasiResource::class;

    public function getSubheading(): ?string
    {
        return 'Menu navigasi di situs publik (atas & mobile). Seret baris untuk mengatur urutan; item dengan induk tampil sebagai submenu dropdown.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('situs')
                ->label('Lihat situs')
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(route('publik.beranda'), shouldOpenInNewTab: true),
            Actions\CreateAction::make()->label('Tambah Item Menu')->icon('heroicon-o-plus'),
        ];
    }
}
