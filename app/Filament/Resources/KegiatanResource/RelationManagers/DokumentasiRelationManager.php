<?php

namespace App\Filament\Resources\KegiatanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DokumentasiRelationManager extends RelationManager
{
    protected static string $relationship = 'dokumentasi';
    protected static ?string $title = 'Dokumentasi';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('jenis_dokumen')
                ->options([
                    'foto' => 'Foto',
                    'video' => 'Video',
                    'dokumen' => 'Dokumen',
                    'lainnya' => 'Lainnya',
                ])
                ->default('foto')
                ->required(),
            Forms\Components\TextInput::make('judul'),
            Forms\Components\FileUpload::make('file_path')
                ->label('File')
                ->directory('dokumentasi-kegiatan')
                ->required(),
            Forms\Components\DatePicker::make('tanggal_upload')->default(now()),
            Forms\Components\Textarea::make('keterangan'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul'),
                Tables\Columns\TextColumn::make('jenis_dokumen')->badge(),
                Tables\Columns\TextColumn::make('tanggal_upload')->date('j M Y'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
