<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumentasiKegiatan extends Model
{
    protected $table = 'dokumentasi_kegiatan';
    protected $guarded = [];
    protected $casts = ['tanggal_upload' => 'date'];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}
