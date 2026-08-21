<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Concerns\TerpisahPerEntitas;
use App\Filament\Resources\PengumumanResource\Pages;
use App\Models\Pengumuman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PengumumanResource extends Resource
{
    use HasAksesModul;
    use TerpisahPerEntitas;

    protected static ?string $model = Pengumuman::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Pengumuman';
    protected static ?string $modelLabel = 'Pengumuman';
    protected static ?string $pluralModelLabel = 'Pengumuman & Informasi';
    protected static ?int $navigationSort = 1;
    protected static array $aksesRole = ['Sekretaris', 'Pengurus'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                static::entitasSelect()
                    ->label('Penerbit (Entitas)')
                    ->helperText('Pengumuman tampil atas nama entitas ini.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create'
                        ? $set('slug', Str::slug($state ?? ''))
                        : null)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                Forms\Components\Select::make('jenis')
                    ->options([
                        'pengumuman' => 'Pengumuman',
                        'kegiatan' => 'Info Kegiatan',
                        'kajian' => 'Jadwal Kajian',
                        'donasi' => 'Info Donasi',
                        'informasi' => 'Informasi Umum',
                        'banner' => 'Banner',
                    ])
                    ->default('pengumuman')
                    ->required(),
                Forms\Components\Select::make('target')
                    ->options([
                        'publik' => 'Publik',
                        'jamaah' => 'Jamaah',
                        'pengurus' => 'Pengurus',
                        'tertentu' => 'Tertentu',
                    ])
                    ->default('publik')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_mulai'),
                Forms\Components\DatePicker::make('tanggal_selesai'),
                Forms\Components\FileUpload::make('gambar')
                    ->image()
                    ->directory('pengumuman')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('konten')->columnSpanFull(),
                Forms\Components\Toggle::make('status_publish')->label('Publish'),
                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => auth()->id())
                    ->dehydratedWhenHidden(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('gambar'),
                static::entitasColumn(),
                Tables\Columns\TextColumn::make('judul')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('jenis')->badge(),
                Tables\Columns\TextColumn::make('target')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('tanggal_mulai')->date('j M Y'),
                Tables\Columns\ToggleColumn::make('status_publish')->label('Publish'),
            ])
            ->filters([
                static::entitasFilter(),
                Tables\Filters\SelectFilter::make('jenis')->options([
                    'pengumuman' => 'Pengumuman',
                    'kegiatan' => 'Info Kegiatan',
                    'kajian' => 'Jadwal Kajian',
                    'donasi' => 'Info Donasi',
                    'informasi' => 'Informasi Umum',
                    'banner' => 'Banner',
                ]),
                Tables\Filters\TernaryFilter::make('status_publish')->label('Publish'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengumuman::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }
}
