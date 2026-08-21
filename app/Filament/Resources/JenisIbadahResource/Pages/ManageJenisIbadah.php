<?php

namespace App\Filament\Resources\JenisIbadahResource\Pages;

use App\Filament\Resources\JenisIbadahResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageJenisIbadah extends ManageRecords
{
    protected static string $resource = JenisIbadahResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
