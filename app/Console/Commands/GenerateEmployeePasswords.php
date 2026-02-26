<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GenerateEmployeePasswords extends Command
{
    protected $signature = 'employee:generate-passwords';
    protected $description = 'Generate passwords for all employees';

    public function handle()
    {
        $pegawais = DB::table('pegawai')->get();
        $rolePegawai = DB::table('role')->where('nama_role', 'Pegawai')->first();
        $idRole = $rolePegawai ? $rolePegawai->id_role : 5;

        foreach ($pegawais as $p) {
            $eks = DB::table('user')->where('email_user', $p->email_pegawai)->first();
            if (!$eks) {
                DB::table('user')->insert([
                    'email_user' => $p->email_pegawai,
                    'password_user' => Hash::make('password123'),
                    'id_role' => $idRole,
                    'id_pegawai' => $p->id_pegawai,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $this->info('Created user for: ' . $p->email_pegawai);
            } else {
                 DB::table('user')->where('email_user', $p->email_pegawai)->update([
                    'password_user' => Hash::make('password123'),
                ]);
                $this->info('Updated password for: ' . $p->email_pegawai);
            }
        }
        $this->info('All employee passwords set to: password123');
    }
}
