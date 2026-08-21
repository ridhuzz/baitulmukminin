<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\JenisIbadahResource\Pages;
use App\Models\JenisIbadah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JenisIbadahResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = JenisIbadah::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Jadwal Ibadah';
    protected static ?string $modelLabel = 'Jenis Ibadah';
    protected static ?string $pluralModelLabel = 'Jenis Ibadah';
    protected static ?int $navigationSort = 3;
    protected static array $aksesRole = ['Koordinator Ibadah'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_jenis')->required(),
            Forms\Components\Select::make('kategori')
                ->options([
                    'harian' => 'Harian',
                    'jumat' => 'Jumat',
                    'ramadhan' => 'Ramadhan',
                    'idul_fitri' => 'Idul Fitri',
                    'idul_adha' => 'Idul Adha',
                    'lainnya' => 'Lainnya',
                ])
                ->default('harian')
                ->required(),
            Forms\Components\TextInput::make('urutan')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->columns([
                Tables\Columns\TextColumn::make('urutan')->sortable(),
                Tables\Columns\TextColumn::make('nama_jenis')->searchable(),
                Tables\Columns\TextColumn::make('kategori')->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageJenisIbadah::route('/')];
    }
}
