<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Concerns\TerpisahPerEntitas;
use App\Filament\Resources\LaporanKeuanganResource\Pages;
use App\Models\LaporanKeuangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LaporanKeuanganResource extends Resource
{
    use HasAksesModul;
    use TerpisahPerEntitas;

    protected static ?string $model = LaporanKeuangan::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Laporan Keuangan';
    protected static ?string $pluralModelLabel = 'Laporan Keuangan';
    protected static ?int $navigationSort = 2;
    protected static array $aksesRole = ['Bendahara'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                static::entitasSelect()
                    ->helperText('Angka laporan dihitung dari transaksi entitas ini saja.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->placeholder('Laporan Keuangan Masjid – Juli 2026')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('periode_mulai')->required()->live(),
                Forms\Components\DatePicker::make('periode_selesai')->required()->live(),
                Forms\Components\Select::make('visibilitas')
                    ->options([
                        'publik' => 'Publik (tampil di halaman publik)',
                        'terbatas' => 'Terbatas (hanya jamaah/pengurus)',
                        'internal' => 'Internal',
                    ])
                    ->default('internal')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('draft')
                    ->required(),
                Forms\Components\Textarea::make('catatan')->columnSpanFull(),
                Forms\Components\Placeholder::make('info')
                    ->label('Angka laporan')
                    ->content('Saldo awal, total pemasukan/pengeluaran, dan saldo akhir dihitung OTOMATIS dari transaksi yang disetujui saat laporan disimpan.')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => auth()->id())
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
                Tables\Columns\TextColumn::make('judul')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('periode_mulai')->date('j M Y')->label('Dari'),
                Tables\Columns\TextColumn::make('periode_selesai')->date('j M Y')->label('Sampai'),
                Tables\Columns\TextColumn::make('saldo_awal')->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('total_pemasukan')->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('total_pengeluaran')->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('saldo_akhir')->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('visibilitas')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray'),
            ])
            ->filters([
                static::entitasFilter(),
            ])
            ->actions([
                Tables\Actions\Action::make('hitung_ulang')
                    ->label('Hitung Ulang')
                    ->icon('heroicon-o-calculator')
                    ->action(function (LaporanKeuangan $record): void {
                        $record->hitungDariTransaksi();
                        $record->save();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function mutateFormData(array $data): array
    {
        $data['slug'] = $data['slug'] ?? Str::slug(($data['judul'] ?? 'laporan') . '-' . Str::random(6));

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKeuangan::route('/'),
            'create' => Pages\CreateLaporanKeuangan::route('/create'),
            'edit' => Pages\EditLaporanKeuangan::route('/{record}/edit'),
        ];
    }
}
