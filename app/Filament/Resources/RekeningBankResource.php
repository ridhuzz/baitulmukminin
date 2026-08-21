<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Concerns\TerpisahPerEntitas;
use App\Filament\Resources\RekeningBankResource\Pages;
use App\Models\RekeningBank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RekeningBankResource extends Resource
{
    use HasAksesModul;
    use TerpisahPerEntitas;

    protected static ?string $model = RekeningBank::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Rekening Bank';
    protected static ?string $pluralModelLabel = 'Rekening Bank';
    protected static ?int $navigationSort = 5;
    protected static array $aksesRole = ['Bendahara'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::entitasSelect()->columnSpanFull(),
            Forms\Components\TextInput::make('nama_bank')->required(),
            Forms\Components\TextInput::make('nomor_rekening')->required(),
            Forms\Components\TextInput::make('nama_pemilik'),
            Forms\Components\Toggle::make('aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                static::entitasColumn(),
                Tables\Columns\TextColumn::make('nama_bank')->searchable(),
                Tables\Columns\TextColumn::make('nomor_rekening'),
                Tables\Columns\TextColumn::make('nama_pemilik'),
                Tables\Columns\TextColumn::make('saldo')
                    ->label('Saldo (transaksi disetujui)')
                    ->state(fn (RekeningBank $record): string => 'Rp ' . number_format($record->saldo, 0, ',', '.')),
                Tables\Columns\IconColumn::make('aktif')->boolean(),
            ])
            ->filters([
                static::entitasFilter(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageRekeningBank::route('/')];
    }
}
