<?php

namespace Database\Seeders;

use App\Models\Entitas;
use App\Models\Jabatan;
use App\Models\JadwalPetugas;
use App\Models\JadwalPetugasDetail;
use App\Models\JenisIbadah;
use App\Models\KategoriKegiatan;
use App\Models\KategoriTransaksi;
use App\Models\Kegiatan;
use App\Models\Kepengurusan;
use App\Models\Masjid;
use App\Models\Pengumuman;
use App\Models\Pengurus;
use App\Models\Petugas;
use App\Models\PicKegiatan;
use App\Models\RekeningBank;
use App\Models\StrukturOrganisasi;
use App\Models\SumberDana;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Data CONTOH untuk presentasi/uji tampilan (meniru isi mockup DKM).
 * TIDAK dijalankan otomatis. Jalankan manual bila perlu:
 *
 *   php artisan db:seed --class=DemoDataSeeder
 *
 * Untuk membersihkan kembali: php artisan migrate:fresh --seed
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@baitulmukminin.test')->first();

        // Profil masjid (kota dipakai untuk jadwal shalat otomatis)
        $masjid = Masjid::first() ?? Masjid::create(['nama_masjid' => 'Masjid Baitul Mukminin']);
        $masjid->update([
            'alamat' => $masjid->alamat ?: 'Jl. Raya Masjid No. 1',
            'kota' => $masjid->kota ?: 'Jakarta',
            'provinsi' => $masjid->provinsi ?: 'DKI Jakarta',
            'kontak' => $masjid->kontak ?: '0812-0000-0000',
            'deskripsi' => 'Selamat datang di portal informasi jamaah. Dapatkan update jadwal shalat, kegiatan kajian, dan transparansi laporan keuangan masjid secara real-time.',
        ]);

        // Entitas (dibuat oleh migration/MasterDataSeeder)
        $entMasjid = Entitas::masjid();
        $entYayasan = Entitas::yayasan();
        $idMasjid = $entMasjid?->id;

        // Struktur & pengurus DKM
        $struktur = StrukturOrganisasi::firstOrCreate(
            ['masjid_id' => $masjid->id, 'nama_struktur' => 'DKM Periode 2024–2027'],
            ['entitas_id' => $idMasjid, 'periode_mulai' => '2024-01-01', 'periode_selesai' => '2027-12-31']
        );
        if (! $struktur->entitas_id && $idMasjid) {
            $struktur->update(['entitas_id' => $idMasjid]);
        }

        // Struktur & pengurus Yayasan (terpisah dari DKM)
        if ($entYayasan) {
            $strukturYayasan = StrukturOrganisasi::firstOrCreate(
                ['masjid_id' => $masjid->id, 'nama_struktur' => 'Pengurus Yayasan 2024–2029'],
                ['entitas_id' => $entYayasan->id, 'periode_mulai' => '2024-01-01', 'periode_selesai' => '2029-12-31']
            );
            foreach ([
                ['H. Muhammad Yusuf', 'Ketua Pembina', '0811-1000-0001'],
                ['Hj. Siti Aminah', 'Ketua Pengawas', '0811-1000-0002'],
                ['Drs. H. Abdullah Rahman', 'Ketua Yayasan', '0811-1000-0003'],
                ['Ahmad Zaki, S.H.', 'Sekretaris Yayasan', '0811-1000-0004'],
                ['Rina Wulandari, S.E.', 'Bendahara Yayasan', '0811-1000-0005'],
            ] as [$nama, $jabatan, $telp]) {
                $p = Pengurus::firstOrCreate(['nama' => $nama], ['telepon' => $telp, 'status_aktif' => true]);
                $j = Jabatan::firstOrCreate(['nama_jabatan' => $jabatan], ['kelompok' => 'yayasan', 'urutan' => 9]);
                Kepengurusan::firstOrCreate(
                    ['pengurus_id' => $p->id, 'jabatan_id' => $j->id, 'struktur_id' => $strukturYayasan->id],
                    ['periode_mulai' => '2024-01-01', 'periode_selesai' => '2029-12-31', 'status_aktif' => true]
                );
            }

            // Kas Yayasan: rekening & transaksi sendiri
            $rekYayasan = RekeningBank::firstOrCreate(
                ['nomor_rekening' => '1234567890'],
                ['entitas_id' => $entYayasan->id, 'nama_bank' => 'Bank Muamalat', 'nama_pemilik' => $entYayasan->nama, 'aktif' => true]
            );
            $katY = fn (string $nama) => KategoriTransaksi::where('nama_kategori', $nama)->value('id');
            foreach ([
                [4, 10, 'pemasukan', 'Wakaf', 250_000_000, 'transfer', 'Wakaf tunai yayasan (tanah & bangunan)'],
                [3, 5, 'pengeluaran', 'Pemeliharaan Bangunan', 60_000_000, 'bank', 'Renovasi gedung TPA'],
                [2, 12, 'pemasukan', 'Donasi Kegiatan', 35_000_000, 'transfer', 'Donatur tetap yayasan'],
                [1, 8, 'pengeluaran', 'Operasional Masjid', 7_500_000, 'bank', 'Operasional yayasan & honor guru TPA'],
                [0, 4, 'pemasukan', 'Donasi Kegiatan', 20_000_000, 'transfer', 'Donasi program beasiswa yatim'],
                [0, 9, 'pengeluaran', 'Kegiatan Sosial', 12_000_000, 'bank', 'Beasiswa yatim semester ganjil'],
            ] as [$mundur, $tgl, $jenis, $kategori, $nominal, $metode, $ket]) {
                $tanggal = now()->startOfMonth()->subMonthsNoOverflow($mundur)->day(min($tgl, 28));
                if ($tanggal->isFuture()) {
                    $tanggal = today();
                }
                TransaksiKeuangan::firstOrCreate(
                    ['tanggal_transaksi' => $tanggal->toDateString(), 'keterangan' => $ket, 'nominal' => $nominal],
                    [
                        'entitas_id' => $entYayasan->id,
                        'jenis_transaksi' => $jenis,
                        'kategori_transaksi_id' => $katY($kategori) ?? KategoriTransaksi::where('tipe', $jenis)->value('id'),
                        'rekening_bank_id' => $rekYayasan->id,
                        'metode_pembayaran' => $metode,
                        'status_approval' => 'disetujui',
                        'approved_by' => $admin?->id,
                        'input_by' => $admin?->id,
                    ]
                );
            }

            Pengumuman::firstOrCreate(
                ['slug' => 'program-beasiswa-yatim-yayasan'],
                [
                    'entitas_id' => $entYayasan->id,
                    'judul' => 'Program Beasiswa Yatim Yayasan',
                    'jenis' => 'donasi',
                    'target' => 'publik',
                    'status_publish' => true,
                    'konten' => '<p>Yayasan membuka pendaftaran beasiswa untuk anak yatim tingkat SD–SMA. Pendaftaran melalui sekretariat yayasan.</p>',
                    'tanggal_mulai' => today(),
                    'created_by' => $admin?->id,
                ]
            );
        }

        $pengurus = [
            ['Dr. H. Ahmad Fauzi, MA', 'Ketua DKM', '0812-3456-7890', true],
            ['Budi Santoso, S.E.', 'Bendahara', '0812-3456-7891', true],
            ['Ust. Ali Nurdin', 'Sekretaris', '0812-3456-7892', true],
            ['Hasan Basri', 'Koordinator Bidang Ibadah', '0812-3456-7893', true],
            ['Deni Setiawan', 'Koordinator Bidang Sosial', '0812-3456-7894', true],
            ['Ir. Rahman', 'Koordinator Bidang Pemuda & Remaja', '0812-3456-7895', false],
        ];
        foreach ($pengurus as [$nama, $jabatan, $telp, $aktif]) {
            $p = Pengurus::firstOrCreate(['nama' => $nama], ['telepon' => $telp, 'status_aktif' => $aktif, 'jenis_kelamin' => 'L']);
            $j = Jabatan::firstOrCreate(['nama_jabatan' => $jabatan], ['urutan' => 9]);
            Kepengurusan::firstOrCreate(
                ['pengurus_id' => $p->id, 'jabatan_id' => $j->id, 'struktur_id' => $struktur->id],
                ['periode_mulai' => '2024-01-01', 'periode_selesai' => '2027-12-31', 'status_aktif' => $aktif]
            );
        }

        // Petugas ibadah
        $petugas = [
            'Ust. Ahmad' => ['imam', 'khotib'],
            'Ust. Hasan' => ['imam'],
            'Ust. Ali' => ['imam', 'penceramah'],
            'Dr. H. Syamsuddin, MA' => ['khotib', 'imam', 'penceramah'],
            'Budi Santoso' => ['muadzin', 'bilal'],
            'Deni' => ['muadzin'],
        ];
        $idPetugas = [];
        foreach ($petugas as $nama => $peran) {
            $idPetugas[$nama] = Petugas::firstOrCreate(['nama' => $nama], ['peran' => $peran, 'status_aktif' => true])->id;
        }

        // Jadwal harian hari ini & besok (jam dikosongkan → dashboard memakai jadwal shalat otomatis)
        $harian = JenisIbadah::where('kategori', 'harian')->orderBy('urutan')->get();
        $imamRotasi = ['Ust. Ahmad', 'Ust. Ali', 'Ust. Hasan', 'Ust. Ahmad', 'Ust. Ali'];
        $muadzinRotasi = ['Budi Santoso', 'Deni', 'Budi Santoso', 'Deni', 'Budi Santoso'];
        foreach ([today(), today()->addDay()] as $hari) {
            foreach ($harian->values() as $i => $jenis) {
                $jadwal = JadwalPetugas::firstOrCreate(
                    ['jenis_ibadah_id' => $jenis->id, 'tanggal_jadwal' => $hari->toDateString()],
                    ['lokasi' => 'Ruang Utama', 'status' => 'terjadwal']
                );
                $this->tugaskan($jadwal, 'imam', $idPetugas[$imamRotasi[$i % 5]]);
                $this->tugaskan($jadwal, 'muadzin', $idPetugas[$muadzinRotasi[$i % 5]]);
            }
        }

        // Shalat Jumat berikutnya
        $jumat = JenisIbadah::where('kategori', 'jumat')->first();
        if ($jumat) {
            $tglJumat = today()->isFriday() ? today() : today()->next('Friday');
            $jadwalJumat = JadwalPetugas::firstOrCreate(
                ['jenis_ibadah_id' => $jumat->id, 'tanggal_jadwal' => $tglJumat->toDateString()],
                ['waktu_mulai' => '11:58', 'lokasi' => 'Ruang Utama', 'status' => 'terjadwal']
            );
            $this->tugaskan($jadwalJumat, 'khotib', $idPetugas['Dr. H. Syamsuddin, MA']);
            $this->tugaskan($jadwalJumat, 'imam', $idPetugas['Dr. H. Syamsuddin, MA']);
            $this->tugaskan($jadwalJumat, 'muadzin', $idPetugas['Budi Santoso']);
        }

        // Keuangan
        $bsi = RekeningBank::firstOrCreate(['nomor_rekening' => '7123456456'], ['entitas_id' => $idMasjid, 'nama_bank' => 'BSI', 'nama_pemilik' => 'DKM Baitul Mukminin', 'aktif' => true]);
        $kat = fn (string $nama) => KategoriTransaksi::where('nama_kategori', $nama)->value('id');
        $sumber = fn (string $nama) => SumberDana::where('nama_sumber', $nama)->value('id');

        $transaksi = [
            // [bulan mundur, tanggal, jenis, kategori, nominal, metode, keterangan, sumber]
            [5, 5, 'pemasukan', 'Infaq & Sedekah', 12_000_000, 'kas', 'Infaq Jumat & kotak amal', 'Kotak Amal'],
            [5, 12, 'pengeluaran', 'Listrik', 1_100_000, 'bank', 'Bayar listrik', null],
            [5, 20, 'pengeluaran', 'Honor Imam/Khotib/Ustadz', 3_000_000, 'kas', 'Honor imam & khotib', null],
            [4, 4, 'pemasukan', 'Infaq & Sedekah', 15_000_000, 'kas', 'Infaq Jumat & kotak amal', 'Kotak Amal'],
            [4, 15, 'pengeluaran', 'Operasional Masjid', 2_500_000, 'kas', 'Operasional bulanan', null],
            [4, 22, 'pengeluaran', 'Kebersihan', 1_500_000, 'kas', 'Honor petugas kebersihan', null],
            [3, 3, 'pemasukan', 'Donasi Kegiatan', 18_000_000, 'transfer', 'Donasi jamaah (transfer BSI)', 'Donasi'],
            [3, 10, 'pengeluaran', 'Kegiatan Sosial', 8_000_000, 'bank', 'Santunan anak yatim', null],
            [3, 25, 'pengeluaran', 'Listrik', 1_200_000, 'bank', 'Bayar listrik', null],
            [2, 6, 'pemasukan', 'Infaq & Sedekah', 14_000_000, 'kas', 'Infaq Jumat & kotak amal', 'Kotak Amal'],
            [2, 18, 'pengeluaran', 'Pemeliharaan Bangunan', 7_000_000, 'bank', 'Perbaikan atap & cat', null],
            [2, 28, 'pengeluaran', 'Operasional Masjid', 2_000_000, 'kas', 'Operasional bulanan', null],
            [1, 2, 'pemasukan', 'Infaq & Sedekah', 16_000_000, 'kas', 'Infaq Jumat & kotak amal', 'Kotak Amal'],
            [1, 14, 'pengeluaran', 'Honor Imam/Khotib/Ustadz', 3_000_000, 'kas', 'Honor imam & khotib', null],
            [1, 21, 'pengeluaran', 'Kegiatan Keagamaan', 5_500_000, 'bank', 'Kajian akbar', null],
            [0, 1, 'pemasukan', 'Infaq & Sedekah', 4_500_000, 'kas', 'Infaq Jumat', 'Kotak Amal'],
            [0, 3, 'pemasukan', 'Donasi Kegiatan', 10_000_000, 'transfer', 'Donasi Hamba Allah', 'Donasi'],
            [0, 5, 'pengeluaran', 'Listrik', 1_200_000, 'bank', 'Bayar listrik bulan lalu', null],
            [0, 8, 'pengeluaran', 'Honor Imam/Khotib/Ustadz', 500_000, 'kas', 'Honor pemateri kajian', null],
            [0, 10, 'pemasukan', 'Infaq & Sedekah', 1_250_000, 'kas', 'Infaq kajian Ahad', 'Infaq'],
            [0, 12, 'pengeluaran', 'Pemeliharaan Bangunan', 350_000, 'kas', 'Perbaikan keran wudhu', null],
            [0, 15, 'pemasukan', 'Wakaf', 100_000_000, 'transfer', 'Wakaf tunai pembangunan', 'Wakaf'],
        ];

        foreach ($transaksi as [$mundur, $tgl, $jenis, $kategori, $nominal, $metode, $ket, $sd]) {
            $tanggal = now()->startOfMonth()->subMonthsNoOverflow($mundur)->day(min($tgl, 28));
            if ($tanggal->isFuture()) {
                $tanggal = today();
            }
            TransaksiKeuangan::firstOrCreate(
                ['tanggal_transaksi' => $tanggal->toDateString(), 'keterangan' => $ket, 'nominal' => $nominal],
                [
                    'entitas_id' => $idMasjid,
                    'jenis_transaksi' => $jenis,
                    'kategori_transaksi_id' => $kat($kategori) ?? KategoriTransaksi::where('tipe', $jenis)->value('id'),
                    'sumber_dana_id' => $sd ? $sumber($sd) : null,
                    'rekening_bank_id' => $metode === 'kas' ? null : $bsi->id,
                    'metode_pembayaran' => $metode,
                    'status_approval' => 'disetujui',
                    'approved_by' => $admin?->id,
                    'input_by' => $admin?->id,
                ]
            );
        }

        // Satu transaksi menunggu approval
        TransaksiKeuangan::firstOrCreate(
            ['tanggal_transaksi' => today()->toDateString(), 'keterangan' => 'Pembelian karpet saf depan', 'nominal' => 4_750_000],
            [
                'entitas_id' => $idMasjid,
                'jenis_transaksi' => 'pengeluaran',
                'kategori_transaksi_id' => $kat('Pembelian Perlengkapan'),
                'metode_pembayaran' => 'kas',
                'status_approval' => 'menunggu',
                'input_by' => $admin?->id,
            ]
        );

        // Kegiatan
        $katKeg = fn (string $nama) => KategoriKegiatan::firstOrCreate(['nama_kategori' => $nama])->id;
        $kegiatan = [
            ['Kajian Tafsir Al-Quran', 'Kajian', today()->next('Sunday')->setTime(5, 30), 'Ruang Utama Masjid', 500_000, 'terjadwal', 'Hasan Basri'],
            ['Santunan Yatim Piatu', 'Santunan', today()->addDays(4)->setTime(16, 0), 'Halaman Depan Masjid', 15_000_000, 'terjadwal', 'Deni Setiawan'],
            ['Rapat Evaluasi Bulanan', 'Kegiatan Pemuda', today()->subDays(2)->setTime(20, 0), 'Ruang Rapat', 200_000, 'selesai', 'Ust. Ali Nurdin'],
            ['Kerja Bakti Kebersihan', 'Bakti Sosial', today()->subDays(7)->setTime(7, 0), 'Area Masjid', 300_000, 'selesai', 'Ir. Rahman'],
            ['Peringatan Maulid Nabi', 'Pengajian', today()->addDays(35)->setTime(19, 30), 'Ruang Utama', 25_000_000, 'draft', 'Dr. H. Ahmad Fauzi, MA'],
        ];
        foreach ($kegiatan as [$nama, $kategori, $mulai, $lokasi, $anggaran, $status, $pic]) {
            $k = Kegiatan::firstOrCreate(
                ['nama_kegiatan' => $nama],
                [
                    'entitas_id' => $idMasjid,
                    'kategori_id' => $katKeg($kategori),
                    'tanggal_mulai' => $mulai,
                    'tanggal_selesai' => $mulai->copy()->addMinutes(90),
                    'lokasi' => $lokasi,
                    'anggaran' => $anggaran,
                    'status' => $status,
                    'created_by' => $admin?->id,
                ]
            );
            if ($p = Pengurus::where('nama', $pic)->first()) {
                PicKegiatan::firstOrCreate(['kegiatan_id' => $k->id, 'pengurus_id' => $p->id], ['peran' => 'Penanggung jawab']);
            }
        }

        // Pengumuman
        $pengumuman = [
            ['Rapat Evaluasi Kegiatan Bulanan', 'pengumuman', 'pengurus', true, 'Diharapkan kehadiran seluruh pengurus inti ba\'da Isya di Ruang Rapat.'],
            ['Kerja Bakti Kebersihan Area Wudhu', 'kegiatan', 'publik', true, 'Agenda pembersihan area wudhu dan pergantian karpet saf depan. Jamaah dipersilakan bergabung.'],
            ['Laporan Keuangan Bulan Lalu', 'informasi', 'publik', true, 'Laporan keuangan bulan lalu telah dipublikasikan di halaman Transparansi Keuangan.'],
            ['Pendaftaran Qurban 1448 H', 'donasi', 'publik', false, 'Pendaftaran peserta qurban dibuka. Hubungi sekretariat masjid untuk informasi lebih lanjut.'],
        ];
        foreach ($pengumuman as [$judul, $jenis, $target, $publish, $konten]) {
            Pengumuman::firstOrCreate(
                ['slug' => Str::slug($judul)],
                [
                    'entitas_id' => $idMasjid,
                    'judul' => $judul,
                    'jenis' => $jenis,
                    'target' => $target,
                    'status_publish' => $publish,
                    'konten' => '<p>' . $konten . '</p>',
                    'tanggal_mulai' => today(),
                    'created_by' => $admin?->id,
                ]
            );
        }
    }

    protected function tugaskan(JadwalPetugas $jadwal, string $peran, int $petugasId): void
    {
        JadwalPetugasDetail::firstOrCreate(
            ['jadwal_id' => $jadwal->id, 'peran' => $peran, 'petugas_id' => $petugasId]
        );
    }
}
