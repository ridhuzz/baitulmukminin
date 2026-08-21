<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kepengurusan extends Model
{
    protected $table = 'kepengurusan';
    protected $guarded = [];
    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'status_aktif' => 'boolean',
    ];

    public function pengurus(): BelongsTo
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function struktur(): BelongsTo
    {
        return $this->belongsTo(StrukturOrganisasi::class, 'struktur_id');
    }
}
