<?php

namespace App\Support;

use App\Models\Entitas;
use App\Models\Kepengurusan;
use App\Models\StrukturOrganisasi;
use Illuminate\Support\Collection;

/**
 * Menyusun data bagan struktur organisasi per entitas:
 * struktur periode berjalan → kepengurusan aktif → dikelompokkan per
 * tingkat (jabatan.tingkat) lalu per jabatan (urutan), siap dirender
 * sebagai bagan berjenjang di panel admin maupun halaman publik.
 */
class BaganStruktur
{
    /**
     * @return array{
     *   entitas: Entitas,
     *   struktur: ?StrukturOrganisasi,
     *   tingkat: Collection<int, Collection<string, Collection<int, Kepengurusan>>>,
     *   jumlah: int
     * }
     */
    public static function untuk(Entitas $entitas): array
    {
        $struktur = StrukturOrganisasi::where('entitas_id', $entitas->id)
            ->berjalan()
            ->orderByDesc('periode_mulai')
            ->first()
            ?? StrukturOrganisasi::where('entitas_id', $entitas->id)->orderByDesc('periode_mulai')->first();

        return static::susun($entitas, $struktur);
    }

    /** Bagan untuk satu struktur/periode tertentu (halaman detail Struktur Organisasi). */
    public static function untukStruktur(StrukturOrganisasi $struktur): array
    {
        $struktur->loadMissing('entitas.induk');

        return static::susun($struktur->entitas ?? new Entitas(['nama' => 'Tanpa entitas', 'jenis' => 'lainnya']), $struktur);
    }

    protected static function susun(Entitas $entitas, ?StrukturOrganisasi $struktur): array
    {
        $kepengurusan = $struktur
            ? Kepengurusan::with(['pengurus', 'jabatan'])
                ->where('struktur_id', $struktur->id)
                ->where('status_aktif', true)
                ->whereHas('pengurus', fn ($q) => $q->where('status_aktif', true))
                ->get()
                ->sortBy(fn (Kepengurusan $k) => sprintf(
                    '%02d-%04d-%s',
                    $k->jabatan->tingkat ?? 9,
                    $k->jabatan->urutan ?? 999,
                    $k->pengurus->nama ?? ''
                ))
                ->values()
            : collect();

        $tingkat = $kepengurusan
            ->groupBy(fn (Kepengurusan $k) => (int) ($k->jabatan->tingkat ?? 4))
            ->sortKeys()
            ->map(fn (Collection $baris) => $baris->groupBy(fn (Kepengurusan $k) => $k->jabatan->nama_jabatan ?? 'Anggota'));

        return [
            'entitas' => $entitas,
            'struktur' => $struktur,
            'tingkat' => $tingkat,
            'jumlah' => $kepengurusan->count(),
        ];
    }

    /** @return Collection<int, array> */
    public static function semua(?int $hanyaEntitasId = null): Collection
    {
        return Entitas::aktif()
            ->when($hanyaEntitasId, fn ($q) => $q->whereKey($hanyaEntitasId))
            ->get()
            ->map(fn (Entitas $e) => static::untuk($e));
    }

    public static function inisial(?string $nama): string
    {
        return collect(explode(' ', trim((string) $nama)))
            ->filter(fn ($x) => preg_match('/^[A-Za-z]/', $x))
            ->map(fn ($x) => mb_strtoupper(mb_substr($x, 0, 1)))
            ->take(2)
            ->implode('') ?: 'P';
    }
}
