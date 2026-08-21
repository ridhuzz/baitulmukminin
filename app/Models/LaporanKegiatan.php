<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKegiatan extends Model
{
    protected $table = 'laporan_kegiatan';
    protected $guarded = [];
    protected $casts = ['tanggal_laporan' => 'date'];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}
