<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriTransaksi extends Model
{
    protected $table = 'kategori_transaksi';
    protected $guarded = [];

    public function induk(): BelongsTo
    {
        return $this->belongsTo(self::class, 'induk_id');
    }

    public function anak(): HasMany
    {
        return $this->hasMany(self::class, 'induk_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class, 'kategori_transaksi_id');
    }
}
