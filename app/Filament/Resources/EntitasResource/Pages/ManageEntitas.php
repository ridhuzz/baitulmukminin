<?php

namespace App\Filament\Resources\EntitasResource\Pages;

use App\Filament\Resources\EntitasResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEntitas extends ManageRecords
{
    protected static string $resource = EntitasResource::class;

    protected static ?string $title = 'Entitas: Yayasan & DKM';

    public function getSubheading(): ?string
    {
        return 'Nama Yayasan dan Masjid (DKM) yang dipakai pada struktur, kas, laporan, dan halaman publik. Bisa ditambah unit lain (mis. Remaja Masjid).';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Entitas')->icon('heroicon-o-plus'),
        ];
    }
}
