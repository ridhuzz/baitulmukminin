<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 4 — Keuangan Masjid.
 *
 * Penyesuaian dari ERD:
 * - Kolom saldo (`rekening_bank.saldo_terakhir`, tabel `saldo_rekening`) dan
 *   kelima tabel laporan (`laporan_kas_harian`, `laporan_kas_bulanan`,
 *   `laporan_pemasukan`, `laporan_pengeluaran`, `laporan_neraca`) TIDAK dibuat
 *   sebagai tabel — semuanya data turunan yang dihitung langsung dari
 *   `transaksi_keuangan` sehingga tidak mungkin selisih.
 * - Sebagai gantinya ada satu tabel `laporan_keuangan` untuk SNAPSHOT laporan
 *   yang dipublikasikan (blueprint: laporan bisa dibuat publik/terbatas).
 * - `transaksi_keuangan` ditambah FK opsional `kegiatan_id` agar sumbangan
 *   dan pengeluaran kegiatan tersambung ke modul kegiatan (realisasi vs
 *   anggaran).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sumber_dana', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sumber'); // Kotak Amal, Infaq, Sedekah, Donasi, Zakat, Wakaf, ...
            $table->enum('tipe', ['pemasukan', 'pengeluaran', 'keduanya'])->default('pemasukan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('kategori_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->foreignId('induk_id')->nullable()->constrained('kategori_transaksi')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('rekening_bank', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nomor_rekening');
            $table->string('nama_pemilik')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('transaksi_keuangan', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_transaksi', ['pemasukan', 'pengeluaran']);
            $table->foreignId('kategori_transaksi_id')->constrained('kategori_transaksi')->restrictOnDelete();
            $table->foreignId('sumber_dana_id')->nullable()->constrained('sumber_dana')->nullOnDelete();
            $table->foreignId('kegiatan_id')->nullable()->constrained('kegiatan')->nullOnDelete();
            $table->foreignId('rekening_bank_id')->nullable()->constrained('rekening_bank')->nullOnDelete();
            $table->date('tanggal_transaksi');
            $table->decimal('nominal', 15, 2);
            $table->enum('metode_pembayaran', ['kas', 'bank', 'transfer', 'qris', 'lainnya'])->default('kas');
            $table->text('keterangan')->nullable();
            $table->string('bukti_path')->nullable();
            $table->enum('status_approval', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('input_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['jenis_transaksi', 'tanggal_transaksi']);
            $table->index('status_approval');
        });

        Schema::create('laporan_keuangan', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('total_pemasukan', 15, 2)->default(0);
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->decimal('saldo_akhir', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->enum('visibilitas', ['publik', 'terbatas', 'internal'])->default('internal');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_keuangan');
        Schema::dropIfExists('transaksi_keuangan');
        Schema::dropIfExists('rekening_bank');
        Schema::dropIfExists('kategori_transaksi');
        Schema::dropIfExists('sumber_dana');
    }
};
