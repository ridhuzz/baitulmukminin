<?php

namespace App\Http\Controllers;

use App\Models\Entitas;
use App\Models\JadwalPetugas;
use App\Models\Kegiatan;
use App\Models\Kepengurusan;
use App\Models\LaporanKeuangan;
use App\Models\Masjid;
use App\Models\Pengumuman;
use App\Models\StrukturOrganisasi;
use App\Models\TransaksiKeuangan;
use App\Services\JadwalShalat;
use Illuminate\View\View;

class PublikController extends Controller
{
    public function __construct(protected JadwalShalat $jadwalShalat)
    {
    }

    public function beranda(): View
    {
        $masjid = Masjid::first();
        $hariIni = today();
        $waktuShalat = $this->jadwalShalat->untuk($hariIni, $masjid);
        $entitasMasjid = Entitas::masjid();

        return view('publik.beranda', [
            'masjid' => $masjid,
            'waktuShalat' => $waktuShalat,
            'shalatAktif' => $this->jadwalShalat->sedangBerlangsung($waktuShalat, now()),
            'shalatBerikutnya' => $this->jadwalShalat->berikutnya($waktuShalat, now()),
            'jadwalJumat' => $this->jadwalJumatBerikutnya(),
            'kegiatan' => Kegiatan::with(['kategori', 'pic.pengurus', 'entitas'])
                ->whereIn('status', ['terjadwal', 'berlangsung'])
                ->whereDate('tanggal_mulai', '>=', $hariIni)
                ->orderBy('tanggal_mulai')
                ->take(4)
                ->get(),
            'pengumuman' => Pengumuman::publik()->with('entitas')->latest()->take(3)->get(),
            // Transparansi di beranda = kas Masjid (DKM); Yayasan tampil di halaman laporan.
            'entitasKeuangan' => $entitasMasjid,
            'keuangan' => $this->ringkasanKeuangan($entitasMasjid),
            'laporan' => LaporanKeuangan::where('status', 'published')
                ->where('visibilitas', 'publik')
                ->orderByDesc('periode_mulai')
                ->take(3)
                ->get(),
        ]);
    }

    public function kegiatan(): View
    {
        return view('publik.kegiatan', [
            'masjid' => Masjid::first(),
            'kegiatan' => Kegiatan::with(['kategori', 'pic.pengurus', 'entitas'])
                ->whereIn('status', ['terjadwal', 'berlangsung'])
                ->whereDate('tanggal_mulai', '>=', today())
                ->orderBy('tanggal_mulai')
                ->paginate(12),
        ]);
    }

    public function pengumuman(string $slug): View
    {
        $item = Pengumuman::publik()->with('entitas')->where('slug', $slug)->firstOrFail();

        return view('publik.pengumuman', [
            'masjid' => Masjid::first(),
            'item' => $item,
            'lainnya' => Pengumuman::publik()->where('id', '!=', $item->id)->latest()->take(4)->get(),
        ]);
    }

    /** Laporan keuangan publik — satu tab per entitas (Masjid / Yayasan). */
    public function laporan(?string $entitas = null): View
    {
        $daftar = Entitas::aktif()->get();
        $aktif = $entitas
            ? $daftar->firstWhere('slug', $entitas)
            : ($daftar->firstWhere('jenis', 'masjid') ?? $daftar->first());

        abort_if($daftar->isNotEmpty() && ! $aktif, 404);

        return view('publik.laporan', [
            'masjid' => Masjid::first(),
            'daftarEntitas' => $daftar,
            'entitasAktif' => $aktif,
            'keuangan' => $this->ringkasanKeuangan($aktif),
            'laporan' => LaporanKeuangan::where('status', 'published')
                ->where('visibilitas', 'publik')
                ->when($aktif, fn ($q) => $q->where('entitas_id', $aktif->id))
                ->orderByDesc('periode_mulai')
                ->paginate(12),
        ]);
    }

    /** Bagan struktur organisasi publik: Yayasan (induk) lalu Masjid/DKM dan unit lain. */
    public function struktur(): View
    {
        $daftar = Entitas::aktif()->get();

        $blok = $daftar->map(function (Entitas $entitas) {
            $struktur = StrukturOrganisasi::where('entitas_id', $entitas->id)
                ->berjalan()
                ->orderByDesc('periode_mulai')
                ->first()
                ?? StrukturOrganisasi::where('entitas_id', $entitas->id)->orderByDesc('periode_mulai')->first();

            $kepengurusan = $struktur
                ? Kepengurusan::with(['pengurus', 'jabatan'])
                    ->where('struktur_id', $struktur->id)
                    ->where('status_aktif', true)
                    ->whereHas('pengurus', fn ($q) => $q->where('status_aktif', true))
                    ->get()
                    ->sortBy(fn (Kepengurusan $k) => sprintf('%04d-%s', $k->jabatan->urutan ?? 999, $k->pengurus->nama ?? ''))
                    ->groupBy(fn (Kepengurusan $k) => $k->jabatan->nama_jabatan ?? 'Anggota')
                : collect();

            return [
                'entitas' => $entitas,
                'struktur' => $struktur,
                'kelompok' => $kepengurusan,
            ];
        });

        return view('publik.struktur', [
            'masjid' => Masjid::first(),
            'blok' => $blok,
        ]);
    }

    protected function ringkasanKeuangan(?Entitas $entitas): array
    {
        return [
            'saldo' => TransaksiKeuangan::saldoTotal($entitas),
            'masuk' => TransaksiKeuangan::totalBulanBerjalan('pemasukan', $entitas),
            'keluar' => TransaksiKeuangan::totalBulanBerjalan('pengeluaran', $entitas),
        ];
    }

    protected function jadwalJumatBerikutnya(): ?JadwalPetugas
    {
        return JadwalPetugas::with(['jenisIbadah', 'detail.petugas'])
            ->whereHas('jenisIbadah', fn ($q) => $q->where('kategori', 'jumat'))
            ->whereDate('tanggal_jadwal', '>=', today())
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_jadwal')
            ->first();
    }
}
