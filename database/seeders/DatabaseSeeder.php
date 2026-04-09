<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            CreateSuperAdminSeeder::class,
            AdministratorSeeder::class,
            MasterDataSeeder::class, // Isi data Master (Departemen, Jabatan, dll)
            PegawaiSeeder::class,    // Isi data Pegawai
            DepartemenOfficerSeeder::class, // Membuat Petugas untuk tiap Departemen
            AbsensiSeeder::class,    // Isi data Absensi Jan-Mar 2026
        ]);
    }
}
