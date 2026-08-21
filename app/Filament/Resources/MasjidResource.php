<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MasjidResource\Pages;
use App\Models\Masjid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MasjidResource extends Resource
{
    protected static ?string $model = Masjid::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Profil & Struktur';
    protected static ?string $modelLabel = 'Profil Masjid';
    protected static ?string $pluralModelLabel = 'Profil Masjid';
    protected static ?int $navigationSort = 0;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['Super Admin', 'Ketua DKM', 'Sekretaris']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('nama_masjid')->required()->columnSpanFull(),
                Forms\Components\TextInput::make('kota'),
                Forms\Components\TextInput::make('provinsi'),
                Forms\Components\TextInput::make('kontak'),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\Textarea::make('alamat')->columnSpanFull(),
                Forms\Components\FileUpload::make('logo')->image()->directory('masjid'),
                Forms\Components\Textarea::make('deskripsi')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo'),
                Tables\Columns\TextColumn::make('nama_masjid'),
                Tables\Columns\TextColumn::make('kota'),
                Tables\Columns\TextColumn::make('kontak'),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageMasjid::route('/')];
    }
}
