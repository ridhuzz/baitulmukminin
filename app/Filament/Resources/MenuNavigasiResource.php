<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\MenuNavigasiResource\Pages;
use App\Models\Halaman;
use App\Models\MenuNavigasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Pengaturan menu navigasi situs publik (mendukung submenu satu tingkat). */
class MenuNavigasiResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = MenuNavigasi::class;
    protected static ?string $slug = 'pengaturan-menu';
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Pengaturan Menu';
    protected static ?string $modelLabel = 'Item Menu';
    protected static ?string $pluralModelLabel = 'Menu Navigasi Situs';
    protected static ?int $navigationSort = 2;
    protected static array $aksesRole = ['Sekretaris', 'Pengurus'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')
                ->label('Teks menu')
                ->required()
                ->maxLength(50),
            Forms\Components\Select::make('induk_id')
                ->label('Submenu dari')
                ->options(fn (?MenuNavigasi $record) => MenuNavigasi::whereNull('induk_id')
                    ->when($record, fn ($q) => $q->whereKeyNot($record->id))
                    ->orderBy('urutan')
                    ->pluck('label', 'id'))
                ->placeholder('— Menu utama —')
                ->helperText('Kosongkan agar tampil di baris menu; pilih induk agar menjadi submenu (dropdown).'),
            Forms\Components\Select::make('tipe')
                ->label('Tujuan')
                ->options(MenuNavigasi::TIPE)
                ->default('rute')
                ->required()
                ->live(),
            Forms\Components\Select::make('rute')
                ->label('Halaman bawaan')
                ->options(MenuNavigasi::RUTE)
                ->required(fn (Forms\Get $get) => $get('tipe') === 'rute')
                ->visible(fn (Forms\Get $get) => $get('tipe') === 'rute'),
            Forms\Components\Select::make('halaman_id')
                ->label('Halaman dinamis')
                ->options(fn () => Halaman::orderBy('judul')->pluck('judul', 'id'))
                ->searchable()
                ->helperText('Kelola kontennya di Pengaturan → Pengaturan Halaman.')
                ->required(fn (Forms\Get $get) => $get('tipe') === 'halaman')
                ->visible(fn (Forms\Get $get) => $get('tipe') === 'halaman'),
            Forms\Components\TextInput::make('url')
                ->label('URL')
                ->placeholder('https://…')
                ->required(fn (Forms\Get $get) => $get('tipe') === 'url')
                ->visible(fn (Forms\Get $get) => $get('tipe') === 'url'),
            Forms\Components\Toggle::make('buka_tab_baru')
                ->label('Buka di tab baru')
                ->visible(fn (Forms\Get $get) => $get('tipe') === 'url'),
            Forms\Components\TextInput::make('urutan')
                ->numeric()
                ->default(0)
                ->helperText('Angka kecil tampil lebih dulu.'),
            Forms\Components\Toggle::make('aktif')
                ->default(true)
                ->inline(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->reorderable('urutan')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Teks menu')
                    ->weight('bold')
                    ->formatStateUsing(fn (string $state, MenuNavigasi $record) => ($record->induk_id ? '↳ ' : '') . $state)
                    ->description(fn (MenuNavigasi $record) => $record->induk ? 'Submenu dari ' . $record->induk->label : null)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state) => MenuNavigasi::TIPE[$state] ?? $state),
                Tables\Columns\TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->state(fn (MenuNavigasi $record) => $record->tujuan()),
                Tables\Columns\TextColumn::make('urutan')->sortable()->alignCenter(),
                Tables\Columns\IconColumn::make('aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Belum ada item menu')
            ->emptyStateDescription('Selama kosong, situs memakai menu bawaan (Beranda, Jadwal Ibadah, Kegiatan, Struktur, Transparansi).');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMenuNavigasi::route('/'),
        ];
    }
}
