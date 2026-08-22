<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\EntitasResource\Pages;
use App\Models\Entitas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Entitas organisasi: Yayasan (induk) dan Masjid/DKM, bisa ditambah unit lain
 * (mis. Remaja Masjid, TPA). Nama di sini dipakai pada laporan, struktur,
 * dan halaman publik.
 */
class EntitasResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = Entitas::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Profil & Struktur';
    protected static ?string $modelLabel = 'Entitas';
    protected static ?string $pluralModelLabel = 'Entitas: Yayasan & DKM';
    protected static ?int $navigationSort = 0;
    protected static array $aksesRole = ['Sekretaris'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama')
                ->label('Nama lengkap')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create'
                    ? $set('slug', Str::slug($state ?? ''))
                    : null),
            Forms\Components\TextInput::make('nama_pendek')
                ->label('Nama pendek (untuk menu/tab)')
                ->maxLength(50)
                ->placeholder('mis. Yayasan / Masjid'),
            Forms\Components\Select::make('jenis')
                ->options(Entitas::JENIS)
                ->required(),
            Forms\Components\Select::make('induk_id')
                ->label('Di bawah naungan')
                ->relationship('induk', 'nama', fn ($query, ?Entitas $record) => $record ? $query->whereKeyNot($record->id) : $query)
                ->nullable()
                ->helperText('Kosongkan untuk entitas tertinggi (Yayasan).'),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Dipakai pada alamat halaman publik, mis. /laporan-keuangan/yayasan'),
            Forms\Components\TextInput::make('urutan')->numeric()->default(0),
            Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
            Forms\Components\Toggle::make('aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->columns([
                Tables\Columns\TextColumn::make('urutan')->sortable(),
                Tables\Columns\TextColumn::make('nama')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('nama_pendek')->label('Nama pendek'),
                Tables\Columns\TextColumn::make('jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Entitas::JENIS[$state] ?? $state)
                    ->color(fn (string $state): string => Entitas::WARNA[$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('induk.nama')->label('Induk')->placeholder('—'),
                Tables\Columns\IconColumn::make('aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Entitas $record): bool => static::canDelete($record)
                        && ! $record->transaksi()->exists()
                        && ! $record->strukturOrganisasi()->exists()),
            ]);
    }

    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->hasRole('Super Admin') ?? false);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageEntitas::route('/')];
    }
}
