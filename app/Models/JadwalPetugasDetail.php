<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPetugasDetail extends Model
{
    protected $table = 'jadwal_petugas_detail';
    protected $guarded = [];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalPetugas::class, 'jadwal_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function pengganti(): HasMany
    {
        return $this->hasMany(PenggantiPetugas::class, 'jadwal_detail_id');
    }
}
