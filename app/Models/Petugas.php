<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Petugas extends Model
{
    public const PERAN = [
        'imam' => 'Imam',
        'khotib' => 'Khotib',
        'muadzin' => 'Muadzin',
        'bilal' => 'Bilal',
        'penceramah' => 'Penceramah',
        'mc' => 'MC',
        'lainnya' => 'Lainnya',
    ];

    protected $table = 'petugas';
    protected $guarded = [];
    protected $casts = [
        'peran' => 'array',
        'status_aktif' => 'boolean',
    ];

    public function penugasan(): HasMany
    {
        return $this->hasMany(JadwalPetugasDetail::class, 'petugas_id');
    }
}
