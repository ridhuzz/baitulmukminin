<?php

namespace App\Filament\Resources\KategoriTransaksiResource\Pages;

use App\Filament\Resources\KategoriTransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKategoriTransaksi extends ManageRecords
{
    protected static string $resource = KategoriTransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
