<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\GaleriResource\Pages;
use App\Models\Galeri;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Galeri "Suasana & Fasilitas Masjid" yang tampil di beranda publik. */
class GaleriResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = Galeri::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Profil & Struktur';
    protected static ?string $navigationLabel = 'Galeri & Fasilitas';
    protected static ?string $modelLabel = 'Foto Galeri';
    protected static ?string $pluralModelLabel = 'Galeri & Fasilitas Masjid';
    protected static ?int $navigationSort = 1;
    protected static array $aksesRole = ['Sekretaris', 'Pengurus'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('gambar')
                ->label('Foto')
                ->image()
                ->imageEditor()
                ->directory('galeri')
                ->maxSize(4096)
                ->helperText('JPG/PNG/WebP, maks. 4 MB. Rasio 4:3 paling pas untuk kartu galeri.')
                ->required()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('judul')
                ->required()
                ->maxLength(100)
                ->placeholder('Ruang Utama Masjid'),
            Forms\Components\Select::make('kategori')
                ->options(Galeri::KATEGORI)
                ->default('fasilitas')
                ->required(),
            Forms\Components\TextInput::make('keterangan')
                ->maxLength(160)
                ->placeholder('Keterangan singkat (opsional)')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('urutan')
                ->numeric()
                ->default(0)
                ->helperText('Angka kecil tampil lebih dulu; foto pertama jadi foto besar.'),
            Forms\Components\Toggle::make('tampil')
                ->label('Tampilkan di beranda')
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
                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Foto')
                    ->disk('public')
                    ->width(96)
                    ->height(72),
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Galeri $record) => $record->keterangan),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state) => Galeri::KATEGORI[$state] ?? $state),
                Tables\Columns\TextColumn::make('urutan')->sortable()->alignCenter(),
                Tables\Columns\IconColumn::make('tampil')->label('Tampil')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')->options(Galeri::KATEGORI),
                Tables\Filters\TernaryFilter::make('tampil')->label('Tampil di beranda'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Belum ada foto galeri')
            ->emptyStateDescription('Selama galeri kosong, beranda menampilkan gambar ilustrasi. Unggah foto asli masjid di sini.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGaleri::route('/'),
        ];
    }
}
