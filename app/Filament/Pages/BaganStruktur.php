<?php

namespace App\Filament\Pages;

use App\Support\BaganStruktur as Bagan;
use App\Support\EntitasAktif;
use Filament\Pages\Page;

/**
 * Bagan (diagram berjenjang) struktur organisasi Yayasan & DKM periode berjalan.
 */
class BaganStruktur extends Page
{
    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Profil & Struktur';

    protected static ?string $navigationLabel = 'Bagan Struktur';

    protected static ?string $title = 'Bagan Struktur Organisasi';

    protected static ?string $slug = 'bagan-struktur';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.bagan-struktur';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['Super Admin', 'Ketua DKM', 'Sekretaris', 'Bendahara', 'Koordinator Ibadah', 'Pengurus']) ?? false;
    }

    public function getSubheading(): ?string
    {
        $entitas = EntitasAktif::entitas();

        return $entitas
            ? 'Struktur kepengurusan ' . $entitas->nama . ' periode berjalan. Jenjang mengikuti "Tingkat pada bagan" di menu Jabatan.'
            : 'Struktur kepengurusan Yayasan dan Masjid (DKM) periode berjalan. Jenjang mengikuti "Tingkat pada bagan" di menu Jabatan.';
    }

    protected function getViewData(): array
    {
        return [
            'blok' => Bagan::semua(EntitasAktif::id()),
            'urlPublik' => route('publik.struktur'),
        ];
    }
}
