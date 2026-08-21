<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Concerns\TerpisahPerEntitas;
use App\Filament\Resources\StrukturOrganisasiResource\Pages;
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
    protected static ?int $navigationSort = 1;
    protected static array $aksesRole = ['Sekretaris'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::entitasSelect()
                ->helperText('Pilih Yayasan atau Masjid (DKM). Struktur kepengurusan dipisah per entitas.'),
            Forms\Components\Hidden::make('masjid_id')
                ->default(fn () => Masjid::first()?->id)
                ->dehydratedWhenHidden(),
            Forms\Components\TextInput::make('nama_struktur')
                ->required()
                ->placeholder('mis. Pengurus Yayasan 2024–2029 / Kepengurusan DKM 2026–2029'),
            Forms\Components\DatePicker::make('periode_mulai'),
            Forms\Components\DatePicker::make('periode_selesai'),
            Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                static::entitasColumn(),
                Tables\Columns\TextColumn::make('nama_struktur')->searchable(),
                Tables\Columns\TextColumn::make('periode_mulai')->date('j M Y')->sortable(),
                Tables\Columns\TextColumn::make('periode_selesai')->date('j M Y')->sortable(),
                Tables\Columns\TextColumn::make('kepengurusan_count')->counts('kepengurusan')->label('Jml. Pengurus'),
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
        return ['index' => Pages\ManageStrukturOrganisasi::route('/')];
    }
}
