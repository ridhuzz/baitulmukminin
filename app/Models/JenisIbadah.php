<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisIbadah extends Model
{
    protected $table = 'jenis_ibadah';
    protected $guarded = [];

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalPetugas::class, 'jenis_ibadah_id');
    }
}
