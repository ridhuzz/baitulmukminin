<?php

namespace App\Filament\Resources\PengurusResource\RelationManagers;

use App\Models\Entitas;
use App\Models\Jabatan;
use App\Models\StrukturOrganisasi;
use App\Support\EntitasAktif;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KepengurusanRelationManager extends RelationManager
{
    protected static string $relationship = 'kepengurusan';
    protected static ?string $title = 'Jabatan & Riwayat Kepengurusan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('struktur_id')
                ->label('Struktur / Periode')
                ->options(fn () => EntitasAktif::terapkan(StrukturOrganisasi::query()->with('entitas'))
                    ->orderByDesc('periode_mulai')
                    ->get()
                    ->mapWithKeys(fn (StrukturOrganisasi $s) => [
                        $s->id => ($s->entitas?->label ? $s->entitas->label . ' · ' : '') . $s->nama_struktur,
                    ]))
                ->live()
                ->required()
                ->native(false)
                ->helperText('Pilih struktur Yayasan atau Masjid (DKM); daftar jabatan mengikuti pilihan ini.'),
            Forms\Components\Select::make('jabatan_id')
                ->label('Jabatan')
                ->options(function (Forms\Get $get) {
                    $struktur = StrukturOrganisasi::with('entitas')->find($get('struktur_id'));
                    $entitas = $struktur?->entitas;
                    $kelompok = $entitas ? $entitas->kelompokJabatan() : array_keys(Jabatan::KELOMPOK);

                    return Jabatan::query()->untukKelompok($kelompok)->pluck('nama_jabatan', 'id');
                })
                ->required()
                ->native(false),
            Forms\Components\DatePicker::make('periode_mulai'),
            Forms\Components\DatePicker::make('periode_selesai'),
            Forms\Components\Toggle::make('status_aktif')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['struktur.entitas', 'jabatan']))
            ->columns([
                Tables\Columns\TextColumn::make('struktur.entitas.label')
                    ->label('Entitas')
                    ->badge()
                    ->color(fn ($record): string => $record->struktur?->entitas?->warna ?? 'gray'),
                Tables\Columns\TextColumn::make('struktur.nama_struktur')->label('Struktur'),
                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')->label('Jabatan'),
                Tables\Columns\TextColumn::make('periode_mulai')->date('j M Y'),
                Tables\Columns\TextColumn::make('periode_selesai')->date('j M Y'),
                Tables\Columns\IconColumn::make('status_aktif')->boolean()->label('Aktif'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()->label('Tambah Jabatan')])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
