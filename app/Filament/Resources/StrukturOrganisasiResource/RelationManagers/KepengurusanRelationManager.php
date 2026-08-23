<?php

namespace App\Filament\Resources\StrukturOrganisasiResource\RelationManagers;

use App\Models\Jabatan;
use App\Models\Pengurus;
use App\Models\StrukturOrganisasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Daftar pengurus + jabatan pada satu struktur/periode. Dari sini pengurus
 * bisa ditambahkan langsung ke struktur (orang dipilih dari Data Pengurus,
 * atau dibuat baru lewat tombol + di samping pilihan).
 */
class KepengurusanRelationManager extends RelationManager
{
    protected static string $relationship = 'kepengurusan';
    protected static ?string $title = 'Kepengurusan (Pengurus & Jabatan)';
    protected static ?string $modelLabel = 'penugasan';

    public function form(Form $form): Form
    {
        /** @var StrukturOrganisasi $struktur */
        $struktur = $this->getOwnerRecord();
        $kelompok = $struktur->entitas?->kelompokJabatan() ?? array_keys(Jabatan::KELOMPOK);

        return $form->schema([
            Forms\Components\Select::make('pengurus_id')
                ->label('Pengurus')
                ->relationship('pengurus', 'nama', fn (Builder $query) => $query->where('status_aktif', true)->orderBy('nama'))
                ->searchable()
                ->preload()
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('nama')->required(),
                    Forms\Components\TextInput::make('telepon')->tel(),
                    Forms\Components\Select::make('jenis_kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                    Forms\Components\Hidden::make('status_aktif')->default(true),
                ])
                ->createOptionUsing(fn (array $data): int => Pengurus::create($data)->getKey()),
            Forms\Components\Select::make('jabatan_id')
                ->label('Jabatan')
                ->options(fn () => Jabatan::query()->untukKelompok($kelompok)->pluck('nama_jabatan', 'id'))
                ->required()
                ->helperText('Daftar jabatan mengikuti jenis entitas struktur ini (Yayasan / Masjid-DKM).'),
            Forms\Components\DatePicker::make('periode_mulai')->default($struktur->periode_mulai),
            Forms\Components\DatePicker::make('periode_selesai')->default($struktur->periode_selesai),
            Forms\Components\Toggle::make('status_aktif')->label('Aktif')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['pengurus', 'jabatan']))
            ->defaultSort(fn (Builder $query) => $query
                ->join('jabatan', 'jabatan.id', '=', 'kepengurusan.jabatan_id')
                ->orderBy('jabatan.tingkat')
                ->orderBy('jabatan.urutan')
                ->select('kepengurusan.*'))
            ->columns([
                Tables\Columns\TextColumn::make('jabatan.tingkat')->label('Tingkat')->badge()->color('gray')
                    ->formatStateUsing(fn ($state): string => 'Tingkat ' . $state),
                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')->label('Jabatan')->weight('bold'),
                Tables\Columns\TextColumn::make('pengurus.nama')->label('Nama pengurus')->searchable(),
                Tables\Columns\TextColumn::make('pengurus.telepon')->label('Telepon')->placeholder('—'),
                Tables\Columns\TextColumn::make('periode_mulai')->date('j M Y')->label('Mulai'),
                Tables\Columns\TextColumn::make('periode_selesai')->date('j M Y')->label('Selesai'),
                Tables\Columns\IconColumn::make('status_aktif')->boolean()->label('Aktif'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Pengurus ke Struktur')->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
