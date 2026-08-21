<?php

namespace App\Filament\Resources\LaporanKeuanganResource\Pages;

use App\Filament\Resources\LaporanKeuanganResource;
use App\Models\LaporanKeuangan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLaporanKeuangan extends CreateRecord
{
    protected static string $resource = LaporanKeuanganResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return LaporanKeuanganResource::mutateFormData($data);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $laporan = new LaporanKeuangan($data);
        $laporan->hitungDariTransaksi();
        $laporan->save();

        return $laporan;
    }
}
