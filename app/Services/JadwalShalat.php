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
 *  1. Jam yang diinput pengurus pada jadwal petugas (jenis ibadah harian) — selalu menang.
 *  2. Kemenag RI via equran.id (butuh Provinsi + Kab/Kota di Profil Masjid) — di-cache per bulan.
 *  3. Cadangan: API Aladhan metode Kemenag (butuh Kota) — di-cache per hari.
 *  4. Tidak tersedia → null ("--:--").
 */
class JadwalShalat
{
    /** Lima waktu shalat fardhu (dipakai kartu beranda & dashboard), key = label tampilan. */
    public const WAKTU = [
        'Subuh' => 'Fajr',
        'Dzuhur' => 'Dhuhr',
        'Ashar' => 'Asr',
        'Maghrib' => 'Maghrib',
        'Isya' => 'Isha',
    ];

    /** Delapan waktu lengkap versi Kemenag, label tampilan => kolom API equran.id. */
    public const LENGKAP = [
        'Imsak' => 'imsak',
        'Subuh' => 'subuh',
        'Terbit' => 'terbit',
        'Dhuha' => 'dhuha',
        'Dzuhur' => 'dzuhur',
        'Ashar' => 'ashar',
        'Maghrib' => 'maghrib',
        'Isya' => 'isya',
    ];

    protected ?string $sumberTerakhir = null;

    public function __construct(protected KemenagShalat $kemenag)
    {
    }

    /**
     * Lima waktu fardhu.
     *
     * @return array<string, ?string>  contoh: ['Subuh' => '04:42', ...]
     */
    public function untuk(CarbonInterface $tanggal, ?Masjid $masjid = null): array
    {
        $lengkap = $this->lengkap($tanggal, $masjid);

        return array_intersect_key($lengkap, self::WAKTU);
    }

    /**
     * Delapan waktu (Imsak s.d. Isya).
     *
     * @return array<string, ?string>
     */
    public function lengkap(CarbonInterface $tanggal, ?Masjid $masjid = null): array
    {
        $hasil = array_fill_keys(array_keys(self::LENGKAP), null);
        $this->sumberTerakhir = null;

        // 2. Kemenag (equran.id)
        $provinsi = trim((string) ($masjid?->provinsi ?? ''));
        $kota = trim((string) ($masjid?->kota ?? ''));
        if ($provinsi !== '' && $kota !== '') {
            $harian = $this->kemenag->harian($provinsi, $kota, $tanggal);
            if ($harian) {
                foreach (self::LENGKAP as $label => $kolom) {
                    $hasil[$label] = $harian[$kolom] ?? null;
                }
                $this->sumberTerakhir = 'Kemenag RI';
            }
        }

        // 3. Cadangan Aladhan (hanya 5 waktu fardhu)
        if ($this->sumberTerakhir === null && $kota !== '') {
            $aladhan = $this->dariAladhan($tanggal, $kota);
            if ($aladhan) {
                foreach ($aladhan as $label => $jam) {
                    $hasil[$label] = $jam;
                }
                $this->sumberTerakhir = 'Aladhan (metode Kemenag)';
            }
        }

        // 1. Jam dari jadwal petugas menang atas API.
        foreach ($this->dariJadwalPetugas($tanggal) as $label => $jam) {
            if ($jam) {
                $hasil[$label] = $jam;
            }
        }

        return $hasil;
    }

    /**
     * Jadwal satu bulan (Kemenag) untuk tabel halaman publik.
     *
     * @return array<string, array<string, ?string>>  [Y-m-d => ['hari'=>..,'imsak'=>..,...]]
     */
    public function bulanan(CarbonInterface $bulan, ?Masjid $masjid = null): array
    {
        $provinsi = trim((string) ($masjid?->provinsi ?? ''));
        $kota = trim((string) ($masjid?->kota ?? ''));
        if ($provinsi === '' || $kota === '') {
            return [];
        }

        return $this->kemenag->bulanan($provinsi, $kota, (int) $bulan->year, (int) $bulan->month);
    }

    /** Sumber data yang dipakai pada pemanggilan lengkap()/untuk() terakhir. */
    public function sumber(): ?string
    {
        return $this->sumberTerakhir;
    }

    /** Nama waktu shalat yang "sedang berlangsung" (terakhir yang sudah masuk, hanya 5 fardhu). */
    public function sedangBerlangsung(array $jadwal, CarbonInterface $sekarang): ?string
    {
        $jam = $sekarang->format('H:i');
        $aktif = null;

        foreach (array_keys(self::WAKTU) as $nama) {
            $waktu = $jadwal[$nama] ?? null;
            if ($waktu && $waktu <= $jam) {
                $aktif = $nama;
            }
        }

        return $aktif;
    }

    /** Nama waktu shalat fardhu berikutnya setelah sekarang. */
    public function berikutnya(array $jadwal, CarbonInterface $sekarang): ?string
    {
        $jam = $sekarang->format('H:i');

        foreach (array_keys(self::WAKTU) as $nama) {
            $waktu = $jadwal[$nama] ?? null;
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
    protected function dariAladhan(CarbonInterface $tanggal, string $kota): array
    {
        $key = 'jadwal-shalat:' . md5(strtolower($kota)) . ':' . $tanggal->toDateString();

        $cache = Cache::get($key);
        if (is_array($cache)) {
            return $cache;
        }

        try {
            $respon = Http::timeout(6)
                ->retry(1, 300)
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
            Log::warning('Gagal mengambil jadwal shalat dari Aladhan: ' . $e->getMessage());
            $hasil = [];
        }

        Cache::put($key, $hasil, $hasil === [] ? now()->addMinutes(10) : now()->addDay());

        return $hasil;
    }
}
