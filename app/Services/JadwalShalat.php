<?php

namespace App\Services;

use App\Models\JadwalPetugas;
use App\Models\Masjid;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Waktu shalat harian untuk satu tanggal.
 *
 * Prioritas sumber:
 *  1. Jam yang diinput pengurus pada jadwal petugas (jenis ibadah harian).
 *  2. API Aladhan (metode 20 = Kemenag RI) berdasarkan kota di profil masjid,
 *     di-cache per hari sehingga hanya satu request per tanggal.
 *  3. Kalau keduanya tidak tersedia → null ("--:--" di tampilan).
 */
class JadwalShalat
{
    /** Urutan & padanan nama waktu shalat dengan key API Aladhan. */
    public const WAKTU = [
        'Subuh' => 'Fajr',
        'Dzuhur' => 'Dhuhr',
        'Ashar' => 'Asr',
        'Maghrib' => 'Maghrib',
        'Isya' => 'Isha',
    ];

    /**
     * @return array<string, ?string>  contoh: ['Subuh' => '04:42', ...]
     */
    public function untuk(CarbonInterface $tanggal, ?Masjid $masjid = null): array
    {
        $hasil = array_fill_keys(array_keys(self::WAKTU), null);

        $dariApi = $this->dariApi($tanggal, $masjid);
        foreach ($hasil as $nama => $_) {
            $hasil[$nama] = $dariApi[$nama] ?? null;
        }

        // Jam dari jadwal petugas (jika diisi) selalu menang atas API.
        foreach ($this->dariJadwalPetugas($tanggal) as $nama => $jam) {
            if ($jam) {
                $hasil[$nama] = $jam;
            }
        }

        return $hasil;
    }

    /** Nama waktu shalat yang "sedang berlangsung" (terakhir yang sudah masuk). */
    public function sedangBerlangsung(array $jadwal, CarbonInterface $sekarang): ?string
    {
        $jam = $sekarang->format('H:i');
        $aktif = null;

        foreach ($jadwal as $nama => $waktu) {
            if ($waktu && $waktu <= $jam) {
                $aktif = $nama;
            }
        }

        return $aktif;
    }

    /** Nama waktu shalat berikutnya setelah sekarang. */
    public function berikutnya(array $jadwal, CarbonInterface $sekarang): ?string
    {
        $jam = $sekarang->format('H:i');

        foreach ($jadwal as $nama => $waktu) {
            if ($waktu && $waktu > $jam) {
                return $nama;
            }
        }

        return null;
    }

    /** @return array<string, ?string> */
    protected function dariJadwalPetugas(CarbonInterface $tanggal): array
    {
        $hasil = [];

        $jadwal = JadwalPetugas::query()
            ->with('jenisIbadah')
            ->whereDate('tanggal_jadwal', $tanggal->toDateString())
            ->whereNotNull('waktu_mulai')
            ->whereHas('jenisIbadah', fn ($q) => $q->where('kategori', 'harian'))
            ->get();

        foreach ($jadwal as $j) {
            $nama = self::namaWaktuDariJenis($j->jenisIbadah?->nama_jenis);
            if ($nama) {
                $hasil[$nama] = substr((string) $j->waktu_mulai, 0, 5);
            }
        }

        return $hasil;
    }

    /** "Shalat Subuh" → "Subuh", "Dzuhur"/"Zuhur" → "Dzuhur", dst. */
    public static function namaWaktuDariJenis(?string $namaJenis): ?string
    {
        if (! $namaJenis) {
            return null;
        }

        $cari = strtolower($namaJenis);
        $alias = [
            'Subuh' => ['subuh', 'fajr', 'shubuh'],
            'Dzuhur' => ['dzuhur', 'zuhur', 'dhuhr', 'duhur'],
            'Ashar' => ['ashar', 'asar', 'asr'],
            'Maghrib' => ['maghrib', 'magrib'],
            'Isya' => ['isya', 'isha'],
        ];

        foreach ($alias as $nama => $kandidat) {
            foreach ($kandidat as $k) {
                if (str_contains($cari, $k)) {
                    return $nama;
                }
            }
        }

        return null;
    }

    /** @return array<string, string> */
    protected function dariApi(CarbonInterface $tanggal, ?Masjid $masjid): array
    {
        $kota = trim((string) ($masjid?->kota ?? ''));
        if ($kota === '') {
            return [];
        }

        $key = 'jadwal-shalat:' . md5(strtolower($kota)) . ':' . $tanggal->toDateString();

        $cache = Cache::get($key);
        if (is_array($cache)) {
            return $cache;
        }

        try {
            $respon = Http::timeout(6)
                ->retry(1, 300)
                // Di lingkungan lokal (MAMP) sertifikat CA cURL sering belum terpasang;
                // set JADWAL_SHALAT_VERIFY_SSL=false di .env lokal bila perlu.
                ->withOptions(['verify' => (bool) config('services.jadwal_shalat.verify_ssl', true)])
                ->get('https://api.aladhan.com/v1/timingsByCity/' . $tanggal->format('d-m-Y'), [
                    'city' => $kota,
                    'country' => 'Indonesia',
                    'method' => 20, // Kemenag RI
                ]);

            $hasil = [];
            if ($respon->ok()) {
                $timings = $respon->json('data.timings', []);
                foreach (self::WAKTU as $nama => $keyApi) {
                    $jam = $timings[$keyApi] ?? null;
                    if (is_string($jam) && preg_match('/^\d{2}:\d{2}/', $jam, $m)) {
                        $hasil[$nama] = $m[0];
                    }
                }
            }
        } catch (Throwable $e) {
            Log::warning('Gagal mengambil jadwal shalat dari API: ' . $e->getMessage());
            $hasil = [];
        }

        // Berhasil → simpan sehari; gagal → coba lagi 10 menit kemudian.
        Cache::put($key, $hasil, $hasil === [] ? now()->addMinutes(10) : now()->addDay());

        return $hasil;
    }
}
