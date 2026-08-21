<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 3 — Jadwal Imam, Khotib & Petugas.
 *
 * Penyesuaian dari ERD:
 * - `petugas.peran` disimpan sebagai JSON (satu petugas bisa merangkap
 *   Imam + Khotib + Penceramah, dst.), bukan satu kolom string tunggal.
 * - Satu `jadwal_petugas` (mis. Shalat Jumat 14 Aug) butuh beberapa petugas
 *   sekaligus (imam, khotib, muadzin, bilal) — dipecah ke tabel detail
 *   `jadwal_petugas_detail` (satu baris per penugasan-peran).
 * - `pengganti_petugas` menunjuk ke baris detail penugasan, bukan ke jadwal,
 *   supaya jelas peran mana yang diganti.
 * - `riwayat_jadwal` tidak dibuat sebagai tabel terpisah; perubahan cukup
 *   terekam lewat `pengganti_petugas` + timestamps (bisa ditambah
 *   activity-log di tahap lanjut).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_ibadah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis');
            $table->enum('kategori', ['harian', 'jumat', 'ramadhan', 'idul_fitri', 'idul_adha', 'lainnya'])->default('harian');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('petugas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->json('peran')->nullable(); // imam, khotib, muadzin, bilal, penceramah, mc, dll.
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('jadwal_petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_ibadah_id')->constrained('jenis_ibadah')->restrictOnDelete();
            $table->date('tanggal_jadwal');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['terjadwal', 'selesai', 'dibatalkan'])->default('terjadwal');
            $table->timestamps();

            $table->unique(['jenis_ibadah_id', 'tanggal_jadwal']);
            $table->index('tanggal_jadwal');
        });

        Schema::create('jadwal_petugas_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal_petugas')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('petugas')->restrictOnDelete();
            $table->enum('peran', ['imam', 'khotib', 'muadzin', 'bilal', 'penceramah', 'mc', 'lainnya']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_id', 'peran', 'petugas_id']);
        });

        Schema::create('pengganti_petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_detail_id')->constrained('jadwal_petugas_detail')->cascadeOnDelete();
            $table->foreignId('petugas_pengganti_id')->constrained('petugas')->restrictOnDelete();
            $table->text('alasan')->nullable();
            $table->date('tanggal_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengganti_petugas');
        Schema::dropIfExists('jadwal_petugas_detail');
        Schema::dropIfExists('jadwal_petugas');
        Schema::dropIfExists('petugas');
        Schema::dropIfExists('jenis_ibadah');
    }
};
