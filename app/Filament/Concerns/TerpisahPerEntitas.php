<?php

namespace App\Filament\Concerns;

use App\Models\Entitas;
use App\Support\EntitasAktif;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

/**
 * Untuk resource yang datanya terpisah per entitas (Masjid/DKM vs Yayasan):
 * - tabel otomatis tersaring sesuai entitas aktif di topbar,
 * - form punya pilihan "Entitas" (default entitas aktif; terkunci bila user dibatasi),
 * - kolom/filter entitas muncul saat mode "Semua Entitas".
 */
trait TerpisahPerEntitas
{
    public static function getEloquentQuery(): Builder
    {
        return EntitasAktif::terapkan(parent::getEloquentQuery());
    }

    public static function entitasSelect(): Forms\Components\Select
    {
        $dipaksa = EntitasAktif::dipaksa();

        return Forms\Components\Select::make('entitas_id')
            ->label('Entitas')
            ->options(fn () => EntitasAktif::daftar()->pluck('nama', 'id'))
            ->default(fn () => EntitasAktif::idDefault())
            ->required()
            ->native(false)
            ->disabled((bool) $dipaksa)
            ->dehydrated()
            ->helperText($dipaksa
                ? 'Akun Anda dibatasi pada entitas ini.'
                : 'Data ini akan tercatat pada kas/struktur entitas yang dipilih.');
    }

    public static function entitasColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('entitas.label')
            ->label('Entitas')
            ->badge()
            ->color(fn ($record): string => $record->entitas?->warna ?? 'gray')
            ->visible(fn (): bool => EntitasAktif::id() === null)
            ->sortable();
    }

    public static function entitasFilter(): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make('entitas_id')
            ->label('Entitas')
            ->options(fn () => EntitasAktif::daftar()->pluck('nama', 'id'))
            ->visible(fn (): bool => EntitasAktif::id() === null);
    }

    public static function entitasMasjidId(): ?int
    {
        return Entitas::masjid()?->id;
    }
}
