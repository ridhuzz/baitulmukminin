<?php

namespace App\Filament\Resources\JadwalPetugasResource\Pages;

use App\Filament\Resources\JadwalPetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalPetugas extends EditRecord
{
    protected static string $resource = JadwalPetugasResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
