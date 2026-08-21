<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    protected $guarded = [];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status_publish' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entitas(): BelongsTo
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    public function scopePublik(Builder $query): Builder
    {
        return $query->where('status_publish', true)
            ->where('target', 'publik')
            ->where(fn (Builder $q) => $q->whereNull('tanggal_selesai')->orWhereDate('tanggal_selesai', '>=', now()));
    }
}
