<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\SumberDanaResource\Pages;
use App\Models\SumberDana;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SumberDanaResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = SumberDana::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Sumber Dana';
    protected static ?string $pluralModelLabel = 'Sumber Dana';
    protected static ?int $navigationSort = 4;
    protected static array $aksesRole = ['Bendahara'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_sumber')->required(),
            Forms\Components\Select::make('tipe')
                ->options([
                    'pemasukan' => 'Pemasukan',
                    'pengeluaran' => 'Pengeluaran',
                    'keduanya' => 'Keduanya',
                ])
                ->default('pemasukan')
                ->required(),
            Forms\Components\Textarea::make('keterangan'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_sumber')->searchable(),
                Tables\Columns\TextColumn::make('tipe')->badge(),
                Tables\Columns\TextColumn::make('transaksi_count')->counts('transaksi')->label('Jml. Transaksi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageSumberDana::route('/')];
    }
}
