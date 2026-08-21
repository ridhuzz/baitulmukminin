<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 1 — Struktur & Pengurus Masjid.
 *
 * Penyesuaian dari ERD:
 * - `kepengurusan` sekaligus menjadi riwayat kepengurusan (baris dengan periode
 *   lampau / status nonaktif = riwayat), sehingga tabel `riwayat_kepengurusan`
 *   tidak diperlukan.
 * - `jabatan` dijadikan master global (tidak terikat struktur) agar bisa
 *   dipakai ulang lintas periode kepengurusan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masjid', function (Blueprint $table) {
            $table->id();
            $table->string('nama_masjid');
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kontak')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('struktur_organisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masjid_id')->constrained('masjid')->cascadeOnDelete();
            $table->string('nama_struktur');
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('pengurus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('kepengurusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengurus_id')->constrained('pengurus')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatan')->restrictOnDelete();
            $table->foreignId('struktur_id')->constrained('struktur_organisasi')->cascadeOnDelete();
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->unique(['pengurus_id', 'jabatan_id', 'struktur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kepengurusan');
        Schema::dropIfExists('pengurus');
        Schema::dropIfExists('jabatan');
        Schema::dropIfExists('struktur_organisasi');
        Schema::dropIfExists('masjid');
    }
};
