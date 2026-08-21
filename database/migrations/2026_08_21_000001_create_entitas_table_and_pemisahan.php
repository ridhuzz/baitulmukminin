<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pemisahan Masjid (DKM) dan Yayasan.
 *
 * - Tabel `entitas`: unit organisasi/badan (Yayasan sebagai induk, Masjid/DKM di bawahnya,
 *   bisa ditambah unit lain mis. Remaja Masjid).
 * - Kolom `entitas_id` pada struktur organisasi, transaksi, rekening, laporan keuangan,
 *   kegiatan, dan pengumuman → kas, laporan, dan struktur terpisah per entitas.
 * - `users.entitas_id` (opsional) membatasi seorang user hanya ke satu entitas
 *   (mis. Bendahara Yayasan tidak melihat kas Masjid). Kosong = semua entitas.
 * - `jabatan.kelompok` memisahkan jabatan Yayasan (Pembina, Pengawas, …) dan DKM.
 *
 * Data yang sudah ada otomatis dimasukkan ke entitas Masjid (DKM).
 */
return new class extends Migration
{
    private const TABEL_TERPISAH = [
        'struktur_organisasi',
        'transaksi_keuangan',
        'rekening_bank',
        'laporan_keuangan',
        'kegiatan',
        'pengumuman',
    ];

    public function up(): void
    {
        Schema::create('entitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_pendek', 50)->nullable();
            $table->string('slug')->unique();
            $table->enum('jenis', ['masjid', 'yayasan', 'lainnya'])->default('lainnya');
            $table->foreignId('induk_id')->nullable()->constrained('entitas')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        $namaMasjid = (string) (DB::table('masjid')->value('nama_masjid') ?: 'Masjid');
        $namaInti = trim(preg_replace('/^masjid\s+/i', '', $namaMasjid)) ?: $namaMasjid;
        $sekarang = now();

        $yayasanId = DB::table('entitas')->insertGetId([
            'nama' => 'Yayasan ' . $namaInti,
            'nama_pendek' => 'Yayasan',
            'slug' => 'yayasan',
            'jenis' => 'yayasan',
            'induk_id' => null,
            'keterangan' => 'Badan hukum yang menaungi masjid.',
            'urutan' => 1,
            'aktif' => true,
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ]);

        $masjidId = DB::table('entitas')->insertGetId([
            'nama' => $namaMasjid . ' (DKM)',
            'nama_pendek' => 'Masjid',
            'slug' => 'masjid',
            'jenis' => 'masjid',
            'induk_id' => $yayasanId,
            'keterangan' => 'Dewan Kemakmuran Masjid — operasional ibadah & kegiatan masjid.',
            'urutan' => 2,
            'aktif' => true,
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ]);

        foreach (self::TABEL_TERPISAH as $tabel) {
            Schema::table($tabel, function (Blueprint $table) {
                $table->foreignId('entitas_id')->nullable()->after('id')
                    ->constrained('entitas')->restrictOnDelete();
            });

            DB::table($tabel)->whereNull('entitas_id')->update(['entitas_id' => $masjidId]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('entitas_id')->nullable()->after('pengurus_id')
                ->constrained('entitas')->nullOnDelete();
        });

        Schema::table('jabatan', function (Blueprint $table) {
            $table->enum('kelompok', ['masjid', 'yayasan', 'umum'])->default('masjid')->after('nama_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('jabatan', fn (Blueprint $table) => $table->dropColumn('kelompok'));
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('entitas_id'));

        foreach (self::TABEL_TERPISAH as $tabel) {
            Schema::table($tabel, fn (Blueprint $table) => $table->dropConstrainedForeignId('entitas_id'));
        }

        Schema::dropIfExists('entitas');
    }
};
