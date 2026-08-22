<?php

namespace App\Filament\Resources\StrukturOrganisasiResource\Pages;

use App\Filament\Resources\StrukturOrganisasiResource;
use App\Support\BaganStruktur;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * Halaman detail struktur: bagan organisasi periode ini + (di bawahnya)
 * tabel kepengurusan dari relation manager.
 */
class ViewStrukturOrganisasi extends ViewRecord
{
    protected static string $resource = StrukturOrganisasiResource::class;

    protected static string $view = 'filament.resources.struktur-organisasi.view';

    public function getTitle(): string
    {
        return $this->getRecord()->nama_struktur;
    }

    public function getSubheading(): ?string
    {
        $r = $this->getRecord();
        $periode = $r->label_periode;

        return 'Bagan struktur ' . ($r->entitas?->nama ?? '') . ($periode ? ' · periode ' . $periode : '') . '. Jenjang mengikuti "Tingkat pada bagan" di menu Jabatan.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publik')
                ->label('Versi publik')
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(route('publik.struktur'), shouldOpenInNewTab: true),
            Actions\EditAction::make()->label('Ubah / Kelola Pengurus'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'blok' => BaganStruktur::untukStruktur($this->getRecord()),
        ];
    }
}
