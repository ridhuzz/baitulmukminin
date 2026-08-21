<?php

namespace App\Filament\Resources\JadwalPetugasResource\Pages;

use App\Filament\Resources\JadwalPetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalPetugas extends ListRecords
{
    protected static string $resource = JadwalPetugasResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
