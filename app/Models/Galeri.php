<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/** Foto galeri "Suasana & Fasilitas Masjid" (beranda publik). */
class Galeri extends Model
{
    protected $table = 'galeri';

    public const KATEGORI = [
        'fasilitas' => 'Fasilitas',
        'kegiatan' => 'Kegiatan',
        'suasana' => 'Suasana Masjid',
    ];

    protected $fillable = ['judul', 'kategori', 'gambar', 'keterangan', 'urutan', 'tampil'];

    protected $casts = [
        'tampil' => 'boolean',
        'urutan' => 'integer',
    ];

    public function scopeTampil(Builder $query): Builder
    {
        return $query->where('tampil', true)->orderBy('urutan')->orderByDesc('id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->gambar);
    }
}
