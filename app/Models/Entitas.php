<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Entitas / unit organisasi: Yayasan (induk), Masjid-DKM, dan unit lain.
 * Struktur, kas, rekening, laporan, kegiatan, dan pengumuman terpisah per entitas.
 */
class Entitas extends Model
{
    public const JENIS = [
        'yayasan' => 'Yayasan',
        'masjid' => 'Masjid (DKM)',
        'lainnya' => 'Unit Lainnya',
    ];

    /** Warna badge Filament per jenis. */
    public const WARNA = [
        'yayasan' => 'info',
        'masjid' => 'success',
        'lainnya' => 'gray',
    ];

    protected $table = 'entitas';
    protected $guarded = [];
    protected $casts = ['aktif' => 'boolean'];

    public function induk(): BelongsTo
    {
        return $this->belongsTo(self::class, 'induk_id');
    }

    public function anak(): HasMany
    {
        return $this->hasMany(self::class, 'induk_id')->orderBy('urutan');
    }

    public function strukturOrganisasi(): HasMany
    {
        return $this->hasMany(StrukturOrganisasi::class, 'entitas_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class, 'entitas_id');
    }

    public function rekening(): HasMany
    {
        return $this->hasMany(RekeningBank::class, 'entitas_id');
    }

    public function laporanKeuangan(): HasMany
    {
        return $this->hasMany(LaporanKeuangan::class, 'entitas_id');
    }

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'entitas_id');
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'entitas_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true)->orderBy('urutan')->orderBy('id');
    }

    public function getLabelAttribute(): string
    {
        return $this->nama_pendek ?: $this->nama;
    }

    public function getWarnaAttribute(): string
    {
        return self::WARNA[$this->jenis] ?? 'gray';
    }

    /** Entitas masjid/DKM utama (dipakai halaman publik & nilai default). */
    public static function masjid(): ?self
    {
        return static::aktif()->where('jenis', 'masjid')->first() ?? static::aktif()->first();
    }

    public static function yayasan(): ?self
    {
        return static::aktif()->where('jenis', 'yayasan')->first();
    }

    /** Jabatan yang relevan untuk entitas ini: kelompok sesuai jenis + 'umum'. */
    public function kelompokJabatan(): array
    {
        return [$this->jenis === 'yayasan' ? 'yayasan' : 'masjid', 'umum'];
    }
}
