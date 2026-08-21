<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKeuangan extends Model
{
    protected $table = 'transaksi_keuangan';
    protected $guarded = [];
    protected $casts = [
        'tanggal_transaksi' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriTransaksi::class, 'kategori_transaksi_id');
    }

    public function sumberDana(): BelongsTo
    {
        return $this->belongsTo(SumberDana::class, 'sumber_dana_id');
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(RekeningBank::class, 'rekening_bank_id');
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function entitas(): BelongsTo
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status_approval', 'disetujui');
    }

    /** Batasi ke satu entitas (null = semua entitas / gabungan). */
    public function scopeEntitas(Builder $query, Entitas|int|null $entitas): Builder
    {
        $id = $entitas instanceof Entitas ? $entitas->id : $entitas;

        return $id ? $query->where('entitas_id', $id) : $query;
    }

    /** Total saldo kas + bank dari seluruh transaksi disetujui (per entitas, atau gabungan). */
    public static function saldoTotal(Entitas|int|null $entitas = null): float
    {
        return (float) static::disetujui()
            ->entitas($entitas)
            ->selectRaw("COALESCE(SUM(CASE WHEN jenis_transaksi = 'pemasukan' THEN nominal ELSE -nominal END), 0) as saldo")
            ->value('saldo');
    }

    public static function totalBulanBerjalan(string $jenis, Entitas|int|null $entitas = null): float
    {
        return (float) static::disetujui()
            ->entitas($entitas)
            ->where('jenis_transaksi', $jenis)
            ->whereBetween('tanggal_transaksi', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('nominal');
    }
}
