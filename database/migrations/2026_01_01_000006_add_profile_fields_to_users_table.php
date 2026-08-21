<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul 6 — User & Hak Akses.
 * Role/permission ditangani spatie/laravel-permission (tabel roles,
 * permissions, model_has_roles, dst. dari migration paket).
 * User dapat ditautkan ke data pengurus via `pengurus_id`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telepon')->nullable()->after('email');
            $table->boolean('status_aktif')->default(true)->after('telepon');
            $table->foreignId('pengurus_id')->nullable()->after('status_aktif')
                ->constrained('pengurus')->nullOnDelete();
            $table->timestamp('last_login_at')->nullable()->after('pengurus_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pengurus_id');
            $table->dropColumn(['telepon', 'status_aktif', 'last_login_at']);
        });
    }
};
