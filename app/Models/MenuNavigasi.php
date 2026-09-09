<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

/** Item menu navigasi situs publik (mendukung satu tingkat submenu). */
class MenuNavigasi extends Model
{
    protected $table = 'menu_navigasi';

    /** Halaman bawaan aplikasi yang bisa dijadikan tujuan menu. */
    public const RUTE = [
        'publik.beranda' => 'Beranda',
        'publik.jadwal' => 'Jadwal Ibadah',
        'publik.kegiatan' => 'Kegiatan',
        'publik.struktur' => 'Struktur Organisasi',
        'publik.laporan' => 'Transparansi Keuangan',
    ];

    public const TIPE = [
        'rute' => 'Halaman bawaan',
        'halaman' => 'Halaman dinamis',
        'url' => 'Tautan URL',
    ];

    protected $fillable = [
        'label', 'induk_id', 'tipe', 'rute', 'halaman_id', 'url',
        'buka_tab_baru', 'urutan', 'aktif',
    ];

    protected $casts = [
        'buka_tab_baru' => 'boolean',
        'aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    public function induk(): BelongsTo
    {
        return $this->belongsTo(static::class, 'induk_id');
    }

    public function anak(): HasMany
    {
        return $this->hasMany(static::class, 'induk_id')->orderBy('urutan');
    }

    public function halaman(): BelongsTo
    {
        return $this->belongsTo(Halaman::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /** URL tujuan item; null bila tujuan tidak valid (mis. halaman di-draft). */
    public function href(): ?string
    {
        return match ($this->tipe) {
            'rute' => $this->rute && Route::has($this->rute) ? route($this->rute) : null,
            'halaman' => $this->halaman && $this->halaman->publish
                ? route('publik.halaman', $this->halaman->slug)
                : null,
            'url' => $this->url ?: null,
            default => null,
        };
    }

    /** Ringkasan tujuan untuk kolom tabel admin. */
    public function tujuan(): string
    {
        return match ($this->tipe) {
            'rute' => static::RUTE[$this->rute] ?? (string) $this->rute,
            'halaman' => 'halaman/' . ($this->halaman->slug ?? '?'),
            'url' => (string) $this->url,
            default => '-',
        };
    }
}
