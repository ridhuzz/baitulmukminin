<?php

namespace App\Filament\Resources\KegiatanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PicRelationManager extends RelationManager
{
    protected static string $relationship = 'pic';
    protected static ?string $title = 'PIC / Penanggung Jawab';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('pengurus_id')
                ->label('Pengurus')
                ->relationship('pengurus', 'nama')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('peran')->placeholder('mis. Ketua Panitia'),
            Forms\Components\Textarea::make('catatan'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengurus.nama')->label('Nama'),
                Tables\Columns\TextColumn::make('peran'),
                Tables\Columns\TextColumn::make('catatan')->wrap(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
