<?php

namespace App\Support;

use App\Models\MenuNavigasi;

/**
 * Menyusun menu navigasi situs publik dari tabel menu_navigasi
 * (Pengaturan → Pengaturan Menu). Bila tabel belum ada / kosong,
 * dipakai menu bawaan agar situs tetap bernavigasi.
 *
 * Bentuk item: ['label', 'href', 'aktif' (bool), 'ikon', 'tab_baru' (bool), 'anak' => item[]]
 */
class MenuPublik
{
    protected const IKON_RUTE = [
        'publik.beranda' => 'heroicon-o-home',
        'publik.jadwal' => 'heroicon-o-clock',
        'publik.kegiatan' => 'heroicon-o-calendar-days',
        'publik.struktur' => 'heroicon-o-user-group',
        'publik.laporan' => 'heroicon-o-banknotes',
    ];

    /** @return array<int, array<string, mixed>> */
    public static function ambil(): array
    {
        $induk = rescue(
            fn () => MenuNavigasi::with(['halaman', 'anak.halaman'])
                ->aktif()
                ->whereNull('induk_id')
                ->orderBy('urutan')
                ->get(),
            null,
            false
        );

        if (! $induk || $induk->isEmpty()) {
            return static::bawaan();
        }

        return $induk
            ->map(fn (MenuNavigasi $m) => static::bentuk($m))
            ->filter()
            ->values()
            ->all();
    }

    /** @return ?array<string, mixed> */
    protected static function bentuk(MenuNavigasi $m): ?array
    {
        $href = $m->href();

        $anak = $m->anak
            ->where('aktif', true)
            ->map(fn (MenuNavigasi $a) => static::bentukAnak($a))
            ->filter()
            ->values()
            ->all();

        if (! $href && empty($anak)) {
            return null;
        }

        return [
            'label' => $m->label,
            'href' => $href,
            'aktif' => static::sedangDibuka($m, $href) || collect($anak)->contains('aktif', true),
            'ikon' => static::ikon($m),
            'tab_baru' => $m->buka_tab_baru,
            'anak' => $anak,
        ];
    }

    /** @return ?array<string, mixed> */
    protected static function bentukAnak(MenuNavigasi $a): ?array
    {
        $href = $a->href();
        if (! $href) {
            return null;
        }

        return [
            'label' => $a->label,
            'href' => $href,
            'aktif' => static::sedangDibuka($a, $href),
            'ikon' => static::ikon($a),
            'tab_baru' => $a->buka_tab_baru,
            'anak' => [],
        ];
    }

    protected static function sedangDibuka(MenuNavigasi $m, ?string $href): bool
    {
        if ($m->tipe === 'rute' && $m->rute) {
            return request()->routeIs($m->rute);
        }

        return $href !== null && url()->current() === $href;
    }

    protected static function ikon(MenuNavigasi $m): string
    {
        if ($m->tipe === 'rute' && isset(static::IKON_RUTE[$m->rute])) {
            return static::IKON_RUTE[$m->rute];
        }

        return $m->tipe === 'url' ? 'heroicon-o-link' : 'heroicon-o-document-text';
    }

    /** Menu bawaan bila tabel menu belum termigrasi / kosong. */
    protected static function bawaan(): array
    {
        return collect(MenuNavigasi::RUTE)
            ->map(fn (string $label, string $rute) => [
                'label' => $rute === 'publik.struktur' ? 'Struktur' : ($rute === 'publik.laporan' ? 'Transparansi' : $label),
                'href' => route($rute),
                'aktif' => request()->routeIs($rute),
                'ikon' => static::IKON_RUTE[$rute] ?? 'heroicon-o-document-text',
                'tab_baru' => false,
                'anak' => [],
            ])
            ->values()
            ->all();
    }
}
