<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\KategoriKegiatanResource\Pages;
use App\Models\KategoriKegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KategoriKegiatanResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = KategoriKegiatan::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Program & Kegiatan';
    protected static ?string $modelLabel = 'Kategori Kegiatan';
    protected static ?string $pluralModelLabel = 'Kategori Kegiatan';
    protected static ?int $navigationSort = 2;
    protected static array $aksesRole = ['Sekretaris', 'Pengurus'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_kategori')->required(),
            Forms\Components\Textarea::make('deskripsi'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kegiatan_count')->counts('kegiatan')->label('Jml. Kegiatan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageKategoriKegiatan::route('/')];
    }
}
