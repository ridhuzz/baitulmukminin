<?php

namespace App\Support;

use App\Models\Entitas;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Entitas yang sedang "aktif" di panel pengurus.
 *
 * - Jika user dibatasi ke satu entitas (users.entitas_id), itu yang berlaku — tidak bisa diganti.
 * - Jika tidak, pilihan disimpan di session (pemilih di topbar): id entitas, atau null = semua.
 * - Semua query resource/dashboard memakai terapkan() supaya data otomatis terpisah.
 */
class EntitasAktif
{
    public const SESSION = 'entitas_aktif';

    protected static ?Collection $daftar = null;

    /** @return Collection<int, Entitas> */
    public static function daftar(): Collection
    {
        return static::$daftar ??= Entitas::aktif()->get();
    }

    public static function dipaksa(): ?Entitas
    {
        /** @var ?User $user */
        $user = auth()->user();

        if (! $user || ! $user->entitas_id) {
            return null;
        }

        return static::daftar()->firstWhere('id', $user->entitas_id);
    }

    public static function bolehSemua(): bool
    {
        return static::dipaksa() === null;
    }

    public static function id(): ?int
    {
        if ($dipaksa = static::dipaksa()) {
            return (int) $dipaksa->id;
        }

        $id = session(static::SESSION);

        if ($id === null || $id === 'semua') {
            return null;
        }

        return static::daftar()->contains('id', (int) $id) ? (int) $id : null;
    }

    public static function entitas(): ?Entitas
    {
        $id = static::id();

        return $id ? static::daftar()->firstWhere('id', $id) : null;
    }

    /** Nilai default untuk form (entitas aktif, atau Masjid bila "Semua"). */
    public static function idDefault(): ?int
    {
        return static::id() ?? Entitas::masjid()?->id;
    }

    public static function set(?int $id): void
    {
        session([static::SESSION => $id ?? 'semua']);
    }

    public static function label(): string
    {
        return static::entitas()?->label ?? 'Semua Entitas';
    }

    /** Terapkan filter entitas aktif ke query (tanpa filter bila "Semua"). */
    public static function terapkan(Builder $query, string $kolom = 'entitas_id'): Builder
    {
        $id = static::id();

        return $id ? $query->where($query->qualifyColumn($kolom), $id) : $query;
    }
}
