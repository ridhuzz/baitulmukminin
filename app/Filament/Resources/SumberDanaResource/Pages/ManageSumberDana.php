<?php

namespace App\Filament\Resources\SumberDanaResource\Pages;

use App\Filament\Resources\SumberDanaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSumberDana extends ManageRecords
{
    protected static string $resource = SumberDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
