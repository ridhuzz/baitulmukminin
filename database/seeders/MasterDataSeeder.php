<?php

namespace Database\Seeders;

use App\Models\JenisIbadah;
use App\Models\KategoriKegiatan;
use App\Models\KategoriTransaksi;
use App\Models\Masjid;
use App\Models\SumberDana;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        Masjid::firstOrCreate(
            ['nama_masjid' => 'Masjid Baitul Mukminin'],
            ['kota' => '', 'provinsi' => '', 'deskripsi' => 'Sistem Informasi Manajemen Masjid']
        );

        // Modul 3 — jenis ibadah
        $jenisIbadah = [
            ['nama_jenis' => 'Shalat Subuh', 'kategori' => 'harian', 'urutan' => 1],
            ['nama_jenis' => 'Shalat Dzuhur', 'kategori' => 'harian', 'urutan' => 2],
            ['nama_jenis' => 'Shalat Ashar', 'kategori' => 'harian', 'urutan' => 3],
            ['nama_jenis' => 'Shalat Maghrib', 'kategori' => 'harian', 'urutan' => 4],
            ['nama_jenis' => 'Shalat Isya', 'kategori' => 'harian', 'urutan' => 5],
            ['nama_jenis' => 'Shalat Jumat', 'kategori' => 'jumat', 'urutan' => 6],
            ['nama_jenis' => 'Shalat Tarawih', 'kategori' => 'ramadhan', 'urutan' => 7],
            ['nama_jenis' => 'Shalat Witir', 'kategori' => 'ramadhan', 'urutan' => 8],
            ['nama_jenis' => 'Shalat Idul Fitri', 'kategori' => 'idul_fitri', 'urutan' => 9],
            ['nama_jenis' => 'Shalat Idul Adha', 'kategori' => 'idul_adha', 'urutan' => 10],
        ];
        foreach ($jenisIbadah as $j) {
            JenisIbadah::firstOrCreate(['nama_jenis' => $j['nama_jenis']], $j);
        }

        // Modul 2 — kategori kegiatan
        foreach ([
            'Kajian', 'Pengajian', 'Pendidikan', 'Sosial', 'Ramadhan',
            'Idul Fitri', 'Idul Adha', 'Santunan', 'Bakti Sosial', 'Kegiatan Pemuda',
        ] as $k) {
            KategoriKegiatan::firstOrCreate(['nama_kategori' => $k]);
        }

        // Modul 4 — sumber dana
        foreach ([
            'Kotak Amal', 'Infaq', 'Sedekah', 'Donasi', 'Zakat',
            'Wakaf', 'Transfer', 'Sumbangan Kegiatan', 'Lainnya',
        ] as $s) {
            SumberDana::firstOrCreate(['nama_sumber' => $s], ['tipe' => 'pemasukan']);
        }

        // Modul 4 — kategori transaksi
        $kategoriTransaksi = [
            ['nama_kategori' => 'Infaq & Sedekah', 'tipe' => 'pemasukan'],
            ['nama_kategori' => 'Zakat', 'tipe' => 'pemasukan'],
            ['nama_kategori' => 'Wakaf', 'tipe' => 'pemasukan'],
            ['nama_kategori' => 'Donasi Kegiatan', 'tipe' => 'pemasukan'],
            ['nama_kategori' => 'Pemasukan Lainnya', 'tipe' => 'pemasukan'],
            ['nama_kategori' => 'Operasional Masjid', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Listrik', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Air', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Internet', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Kebersihan', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Honor Imam/Khotib/Ustadz', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Pemeliharaan Bangunan', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Pembelian Perlengkapan', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Kegiatan Sosial', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Kegiatan Keagamaan', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Kegiatan Ramadhan', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Pengeluaran Lainnya', 'tipe' => 'pengeluaran'],
        ];
        foreach ($kategoriTransaksi as $kt) {
            KategoriTransaksi::firstOrCreate(['nama_kategori' => $kt['nama_kategori']], $kt);
        }

        // Entitas: Yayasan (induk) & Masjid/DKM — migration sudah membuatnya; ini pengaman bila belum ada.
        $namaInti = trim(preg_replace('/^masjid\s+/i', '', 'Masjid Baitul Mukminin'));
        $yayasan = \App\Models\Entitas::firstOrCreate(
            ['slug' => 'yayasan'],
            ['nama' => 'Yayasan ' . $namaInti, 'nama_pendek' => 'Yayasan', 'jenis' => 'yayasan', 'urutan' => 1,
                'keterangan' => 'Badan hukum yang menaungi masjid.']
        );
        \App\Models\Entitas::firstOrCreate(
            ['slug' => 'masjid'],
            ['nama' => 'Masjid Baitul Mukminin (DKM)', 'nama_pendek' => 'Masjid', 'jenis' => 'masjid', 'urutan' => 2,
                'induk_id' => $yayasan->id, 'keterangan' => 'Dewan Kemakmuran Masjid — operasional ibadah & kegiatan masjid.']
        );

        // Modul 1 — jabatan standar: Yayasan dan DKM dipisah lewat kolom `kelompok`
        foreach ([
            // Yayasan (organ yayasan sesuai UU Yayasan: Pembina → Pengawas → Pengurus)
            ['nama_jabatan' => 'Ketua Pembina', 'kelompok' => 'yayasan', 'tingkat' => 1, 'urutan' => 1],
            ['nama_jabatan' => 'Anggota Pembina', 'kelompok' => 'yayasan', 'tingkat' => 1, 'urutan' => 2],
            ['nama_jabatan' => 'Ketua Pengawas', 'kelompok' => 'yayasan', 'tingkat' => 2, 'urutan' => 3],
            ['nama_jabatan' => 'Anggota Pengawas', 'kelompok' => 'yayasan', 'tingkat' => 2, 'urutan' => 4],
            ['nama_jabatan' => 'Ketua Yayasan', 'kelompok' => 'yayasan', 'tingkat' => 3, 'urutan' => 5],
            ['nama_jabatan' => 'Sekretaris Yayasan', 'kelompok' => 'yayasan', 'tingkat' => 4, 'urutan' => 6],
            ['nama_jabatan' => 'Bendahara Yayasan', 'kelompok' => 'yayasan', 'tingkat' => 4, 'urutan' => 7],
            // Masjid / DKM
            ['nama_jabatan' => 'Ketua DKM', 'kelompok' => 'masjid', 'tingkat' => 1, 'urutan' => 1],
            ['nama_jabatan' => 'Wakil Ketua', 'kelompok' => 'masjid', 'tingkat' => 2, 'urutan' => 2],
            ['nama_jabatan' => 'Sekretaris', 'kelompok' => 'masjid', 'tingkat' => 2, 'urutan' => 3],
            ['nama_jabatan' => 'Bendahara', 'kelompok' => 'masjid', 'tingkat' => 2, 'urutan' => 4],
            ['nama_jabatan' => 'Koordinator Bidang Ibadah', 'kelompok' => 'masjid', 'tingkat' => 3, 'urutan' => 5],
            ['nama_jabatan' => 'Koordinator Bidang Pendidikan', 'kelompok' => 'masjid', 'tingkat' => 3, 'urutan' => 6],
            ['nama_jabatan' => 'Koordinator Bidang Sosial', 'kelompok' => 'masjid', 'tingkat' => 3, 'urutan' => 7],
            ['nama_jabatan' => 'Koordinator Bidang Pemuda & Remaja', 'kelompok' => 'masjid', 'tingkat' => 3, 'urutan' => 8],
            // Umum (bisa dipakai keduanya)
            ['nama_jabatan' => 'Anggota', 'kelompok' => 'umum', 'tingkat' => 4, 'urutan' => 99],
        ] as $jab) {
            $model = \App\Models\Jabatan::firstOrCreate(['nama_jabatan' => $jab['nama_jabatan']], $jab);
            $model->update(['kelompok' => $jab['kelompok'], 'tingkat' => $jab['tingkat']]);
        }
    }
}
