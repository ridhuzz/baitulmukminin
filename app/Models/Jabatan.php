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

    /** Jenjang pada bagan struktur (1 = paling atas). */
    public const TINGKAT = [
        1 => 'Tingkat 1 — Pimpinan tertinggi',
        2 => 'Tingkat 2 — Wakil / Sekretaris / Bendahara',
        3 => 'Tingkat 3 — Koordinator / Bidang',
        4 => 'Tingkat 4 — Anggota / Staf',
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
