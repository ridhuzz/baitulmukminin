<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Concerns\TerpisahPerEntitas;
use App\Filament\Resources\KegiatanResource\Pages;
use App\Filament\Resources\KegiatanResource\RelationManagers;
use App\Models\Kegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KegiatanResource extends Resource
{
    use HasAksesModul;
    use TerpisahPerEntitas;

    protected static ?string $model = Kegiatan::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Program & Kegiatan';
    protected static ?string $modelLabel = 'Kegiatan';
    protected static ?string $pluralModelLabel = 'Kegiatan';
    protected static ?int $navigationSort = 1;
    protected static array $aksesRole = ['Sekretaris', 'Pengurus'];

    public const STATUS = [
        'draft' => 'Draft',
        'terjadwal' => 'Terjadwal',
        'berlangsung' => 'Berlangsung',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                static::entitasSelect()
                    ->label('Penyelenggara (Entitas)')
                    ->helperText('Kegiatan, laporan, dan dokumentasinya tercatat pada entitas ini.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('nama_kegiatan')->required()->columnSpanFull(),
                Forms\Components\Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->options(self::STATUS)
                    ->default('draft')
                    ->required(),
                Forms\Components\DateTimePicker::make('tanggal_mulai')->seconds(false),
                Forms\Components\DateTimePicker::make('tanggal_selesai')->seconds(false),
                Forms\Components\TextInput::make('lokasi'),
                Forms\Components\TextInput::make('anggaran')->numeric()->prefix('Rp'),
                Forms\Components\Textarea::make('deskripsi')->columnSpanFull(),
                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => auth()->id())
                    ->dehydratedWhenHidden(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_mulai', 'desc')
            ->columns([
                static::entitasColumn(),
                Tables\Columns\TextColumn::make('nama_kegiatan')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('kategori.nama_kategori')->badge()->label('Kategori'),
                Tables\Columns\TextColumn::make('tanggal_mulai')->dateTime('j M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('lokasi')->toggleable(),
                Tables\Columns\TextColumn::make('anggaran')->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'terjadwal' => 'info',
                        'berlangsung' => 'warning',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                static::entitasFilter(),
                Tables\Filters\SelectFilter::make('status')->options(self::STATUS),
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PicRelationManager::class,
            RelationManagers\DokumentasiRelationManager::class,
            RelationManagers\LaporanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKegiatan::route('/'),
            'create' => Pages\CreateKegiatan::route('/create'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
        ];
    }
}
