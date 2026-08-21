<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKegiatan extends Model
{
    protected $table = 'kategori_kegiatan';
    protected $guarded = [];

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'kategori_id');
    }
}
