<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk Role dan Permission - Production Ready
 * 
 * Role Structure (3 Role Utama):
 * 1. Super Admin - Akses penuh ke seluruh sistem
 * 2. Petugas (Officer) - Akses hanya data departemen sendiri
 * 3. Pegawai (Employee) - Self-service data pribadi saja
 * 
 * @author Your Name
 * @version 1.0
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
            ['nama_permission' => 'gaji.view', 'deskripsi' => 'Lihat data penggajian semua', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.view_own', 'deskripsi' => 'Lihat slip gaji pribadi', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.view_dept', 'deskripsi' => 'Lihat gaji departemen sendiri', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.create', 'deskripsi' => 'Buat perhitungan gaji', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.edit', 'deskripsi' => 'Edit perhitungan gaji', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.delete', 'deskripsi' => 'Hapus perhitungan gaji', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.approve', 'deskripsi' => 'Approve perhitungan gaji', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.print_slip', 'deskripsi' => 'Print slip gaji', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.print_slip_dept', 'deskripsi' => 'Print slip gaji departemen sendiri', 'kategori' => 'penggajian'],
            ['nama_permission' => 'gaji.export_dept', 'deskripsi' => 'Export gaji departemen sendiri', 'kategori' => 'penggajian'],

            // ============= KATEGORI: ABSENSI =============
            ['nama_permission' => 'absensi.view', 'deskripsi' => 'Lihat data absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.view_own', 'deskripsi' => 'Lihat absensi pribadi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.view_all_dept', 'deskripsi' => 'Lihat semua absensi departemen sendiri', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.create', 'deskripsi' => 'Input absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.edit', 'deskripsi' => 'Edit absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.delete', 'deskripsi' => 'Hapus absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.approve', 'deskripsi' => 'Approve absensi', 'kategori' => 'absensi'],

            // ============= KATEGORI: LEMBUR =============
            ['nama_permission' => 'lembur.view', 'deskripsi' => 'Lihat data lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.view_own', 'deskripsi' => 'Lihat lembur pribadi', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.view_all_dept', 'deskripsi' => 'Lihat semua lembur departemen sendiri', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.create', 'deskripsi' => 'Input lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.edit', 'deskripsi' => 'Edit lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.delete', 'deskripsi' => 'Hapus lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.approve', 'deskripsi' => 'Approve lembur', 'kategori' => 'lembur'],

            // ============= KATEGORI: PEGAWAI =============
            ['nama_permission' => 'pegawai.view', 'deskripsi' => 'Lihat data pegawai semua', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.view_dept', 'deskripsi' => 'Lihat data pegawai departemen sendiri', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.create', 'deskripsi' => 'Tambah pegawai baru', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.edit', 'deskripsi' => 'Edit data pegawai', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.delete', 'deskripsi' => 'Hapus pegawai', 'kategori' => 'pegawai'],

            // ============= KATEGORI: TUNJANGAN =============
            ['nama_permission' => 'tunjangan.view', 'deskripsi' => 'Lihat data tunjangan', 'kategori' => 'tunjangan'],
            ['nama_permission' => 'tunjangan.create', 'deskripsi' => 'Buat tunjangan baru', 'kategori' => 'tunjangan'],
            ['nama_permission' => 'tunjangan.edit', 'deskripsi' => 'Edit tunjangan', 'kategori' => 'tunjangan'],
            ['nama_permission' => 'tunjangan.delete', 'deskripsi' => 'Hapus tunjangan', 'kategori' => 'tunjangan'],
            ['nama_permission' => 'tunjangan.assign', 'deskripsi' => 'Berikan tunjangan ke pegawai', 'kategori' => 'tunjangan'],

            // ============= KATEGORI: POTONGAN =============
            ['nama_permission' => 'potongan.view', 'deskripsi' => 'Lihat data potongan', 'kategori' => 'potongan'],
            ['nama_permission' => 'potongan.create', 'deskripsi' => 'Buat potongan baru', 'kategori' => 'potongan'],
            ['nama_permission' => 'potongan.edit', 'deskripsi' => 'Edit potongan', 'kategori' => 'potongan'],
            ['nama_permission' => 'potongan.delete', 'deskripsi' => 'Hapus potongan', 'kategori' => 'potongan'],
            ['nama_permission' => 'potongan.assign', 'deskripsi' => 'Berikan potongan ke pegawai', 'kategori' => 'potongan'],

            // ============= KATEGORI: DEPARTEMEN =============
            ['nama_permission' => 'departemen.view', 'deskripsi' => 'Lihat departemen', 'kategori' => 'departemen'],
            ['nama_permission' => 'departemen.create', 'deskripsi' => 'Buat departemen', 'kategori' => 'departemen'],
            ['nama_permission' => 'departemen.edit', 'deskripsi' => 'Edit departemen', 'kategori' => 'departemen'],
            ['nama_permission' => 'departemen.delete', 'deskripsi' => 'Hapus departemen', 'kategori' => 'departemen'],

            // ============= KATEGORI: JABATAN =============
            ['nama_permission' => 'jabatan.view', 'deskripsi' => 'Lihat jabatan', 'kategori' => 'jabatan'],
            ['nama_permission' => 'jabatan.create', 'deskripsi' => 'Buat jabatan', 'kategori' => 'jabatan'],
            ['nama_permission' => 'jabatan.edit', 'deskripsi' => 'Edit jabatan', 'kategori' => 'jabatan'],
            ['nama_permission' => 'jabatan.delete', 'deskripsi' => 'Hapus jabatan', 'kategori' => 'jabatan'],

            // ============= KATEGORI: LAPORAN =============
            ['nama_permission' => 'laporan.view', 'deskripsi' => 'Lihat laporan', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.view_own', 'deskripsi' => 'Lihat laporan departemen sendiri', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.gaji', 'deskripsi' => 'Lihat laporan gaji', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.dept_gaji', 'deskripsi' => 'Lihat laporan gaji departemen', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.absensi', 'deskripsi' => 'Lihat laporan absensi', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.dept_absensi', 'deskripsi' => 'Lihat laporan absensi departemen', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.lembur', 'deskripsi' => 'Lihat laporan lembur', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.dept_lembur', 'deskripsi' => 'Lihat laporan lembur departemen', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.export', 'deskripsi' => 'Export laporan', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.export_own', 'deskripsi' => 'Export laporan departemen sendiri', 'kategori' => 'laporan'],

            // ============= KATEGORI: PROFILE & ACCOUNT =============
            ['nama_permission' => 'profile.edit_own', 'deskripsi' => 'Edit profil pribadi', 'kategori' => 'profile'],
            ['nama_permission' => 'profile.view_all', 'deskripsi' => 'Lihat semua profil', 'kategori' => 'profile'],

            // ============= KATEGORI: SYSTEM & MAINTENANCE =============
            ['nama_permission' => 'system.settings', 'deskripsi' => 'Akses pengaturan sistem', 'kategori' => 'system'],
            ['nama_permission' => 'system.user_management', 'deskripsi' => 'Manajemen user', 'kategori' => 'system'],
            ['nama_permission' => 'system.role_permission', 'deskripsi' => 'Manajemen role & permission', 'kategori' => 'system'],
            ['nama_permission' => 'system.audit_log', 'deskripsi' => 'Lihat activity log', 'kategori' => 'system'],
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            DB::table('permission')->insertOrIgnore($permission);
        }

        // Data Role yang akan dibuat (Production Ready - 3 Role Utama)
        $roles = [
            [
                'nama_role' => 'Super Admin',
                'deskripsi' => 'Super Administrator - Akses penuh ke seluruh sistem tanpa batasan'
            ],
            [
                'nama_role' => 'Petugas',
                'deskripsi' => 'Petugas HR/Departemen - Akses hanya data departemen sendiri, input absensi & lembur, approve dalam batas'
            ],
            [
                'nama_role' => 'Pegawai',
                'deskripsi' => 'Pegawai - Self-service: lihat data pribadi, absensi, lembur, slip gaji, tidak ada edit'
            ],
        ];

        // Insert roles
        $roleIds = [];
        foreach ($roles as $role) {
            // Check if role already exists
            $existing = DB::table('role')->where('nama_role', $role['nama_role'])->first();
            if ($existing) {
                $roleIds[$role['nama_role']] = $existing->id_role;
            } else {
                $id = DB::table('role')->insertGetId($role);
                $roleIds[$role['nama_role']] = $id;
            }
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

        // 2. PETUGAS (OFFICER) - Hanya dapat:
        // - Input & approve absensi untuk departemen sendiri
        // - Input & approve lembur untuk departemen sendiri
        // - Lihat data pegawai di departemen sendiri
        // - Lihat data gaji (READONLY - tidak bisa edit/approve)
        // - Lihat laporan departemen sendiri
        $petugasPermissions = [
            // Absensi - bisa input & approve departemen sendiri
            'absensi.view',
            'absensi.create',
            'absensi.edit',
            'absensi.approve',
            'absensi.view_all_dept',      // Untuk lihat all absensi di dept
            
            // Lembur - bisa input & approve departemen sendiri
            'lembur.view',
            'lembur.create',
            'lembur.edit',
            'lembur.approve',
            'lembur.view_all_dept',        // Untuk lihat all lembur di dept
            
            // Pegawai - lihat data departemen sendiri
            'pegawai.view_dept',
            
            // Gaji - hanya view (readonly)
            'gaji.view_dept',
            'gaji.print_slip_dept',
            'gaji.export_dept',
            
            // Laporan - departemen sendiri
            'laporan.view_own',
            'laporan.dept_absensi',
            'laporan.dept_lembur',
            'laporan.dept_gaji',
            'laporan.export_own',
        ];

        $petugasPermissionIds = DB::table('permission')
            ->whereIn('nama_permission', $petugasPermissions)
            ->pluck('id_permission')
            ->toArray();

        foreach ($petugasPermissionIds as $permissionId) {
            DB::table('role_permission')->insertOrIgnore([
                'id_role' => $roleIds['Petugas'],
                'id_permission' => $permissionId
            ]);
        }

        // 3. PEGAWAI (EMPLOYEE) - Self-Service hanya lihat data pribadi
        // - Lihat absensi pribadi
        // - Lihat lembur pribadi
        // - Lihat slip gaji pribadi & rincian tunjangan/potongan
        // - Edit profil pribadi (limited fields)
        $pegawaiPermissions = [
            'absensi.view_own',        // Lihat absensi pribadi
            'lembur.view_own',         // Lihat lembur pribadi
            'gaji.view_own',           // Lihat slip gaji pribadi
            'profile.edit_own',        // Edit profil pribadi
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

        $this->command->info('=== SUCCESS: Role & Permission Setup Selesai ===');
        $this->command->line('');
        $this->command->line('3 Roles telah dibuat:');
        $this->command->line('');
        $this->command->line('1. 👑 SUPER ADMIN');
        $this->command->line('   └─ Akses penuh ke seluruh sistem tanpa batasan');
        $this->command->line('   └─ Menu: Dashboard, User Mgmt, Data Master (All), Penggajian, Laporan, System');
        $this->command->line('');
        $this->command->line('2. 👨‍💼 PETUGAS (Officer)');
        $this->command->line('   └─ Akses hanya data departemen sendiri');
        $this->command->line('   └─ Bisa: Input & approve absensi, lembur');
        $this->command->line('   └─ Tidak bisa: Edit gaji, system setup');
        $this->command->line('   └─ Menu: Dashboard, My Team, Absensi, Lembur, My Reports');
        $this->command->line('');
        $this->command->line('3. 👤 PEGAWAI (Employee)');
        $this->command->line('   └─ Self-service: Data pribadi, absensi, lembur, slip gaji');
        $this->command->line('   └─ Tidak bisa: Lihat data karyawan lain, edit gaji');
        $this->command->line('   └─ Menu: Dashboard, My Profile, My Attendance, My Overtime, My Salary');
        $this->command->line('');
        $this->command->info('Seeder siap untuk production!');
    }
}
