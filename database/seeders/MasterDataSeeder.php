<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== MASTER DATA SEEDER ===');

        // 1. PTKP STATUS
        $ptkpStatuses = [
            ['kode_ptkp_status' => 'TK/0', 'deskripsi' => 'Tidak Kawin (Sendiri)',   'nominal' => 54000000],
            ['kode_ptkp_status' => 'K/0',  'deskripsi' => 'Kawin Tanpa Anak',        'nominal' => 58500000],
            ['kode_ptkp_status' => 'K/1',  'deskripsi' => 'Kawin 1 Anak',            'nominal' => 63000000],
            ['kode_ptkp_status' => 'K/2',  'deskripsi' => 'Kawin 2 Anak',            'nominal' => 67500000],
            ['kode_ptkp_status' => 'K/3',  'deskripsi' => 'Kawin 3 Anak',            'nominal' => 72000000],
        ];
        foreach ($ptkpStatuses as $s) {
            DB::table('ptkp_status')->updateOrInsert(['kode_ptkp_status' => $s['kode_ptkp_status']], $s + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 2. DEPARTEMEN
        $depts = [
            'Software Development',
            'Quality Assurance',
            'Infrastructure & Security',
            'Project Management',
            'Human Resources',
        ];
        $deptIds = [];
        foreach ($depts as $nama) {
            $id = DB::table('departemen')->insertGetId([
                'nama_departemen' => $nama,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $deptIds[$nama] = $id;
        }

        // 3. JABATAN
        $jabatanList = [
            ['nama_jabatan' => 'Tech Lead',              'id_departemen' => $deptIds['Software Development'],      'min_gaji' => 9000000,  'max_gaji' => 13000000],
            ['nama_jabatan' => 'Senior Developer',        'id_departemen' => $deptIds['Software Development'],      'min_gaji' => 6000000,  'max_gaji' => 9000000],
            ['nama_jabatan' => 'Software Developer',      'id_departemen' => $deptIds['Software Development'],      'min_gaji' => 4000000,  'max_gaji' => 6000000],
            ['nama_jabatan' => 'Junior Developer',        'id_departemen' => $deptIds['Software Development'],      'min_gaji' => 4000000,  'max_gaji' => 6000000],
            ['nama_jabatan' => 'QA Lead',                 'id_departemen' => $deptIds['Quality Assurance'],         'min_gaji' => 9000000,  'max_gaji' => 13000000],
            ['nama_jabatan' => 'QA Engineer',             'id_departemen' => $deptIds['Quality Assurance'],         'min_gaji' => 4000000,  'max_gaji' => 6000000],
            ['nama_jabatan' => 'DevOps Engineer',         'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' => 4000000,  'max_gaji' => 6000000],
            ['nama_jabatan' => 'Project Manager',         'id_departemen' => $deptIds['Project Management'],        'min_gaji' => 13000000, 'max_gaji' => 18000000],
            ['nama_jabatan' => 'HR Officer',              'id_departemen' => $deptIds['Human Resources'],            'min_gaji' => 4000000,  'max_gaji' => 6000000],
        ];
        foreach ($jabatanList as $jab) {
            DB::table('jabatan')->insert($jab + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 4. TUNJANGAN
        $tunjanganList = [
            ['nama_tunjangan' => 'Tunjangan Makan',      'nominal' =>  500000],
            ['nama_tunjangan' => 'Tunjangan Transport',  'nominal' =>  750000],
            ['nama_tunjangan' => 'Tunjangan Komunikasi', 'nominal' =>  300000],
        ];
        foreach ($tunjanganList as $t) {
            DB::table('tunjangan')->updateOrInsert(['nama_tunjangan' => $t['nama_tunjangan']], $t + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 5. POTONGAN
        $potonganList = [
            ['nama_potongan' => 'BPJS Kesehatan',        'nominal' => 150000],
            ['nama_potongan' => 'BPJS Ketenagakerjaan',  'nominal' => 200000],
        ];
        foreach ($potonganList as $p) {
            DB::table('potongan')->updateOrInsert(['nama_potongan' => $p['nama_potongan']], $p + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 6. JADWAL KERJA
        foreach ($deptIds as $nama => $id) {
            DB::table('jadwal_kerja')->updateOrInsert(['id_departemen' => $id], [
                'hari' => 'Senin-Jumat',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Master data berhasil diisi.');
    }
}
