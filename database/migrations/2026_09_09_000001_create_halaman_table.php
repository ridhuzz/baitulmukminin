<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Halaman dinamis (konten HTML) yang dikelola dari admin dan tampil di situs publik. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halaman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('ringkasan')->nullable();      // deskripsi singkat (meta/subjudul header)
            $table->longText('konten')->nullable();       // HTML hasil rich editor
            $table->boolean('tampil_di_menu')->default(false);
            $table->string('label_menu', 30)->nullable(); // teks di menu; kosong = pakai judul
            $table->unsignedSmallInteger('urutan_menu')->default(0);
            $table->boolean('publish')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halaman');
    }
};
