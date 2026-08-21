<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    /** Pengelompokan jabatan agar pilihan tersaring sesuai entitas struktur. */
    public const KELOMPOK = [
        'yayasan' => 'Yayasan',
        'masjid' => 'Masjid (DKM)',
        'umum' => 'Umum (keduanya)',
    ];

    protected $table = 'jabatan';
    protected $guarded = [];

    public function kepengurusan(): HasMany
    {
        return $this->hasMany(Kepengurusan::class, 'jabatan_id');
    }

    public function scopeUntukKelompok(Builder $query, array $kelompok): Builder
    {
        return $query->whereIn('kelompok', $kelompok)->orderBy('urutan')->orderBy('nama_jabatan');
    }
}
