<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Concerns\TerpisahPerEntitas;
use App\Filament\Resources\StrukturOrganisasiResource\Pages;
use App\Filament\Resources\StrukturOrganisasiResource\RelationManagers\KepengurusanRelationManager;
use App\Models\Masjid;
use App\Models\StrukturOrganisasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StrukturOrganisasiResource extends Resource
{
    use HasAksesModul;
    use TerpisahPerEntitas;

    protected static ?string $model = StrukturOrganisasi::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Profil & Struktur';
    protected static ?string $modelLabel = 'Struktur Organisasi';
    protected static ?string $pluralModelLabel = 'Struktur Organisasi';
    protected static ?string $recordTitleAttribute = 'nama_struktur';
    protected static ?int $navigationSort = 1;
    protected static array $aksesRole = ['Sekretaris'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Periode Kepengurusan')
                ->description('Satu struktur = satu periode kepengurusan pada satu entitas (Yayasan atau Masjid/DKM). Pengurus & jabatannya diisi pada tab Kepengurusan setelah disimpan.')
                ->columns(2)
                ->schema([
                    static::entitasSelect()
                        ->helperText('Pilih Yayasan atau Masjid (DKM). Struktur kepengurusan dipisah per entitas.'),
                    Forms\Components\TextInput::make('nama_struktur')
                        ->required()
                        ->placeholder('mis. Pengurus Yayasan 2024–2029 / Kepengurusan DKM 2026–2029'),
                    Forms\Components\DatePicker::make('periode_mulai')->native(false)->displayFormat('d M Y'),
                    Forms\Components\DatePicker::make('periode_selesai')->native(false)->displayFormat('d M Y'),
                    Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
                    Forms\Components\Hidden::make('masjid_id')
                        ->default(fn () => Masjid::first()?->id)
                        ->dehydratedWhenHidden(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('periode_mulai', 'desc')
            ->columns([
                static::entitasColumn(),
                Tables\Columns\TextColumn::make('nama_struktur')->label('Nama struktur')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('periode_mulai')->label('Periode mulai')->date('j M Y')->sortable(),
                Tables\Columns\TextColumn::make('periode_selesai')->label('Periode selesai')->date('j M Y')->sortable(),
                Tables\Columns\TextColumn::make('kepengurusan_count')->counts('kepengurusan')->label('Jml. Pengurus')->alignCenter(),
                Tables\Columns\IconColumn::make('berjalan')
                    ->label('Berjalan')
                    ->boolean()
                    ->state(fn (StrukturOrganisasi $record): bool => (! $record->periode_mulai || $record->periode_mulai->lte(today()))
                        && (! $record->periode_selesai || $record->periode_selesai->gte(today()))),
            ])
            ->filters([
                static::entitasFilter(),
            ])
            ->recordUrl(fn (StrukturOrganisasi $record): string => static::getUrl('view', ['record' => $record]))
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat Bagan')->icon('heroicon-o-rectangle-group'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [KepengurusanRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStrukturOrganisasi::route('/'),
            'create' => Pages\CreateStrukturOrganisasi::route('/create'),
            'view' => Pages\ViewStrukturOrganisasi::route('/{record}'),
            'edit' => Pages\EditStrukturOrganisasi::route('/{record}/edit'),
        ];
    }
}
