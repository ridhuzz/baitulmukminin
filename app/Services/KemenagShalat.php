<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Klien API jadwal shalat Kemenag RI (data bulanan via https://equran.id/apidev/shalat).
 *
 *  GET  /api/v2/shalat/provinsi                → daftar provinsi
 *  POST /api/v2/shalat/kabkota {provinsi}      → daftar kab/kota
 *  POST /api/v2/shalat {provinsi,kabkota,bulan,tahun} → jadwal satu bulan
 *
 * Semua respons di-cache (daftar wilayah 7 hari, jadwal bulanan sampai akhir bulan)
 * sehingga hanya beberapa request per bulan.
 */
class KemenagShalat
{
    public const BASE = 'https://equran.id/api/v2/shalat';

    /** Kolom waktu yang dikembalikan API, berurutan. */
    public const KOLOM = ['imsak', 'subuh', 'terbit', 'dhuha', 'dzuhur', 'ashar', 'maghrib', 'isya'];

    /** @return array<int, string> */
    public function provinsi(): array
    {
        return Cache::remember('kemenag:provinsi', now()->addDays(7), function (): array {
            try {
                $r = $this->http()->get(self::BASE . '/provinsi');

                return $r->ok() ? array_values((array) $r->json('data', [])) : [];
            } catch (Throwable $e) {
                Log::warning('Kemenag: gagal ambil provinsi: ' . $e->getMessage());

                return [];
            }
        });
    }

    /** @return array<int, string> */
    public function kabkota(string $provinsi): array
    {
        $provinsi = trim($provinsi);
        if ($provinsi === '') {
            return [];
        }

        return Cache::remember('kemenag:kabkota:' . md5($provinsi), now()->addDays(7), function () use ($provinsi): array {
            try {
                $r = $this->http()->post(self::BASE . '/kabkota', ['provinsi' => $provinsi]);

                return $r->ok() ? array_values((array) $r->json('data', [])) : [];
            } catch (Throwable $e) {
                Log::warning('Kemenag: gagal ambil kab/kota: ' . $e->getMessage());

                return [];
            }
        });
    }

    /**
     * Jadwal satu bulan, dikunci tanggal (Y-m-d).
     *
     * @return array<string, array{hari:string,imsak:string,subuh:string,terbit:string,dhuha:string,dzuhur:string,ashar:string,maghrib:string,isya:string}>
     */
    public function bulanan(string $provinsi, string $kabkota, int $tahun, int $bulan): array
    {
        $provinsi = trim($provinsi);
        $kabkota = trim($kabkota);
        if ($provinsi === '' || $kabkota === '') {
            return [];
        }

        $key = sprintf('kemenag:jadwal:%s:%04d-%02d', md5(strtolower($provinsi . '|' . $kabkota)), $tahun, $bulan);
        $cache = Cache::get($key);
        if (is_array($cache)) {
            return $cache;
        }

        $hasil = [];
        try {
            $r = $this->http()->post(self::BASE, [
                'provinsi' => $provinsi,
                'kabkota' => $kabkota,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

            if ($r->ok()) {
                foreach ((array) $r->json('data.jadwal', []) as $baris) {
                    $tgl = $baris['tanggal_lengkap'] ?? null;
                    if (! $tgl) {
                        continue;
                    }
                    $item = ['hari' => (string) ($baris['hari'] ?? '')];
                    foreach (self::KOLOM as $k) {
                        $jam = $baris[$k] ?? null;
                        $item[$k] = (is_string($jam) && preg_match('/^\d{2}:\d{2}/', $jam, $m)) ? $m[0] : null;
                    }
                    $hasil[$tgl] = $item;
                }
            }
        } catch (Throwable $e) {
            Log::warning('Kemenag: gagal ambil jadwal shalat: ' . $e->getMessage());
        }

        // Sukses → simpan sampai akhir bulan (+1 hari); gagal → coba lagi 10 menit kemudian.
        $ttl = $hasil === [] ? now()->addMinutes(10) : now()->endOfMonth()->addDay();
        Cache::put($key, $hasil, $ttl);

        return $hasil;
    }

    /** @return array<string, ?string>|null  kolom KOLOM untuk satu tanggal, null bila tidak tersedia */
    public function harian(string $provinsi, string $kabkota, CarbonInterface $tanggal): ?array
    {
        $bulan = $this->bulanan($provinsi, $kabkota, (int) $tanggal->year, (int) $tanggal->month);

        return $bulan[$tanggal->toDateString()] ?? null;
    }

    protected function http(): PendingRequest
    {
        return Http::timeout(8)
            ->retry(1, 300)
            ->acceptJson()
            ->withOptions(['verify' => (bool) config('services.jadwal_shalat.verify_ssl', true)]);
    }
}
