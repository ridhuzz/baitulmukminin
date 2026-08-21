<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasAksesModul;
use App\Filament\Resources\PengurusResource\Pages;
use App\Filament\Resources\PengurusResource\RelationManagers\KepengurusanRelationManager;
use App\Models\Pengurus;
use App\Support\EntitasAktif;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengurusResource extends Resource
{
    use HasAksesModul;

    protected static ?string $model = Pengurus::class;
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Profil & Struktur';
    protected static ?string $modelLabel = 'Pengurus';
    protected static ?string $pluralModelLabel = 'Data Pengurus';
    protected static ?int $navigationSort = 2;
    protected static array $aksesRole = ['Sekretaris'];

    /**
     * Orang bisa duduk di Yayasan dan DKM sekaligus, jadi data pengurus tidak punya
     * entitas sendiri. Saat satu entitas aktif: tampilkan pengurus yang punya jabatan
     * aktif di entitas itu, plus yang belum punya jabatan sama sekali (agar bisa ditugaskan).
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $entitasId = EntitasAktif::id();

        if (! $entitasId) {
            return $query;
        }

        return $query->where(fn (Builder $q) => $q
            ->whereHas('kepengurusan', fn (Builder $k) => $k->where('status_aktif', true)
                ->whereHas('struktur', fn (Builder $s) => $s->where('entitas_id', $entitasId)))
            ->orWhereDoesntHave('kepengurusan', fn (Builder $k) => $k->where('status_aktif', true)));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Diri')->columns(2)->schema([
                Forms\Components\TextInput::make('nama')->required()->columnSpanFull(),
                Forms\Components\Select::make('jenis_kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                Forms\Components\DatePicker::make('tanggal_lahir'),
                Forms\Components\TextInput::make('telepon')->tel(),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\Textarea::make('alamat')->columnSpanFull(),
                Forms\Components\FileUpload::make('foto')->image()->directory('pengurus')->columnSpanFull(),
                Forms\Components\Toggle::make('status_aktif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['kepengurusan.jabatan', 'kepengurusan.struktur.entitas']))
            ->defaultSort('nama')
            // Tampilan kartu seperti mockup (grid 1/2/3 kolom)
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ViewColumn::make('kartu')
                        ->view('filament.tables.columns.pengurus-card')
                        ->searchable(['nama', 'telepon', 'email']),
                ]),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status_aktif')->label('Status Aktif'),
                Tables\Filters\SelectFilter::make('jabatan')
                    ->label('Jabatan')
                    ->relationship('kepengurusan.jabatan', 'nama_jabatan'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->recordUrl(fn (Pengurus $record): ?string => static::canEdit($record) ? static::getUrl('edit', ['record' => $record]) : null);
    }

    public static function getRelations(): array
    {
        return [KepengurusanRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengurus::route('/'),
            'create' => Pages\CreatePengurus::route('/create'),
            'edit' => Pages\EditPengurus::route('/{record}/edit'),
        ];
    }
}
