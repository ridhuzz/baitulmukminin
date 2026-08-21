<?php

namespace App\Filament\Resources\LaporanKeuanganResource\Pages;

use App\Filament\Resources\LaporanKeuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanKeuangan extends ListRecords
{
    protected static string $resource = LaporanKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
