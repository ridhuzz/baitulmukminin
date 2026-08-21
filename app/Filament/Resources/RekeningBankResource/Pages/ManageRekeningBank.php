<?php

namespace App\Filament\Resources\RekeningBankResource\Pages;

use App\Filament\Resources\RekeningBankResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRekeningBank extends ManageRecords
{
    protected static string $resource = RekeningBankResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
