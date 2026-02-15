<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\User;

class ReinitializeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Disable FK checks
        Schema::disableForeignKeyConstraints();

        // 2. Truncate tables
        $tables = [
            'absensi',
            'pegawai_potongan',
            'pegawai_tunjangan',
            'penggajian',
            'lembur',
            'pegawai',
            'jabatan',
            'departemen',
            'ptkp_status',
            'tunjangan',
            'potongan',
            'role_permission',
            'user',
            'role',
            'permission',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("Truncated table: $table");
            }
        }

        // 3. Enable FK checks
        Schema::enableForeignKeyConstraints();

        // 4. Run RoleAndPermissionSeeder
        $this->call(RoleAndPermissionSeeder::class);

        // 5. Create Super Admin User
        $superAdminRole = Role::where('nama_role', 'Super Admin')->first();

        if ($superAdminRole) {
            DB::table('user')->insert([
                'email_user' => 'superadmin@gmail.com',
                'password_user' => Hash::make('admin123'),
                'id_role' => $superAdminRole->id_role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Created Super Admin: superadmin@gmail.com / admin123');
        }

        // 6. Create Demo Data (Department, Job, Employee)
        $deptIT = Departemen::create([
            'nama_departemen' => 'Information Technology',
            'manager_departemen' => null
        ]);

        $jabatanStaff = Jabatan::create([
            'nama_jabatan' => 'Staff IT',
            'min_gaji' => 5000000,
            'max_gaji' => 10000000,
            'id_departemen' => $deptIT->id_departemen
        ]);

        $pegawai = Pegawai::create([
            'nik_pegawai' => '12345',
            'nama_pegawai' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'status_pegawai' => 'aktif',
            'tgl_masuk' => now(),
            'gaji_pokok' => 6000000,
            'id_departemen' => $deptIT->id_departemen,
            'id_jabatan' => $jabatanStaff->id_jabatan,
            'email_pegawai' => 'budi@gmail.com',
            'alamat' => 'Jl. Sudirman No. 1'
        ]);

        // 7. Create User for Employee
        $pegawaiRole = Role::where('nama_role', 'Pegawai')->first();
        
        if ($pegawaiRole) {
             DB::table('user')->insert([
                'email_user' => 'budi@gmail.com',
                'password_user' => Hash::make('12345678'),
                'id_role' => $pegawaiRole->id_role,
                'id_pegawai' => $pegawai->id_pegawai,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Created Pegawai User: budi@gmail.com / 12345678');
        }
    }
}
