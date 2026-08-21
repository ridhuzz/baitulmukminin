<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Role sesuai blueprint "User & Hak Akses":
     * Super Admin (semua modul), Ketua DKM (monitoring & approval),
     * Sekretaris (struktur & kegiatan), Bendahara (keuangan),
     * Koordinator Ibadah (imam/khotib/petugas), Pengurus (kegiatan & informasi).
     * Jamaah/Public tidak perlu akun — halaman publik tanpa login.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ([
            'Super Admin',
            'Ketua DKM',
            'Sekretaris',
            'Bendahara',
            'Koordinator Ibadah',
            'Pengurus',
        ] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@baitulmukminin.test'],
            [
                'name' => 'Super Admin',
                'password' => 'password', // ganti setelah login pertama!
                'status_aktif' => true,
            ]
        );
        $admin->syncRoles(['Super Admin']);
    }
}
