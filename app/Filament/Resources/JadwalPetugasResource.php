<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\JadwalPetugasResource\Pages;
use App\Filament\Resources\JadwalPetugasResource\RelationManagers\DetailRelationManager;
use App\Models\JadwalPetugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JadwalPetugasResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = JadwalPetugas::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Jadwal Ibadah';
    protected static ?string $modelLabel = 'Jadwal Petugas';
    protected static ?string $pluralModelLabel = 'Jadwal Petugas';
    protected static ?int $navigationSort = 1;
    protected static array $aksesRole = ['Koordinator Ibadah'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\Select::make('jenis_ibadah_id')
                    ->label('Jenis Ibadah')
                    ->relationship('jenisIbadah', 'nama_jenis')
                    ->live()
                    ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => static::isiWaktuOtomatis($get, $set))
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_jadwal')
                    ->live()
                    ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => static::isiWaktuOtomatis($get, $set))
                    ->required(),
                Forms\Components\TimePicker::make('waktu_mulai')
                    ->seconds(false)
                    ->helperText('Untuk shalat harian & Jumat, jam terisi otomatis dari jadwal Kemenag (bisa diubah).'),
                Forms\Components\TimePicker::make('waktu_selesai')->seconds(false),
                Forms\Components\TextInput::make('lokasi')->placeholder('Ruang Utama'),
                Forms\Components\Select::make('status')
                    ->options([
                        'terjadwal' => 'Terjadwal',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('terjadwal')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
            ]),
        ]);
    }

    /**
     * Isi waktu_mulai otomatis dari jadwal shalat Kemenag bila jenis ibadah
     * harian (Subuh…Isya) atau Jumat (= Dzuhur) dan tanggal sudah dipilih.
     */
    public static function isiWaktuOtomatis(Forms\Get $get, Forms\Set $set): void
    {
        $jenis = \App\Models\JenisIbadah::find($get('jenis_ibadah_id'));
        $tanggal = $get('tanggal_jadwal');
        if (! $jenis || ! $tanggal) {
            return;
        }

        $nama = $jenis->kategori === 'jumat'
            ? 'Dzuhur'
            : \App\Services\JadwalShalat::namaWaktuDariJenis($jenis->nama_jenis);
        if (! $nama) {
            return;
        }

        try {
            $waktu = app(\App\Services\JadwalShalat::class)->untuk(
                \Carbon\Carbon::parse($tanggal),
                \App\Models\Masjid::first()
            );
        } catch (\Throwable) {
            return;
        }

        if (! empty($waktu[$nama])) {
            $set('waktu_mulai', $waktu[$nama]);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_jadwal', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_jadwal')->date('D, j M Y')->sortable(),
                Tables\Columns\TextColumn::make('jenisIbadah.nama_jenis')->label('Ibadah'),
                Tables\Columns\TextColumn::make('waktu_mulai')->time('H:i')->label('Waktu'),
                Tables\Columns\TextColumn::make('detail')
                    ->label('Petugas')
                    ->state(fn (JadwalPetugas $record): string => $record->detail
                        ->map(fn ($d) => ucfirst($d->peran) . ': ' . ($d->petugas->nama ?? '-'))
                        ->implode(' · ') ?: '-')
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'terjadwal' => 'info',
                        'selesai' => 'success',
                        default => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_ibadah_id')
                    ->label('Jenis Ibadah')
                    ->relationship('jenisIbadah', 'nama_jenis'),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari'),
                        Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'] ?? null, fn ($q, $d) => $q->whereDate('tanggal_jadwal', '>=', $d))
                            ->when($data['sampai'] ?? null, fn ($q, $d) => $q->whereDate('tanggal_jadwal', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [DetailRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalPetugas::route('/'),
            'create' => Pages\CreateJadwalPetugas::route('/create'),
            'edit' => Pages\EditJadwalPetugas::route('/{record}/edit'),
        ];
    }
}
