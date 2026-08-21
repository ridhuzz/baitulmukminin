<?php

namespace App\Filament\Resources\JadwalPetugasResource\RelationManagers;

use App\Models\Petugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DetailRelationManager extends RelationManager
{
    protected static string $relationship = 'detail';
    protected static ?string $title = 'Penugasan Petugas';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('peran')
                ->options(Petugas::PERAN)
                ->required(),
            Forms\Components\Select::make('petugas_id')
                ->label('Petugas')
                ->options(fn () => Petugas::where('status_aktif', true)->orderBy('nama')->pluck('nama', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Textarea::make('catatan'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('peran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Petugas::PERAN[$state] ?? $state),
                Tables\Columns\TextColumn::make('petugas.nama')->label('Petugas'),
                Tables\Columns\TextColumn::make('pengganti')
                    ->label('Pengganti')
                    ->state(fn ($record): string => $record->pengganti
                        ->map(fn ($p) => $p->petugasPengganti->nama ?? '-')
                        ->implode(', ') ?: '-'),
                Tables\Columns\TextColumn::make('catatan')->wrap(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()->label('Tambah Penugasan')])
            ->actions([
                Tables\Actions\Action::make('ganti')
                    ->label('Ganti Petugas')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('petugas_pengganti_id')
                            ->label('Petugas Pengganti')
                            ->options(fn () => Petugas::where('status_aktif', true)->orderBy('nama')->pluck('nama', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Textarea::make('alasan'),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->pengganti()->create([
                            'petugas_pengganti_id' => $data['petugas_pengganti_id'],
                            'alasan' => $data['alasan'] ?? null,
                            'tanggal_update' => now()->toDateString(),
                        ]);
                        $record->update(['petugas_id' => $data['petugas_pengganti_id']]);

                        Notification::make()
                            ->title('Petugas berhasil diganti')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
