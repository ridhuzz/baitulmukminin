<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\PetugasResource\Pages;
use App\Models\Petugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PetugasResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = Petugas::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Jadwal Ibadah';
    protected static ?string $modelLabel = 'Petugas';
    protected static ?string $pluralModelLabel = 'Master Petugas';
    protected static ?int $navigationSort = 2;
    protected static array $aksesRole = ['Koordinator Ibadah'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('nama')->required()->columnSpanFull(),
                Forms\Components\Select::make('jenis_kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                Forms\Components\TextInput::make('telepon')->tel(),
                Forms\Components\Textarea::make('alamat')->columnSpanFull(),
                Forms\Components\CheckboxList::make('peran')
                    ->options(Petugas::PERAN)
                    ->columns(4)
                    ->columnSpanFull()
                    ->helperText('Satu petugas boleh merangkap beberapa peran.'),
                Forms\Components\Toggle::make('status_aktif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('peran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Petugas::PERAN[$state] ?? $state)
                    ->separator(','),
                Tables\Columns\TextColumn::make('telepon'),
                Tables\Columns\IconColumn::make('status_aktif')->boolean()->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status_aktif')->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPetugas::route('/'),
            'create' => Pages\CreatePetugas::route('/create'),
            'edit' => Pages\EditPetugas::route('/{record}/edit'),
        ];
    }
}
