<?php

namespace App\Filament\Resources\LaporanKeuanganResource\Pages;

use App\Filament\Resources\LaporanKeuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditLaporanKeuangan extends EditRecord
{
    protected static string $resource = LaporanKeuanganResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill($data);
        /** @var \App\Models\LaporanKeuangan $record */
        $record->hitungDariTransaksi();
        $record->save();

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
