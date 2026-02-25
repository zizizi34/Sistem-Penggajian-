<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ITCompanyDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Clear existing data to avoid conflicts (optional but better for clean demo)
        // Order matters because of foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('penggajian')->truncate();
        DB::table('pegawai_tunjangan')->truncate();
        DB::table('pegawai_potongan')->truncate();
        DB::table('pegawai')->truncate();
        DB::table('jabatan')->truncate();
        DB::table('departemen')->truncate();
        DB::table('tunjangan')->truncate();
        DB::table('potongan')->truncate();
        DB::table('ptkp_status')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Clearing existing data... Done.');

        // 2. PTKP Status
        $ptkp_statuses = [
            ['kode_ptkp_status' => 'TK/0', 'deskripsi' => 'Tidak Kawin (Sendiri)', 'nominal' => 54000000],
            ['kode_ptkp_status' => 'K/0', 'deskripsi' => 'Kawin Tanpa Anak', 'nominal' => 58500000],
            ['kode_ptkp_status' => 'K/1', 'deskripsi' => 'Kawin 1 Anak', 'nominal' => 63000000],
            ['kode_ptkp_status' => 'K/2', 'deskripsi' => 'Kawin 2 Anak', 'nominal' => 67500000],
            ['kode_ptkp_status' => 'K/3', 'deskripsi' => 'Kawin 3 Anak', 'nominal' => 72000000],
        ];
        foreach ($ptkp_statuses as $status) {
            DB::table('ptkp_status')->insert($status + ['created_at' => now(), 'updated_at' => now()]);
        }
        $ptkp_tk0 = DB::table('ptkp_status')->where('kode_ptkp_status', 'TK/0')->first()->id_ptkp_status;
        $ptkp_k0 = DB::table('ptkp_status')->where('kode_ptkp_status', 'K/0')->first()->id_ptkp_status;

        // 3. Departemen
        $depts = [
            ['nama_departemen' => 'Software Development'],
            ['nama_departemen' => 'Quality Assurance'],
            ['nama_departemen' => 'Infrastructure & Security'],
            ['nama_departemen' => 'Project Management'],
            ['nama_departemen' => 'Human Resources'],
        ];
        $deptIds = [];
        foreach ($depts as $dept) {
            $id = DB::table('departemen')->insertGetId($dept + ['created_at' => now(), 'updated_at' => now()]);
            $deptIds[$dept['nama_departemen']] = $id;
        }

        // 4. Jabatan
        $jabatans = [
            // Software Dev
            ['nama_jabatan' => 'Tech Lead', 'id_departemen' => $deptIds['Software Development'], 'min_gaji' => 15000000, 'max_gaji' => 25000000],
            ['nama_jabatan' => 'Senior Developer', 'id_departemen' => $deptIds['Software Development'], 'min_gaji' => 10000000, 'max_gaji' => 18000000],
            ['nama_jabatan' => 'Junior Developer', 'id_departemen' => $deptIds['Software Development'], 'min_gaji' => 5000000, 'max_gaji' => 8000000],
            // QA
            ['nama_jabatan' => 'QA Lead', 'id_departemen' => $deptIds['Quality Assurance'], 'min_gaji' => 12000000, 'max_gaji' => 20000000],
            ['nama_jabatan' => 'QA Engineer', 'id_departemen' => $deptIds['Quality Assurance'], 'min_gaji' => 6000000, 'max_gaji' => 10000000],
            // Infrastructure
            ['nama_jabatan' => 'DevOps Engineer', 'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' => 12000000, 'max_gaji' => 22000000],
            ['nama_jabatan' => 'System Administrator', 'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' => 8000000, 'max_gaji' => 14000000],
            // PM
            ['nama_jabatan' => 'Project Manager', 'id_departemen' => $deptIds['Project Management'], 'min_gaji' => 15000000, 'max_gaji' => 25000000],
        ];
        $jabatanIds = [];
        foreach ($jabatans as $jab) {
            $id = DB::table('jabatan')->insertGetId($jab + ['created_at' => now(), 'updated_at' => now()]);
            $jabatanIds[$jab['nama_jabatan']] = $id;
        }

        // 5. Tunjangan
        $tunjangans = [
            ['nama_tunjangan' => 'Tunjangan Makan', 'nominal' => 500000],
            ['nama_tunjangan' => 'Tunjangan Transport', 'nominal' => 750000],
            ['nama_tunjangan' => 'Tunjangan Komunikasi', 'nominal' => 300000],
            ['nama_tunjangan' => 'Tunjangan Sertifikasi', 'nominal' => 1000000],
        ];
        $tunjanganIds = [];
        foreach ($tunjangans as $t) {
            $id = DB::table('tunjangan')->insertGetId($t + ['created_at' => now(), 'updated_at' => now()]);
            $tunjanganIds[$t['nama_tunjangan']] = $id;
        }

        // 6. Potongan
        $potongans = [
            ['nama_potongan' => 'BPJS Kesehatan', 'nominal' => 150000],
            ['nama_potongan' => 'BPJS Ketenagakerjaan', 'nominal' => 200000],
            ['nama_potongan' => 'Iuran Pensiun', 'nominal' => 100000],
        ];
        $potonganIds = [];
        foreach ($potongans as $p) {
            $id = DB::table('potongan')->insertGetId($p + ['created_at' => now(), 'updated_at' => now()]);
            $potonganIds[$p['nama_potongan']] = $id;
        }

        // 7. Pegawai
        $pegawais = [
            [
                'nik_pegawai' => '1001',
                'nama_pegawai' => 'Budi Santoso',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1990-05-15',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'no_hp' => '081234567890',
                'email_pegawai' => 'budi.santoso@laguna.com',
                'bank_pegawai' => 'BCA',
                'no_rekening' => '1234567890',
                'npwp' => '123456789012345',
                'id_ptkp_status' => $ptkp_k0,
                'id_jabatan' => $jabatanIds['Tech Lead'],
                'id_departemen' => $deptIds['Software Development'],
                'status_pegawai' => 'aktif',
                'tgl_masuk' => '2020-01-01',
                'gaji_pokok' => 20000000,
            ],
            [
                'nik_pegawai' => '2001',
                'nama_pegawai' => 'Siti Aminah',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1995-08-20',
                'alamat' => 'Jl. Sudirman No. 45, Jakarta',
                'no_hp' => '081298765432',
                'email_pegawai' => 'siti.aminah@laguna.com',
                'bank_pegawai' => 'Mandiri',
                'no_rekening' => '0987654321',
                'npwp' => '543210987654321',
                'id_ptkp_status' => $ptkp_tk0,
                'id_jabatan' => $jabatanIds['QA Engineer'],
                'id_departemen' => $deptIds['Quality Assurance'],
                'status_pegawai' => 'aktif',
                'tgl_masuk' => '2021-03-15',
                'gaji_pokok' => 9000000,
            ],
            [
                'nik_pegawai' => '1002',
                'nama_pegawai' => 'Andi Wijaya',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1998-12-10',
                'alamat' => 'Jl. Gatot Subroto No. 67, Jakarta',
                'no_hp' => '081345678901',
                'email_pegawai' => 'andi.wijaya@laguna.com',
                'bank_pegawai' => 'BNI',
                'no_rekening' => '1122334455',
                'npwp' => '135792468013579',
                'id_ptkp_status' => $ptkp_tk0,
                'id_jabatan' => $jabatanIds['Junior Developer'],
                'id_departemen' => $deptIds['Software Development'],
                'status_pegawai' => 'aktif',
                'tgl_masuk' => '2023-01-10',
                'gaji_pokok' => 7000000,
            ],
            [
                'nik_pegawai' => '3001',
                'nama_pegawai' => 'Rina Wijayanti',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1992-02-28',
                'alamat' => 'Jl. Thamrin No. 89, Jakarta',
                'no_hp' => '081567890123',
                'email_pegawai' => 'rina.w@laguna.com',
                'bank_pegawai' => 'BCA',
                'no_rekening' => '3344556677',
                'npwp' => '246801357924680',
                'id_ptkp_status' => $ptkp_tk0,
                'id_jabatan' => $jabatanIds['DevOps Engineer'],
                'id_departemen' => $deptIds['Infrastructure & Security'],
                'status_pegawai' => 'aktif',
                'tgl_masuk' => '2019-11-01',
                'gaji_pokok' => 16000000,
            ],
        ];

        foreach ($pegawais as $peg) {
            $pegawaiId = DB::table('pegawai')->insertGetId($peg + ['created_at' => now(), 'updated_at' => now()]);
            
            // Assign Tunjangan
            DB::table('pegawai_tunjangan')->insert([
                'id_pegawai' => $pegawaiId,
                'id_tunjangan' => $tunjanganIds['Tunjangan Makan'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::table('pegawai_tunjangan')->insert([
                'id_pegawai' => $pegawaiId,
                'id_tunjangan' => $tunjanganIds['Tunjangan Transport'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Assign Potongan
            DB::table('pegawai_potongan')->insert([
                'id_pegawai' => $pegawaiId,
                'id_potongan' => $potonganIds['BPJS Kesehatan'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::table('pegawai_potongan')->insert([
                'id_pegawai' => $pegawaiId,
                'id_potongan' => $potonganIds['BPJS Ketenagakerjaan'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 8. Create Payroll for Februari 2026
            $totalTunjangan = 500000 + 750000;
            $totalPotongan = 150000 + 200000;
            $gajiBersih = $peg['gaji_pokok'] + $totalTunjangan - $totalPotongan;

            DB::table('penggajian')->insert([
                'id_pegawai' => $pegawaiId,
                'periode' => 'Februari 2026',
                'gaji_pokok' => $peg['gaji_pokok'],
                'total_tunjangan' => $totalTunjangan,
                'total_potongan' => $totalPotongan,
                'lembur' => 0,
                'pajak_pph21' => 0, // Simplified for demo
                'gaji_bersih' => $gajiBersih,
                'tanggal_transfer' => '2026-02-25',
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('IT Company Demo Data successfully populated!');
    }
}
