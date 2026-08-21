<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 5 — Pengumuman & Informasi Masjid.
 *
 * Penyesuaian dari ERD: tabel `target_audience` (M2M) disederhanakan menjadi
 * satu kolom enum `target` — untuk MVP satu pengumuman cukup punya satu
 * audiens; M2M bisa ditambahkan lagi jika kelak benar-benar dibutuhkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->longText('konten')->nullable();
            $table->enum('jenis', ['pengumuman', 'kegiatan', 'kajian', 'donasi', 'informasi', 'banner'])->default('pengumuman');
            $table->string('gambar')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('target', ['publik', 'jamaah', 'pengurus', 'tertentu'])->default('publik');
            $table->boolean('status_publish')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status_publish', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
