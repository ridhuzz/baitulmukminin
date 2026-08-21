<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Concerns\TerpisahPerEntitas;
use App\Filament\Resources\TransaksiKeuanganResource\Pages;
use App\Models\KategoriTransaksi;
use App\Models\TransaksiKeuangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransaksiKeuanganResource extends Resource
{
    use HasAksesModul;
    use TerpisahPerEntitas;

    protected static ?string $model = TransaksiKeuangan::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $pluralModelLabel = 'Transaksi Keuangan';
    protected static ?int $navigationSort = 1;
    protected static array $aksesRole = ['Bendahara'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                static::entitasSelect()->columnSpanFull(),
                Forms\Components\Select::make('jenis_transaksi')
                    ->options([
                        'pemasukan' => 'Kas Masuk',
                        'pengeluaran' => 'Kas Keluar',
                    ])
                    ->live()
                    ->required(),
                Forms\Components\Select::make('kategori_transaksi_id')
                    ->label('Kategori')
                    ->options(fn (Forms\Get $get) => KategoriTransaksi::query()
                        ->when($get('jenis_transaksi'), fn ($q, $jenis) => $q->where('tipe', $jenis))
                        ->orderBy('nama_kategori')
                        ->pluck('nama_kategori', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_transaksi')->default(now())->required(),
                Forms\Components\TextInput::make('nominal')->numeric()->prefix('Rp')->required()->minValue(0),
                Forms\Components\Select::make('metode_pembayaran')
                    ->options([
                        'kas' => 'Kas Tunai',
                        'bank' => 'Bank',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'lainnya' => 'Lainnya',
                    ])
                    ->default('kas')
                    ->live()
                    ->required(),
                Forms\Components\Select::make('rekening_bank_id')
                    ->label('Rekening')
                    ->relationship('rekening', 'nama_bank')
                    ->visible(fn (Forms\Get $get): bool => in_array($get('metode_pembayaran'), ['bank', 'transfer', 'qris'])),
                Forms\Components\Select::make('sumber_dana_id')
                    ->label('Sumber Dana')
                    ->relationship('sumberDana', 'nama_sumber'),
                Forms\Components\Select::make('kegiatan_id')
                    ->label('Terkait Kegiatan')
                    ->relationship('kegiatan', 'nama_kegiatan')
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
                Forms\Components\FileUpload::make('bukti_path')
                    ->label('Bukti Transaksi')
                    ->directory('bukti-transaksi')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('input_by')
                    ->default(fn () => auth()->id())
                    ->dehydratedWhenHidden(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_transaksi', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_transaksi')->date('j M Y')->sortable(),
                static::entitasColumn(),
                Tables\Columns\TextColumn::make('jenis_transaksi')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'pemasukan' ? 'Masuk' : 'Keluar')
                    ->color(fn (string $state): string => $state === 'pemasukan' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('kategori.nama_kategori')->label('Kategori')->searchable(),
                Tables\Columns\TextColumn::make('nominal')->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('metode_pembayaran')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('keterangan')->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('status_approval')
                    ->label('Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                static::entitasFilter(),
                Tables\Filters\SelectFilter::make('jenis_transaksi')->options([
                    'pemasukan' => 'Pemasukan',
                    'pengeluaran' => 'Pengeluaran',
                ]),
                Tables\Filters\SelectFilter::make('status_approval')->options([
                    'menunggu' => 'Menunggu',
                    'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak',
                ]),
                Tables\Filters\Filter::make('periode')
                    ->form([
                        Forms\Components\DatePicker::make('dari'),
                        Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'] ?? null, fn ($q, $d) => $q->whereDate('tanggal_transaksi', '>=', $d))
                            ->when($data['sampai'] ?? null, fn ($q, $d) => $q->whereDate('tanggal_transaksi', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (TransaksiKeuangan $record): bool => $record->status_approval === 'menunggu'
                        && (auth()->user()?->hasAnyRole(['Super Admin', 'Ketua DKM']) ?? false))
                    ->action(function (TransaksiKeuangan $record): void {
                        $record->update([
                            'status_approval' => 'disetujui',
                            'approved_by' => auth()->id(),
                        ]);
                        Notification::make()->title('Transaksi disetujui')->success()->send();
                    }),
                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (TransaksiKeuangan $record): bool => $record->status_approval === 'menunggu'
                        && (auth()->user()?->hasAnyRole(['Super Admin', 'Ketua DKM']) ?? false))
                    ->action(function (TransaksiKeuangan $record): void {
                        $record->update([
                            'status_approval' => 'ditolak',
                            'approved_by' => auth()->id(),
                        ]);
                        Notification::make()->title('Transaksi ditolak')->danger()->send();
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn (TransaksiKeuangan $record): bool => $record->status_approval !== 'disetujui'
                        && static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (TransaksiKeuangan $record): bool => $record->status_approval !== 'disetujui'
                        && static::canDelete($record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiKeuangan::route('/'),
            'create' => Pages\CreateTransaksiKeuangan::route('/create'),
            'edit' => Pages\EditTransaksiKeuangan::route('/{record}/edit'),
        ];
    }
}
