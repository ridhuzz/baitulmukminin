<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPetugas extends Model
{
    protected $table = 'jadwal_petugas';
    protected $guarded = [];
    protected $casts = ['tanggal_jadwal' => 'date'];

    public function jenisIbadah(): BelongsTo
    {
        return $this->belongsTo(JenisIbadah::class, 'jenis_ibadah_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(JadwalPetugasDetail::class, 'jadwal_id');
    }
}
