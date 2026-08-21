<?php

namespace App\Filament\Pages;

use App\Filament\Resources\JadwalPetugasResource;
use App\Filament\Resources\KegiatanResource;
use App\Filament\Resources\PengumumanResource;
use App\Filament\Resources\TransaksiKeuanganResource;
use App\Models\JadwalPetugas;
use App\Models\Masjid;
use App\Models\Pengumuman;
use App\Models\RekeningBank;
use App\Models\TransaksiKeuangan;
use App\Services\JadwalShalat;
use App\Support\EntitasAktif;
use App\Support\Tanggal;
use Carbon\CarbonInterface;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Str;

/**
 * Dashboard pengurus — tata letak mengikuti mockup:
 * aksi cepat, 4 kartu ringkasan keuangan, grafik arus kas, jadwal imam
 * (hari ini / besok), kartu Jumat berikutnya, dan pengumuman terbaru.
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    /** Tab jadwal imam: hari_ini | besok */
    public string $tabJadwal = 'hari_ini';

    public function getHeading(): string
    {
        $entitas = EntitasAktif::entitas();

        return 'Dashboard' . ($entitas ? ' — ' . $entitas->label : '');
    }

    public function getSubheading(): ?string
    {
        $entitas = EntitasAktif::entitas();

        return $entitas
            ? 'Ringkasan keuangan & kegiatan ' . $entitas->nama . '.'
            : 'Ringkasan gabungan seluruh entitas (Yayasan & Masjid). Pilih entitas di bagian atas untuk melihat masing-masing.';
    }

    public function setTabJadwal(string $tab): void
    {
        $this->tabJadwal = in_array($tab, ['hari_ini', 'besok'], true) ? $tab : 'hari_ini';
    }

    protected function getViewData(): array
    {
        $masjid = Masjid::first();

        return [
            'aksiCepat' => $this->aksiCepat(),
            'ringkasan' => $this->ringkasanKeuangan(),
            'jadwalImam' => $this->jadwalImam($masjid),
            'tanggalJadwal' => $this->tabJadwal === 'besok' ? today()->addDay() : today(),
            'jumat' => $this->jumatBerikutnya(),
            'pengumuman' => $this->pengumumanTerbaru(),
            'urlPengumuman' => PengumumanResource::canViewAny() ? PengumumanResource::getUrl() : null,
        ];
    }

    /** @return array<int, array{label:string,url:string,icon:string,utama:bool}> */
    protected function aksiCepat(): array
    {
        $aksi = [];

        if (TransaksiKeuanganResource::canCreate()) {
            $aksi[] = [
                'label' => 'Transaksi Baru',
                'url' => TransaksiKeuanganResource::getUrl('create'),
                'icon' => 'heroicon-o-plus',
                'warna' => 'text-white',
                'utama' => true,
            ];
        }

        if (PengumumanResource::canCreate()) {
            $aksi[] = [
                'label' => 'Buat Pengumuman',
                'url' => PengumumanResource::getUrl('create'),
                'icon' => 'heroicon-o-megaphone',
                'warna' => 'text-amber-500',
                'utama' => false,
            ];
        }

        if (KegiatanResource::canCreate()) {
            $aksi[] = [
                'label' => 'Jadwal Kegiatan',
                'url' => KegiatanResource::getUrl('create'),
                'icon' => 'heroicon-o-calendar-days',
                'warna' => 'text-blue-500',
                'utama' => false,
            ];
        }

        if (JadwalPetugasResource::canCreate()) {
            $aksi[] = [
                'label' => 'Jadwal Petugas',
                'url' => JadwalPetugasResource::getUrl('create'),
                'icon' => 'heroicon-o-clock',
                'warna' => 'text-emerald-600',
                'utama' => false,
            ];
        }

        return $aksi;
    }

    protected function ringkasanKeuangan(): array
    {
        $entitasId = EntitasAktif::id();

        $saldoMetode = fn (bool $tunai): float => (float) TransaksiKeuangan::disetujui()
            ->entitas($entitasId)
            ->when($tunai, fn ($q) => $q->where('metode_pembayaran', 'kas'), fn ($q) => $q->where('metode_pembayaran', '!=', 'kas'))
            ->selectRaw("COALESCE(SUM(CASE WHEN jenis_transaksi = 'pemasukan' THEN nominal ELSE -nominal END), 0) as saldo")
            ->value('saldo');

        $totalBulan = fn (string $jenis, CarbonInterface $bulan): float => (float) TransaksiKeuangan::disetujui()
            ->entitas($entitasId)
            ->where('jenis_transaksi', $jenis)
            ->whereBetween('tanggal_transaksi', [
                $bulan->copy()->startOfMonth()->toDateString(),
                $bulan->copy()->endOfMonth()->toDateString(),
            ])
            ->sum('nominal');

        $persen = function (float $ini, float $lalu): ?int {
            if ($lalu <= 0) {
                return null;
            }

            return (int) round(($ini - $lalu) / $lalu * 100);
        };

        $bulanIni = now();
        $bulanLalu = now()->subMonthNoOverflow();

        $masukIni = $totalBulan('pemasukan', $bulanIni);
        $masukLalu = $totalBulan('pemasukan', $bulanLalu);
        $keluarIni = $totalBulan('pengeluaran', $bulanIni);
        $keluarLalu = $totalBulan('pengeluaran', $bulanLalu);

        $rekening = EntitasAktif::terapkan(RekeningBank::query())->where('aktif', true)->orderBy('id')->get();
        $labelRekening = $rekening->isEmpty()
            ? 'Belum ada rekening aktif'
            : $rekening->map(fn (RekeningBank $r) => $r->nama_bank . ': ' . self::samarkanNomor($r->nomor_rekening))->implode(' · ');

        $tanggalUpdate = TransaksiKeuangan::disetujui()->entitas($entitasId)->max('tanggal_transaksi');

        return [
            'kas_tunai' => $saldoMetode(true),
            'kas_tunai_ket' => $tanggalUpdate
                ? 'Update ' . Tanggal::pendek(\Carbon\Carbon::parse($tanggalUpdate))
                : 'Belum ada transaksi disetujui',
            'rekening' => $saldoMetode(false),
            'rekening_ket' => $labelRekening,
            'masuk' => $masukIni,
            'masuk_persen' => $persen($masukIni, $masukLalu),
            'keluar' => $keluarIni,
            'keluar_persen' => $persen($keluarIni, $keluarLalu),
        ];
    }

    public static function samarkanNomor(?string $nomor): string
    {
        $nomor = preg_replace('/\s+/', '', (string) $nomor);
        $panjang = strlen($nomor);

        if ($panjang <= 6) {
            return $nomor;
        }

        return substr($nomor, 0, 4) . str_repeat('*', max(3, $panjang - 7)) . substr($nomor, -3);
    }

    /** @return array<int, array<string, mixed>> */
    protected function jadwalImam(?Masjid $masjid): array
    {
        $tanggal = $this->tabJadwal === 'besok' ? today()->addDay() : today();
        $layanan = app(JadwalShalat::class);
        $waktu = $layanan->untuk($tanggal, $masjid);

        $jadwal = JadwalPetugas::query()
            ->with(['jenisIbadah', 'detail.petugas'])
            ->whereDate('tanggal_jadwal', $tanggal->toDateString())
            ->whereHas('jenisIbadah', fn ($q) => $q->where('kategori', 'harian'))
            ->get()
            ->keyBy(fn (JadwalPetugas $j) => JadwalShalat::namaWaktuDariJenis($j->jenisIbadah?->nama_jenis) ?? $j->id);

        $berikutnya = $this->tabJadwal === 'hari_ini' ? $layanan->berikutnya($waktu, now()) : null;
        $jamSekarang = now()->format('H:i');
        $bolehLihat = JadwalPetugasResource::canViewAny();

        $baris = [];
        foreach (array_keys(JadwalShalat::WAKTU) as $nama) {
            /** @var ?JadwalPetugas $j */
            $j = $jadwal->get($nama);
            $jam = $waktu[$nama] ?? null;

            if ($this->tabJadwal === 'besok') {
                $status = 'Terjadwal';
            } elseif ($jam && $jam <= $jamSekarang && $nama !== $berikutnya) {
                $status = 'Selesai';
            } elseif ($nama === $berikutnya) {
                $status = 'Berikutnya';
            } else {
                $status = 'Menunggu';
            }

            $baris[] = [
                'nama' => $nama,
                'jam' => $jam ?: '--:--',
                'imam' => $j?->detail->firstWhere('peran', 'imam')?->petugas?->nama,
                'muadzin' => $j?->detail->firstWhere('peran', 'muadzin')?->petugas?->nama,
                'status' => $status,
                'url' => ($bolehLihat && $j) ? JadwalPetugasResource::getUrl('edit', ['record' => $j]) : null,
            ];
        }

        return $baris;
    }

    protected function jumatBerikutnya(): ?array
    {
        $j = JadwalPetugas::query()
            ->with(['detail.petugas'])
            ->whereHas('jenisIbadah', fn ($q) => $q->where('kategori', 'jumat'))
            ->whereDate('tanggal_jadwal', '>=', today())
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_jadwal')
            ->first();

        if (! $j) {
            return null;
        }

        $nama = fn (string $peran): ?string => $j->detail->firstWhere('peran', $peran)?->petugas?->nama;

        return [
            'tanggal' => Tanggal::panjang($j->tanggal_jadwal),
            'hari' => Tanggal::hari($j->tanggal_jadwal),
            'khotib' => $nama('khotib'),
            'imam' => $nama('imam'),
            'muadzin' => $nama('muadzin'),
            'waktu' => $j->waktu_mulai ? substr((string) $j->waktu_mulai, 0, 5) . ' WIB' : null,
            'url' => JadwalPetugasResource::canViewAny() ? JadwalPetugasResource::getUrl('edit', ['record' => $j]) : null,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    protected function pengumumanTerbaru(): array
    {
        $warna = [
            'pengumuman' => 'bg-rose-500',
            'kegiatan' => 'bg-blue-500',
            'kajian' => 'bg-emerald-500',
            'donasi' => 'bg-amber-500',
            'informasi' => 'bg-slate-400',
            'banner' => 'bg-violet-500',
        ];

        $boleh = PengumumanResource::canViewAny();

        return EntitasAktif::terapkan(Pengumuman::query())
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (Pengumuman $p) => [
                'judul' => $p->judul,
                'tanggal' => Tanggal::pendek($p->created_at),
                'ringkas' => Str::limit(trim(strip_tags((string) $p->konten)), 110),
                'warna' => $warna[$p->jenis] ?? 'bg-slate-400',
                'publish' => $p->status_publish,
                'url' => $boleh ? PengumumanResource::getUrl('edit', ['record' => $p]) : null,
            ])
            ->all();
    }
}
