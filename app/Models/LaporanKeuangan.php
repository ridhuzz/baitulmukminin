<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKeuangan extends Model
{
    protected $table = 'laporan_keuangan';
    protected $guarded = [];
    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'saldo_awal' => 'decimal:2',
        'total_pemasukan' => 'decimal:2',
        'total_pengeluaran' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entitas(): BelongsTo
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    /** Hitung ulang angka laporan dari transaksi_keuangan entitas ini (snapshot). */
    public function hitungDariTransaksi(): void
    {
        $mulai = $this->periode_mulai->toDateString();
        $selesai = $this->periode_selesai->toDateString();
        $entitas = $this->entitas_id;

        $saldoAwal = (float) TransaksiKeuangan::disetujui()
            ->entitas($entitas)
            ->where('tanggal_transaksi', '<', $mulai)
            ->selectRaw("COALESCE(SUM(CASE WHEN jenis_transaksi = 'pemasukan' THEN nominal ELSE -nominal END), 0) as saldo")
            ->value('saldo');

        $pemasukan = (float) TransaksiKeuangan::disetujui()
            ->entitas($entitas)
            ->where('jenis_transaksi', 'pemasukan')
            ->whereBetween('tanggal_transaksi', [$mulai, $selesai])
            ->sum('nominal');

        $pengeluaran = (float) TransaksiKeuangan::disetujui()
            ->entitas($entitas)
            ->where('jenis_transaksi', 'pengeluaran')
            ->whereBetween('tanggal_transaksi', [$mulai, $selesai])
            ->sum('nominal');

        $this->fill([
            'saldo_awal' => $saldoAwal,
            'total_pemasukan' => $pemasukan,
            'total_pengeluaran' => $pengeluaran,
            'saldo_akhir' => $saldoAwal + $pemasukan - $pengeluaran,
        ]);
    }
}
