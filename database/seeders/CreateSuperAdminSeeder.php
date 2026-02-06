<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dapatkan ID role Super Admin
        $superAdminRole = DB::table('role')->where('nama_role', 'Super Admin')->first();
        
        if (!$superAdminRole) {
            $this->command->error('Role Super Admin tidak ditemukan! Jalankan RoleAndPermissionSeeder terlebih dahulu.');
            return;
        }

        // Cek apakah user super admin sudah ada
        $superAdminUser = DB::table('users')->where('email', 'superadmin@gmail.com')->first();
        
        if ($superAdminUser) {
            // Update password
            DB::table('users')
                ->where('email', 'superadmin@gmail.com')
                ->update([
                    'password' => Hash::make('12345678'),
                    'id_role' => $superAdminRole->id_role,
                    'name' => 'Super Admin',
                    'updated_at' => now()
                ]);
            $this->command->line('✓ User Super Admin diperbaharui: superadmin@gmail.com');
        } else {
            // Buat user baru
            DB::table('users')->insert([
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'id_role' => $superAdminRole->id_role,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->command->line('✓ User Super Admin berhasil dibuat: superadmin@gmail.com');
        }

        $this->command->line('');
        $this->command->line('Kredensial Super Admin:');
        $this->command->line('Email: superadmin@gmail.com');
        $this->command->line('Password: 12345678');
        $this->command->line('');
        $this->command->info('Super Admin memiliki akses penuh ke seluruh sistem!');
    }
}
