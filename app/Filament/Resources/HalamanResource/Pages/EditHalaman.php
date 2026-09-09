<?php

namespace App\Filament\Resources\HalamanResource\Pages;

use App\Filament\Resources\HalamanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHalaman extends EditRecord
{
    protected static string $resource = HalamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('lihat')
                ->label('Lihat halaman')
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(fn () => route('publik.halaman', $this->record->slug), shouldOpenInNewTab: true)
                ->visible(fn () => $this->record->publish),
            Actions\DeleteAction::make(),
        ];
    }
}
