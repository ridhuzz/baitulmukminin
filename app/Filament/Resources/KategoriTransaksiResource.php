<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\KategoriTransaksiResource\Pages;
use App\Models\KategoriTransaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KategoriTransaksiResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = KategoriTransaksi::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Kategori Transaksi';
    protected static ?string $pluralModelLabel = 'Kategori Transaksi';
    protected static ?int $navigationSort = 3;
    protected static array $aksesRole = ['Bendahara'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_kategori')->required(),
            Forms\Components\Select::make('tipe')
                ->options([
                    'pemasukan' => 'Pemasukan',
                    'pengeluaran' => 'Pengeluaran',
                ])
                ->required(),
            Forms\Components\Select::make('induk_id')
                ->label('Induk Kategori')
                ->relationship('induk', 'nama_kategori')
                ->searchable()
                ->preload()
                ->nullable(),
            Forms\Components\Textarea::make('keterangan'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')->searchable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'pemasukan' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('induk.nama_kategori')->label('Induk')->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')->options([
                    'pemasukan' => 'Pemasukan',
                    'pengeluaran' => 'Pengeluaran',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageKategoriTransaksi::route('/')];
    }
}
