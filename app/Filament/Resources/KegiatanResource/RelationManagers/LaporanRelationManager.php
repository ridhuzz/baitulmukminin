<?php

namespace App\Filament\Resources\KegiatanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LaporanRelationManager extends RelationManager
{
    protected static string $relationship = 'laporan';
    protected static ?string $title = 'Laporan Pelaksanaan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('tanggal_laporan')->default(now()),
            Forms\Components\Textarea::make('ringkasan')->rows(4),
            Forms\Components\FileUpload::make('file_laporan')
                ->label('File Laporan')
                ->directory('laporan-kegiatan'),
            Forms\Components\Hidden::make('created_by')
                ->default(fn () => auth()->id())
                ->dehydratedWhenHidden(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_laporan')->date('j M Y'),
                Tables\Columns\TextColumn::make('ringkasan')->limit(80)->wrap(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
