<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RekeningBank extends Model
{
    protected $table = 'rekening_bank';
    protected $guarded = [];
    protected $casts = ['aktif' => 'boolean'];

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class, 'rekening_bank_id');
    }

    public function entitas(): BelongsTo
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    /** Saldo dihitung langsung dari transaksi yang disetujui. */
    public function getSaldoAttribute(): float
    {
        return (float) $this->transaksi()
            ->where('status_approval', 'disetujui')
            ->selectRaw("COALESCE(SUM(CASE WHEN jenis_transaksi = 'pemasukan' THEN nominal ELSE -nominal END), 0) as saldo")
            ->value('saldo');
    }
}
