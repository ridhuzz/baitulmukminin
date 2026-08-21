<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PicKegiatan extends Model
{
    protected $table = 'pic_kegiatan';
    protected $guarded = [];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function pengurus(): BelongsTo
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }
}
