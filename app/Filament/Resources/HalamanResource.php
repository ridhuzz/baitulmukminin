<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\HalamanResource\Pages;
use App\Models\Halaman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/** Halaman dinamis situs publik: konten HTML bebas + opsi tampil di menu navigasi. */
class HalamanResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = Halaman::class;
    protected static ?string $slug = 'halaman-dinamis';
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Profil & Struktur';
    protected static ?string $navigationLabel = 'Halaman & Menu';
    protected static ?string $modelLabel = 'Halaman';
    protected static ?string $pluralModelLabel = 'Halaman Dinamis';
    protected static ?int $navigationSort = 2;
    protected static array $aksesRole = ['Sekretaris', 'Pengurus'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Konten Halaman')->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(120)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create'
                        ? $set('slug', Str::slug($state ?? ''))
                        : null),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->prefix('halaman/')
                    ->helperText('Alamat halaman, mis. "sejarah-masjid" → situs.com/halaman/sejarah-masjid'),
                Forms\Components\TextInput::make('ringkasan')
                    ->maxLength(200)
                    ->helperText('Kalimat singkat di bawah judul header (opsional).')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('konten')
                    ->label('Isi halaman')
                    ->fileAttachmentsDirectory('halaman')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Menu & Publikasi')->schema([
                Forms\Components\Toggle::make('publish')
                    ->label('Publish')
                    ->default(true)
                    ->helperText('Nonaktifkan untuk menyembunyikan halaman (draft).'),
                Forms\Components\Toggle::make('tampil_di_menu')
                    ->label('Tampilkan di menu navigasi')
                    ->live()
                    ->helperText('Muncul di menu atas situs & menu mobile.'),
                Forms\Components\TextInput::make('label_menu')
                    ->label('Teks di menu')
                    ->maxLength(30)
                    ->placeholder('Kosongkan untuk memakai judul')
                    ->visible(fn (Forms\Get $get) => (bool) $get('tampil_di_menu')),
                Forms\Components\TextInput::make('urutan_menu')
                    ->numeric()
                    ->default(0)
                    ->helperText('Angka kecil tampil lebih dulu.')
                    ->visible(fn (Forms\Get $get) => (bool) $get('tampil_di_menu')),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('judul')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Halaman $record) => 'halaman/' . $record->slug),
                Tables\Columns\IconColumn::make('publish')->label('Publish')->boolean(),
                Tables\Columns\IconColumn::make('tampil_di_menu')->label('Di Menu')->boolean(),
                Tables\Columns\TextColumn::make('urutan_menu')->label('Urutan')->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('updated_at')->label('Diubah')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('publish'),
                Tables\Filters\TernaryFilter::make('tampil_di_menu')->label('Tampil di menu'),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat')
                    ->label('Lihat')
                    ->icon('heroicon-o-globe-alt')
                    ->color('gray')
                    ->url(fn (Halaman $record) => route('publik.halaman', $record->slug), shouldOpenInNewTab: true)
                    ->visible(fn (Halaman $record) => $record->publish),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Belum ada halaman')
            ->emptyStateDescription('Buat halaman bebas (mis. Sejarah Masjid, Tata Tertib, Kontak) dan tampilkan di menu navigasi situs.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHalaman::route('/'),
            'create' => Pages\CreateHalaman::route('/create'),
            'edit' => Pages\EditHalaman::route('/{record}/edit'),
        ];
    }
}
