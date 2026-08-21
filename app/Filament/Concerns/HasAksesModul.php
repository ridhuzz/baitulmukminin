<?php

namespace App\Filament\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Hak akses per modul sesuai blueprint "User & Hak Akses".
 *
 * - Super Admin  : semua modul, semua aksi.
 * - Ketua DKM    : bisa MELIHAT semua modul (monitoring) tetapi tidak
 *                  membuat/mengubah data — kecuali resource yang secara
 *                  eksplisit memasukkannya ke $aksesRole (mis. approval).
 * - Role lain    : akses penuh hanya pada modul yang terdaftar di $aksesRole.
 *
 * Pakai: tambahkan `use HasAksesModul;` di resource dan definisikan
 * `protected static array $aksesRole = ['Bendahara'];`
 */
trait HasAksesModul
{
    protected static function currentUser(): ?User
    {
        /** @var ?User $user */
        $user = auth()->user();

        return $user;
    }

    protected static function bolehKelola(): bool
    {
        $user = static::currentUser();

        if (! $user) {
            return false;
        }

        return $user->hasRole('Super Admin')
            || $user->hasAnyRole(static::$aksesRole ?? []);
    }

    public static function canViewAny(): bool
    {
        $user = static::currentUser();

        if (! $user) {
            return false;
        }

        return $user->hasRole('Ketua DKM') || static::bolehKelola();
    }

    public static function canCreate(): bool
    {
        return static::bolehKelola();
    }

    public static function canEdit(Model $record): bool
    {
        return static::bolehKelola();
    }

    public static function canDelete(Model $record): bool
    {
        return static::bolehKelola();
    }

    public static function canDeleteAny(): bool
    {
        return static::bolehKelola();
    }
}
