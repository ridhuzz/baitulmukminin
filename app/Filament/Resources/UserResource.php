<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'User & Hak Akses';
    protected static ?int $navigationSort = 1;

    /** Hanya Super Admin yang boleh mengelola user. */
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny() && $record->getKey() !== auth()->id();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nama')->required(),
                Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('telepon')->tel(),
                Forms\Components\Select::make('pengurus_id')
                    ->label('Tautkan ke Data Pengurus')
                    ->relationship('pengurus', 'nama')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('entitas_id')
                    ->label('Batasi ke Entitas')
                    ->relationship('entitas', 'nama')
                    ->nullable()
                    ->placeholder('Semua entitas (Yayasan & Masjid)')
                    ->helperText('Isi bila user hanya boleh mengelola satu entitas, mis. Bendahara Yayasan.'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText('Kosongkan jika tidak ingin mengganti password.'),
                Forms\Components\Toggle::make('status_aktif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Role')->badge(),
                Tables\Columns\TextColumn::make('entitas.label')->label('Entitas')->badge()
                    ->color(fn ($record): string => $record->entitas?->warna ?? 'gray')
                    ->placeholder('Semua'),
                Tables\Columns\TextColumn::make('pengurus.nama')->label('Pengurus')->placeholder('-'),
                Tables\Columns\IconColumn::make('status_aktif')->boolean()->label('Aktif'),
                Tables\Columns\TextColumn::make('last_login_at')->dateTime('j M Y H:i')->label('Login Terakhir')->placeholder('-'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
