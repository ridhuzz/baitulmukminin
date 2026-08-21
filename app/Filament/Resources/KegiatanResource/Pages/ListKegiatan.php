<?php

namespace App\Filament\Resources\KegiatanResource\Pages;

use App\Filament\Resources\KegiatanResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListKegiatan extends ListRecords
{
    protected static string $resource = KegiatanResource::class;

    protected static ?string $title = 'Program & Kegiatan';

    public function getSubheading(): ?string
    {
        return 'Kelola master program, jadwal kajian, dan kegiatan sosial.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Kegiatan Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'terjadwal' => Tab::make('Terjadwal')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['terjadwal', 'berlangsung'])),
            'selesai' => Tab::make('Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'selesai')),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),
        ];
    }
}
