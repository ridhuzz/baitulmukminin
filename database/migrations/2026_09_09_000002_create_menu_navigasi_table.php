<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menu navigasi situs publik dikelola dari admin (Pengaturan → Pengaturan Menu),
 * mendukung satu tingkat submenu. Item bawaan di-seed, dan halaman dinamis yang
 * sebelumnya dicentang "tampil di menu" dipindahkan menjadi item menu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_navigasi', function (Blueprint $table) {
            $table->id();
            $table->string('label', 50);
            $table->foreignId('induk_id')->nullable()->constrained('menu_navigasi')->cascadeOnDelete();
            $table->string('tipe', 10)->default('rute'); // rute | halaman | url
            $table->string('rute', 60)->nullable();      // nama route Laravel (halaman bawaan)
            $table->foreignId('halaman_id')->nullable()->constrained('halaman')->nullOnDelete();
            $table->string('url')->nullable();
            $table->boolean('buka_tab_baru')->default(false);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Item bawaan (menu yang selama ini hardcoded di layout publik)
        $bawaan = [
            [1, 'Beranda', 'publik.beranda'],
            [2, 'Jadwal Ibadah', 'publik.jadwal'],
            [3, 'Kegiatan', 'publik.kegiatan'],
            [4, 'Struktur', 'publik.struktur'],
            [5, 'Transparansi', 'publik.laporan'],
        ];
        foreach ($bawaan as [$urutan, $label, $rute]) {
            DB::table('menu_navigasi')->insert([
                'label' => $label, 'tipe' => 'rute', 'rute' => $rute,
                'urutan' => $urutan, 'aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Pindahkan halaman dinamis ber-"tampil di menu" menjadi item menu
        if (Schema::hasColumn('halaman', 'tampil_di_menu')) {
            foreach (DB::table('halaman')->where('tampil_di_menu', true)->get() as $h) {
                DB::table('menu_navigasi')->insert([
                    'label' => $h->label_menu ?: $h->judul, 'tipe' => 'halaman', 'halaman_id' => $h->id,
                    'urutan' => 100 + (int) $h->urutan_menu, 'aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            Schema::table('halaman', function (Blueprint $table) {
                $table->dropColumn(['tampil_di_menu', 'label_menu', 'urutan_menu']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('halaman', function (Blueprint $table) {
            $table->boolean('tampil_di_menu')->default(false);
            $table->string('label_menu', 30)->nullable();
            $table->unsignedSmallInteger('urutan_menu')->default(0);
        });
        Schema::dropIfExists('menu_navigasi');
    }
};
