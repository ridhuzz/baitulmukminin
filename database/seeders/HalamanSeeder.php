<?php

namespace Database\Seeders;

use App\Models\Halaman;
use Illuminate\Database\Seeder;

/**
 * Halaman statis contoh ("Tentang Masjid"). Sengaja TIDAK dipasang ke menu —
 * pengurus menambahkannya sendiri lewat Pengaturan → Pengaturan Menu.
 * Jalankan: php artisan db:seed --class=HalamanSeeder --force
 */
class HalamanSeeder extends Seeder
{
    public function run(): void
    {
        Halaman::updateOrCreate(
            ['slug' => 'tentang-masjid'],
            [
                'judul' => 'Tentang Masjid',
                'ringkasan' => 'Sejarah singkat, visi & misi, dan fasilitas Masjid Baitul Mukminin.',
                'publish' => true,
                'konten' => <<<'HTML'
<h2>Sejarah Singkat</h2>
<p>Masjid Baitul Mukminin berdiri di tengah Perumahan Cimone Permai, Kelurahan Cimone, Kecamatan Karawaci, Kota Tangerang. Masjid ini dibangun atas swadaya warga dan terus berkembang menjadi pusat ibadah sekaligus pusat kegiatan sosial keagamaan bagi jamaah di lingkungan Cimone Permai dan sekitarnya.</p>
<h2>Visi</h2>
<p>Menjadi masjid yang makmur, mandiri, dan memakmurkan jamaah — rumah kedua bagi warga untuk merajut ukhuwah dan menebar kebaikan.</p>
<h2>Misi</h2>
<ul>
<li>Menyelenggarakan ibadah yang tertib, nyaman, dan sesuai tuntunan.</li>
<li>Menghidupkan kajian, pendidikan Al-Qur'an, dan pembinaan generasi muda.</li>
<li>Mengelola ZISWAF secara amanah dan transparan untuk kesejahteraan umat.</li>
<li>Menjadikan masjid pusat kegiatan sosial yang bermanfaat bagi lingkungan.</li>
</ul>
<h2>Fasilitas</h2>
<ul>
<li>Ruang shalat utama yang bersih dan nyaman</li>
<li>Area wudhu pria dan wanita terpisah</li>
<li>Aula serbaguna untuk kajian dan kegiatan jamaah</li>
<li>Taman Pendidikan Al-Qur'an (TPA)</li>
<li>Halaman dan area parkir</li>
</ul>
<p><em>Konten halaman ini dapat diubah pengurus melalui Admin → Pengaturan → Pengaturan Halaman.</em></p>
HTML,
            ]
        );
    }
}
