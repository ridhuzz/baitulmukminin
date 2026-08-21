<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengurus extends Model
{
    protected $table = 'pengurus';
    protected $guarded = [];
    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_aktif' => 'boolean',
    ];

    public function kepengurusan(): HasMany
    {
        return $this->hasMany(Kepengurusan::class, 'pengurus_id');
    }

    public function picKegiatan(): HasMany
    {
        return $this->hasMany(PicKegiatan::class, 'pengurus_id');
    }
}
