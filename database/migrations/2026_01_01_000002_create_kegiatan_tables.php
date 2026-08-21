<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 2 — Program & Kegiatan Masjid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_kegiatan')->nullOnDelete();
            $table->string('nama_kegiatan');
            $table->text('deskripsi')->nullable();
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->decimal('anggaran', 15, 2)->nullable();
            $table->enum('status', ['draft', 'terjadwal', 'berlangsung', 'selesai', 'dibatalkan'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'tanggal_mulai']);
        });

        Schema::create('pic_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('pengurus_id')->constrained('pengurus')->cascadeOnDelete();
            $table->string('peran')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['kegiatan_id', 'pengurus_id']);
        });

        Schema::create('dokumentasi_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->enum('jenis_dokumen', ['foto', 'video', 'dokumen', 'lainnya'])->default('foto');
            $table->string('judul')->nullable();
            $table->string('file_path');
            $table->date('tanggal_upload')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('laporan_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->date('tanggal_laporan')->nullable();
            $table->text('ringkasan')->nullable();
            $table->string('file_laporan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kegiatan');
        Schema::dropIfExists('dokumentasi_kegiatan');
        Schema::dropIfExists('pic_kegiatan');
        Schema::dropIfExists('kegiatan');
        Schema::dropIfExists('kategori_kegiatan');
    }
};
