<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\JabatanResource\Pages;
use App\Models\Jabatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JabatanResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = Jabatan::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Profil & Struktur';
    protected static ?string $modelLabel = 'Jabatan';
    protected static ?string $pluralModelLabel = 'Jabatan';
    protected static ?int $navigationSort = 3;
    protected static array $aksesRole = ['Sekretaris'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_jabatan')->required()->maxLength(255),
            Forms\Components\Select::make('kelompok')
                ->label('Berlaku untuk')
                ->options(Jabatan::KELOMPOK)
                ->default('masjid')
                ->required()
                ->helperText('Menentukan jabatan ini muncul di struktur Yayasan, Masjid (DKM), atau keduanya.'),
            Forms\Components\Select::make('tingkat')
                ->label('Tingkat pada bagan')
                ->options(Jabatan::TINGKAT)
                ->default(3)
                ->required()
                ->helperText('Baris/jenjang tempat jabatan ini digambar pada bagan struktur organisasi.'),
            Forms\Components\TextInput::make('urutan')->numeric()->default(0)
                ->helperText('Urutan kiri→kanan dalam satu tingkat (angka kecil lebih dulu).'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->columns([
                Tables\Columns\TextColumn::make('urutan')->sortable(),
                Tables\Columns\TextColumn::make('nama_jabatan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kelompok')
                    ->label('Berlaku untuk')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Jabatan::KELOMPOK[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'yayasan' => 'info',
                        'masjid' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state): string => 'Tingkat ' . $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('kepengurusan_count')->counts('kepengurusan')->label('Jml. Pengurus'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelompok')->label('Berlaku untuk')->options(Jabatan::KELOMPOK),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageJabatan::route('/')];
    }
}
