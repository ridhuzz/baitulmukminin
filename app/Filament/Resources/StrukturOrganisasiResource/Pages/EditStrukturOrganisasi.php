<?php

namespace App\Filament\Resources\StrukturOrganisasiResource\Pages;

use App\Filament\Resources\StrukturOrganisasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStrukturOrganisasi extends EditRecord
{
    protected static string $resource = StrukturOrganisasiResource::class;

    public function getTitle(): string
    {
        return 'Ubah: ' . $this->getRecord()->nama_struktur;
    }

    public function getSubheading(): ?string
    {
        return 'Ubah data periode, lalu kelola pengurus & jabatannya pada bagian Kepengurusan di bawah.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('Lihat Bagan')->icon('heroicon-o-rectangle-group'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
