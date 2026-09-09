<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Halaman dinamis (konten HTML) untuk situs publik, dikelola dari admin. */
class Halaman extends Model
{
    protected $table = 'halaman';

    protected $fillable = ['judul', 'slug', 'ringkasan', 'konten', 'publish'];

    protected $casts = [
        'publish' => 'boolean',
    ];

    public function scopePublik(Builder $query): Builder
    {
        return $query->where('publish', true);
    }
}
