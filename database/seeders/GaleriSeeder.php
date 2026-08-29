<?php

namespace Database\Seeders;

use App\Models\Galeri;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Contoh data Galeri & Fasilitas Masjid (memakai gambar ilustrasi bawaan).
 * Jalankan: php artisan db:seed --class=GaleriSeeder
 * Aman dijalankan berulang (updateOrCreate berdasarkan judul).
 */
class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        $contoh = [
            ['ruang-utama', 'Ruang Utama Masjid', 'fasilitas', 'Ruang shalat utama berkapasitas ± 500 jamaah'],
            ['mihrab-mimbar', 'Mihrab & Mimbar', 'fasilitas', 'Mihrab dan mimbar khotib'],
            ['area-wudhu', 'Area Wudhu', 'fasilitas', 'Tempat wudhu pria & wanita terpisah'],
            ['halaman-parkir', 'Halaman & Parkir', 'fasilitas', 'Area parkir motor dan mobil jamaah'],
            ['kajian-rutin', 'Kajian Rutin Jamaah', 'kegiatan', 'Kajian ba\'da Maghrib setiap pekan'],
            ['tpa-anak', 'TPA Anak-anak', 'kegiatan', 'Taman Pendidikan Al-Qur\'an sore hari'],
            ['santunan-yatim', 'Santunan Yatim', 'kegiatan', 'Program santunan rutin bulanan'],
        ];

        $disk = Storage::disk('public');

        foreach ($contoh as $i => [$slug, $judul, $kategori, $keterangan]) {
            $sumber = public_path("images/ilustrasi/{$slug}.svg");
            $tujuan = "galeri/contoh-{$slug}.svg";

            if (File::exists($sumber) && ! $disk->exists($tujuan)) {
                $disk->put($tujuan, File::get($sumber));
            }

            Galeri::updateOrCreate(
                ['judul' => $judul],
                [
                    'kategori' => $kategori,
                    'gambar' => $tujuan,
                    'keterangan' => $keterangan,
                    'urutan' => $i + 1,
                    'tampil' => true,
                ]
            );
        }
    }
}
