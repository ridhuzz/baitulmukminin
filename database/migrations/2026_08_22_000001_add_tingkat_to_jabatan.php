<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `jabatan.tingkat` = baris/jenjang pada bagan struktur organisasi
 * (1 = paling atas). Nilai awal diturunkan dari `urutan`; MasterDataSeeder
 * menetapkan nilai yang tepat untuk jabatan standar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->unsignedTinyInteger('tingkat')->default(3)->after('kelompok');
        });

        DB::table('jabatan')->where('urutan', '<=', 1)->update(['tingkat' => 1]);
        DB::table('jabatan')->whereBetween('urutan', [2, 4])->update(['tingkat' => 2]);
        DB::table('jabatan')->whereBetween('urutan', [5, 8])->update(['tingkat' => 3]);
        DB::table('jabatan')->where('urutan', '>', 8)->update(['tingkat' => 4]);
    }

    public function down(): void
    {
        Schema::table('jabatan', fn (Blueprint $table) => $table->dropColumn('tingkat'));
    }
};
