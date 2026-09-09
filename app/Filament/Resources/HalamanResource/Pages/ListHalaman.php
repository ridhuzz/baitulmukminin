<?php

namespace App\Filament\Resources\HalamanResource\Pages;

use App\Filament\Resources\HalamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHalaman extends ListRecords
{
    protected static string $resource = HalamanResource::class;

    public function getSubheading(): ?string
    {
        return 'Halaman bebas berisi konten HTML (rich editor) yang tampil di situs publik. Untuk memunculkannya di menu navigasi, tambahkan itemnya di Pengaturan Menu.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Halaman')->icon('heroicon-o-plus'),
        ];
    }
}
