<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk Role dan Permission
 * 
 * Role Hierarchy:
 * 1. Admin HRD - Kelola semua aspek HR dan gaji
 * 2. Manager - Kelola departemen dan laporan
 * 3. Direktur - Approval dan monitoring
 * 4. Pegawai - Hanya lihat data pribadi dan slip gaji sendiri
 */
class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data Permission yang akan dibuat
        $permissions = [
            // ============= KATEGORI: PENGGAJIAN =============
            [
                'nama_permission' => 'gaji.view',
                'deskripsi' => 'Lihat data penggajian',
                'kategori' => 'penggajian'
            ],
            [
                'nama_permission' => 'gaji.view_own',
                'deskripsi' => 'Lihat slip gaji sendiri',
                'kategori' => 'penggajian'
            ],
            [
                'nama_permission' => 'gaji.create',
                'deskripsi' => 'Buat perhitungan gaji',
                'kategori' => 'penggajian'
            ],
            [
                'nama_permission' => 'gaji.edit',
                'deskripsi' => 'Edit perhitungan gaji',
                'kategori' => 'penggajian'
            ],
            [
                'nama_permission' => 'gaji.delete',
                'deskripsi' => 'Hapus perhitungan gaji',
                'kategori' => 'penggajian'
            ],
            [
                'nama_permission' => 'gaji.approve',
                'deskripsi' => 'Approve perhitungan gaji',
                'kategori' => 'penggajian'
            ],
            [
                'nama_permission' => 'gaji.print_slip',
                'deskripsi' => 'Print slip gaji',
                'kategori' => 'penggajian'
            ],

            // ============= KATEGORI: ABSENSI =============
            [
                'nama_permission' => 'absensi.view',
                'deskripsi' => 'Lihat data absensi',
                'kategori' => 'absensi'
            ],
            [
                'nama_permission' => 'absensi.view_own',
                'deskripsi' => 'Lihat absensi sendiri',
                'kategori' => 'absensi'
            ],
            [
                'nama_permission' => 'absensi.create',
                'deskripsi' => 'Input absensi',
                'kategori' => 'absensi'
            ],
            [
                'nama_permission' => 'absensi.edit',
                'deskripsi' => 'Edit absensi',
                'kategori' => 'absensi'
            ],
            [
                'nama_permission' => 'absensi.delete',
                'deskripsi' => 'Hapus absensi',
                'kategori' => 'absensi'
            ],
            [
                'nama_permission' => 'absensi.approve',
                'deskripsi' => 'Approve absensi',
                'kategori' => 'absensi'
            ],

            // ============= KATEGORI: LEMBUR =============
            [
                'nama_permission' => 'lembur.view',
                'deskripsi' => 'Lihat data lembur',
                'kategori' => 'lembur'
            ],
            [
                'nama_permission' => 'lembur.view_own',
                'deskripsi' => 'Lihat lembur sendiri',
                'kategori' => 'lembur'
            ],
            [
                'nama_permission' => 'lembur.create',
                'deskripsi' => 'Input lembur',
                'kategori' => 'lembur'
            ],
            [
                'nama_permission' => 'lembur.edit',
                'deskripsi' => 'Edit lembur',
                'kategori' => 'lembur'
            ],
            [
                'nama_permission' => 'lembur.delete',
                'deskripsi' => 'Hapus lembur',
                'kategori' => 'lembur'
            ],
            [
                'nama_permission' => 'lembur.approve',
                'deskripsi' => 'Approve lembur',
                'kategori' => 'lembur'
            ],

            // ============= KATEGORI: PEGAWAI =============
            [
                'nama_permission' => 'pegawai.view',
                'deskripsi' => 'Lihat data pegawai',
                'kategori' => 'pegawai'
            ],
            [
                'nama_permission' => 'pegawai.create',
                'deskripsi' => 'Tambah pegawai baru',
                'kategori' => 'pegawai'
            ],
            [
                'nama_permission' => 'pegawai.edit',
                'deskripsi' => 'Edit data pegawai',
                'kategori' => 'pegawai'
            ],
            [
                'nama_permission' => 'pegawai.delete',
                'deskripsi' => 'Hapus pegawai',
                'kategori' => 'pegawai'
            ],

            // ============= KATEGORI: TUNJANGAN =============
            [
                'nama_permission' => 'tunjangan.view',
                'deskripsi' => 'Lihat data tunjangan',
                'kategori' => 'tunjangan'
            ],
            [
                'nama_permission' => 'tunjangan.create',
                'deskripsi' => 'Buat tunjangan baru',
                'kategori' => 'tunjangan'
            ],
            [
                'nama_permission' => 'tunjangan.edit',
                'deskripsi' => 'Edit tunjangan',
                'kategori' => 'tunjangan'
            ],
            [
                'nama_permission' => 'tunjangan.delete',
                'deskripsi' => 'Hapus tunjangan',
                'kategori' => 'tunjangan'
            ],
            [
                'nama_permission' => 'tunjangan.assign',
                'deskripsi' => 'Berikan tunjangan ke pegawai',
                'kategori' => 'tunjangan'
            ],

            // ============= KATEGORI: POTONGAN =============
            [
                'nama_permission' => 'potongan.view',
                'deskripsi' => 'Lihat data potongan',
                'kategori' => 'potongan'
            ],
            [
                'nama_permission' => 'potongan.create',
                'deskripsi' => 'Buat potongan baru',
                'kategori' => 'potongan'
            ],
            [
                'nama_permission' => 'potongan.edit',
                'deskripsi' => 'Edit potongan',
                'kategori' => 'potongan'
            ],
            [
                'nama_permission' => 'potongan.delete',
                'deskripsi' => 'Hapus potongan',
                'kategori' => 'potongan'
            ],
            [
                'nama_permission' => 'potongan.assign',
                'deskripsi' => 'Berikan potongan ke pegawai',
                'kategori' => 'potongan'
            ],

            // ============= KATEGORI: DEPARTEMEN =============
            [
                'nama_permission' => 'departemen.view',
                'deskripsi' => 'Lihat departemen',
                'kategori' => 'departemen'
            ],
            [
                'nama_permission' => 'departemen.create',
                'deskripsi' => 'Buat departemen',
                'kategori' => 'departemen'
            ],
            [
                'nama_permission' => 'departemen.edit',
                'deskripsi' => 'Edit departemen',
                'kategori' => 'departemen'
            ],
            [
                'nama_permission' => 'departemen.delete',
                'deskripsi' => 'Hapus departemen',
                'kategori' => 'departemen'
            ],

            // ============= KATEGORI: JABATAN =============
            [
                'nama_permission' => 'jabatan.view',
                'deskripsi' => 'Lihat jabatan',
                'kategori' => 'jabatan'
            ],
            [
                'nama_permission' => 'jabatan.create',
                'deskripsi' => 'Buat jabatan',
                'kategori' => 'jabatan'
            ],
            [
                'nama_permission' => 'jabatan.edit',
                'deskripsi' => 'Edit jabatan',
                'kategori' => 'jabatan'
            ],
            [
                'nama_permission' => 'jabatan.delete',
                'deskripsi' => 'Hapus jabatan',
                'kategori' => 'jabatan'
            ],

            // ============= KATEGORI: LAPORAN =============
            [
                'nama_permission' => 'laporan.view',
                'deskripsi' => 'Lihat laporan',
                'kategori' => 'laporan'
            ],
            [
                'nama_permission' => 'laporan.gaji',
                'deskripsi' => 'Lihat laporan gaji',
                'kategori' => 'laporan'
            ],
            [
                'nama_permission' => 'laporan.absensi',
                'deskripsi' => 'Lihat laporan absensi',
                'kategori' => 'laporan'
            ],
            [
                'nama_permission' => 'laporan.lembur',
                'deskripsi' => 'Lihat laporan lembur',
                'kategori' => 'laporan'
            ],
            [
                'nama_permission' => 'laporan.export',
                'deskripsi' => 'Export laporan',
                'kategori' => 'laporan'
            ],

            // ============= KATEGORI: MASTER DATA =============
            [
                'nama_permission' => 'ptkp.view',
                'deskripsi' => 'Lihat PTKP',
                'kategori' => 'master_data'
            ],
            [
                'nama_permission' => 'ptkp.edit',
                'deskripsi' => 'Edit PTKP',
                'kategori' => 'master_data'
            ],
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            DB::table('permission')->insertOrIgnore($permission);
        }

        // Data Role yang akan dibuat
        $roles = [
            [
                'nama_role' => 'Super Admin',
                'deskripsi' => 'Super Administrator - Akses penuh ke seluruh sistem'
            ],
            [
                'nama_role' => 'Admin HRD',
                'deskripsi' => 'Administrator HR - Kelola semua aspek HR dan gaji'
            ],
            [
                'nama_role' => 'Manager',
                'deskripsi' => 'Manager - Kelola departemen dan member, approve absensi/lembur'
            ],
            [
                'nama_role' => 'Direktur',
                'deskripsi' => 'Direktur - Monitoring dan approval gaji'
            ],
            [
                'nama_role' => 'Pegawai',
                'deskripsi' => 'Pegawai - Hanya lihat data pribadi dan slip gaji'
            ],
        ];

        // Insert roles
        $roleIds = [];
        foreach ($roles as $role) {
            $id = DB::table('role')->insertGetId($role);
            $roleIds[$role['nama_role']] = $id;
        }

        // ============= ROLE PERMISSIONS MAPPING =============

        // 1. SUPER ADMIN - Memiliki semua permission di atas roles lainnya
        $allPermissions = DB::table('permission')->pluck('id_permission')->toArray();
        foreach ($allPermissions as $permissionId) {
            DB::table('role_permission')->insertOrIgnore([
                'id_role' => $roleIds['Super Admin'],
                'id_permission' => $permissionId
            ]);
        }

        // 2. ADMIN HRD - Memiliki semua permission
        $adminHrdPermissions = DB::table('permission')->pluck('id_permission')->toArray();
        foreach ($adminHrdPermissions as $permissionId) {
            DB::table('role_permission')->insertOrIgnore([
                'id_role' => $roleIds['Admin HRD'],
                'id_permission' => $permissionId
            ]);
        }

        // 2. MANAGER - Kelola departemen, approve absensi/lembur, lihat gaji
        $managerPermissions = [
            'gaji.view',
            'gaji.print_slip',
            'absensi.view',
            'absensi.approve',
            'lembur.view',
            'lembur.approve',
            'pegawai.view',
            'laporan.view',
            'laporan.absensi',
            'laporan.lembur',
            'departemen.view',
            'jabatan.view',
        ];

        $managerPermissionIds = DB::table('permission')
            ->whereIn('nama_permission', $managerPermissions)
            ->pluck('id_permission')
            ->toArray();

        foreach ($managerPermissionIds as $permissionId) {
            DB::table('role_permission')->insertOrIgnore([
                'id_role' => $roleIds['Manager'],
                'id_permission' => $permissionId
            ]);
        }

        // 3. DIREKTUR - Monitoring & approval
        $direktorPermissions = [
            'gaji.view',
            'gaji.approve',
            'gaji.print_slip',
            'laporan.view',
            'laporan.gaji',
            'laporan.absensi',
            'laporan.lembur',
            'laporan.export',
            'pegawai.view',
            'departemen.view',
            'jabatan.view',
        ];

        $direktorPermissionIds = DB::table('permission')
            ->whereIn('nama_permission', $direktorPermissions)
            ->pluck('id_permission')
            ->toArray();

        foreach ($direktorPermissionIds as $permissionId) {
            DB::table('role_permission')->insertOrIgnore([
                'id_role' => $roleIds['Direktur'],
                'id_permission' => $permissionId
            ]);
        }

        // 4. PEGAWAI - Hanya lihat data pribadi
        $pegawaiPermissions = [
            'gaji.view_own',
            'gaji.print_slip',
            'absensi.view_own',
            'lembur.view_own',
            'pegawai.view', // Bisa lihat semua pegawai tapi edit sendiri aja
        ];

        $pegawaiPermissionIds = DB::table('permission')
            ->whereIn('nama_permission', $pegawaiPermissions)
            ->pluck('id_permission')
            ->toArray();

        foreach ($pegawaiPermissionIds as $permissionId) {
            DB::table('role_permission')->insertOrIgnore([
                'id_role' => $roleIds['Pegawai'],
                'id_permission' => $permissionId
            ]);
        }

        $this->command->info('Role dan Permission berhasil dibuat!');
        $this->command->line('');
        $this->command->line('Roles yang dibuat:');
        $this->command->line('1. Super Admin - Akses penuh ke seluruh sistem');
        $this->command->line('2. Admin HRD - Semua permission HR & Gaji');
        $this->command->line('3. Manager - Kelola departemen & approve absensi/lembur');
        $this->command->line('4. Direktur - Monitoring & approval gaji');
        $this->command->line('5. Pegawai - Lihat data pribadi');
    }
}
