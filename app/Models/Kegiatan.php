<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';
    protected $guarded = [];
    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'anggaran' => 'decimal:2',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKegiatan::class, 'kategori_id');
    }

    public function entitas(): BelongsTo
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    public function pic(): HasMany
    {
        return $this->hasMany(PicKegiatan::class, 'kegiatan_id');
    }

    public function dokumentasi(): HasMany
    {
        return $this->hasMany(DokumentasiKegiatan::class, 'kegiatan_id');
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(LaporanKegiatan::class, 'kegiatan_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class, 'kegiatan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
