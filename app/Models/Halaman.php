<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Halaman dinamis (konten HTML) untuk situs publik, dikelola dari admin. */
class Halaman extends Model
{
    protected $table = 'halaman';

    protected $fillable = [
        'judul', 'slug', 'ringkasan', 'konten',
        'tampil_di_menu', 'label_menu', 'urutan_menu', 'publish',
    ];

    protected $casts = [
        'tampil_di_menu' => 'boolean',
        'publish' => 'boolean',
        'urutan_menu' => 'integer',
    ];

    public function scopePublik(Builder $query): Builder
    {
        return $query->where('publish', true);
    }

    /** Halaman yang tampil sebagai item menu navigasi publik. */
    public static function menu()
    {
        return static::publik()
            ->where('tampil_di_menu', true)
            ->orderBy('urutan_menu')
            ->orderBy('judul')
            ->get(['judul', 'slug', 'label_menu']);
    }

    public function getLabelAttribute(): string
    {
        return $this->label_menu ?: $this->judul;
    }
}
