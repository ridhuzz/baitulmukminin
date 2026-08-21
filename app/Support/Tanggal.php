<?php

namespace App\Support;

use Carbon\CarbonInterface;
use IntlDateFormatter;
use Throwable;

/**
 * Helper tanggal untuk tampilan: nama hari versi masjid (Ahad) dan
 * konversi ke kalender Hijriah via ekstensi intl.
 */
class Tanggal
{
    public const HARI = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public const BULAN = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    public const BULAN_PENDEK = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
    ];

    public static function hari(CarbonInterface $tanggal): string
    {
        return self::HARI[$tanggal->dayOfWeek];
    }

    /** Contoh: "16 Agustus 2026". */
    public static function panjang(CarbonInterface $tanggal): string
    {
        return $tanggal->day . ' ' . self::BULAN[$tanggal->month] . ' ' . $tanggal->year;
    }

    /** Contoh: "16 Ags 2026". */
    public static function pendek(CarbonInterface $tanggal): string
    {
        return $tanggal->day . ' ' . self::BULAN_PENDEK[$tanggal->month] . ' ' . $tanggal->year;
    }

    /** Contoh: "Ahad, 16 Agustus 2026". */
    public static function lengkap(CarbonInterface $tanggal): string
    {
        return self::hari($tanggal) . ', ' . self::panjang($tanggal);
    }

    /** Contoh: "25 Safar 1448 H" — dihitung dengan kalender islamic ICU. */
    public static function hijriah(CarbonInterface $tanggal): ?string
    {
        if (! class_exists(IntlDateFormatter::class)) {
            return null;
        }

        try {
            $fmt = new IntlDateFormatter(
                'id_ID@calendar=islamic',
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE,
                $tanggal->getTimezone()->getName(),
                IntlDateFormatter::TRADITIONAL,
                'd MMMM y'
            );

            $hasil = $fmt->format($tanggal->getTimestamp());

            return $hasil ? $hasil . ' H' : null;
        } catch (Throwable) {
            return null;
        }
    }

    /** Contoh: "Ahad, 16 Agustus 2026 / 25 Safar 1448 H". */
    public static function lengkapDenganHijriah(CarbonInterface $tanggal): string
    {
        $hijriah = self::hijriah($tanggal);

        return self::lengkap($tanggal) . ($hijriah ? ' / ' . $hijriah : '');
    }
}
