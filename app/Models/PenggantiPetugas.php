<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggantiPetugas extends Model
{
    protected $table = 'pengganti_petugas';
    protected $guarded = [];
    protected $casts = ['tanggal_update' => 'date'];

    public function jadwalDetail(): BelongsTo
    {
        return $this->belongsTo(JadwalPetugasDetail::class, 'jadwal_detail_id');
    }

    public function petugasPengganti(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'petugas_pengganti_id');
    }
}
