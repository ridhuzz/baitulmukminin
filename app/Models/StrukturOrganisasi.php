<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StrukturOrganisasi extends Model
{
    protected $table = 'struktur_organisasi';
    protected $guarded = [];
    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    public function masjid(): BelongsTo
    {
        return $this->belongsTo(Masjid::class);
    }

    public function entitas(): BelongsTo
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    public function kepengurusan(): HasMany
    {
        return $this->hasMany(Kepengurusan::class, 'struktur_id');
    }

    /** Struktur yang periodenya mencakup hari ini (atau tanpa batas periode). */
    public function scopeBerjalan(Builder $query): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('periode_mulai')->orWhereDate('periode_mulai', '<=', today()))
            ->where(fn (Builder $q) => $q->whereNull('periode_selesai')->orWhereDate('periode_selesai', '>=', today()));
    }

    public function getLabelPeriodeAttribute(): string
    {
        if (! $this->periode_mulai) {
            return '';
        }

        return $this->periode_mulai->format('Y') . ($this->periode_selesai ? '–' . $this->periode_selesai->format('Y') : '');
    }
}
