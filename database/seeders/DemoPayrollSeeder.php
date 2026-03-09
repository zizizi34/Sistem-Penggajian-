<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * DemoPayrollSeeder
 *
 * Seeder khusus DEMO untuk keperluan presentasi.
 * Menghasilkan data yang terlihat realistis:
 * - 4 bulan data absensi (Nov 2025 – Feb 2026)
 * - Penggajian BULANAN (bukan harian), dibayarkan di AKHIR BULAN
 * - Status "paid" untuk bulan lampau, "pending" untuk bulan berjalan
 * - Lembur beberapa kali per bulan
 * - Data pegawai, jabatan, departemen, tunjangan, potongan
 *
 * Cara jalankan:
 *   php artisan db:seed --class=DemoPayrollSeeder
 */
class DemoPayrollSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=== DEMO PAYROLL SEEDER ===');
        $this->command->info('Membersihkan data lama...');

        // Bersihkan tabel terkait (urutan penting: FK dulu)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('penggajian')->truncate();
        DB::table('lembur')->truncate();
        DB::table('absensi')->truncate();
        DB::table('pegawai_tunjangan')->truncate();
        DB::table('pegawai_potongan')->truncate();
        DB::table('pegawai')->truncate();
        DB::table('jabatan')->truncate();
        DB::table('departemen')->truncate();
        DB::table('tunjangan')->truncate();
        DB::table('potongan')->truncate();
        DB::table('ptkp_status')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Data lama dihapus. Mulai mengisi data demo...');

        // ─────────────────────────────────────────────────────────
        // 1. PTKP STATUS
        // ─────────────────────────────────────────────────────────
        $ptkpStatuses = [
            ['kode_ptkp_status' => 'TK/0', 'deskripsi' => 'Tidak Kawin (Sendiri)',   'nominal' => 54000000],
            ['kode_ptkp_status' => 'K/0',  'deskripsi' => 'Kawin Tanpa Anak',        'nominal' => 58500000],
            ['kode_ptkp_status' => 'K/1',  'deskripsi' => 'Kawin 1 Anak',            'nominal' => 63000000],
            ['kode_ptkp_status' => 'K/2',  'deskripsi' => 'Kawin 2 Anak',            'nominal' => 67500000],
            ['kode_ptkp_status' => 'K/3',  'deskripsi' => 'Kawin 3 Anak',            'nominal' => 72000000],
        ];
        foreach ($ptkpStatuses as $s) {
            DB::table('ptkp_status')->insert($s + ['created_at' => now(), 'updated_at' => now()]);
        }
        $ptkpTK0 = DB::table('ptkp_status')->where('kode_ptkp_status', 'TK/0')->first()->id_ptkp_status;
        $ptkpK0  = DB::table('ptkp_status')->where('kode_ptkp_status', 'K/0')->first()->id_ptkp_status;
        $ptkpK1  = DB::table('ptkp_status')->where('kode_ptkp_status', 'K/1')->first()->id_ptkp_status;

        // ─────────────────────────────────────────────────────────
        // 2. DEPARTEMEN
        // ─────────────────────────────────────────────────────────
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

        // ─────────────────────────────────────────────────────────
        // 3. JABATAN
        // ─────────────────────────────────────────────────────────
        $jabatanList = [
            // Software Development
            ['nama_jabatan' => 'Tech Lead',              'id_departemen' => $deptIds['Software Development'],      'min_gaji' => 18000000, 'max_gaji' => 28000000],
            ['nama_jabatan' => 'Senior Developer',        'id_departemen' => $deptIds['Software Development'],      'min_gaji' => 12000000, 'max_gaji' => 20000000],
            ['nama_jabatan' => 'Software Developer',      'id_departemen' => $deptIds['Software Development'],      'min_gaji' =>  8000000, 'max_gaji' => 15000000],
            ['nama_jabatan' => 'Junior Developer',        'id_departemen' => $deptIds['Software Development'],      'min_gaji' =>  5000000, 'max_gaji' =>  8000000],
            ['nama_jabatan' => 'Frontend Developer',      'id_departemen' => $deptIds['Software Development'],      'min_gaji' =>  7000000, 'max_gaji' => 14000000],
            ['nama_jabatan' => 'Backend Developer',       'id_departemen' => $deptIds['Software Development'],      'min_gaji' =>  7000000, 'max_gaji' => 14000000],
            ['nama_jabatan' => 'Full Stack Developer',    'id_departemen' => $deptIds['Software Development'],      'min_gaji' =>  9000000, 'max_gaji' => 16000000],
            ['nama_jabatan' => 'Mobile Developer',        'id_departemen' => $deptIds['Software Development'],      'min_gaji' =>  8000000, 'max_gaji' => 15000000],
            // Quality Assurance
            ['nama_jabatan' => 'QA Lead',                 'id_departemen' => $deptIds['Quality Assurance'],         'min_gaji' => 14000000, 'max_gaji' => 22000000],
            ['nama_jabatan' => 'Senior QA Engineer',      'id_departemen' => $deptIds['Quality Assurance'],         'min_gaji' => 10000000, 'max_gaji' => 16000000],
            ['nama_jabatan' => 'QA Engineer',             'id_departemen' => $deptIds['Quality Assurance'],         'min_gaji' =>  7000000, 'max_gaji' => 12000000],
            ['nama_jabatan' => 'Junior QA Engineer',      'id_departemen' => $deptIds['Quality Assurance'],         'min_gaji' =>  5000000, 'max_gaji' =>  8000000],
            ['nama_jabatan' => 'Automation Test Engineer','id_departemen' => $deptIds['Quality Assurance'],         'min_gaji' =>  8000000, 'max_gaji' => 14000000],
            ['nama_jabatan' => 'Manual Tester',           'id_departemen' => $deptIds['Quality Assurance'],         'min_gaji' =>  5000000, 'max_gaji' =>  8000000],
            // Infrastructure & Security
            ['nama_jabatan' => 'Infrastructure Manager',  'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' => 18000000, 'max_gaji' => 28000000],
            ['nama_jabatan' => 'DevOps Lead',             'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' => 15000000, 'max_gaji' => 24000000],
            ['nama_jabatan' => 'DevOps Engineer',         'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' => 10000000, 'max_gaji' => 18000000],
            ['nama_jabatan' => 'Cloud Engineer',          'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' => 10000000, 'max_gaji' => 18000000],
            ['nama_jabatan' => 'System Administrator',    'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' =>  7000000, 'max_gaji' => 12000000],
            ['nama_jabatan' => 'Network Engineer',        'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' =>  8000000, 'max_gaji' => 14000000],
            ['nama_jabatan' => 'Security Engineer',       'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' => 10000000, 'max_gaji' => 18000000],
            ['nama_jabatan' => 'Cybersecurity Analyst',   'id_departemen' => $deptIds['Infrastructure & Security'], 'min_gaji' =>  9000000, 'max_gaji' => 16000000],
            // Project Management
            ['nama_jabatan' => 'Head of Project Management','id_departemen' => $deptIds['Project Management'],      'min_gaji' => 22000000, 'max_gaji' => 35000000],
            ['nama_jabatan' => 'Senior Project Manager',  'id_departemen' => $deptIds['Project Management'],        'min_gaji' => 18000000, 'max_gaji' => 28000000],
            ['nama_jabatan' => 'Project Manager',         'id_departemen' => $deptIds['Project Management'],        'min_gaji' => 14000000, 'max_gaji' => 22000000],
            ['nama_jabatan' => 'Scrum Master',            'id_departemen' => $deptIds['Project Management'],        'min_gaji' => 12000000, 'max_gaji' => 20000000],
            ['nama_jabatan' => 'Product Manager',         'id_departemen' => $deptIds['Project Management'],        'min_gaji' => 15000000, 'max_gaji' => 25000000],
            ['nama_jabatan' => 'Product Owner',           'id_departemen' => $deptIds['Project Management'],        'min_gaji' => 14000000, 'max_gaji' => 22000000],
            ['nama_jabatan' => 'Project Coordinator',     'id_departemen' => $deptIds['Project Management'],        'min_gaji' =>  7000000, 'max_gaji' => 12000000],
            // Human Resources
            ['nama_jabatan' => 'HR Manager',              'id_departemen' => $deptIds['Human Resources'],            'min_gaji' => 14000000, 'max_gaji' => 22000000],
            ['nama_jabatan' => 'HR Business Partner',     'id_departemen' => $deptIds['Human Resources'],            'min_gaji' => 12000000, 'max_gaji' => 18000000],
            ['nama_jabatan' => 'HR Specialist',           'id_departemen' => $deptIds['Human Resources'],            'min_gaji' =>  8000000, 'max_gaji' => 13000000],
            ['nama_jabatan' => 'HR Recruiter',            'id_departemen' => $deptIds['Human Resources'],            'min_gaji' =>  6000000, 'max_gaji' => 10000000],
            ['nama_jabatan' => 'HR Officer',              'id_departemen' => $deptIds['Human Resources'],            'min_gaji' =>  6000000, 'max_gaji' =>  9000000],
            ['nama_jabatan' => 'HR Administrator',        'id_departemen' => $deptIds['Human Resources'],            'min_gaji' =>  5000000, 'max_gaji' =>  8000000],
            ['nama_jabatan' => 'Payroll Staff',           'id_departemen' => $deptIds['Human Resources'],            'min_gaji' =>  6000000, 'max_gaji' => 10000000],
            ['nama_jabatan' => 'Payroll Manager',         'id_departemen' => $deptIds['Human Resources'],            'min_gaji' => 12000000, 'max_gaji' => 18000000],
        ];
        $jabatanIds = [];
        foreach ($jabatanList as $jab) {
            $id = DB::table('jabatan')->insertGetId($jab + ['created_at' => now(), 'updated_at' => now()]);
            $jabatanIds[$jab['nama_jabatan']] = $id;
        }

        // ─────────────────────────────────────────────────────────
        // 4. TUNJANGAN
        // ─────────────────────────────────────────────────────────
        $tunjanganList = [
            ['nama_tunjangan' => 'Tunjangan Makan',      'nominal' =>  500000],
            ['nama_tunjangan' => 'Tunjangan Transport',  'nominal' =>  750000],
            ['nama_tunjangan' => 'Tunjangan Komunikasi', 'nominal' =>  300000],
            ['nama_tunjangan' => 'Tunjangan Jabatan',    'nominal' => 1500000],
            ['nama_tunjangan' => 'Tunjangan Sertifikasi','nominal' => 1000000],
        ];
        $tunjanganIds = [];
        foreach ($tunjanganList as $t) {
            $id = DB::table('tunjangan')->insertGetId($t + ['created_at' => now(), 'updated_at' => now()]);
            $tunjanganIds[$t['nama_tunjangan']] = $id;
        }

        // ─────────────────────────────────────────────────────────
        // 5. POTONGAN
        // ─────────────────────────────────────────────────────────
        $potonganList = [
            ['nama_potongan' => 'BPJS Kesehatan',        'nominal' => 150000],
            ['nama_potongan' => 'BPJS Ketenagakerjaan',  'nominal' => 200000],
            ['nama_potongan' => 'Iuran Pensiun',         'nominal' => 100000],
        ];
        $potonganIds = [];
        foreach ($potonganList as $p) {
            $id = DB::table('potongan')->insertGetId($p + ['created_at' => now(), 'updated_at' => now()]);
            $potonganIds[$p['nama_potongan']] = $id;
        }

        // ─────────────────────────────────────────────────────────
        // 6. PEGAWAI
        // ─────────────────────────────────────────────────────────
        $pegawaiData = [
            [
                'nik_pegawai'    => 'EMP-001',
                'nama_pegawai'   => 'Budi Santoso',
                'jenis_kelamin'  => 'L',
                'tanggal_lahir'  => '1990-05-15',
                'alamat'         => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'no_hp'          => '081234567890',
                'email_pegawai'  => 'budi.santoso@perusahaan.com',
                'bank_pegawai'   => 'BCA',
                'no_rekening'    => '1234567890',
                'npwp'           => '12.345.678.9-012.000',
                'id_ptkp_status' => $ptkpK1,
                'id_jabatan'     => $jabatanIds['Tech Lead'],
                'id_departemen'  => $deptIds['Software Development'],
                'status_pegawai' => 'aktif',
                'tgl_masuk'      => '2020-01-02',
                'gaji_pokok'     => 20000000,
                // tunjangan: Makan + Transport + Jabatan + Sertifikasi
                'tunjangan'      => ['Tunjangan Makan', 'Tunjangan Transport', 'Tunjangan Jabatan', 'Tunjangan Sertifikasi'],
                'potongan'       => ['BPJS Kesehatan', 'BPJS Ketenagakerjaan', 'Iuran Pensiun'],
            ],
            [
                'nik_pegawai'    => 'EMP-002',
                'nama_pegawai'   => 'Siti Aminah',
                'jenis_kelamin'  => 'P',
                'tanggal_lahir'  => '1995-08-20',
                'alamat'         => 'Jl. Sudirman No. 45, Jakarta Selatan',
                'no_hp'          => '081298765432',
                'email_pegawai'  => 'siti.aminah@perusahaan.com',
                'bank_pegawai'   => 'Mandiri',
                'no_rekening'    => '0987654321',
                'npwp'           => '54.321.098.7-654.000',
                'id_ptkp_status' => $ptkpTK0,
                'id_jabatan'     => $jabatanIds['QA Engineer'],
                'id_departemen'  => $deptIds['Quality Assurance'],
                'status_pegawai' => 'aktif',
                'tgl_masuk'      => '2021-03-15',
                'gaji_pokok'     => 9000000,
                'tunjangan'      => ['Tunjangan Makan', 'Tunjangan Transport'],
                'potongan'       => ['BPJS Kesehatan', 'BPJS Ketenagakerjaan'],
            ],
            [
                'nik_pegawai'    => 'EMP-003',
                'nama_pegawai'   => 'Andi Wijaya',
                'jenis_kelamin'  => 'L',
                'tanggal_lahir'  => '1998-12-10',
                'alamat'         => 'Jl. Gatot Subroto No. 67, Jakarta Selatan',
                'no_hp'          => '081345678901',
                'email_pegawai'  => 'andi.wijaya@perusahaan.com',
                'bank_pegawai'   => 'BNI',
                'no_rekening'    => '1122334455',
                'npwp'           => '13.579.246.8-013.000',
                'id_ptkp_status' => $ptkpTK0,
                'id_jabatan'     => $jabatanIds['Junior Developer'],
                'id_departemen'  => $deptIds['Software Development'],
                'status_pegawai' => 'aktif',
                'tgl_masuk'      => '2023-01-10',
                'gaji_pokok'     => 7000000,
                'tunjangan'      => ['Tunjangan Makan', 'Tunjangan Transport'],
                'potongan'       => ['BPJS Kesehatan', 'BPJS Ketenagakerjaan'],
            ],
            [
                'nik_pegawai'    => 'EMP-004',
                'nama_pegawai'   => 'Rina Wijayanti',
                'jenis_kelamin'  => 'P',
                'tanggal_lahir'  => '1992-02-28',
                'alamat'         => 'Jl. Thamrin No. 89, Jakarta Pusat',
                'no_hp'          => '081567890123',
                'email_pegawai'  => 'rina.wijayanti@perusahaan.com',
                'bank_pegawai'   => 'BCA',
                'no_rekening'    => '3344556677',
                'npwp'           => '24.680.135.7-924.000',
                'id_ptkp_status' => $ptkpK0,
                'id_jabatan'     => $jabatanIds['DevOps Engineer'],
                'id_departemen'  => $deptIds['Infrastructure & Security'],
                'status_pegawai' => 'aktif',
                'tgl_masuk'      => '2019-11-01',
                'gaji_pokok'     => 16000000,
                'tunjangan'      => ['Tunjangan Makan', 'Tunjangan Transport', 'Tunjangan Jabatan', 'Tunjangan Komunikasi'],
                'potongan'       => ['BPJS Kesehatan', 'BPJS Ketenagakerjaan', 'Iuran Pensiun'],
            ],
            [
                'nik_pegawai'    => 'EMP-005',
                'nama_pegawai'   => 'Deni Kurniawan',
                'jenis_kelamin'  => 'L',
                'tanggal_lahir'  => '1988-07-04',
                'alamat'         => 'Jl. Kuningan No. 12, Jakarta Selatan',
                'no_hp'          => '081678901234',
                'email_pegawai'  => 'deni.kurniawan@perusahaan.com',
                'bank_pegawai'   => 'BRI',
                'no_rekening'    => '5566778899',
                'npwp'           => '97.531.864.2-075.000',
                'id_ptkp_status' => $ptkpK1,
                'id_jabatan'     => $jabatanIds['Project Manager'],
                'id_departemen'  => $deptIds['Project Management'],
                'status_pegawai' => 'aktif',
                'tgl_masuk'      => '2018-06-01',
                'gaji_pokok'     => 18000000,
                'tunjangan'      => ['Tunjangan Makan', 'Tunjangan Transport', 'Tunjangan Jabatan', 'Tunjangan Komunikasi'],
                'potongan'       => ['BPJS Kesehatan', 'BPJS Ketenagakerjaan', 'Iuran Pensiun'],
            ],
        ];

        $pegawaiIds = [];
        foreach ($pegawaiData as $peg) {
            $tunjangan = $peg['tunjangan'];
            $potongan  = $peg['potongan'];
            unset($peg['tunjangan'], $peg['potongan']);

            $pegId = DB::table('pegawai')->insertGetId(array_merge($peg, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $pegawaiIds[] = $pegId;

            // Assign tunjangan
            foreach ($tunjangan as $tNama) {
                DB::table('pegawai_tunjangan')->insert([
                    'id_pegawai'  => $pegId,
                    'id_tunjangan'=> $tunjanganIds[$tNama],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
            // Assign potongan
            foreach ($potongan as $pNama) {
                DB::table('pegawai_potongan')->insert([
                    'id_pegawai' => $pegId,
                    'id_potongan'=> $potonganIds[$pNama],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Data pegawai berhasil dibuat (' . count($pegawaiIds) . ' pegawai).');

        // ─────────────────────────────────────────────────────────
        // 7. BUAT ABSENSI + LEMBUR + PENGGAJIAN PER BULAN
        //
        //    Periode demo: Nov 2025, Des 2025, Jan 2026, Feb 2026
        //    Penggajian = BULANAN, transfer di akhir bulan
        //    Nov–Jan = paid, Feb = pending (bulan berjalan)
        // ─────────────────────────────────────────────────────────
        $periodeList = [
            ['tahun' => 2026, 'bulan' =>  2, 'label' => 'Februari 2026', 'transfer' => '2026-02-27', 'status' => 'paid'],
            ['tahun' => 2026, 'bulan' =>  3, 'label' => 'Maret 2026',    'transfer' => '2026-03-27', 'status' => 'pending'],
        ];

        $jamMasukVariasi = ['07:55:00', '08:00:00', '08:05:00', '08:00:00', '07:58:00', '08:10:00', '08:00:00'];
        $jamPulangVariasi = ['17:00:00', '17:05:00', '17:00:00', '18:00:00', '17:30:00', '17:00:00', '18:30:00'];

        foreach ($periodeList as $periode) {
            $this->command->info("Membuat data untuk periode: {$periode['label']}");

            $startOfMonth = Carbon::create($periode['tahun'], $periode['bulan'], 1);
            $endOfMonth   = $startOfMonth->copy()->endOfMonth();

            foreach ($pegawaiData as $idx => $pegRaw) {
                $pegId     = $pegawaiIds[$idx];
                $gajiPokok = $pegRaw['gaji_pokok'];

                // --- Hitung tunjangan pegawai ini ---
                $totalTunjangan = 0;
                foreach ($pegRaw['tunjangan'] as $tNama) {
                    foreach ($tunjanganList as $t) {
                        if ($t['nama_tunjangan'] === $tNama) {
                            $totalTunjangan += $t['nominal'];
                        }
                    }
                }

                // --- Hitung potongan pegawai ini ---
                $totalPotongan = 0;
                foreach ($pegRaw['potongan'] as $pNama) {
                    foreach ($potonganList as $p) {
                        if ($p['nama_potongan'] === $pNama) {
                            $totalPotongan += $p['nominal'];
                        }
                    }
                }

                // --- Buat absensi harian (Senin-Jumat) ---
                $hariHadir   = 0;
                $hariAlpha   = 0;
                $hariIzin    = 0;
                $denda       = 0;
                $totalLembur = 0; // nominal

                $current = $startOfMonth->copy();
                $batasAkhir = $endOfMonth->copy();
                // Jika bulan dan tahun sama dengan saat ini, batasi sampai H-1 agar hari ini kosong
                if ($periode['tahun'] == now()->year && $periode['bulan'] == now()->month) {
                    $batasAkhir = now()->subDay();
                }

                while ($current->lte($batasAkhir)) {
                    // Lewati Sabtu & Minggu
                    if ($current->isWeekend()) {
                        $current->addDay();
                        continue;
                    }

                    // Tentukan status absensi: 90% hadir, 5% izin, 5% alpha
                    $rand = rand(1, 100);
                    if ($rand <= 90) {
                        // Hadir — variasi jam masuk & pulang
                        $vIdx      = ($idx + $current->day) % count($jamMasukVariasi);
                        $jamMasuk  = $jamMasukVariasi[$vIdx];
                        $jamPulang = $jamPulangVariasi[$vIdx];

                        // Tambah potongan keterlambatan jika telat > 10 menit
                        if ($jamMasuk > '08:10:00') {
                            $denda += 25000;
                        }

                        DB::table('absensi')->insert([
                            'id_pegawai'      => $pegId,
                            'tanggal_absensi' => $current->format('Y-m-d'),
                            'jam_masuk'       => $jamMasuk,
                            'jam_pulang'      => $jamPulang,
                            'status'          => 'hadir',
                            'keterangan'      => null,
                            'created_at'      => $current->format('Y-m-d') . ' ' . $jamMasuk,
                            'updated_at'      => $current->format('Y-m-d') . ' ' . $jamPulang,
                        ]);
                        $hariHadir++;
                    } elseif ($rand <= 95) {
                        // Izin
                        DB::table('absensi')->insert([
                            'id_pegawai'      => $pegId,
                            'tanggal_absensi' => $current->format('Y-m-d'),
                            'jam_masuk'       => null,
                            'jam_pulang'      => null,
                            'status'          => 'izin',
                            'keterangan'      => 'Izin keperluan pribadi',
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                        $hariIzin++;
                    } else {
                        // Alpha — potong gaji harian
                        $dailySalary = $gajiPokok / 22;
                        $denda      += $dailySalary;

                        DB::table('absensi')->insert([
                            'id_pegawai'      => $pegId,
                            'tanggal_absensi' => $current->format('Y-m-d'),
                            'jam_masuk'       => null,
                            'jam_pulang'      => null,
                            'status'          => 'alpha',
                            'keterangan'      => null,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                        $hariAlpha++;
                    }

                    $current->addDay();
                }

                // --- Buat Lembur (2–3 kali per bulan) ---
                $jumlahLembur = rand(2, 3);
                $lemburDays = [5, 12, 19]; // tanggal lembur tetap agar konsisten
                for ($li = 0; $li < $jumlahLembur; $li++) {
                    $tglLemburDay = $lemburDays[$li];
                    $tglLembur = Carbon::create($periode['tahun'], $periode['bulan'], $tglLemburDay);

                    // Pastikan bukan weekend
                    if ($tglLembur->isWeekend()) {
                        $tglLembur->addDays(2);
                    }

                    // Durasi lembur: 1–3 jam
                    $durasiJam = rand(1, 3);
                    $jamMulaiLembur   = '17:00:00';
                    $jamSelesaiLembur = Carbon::parse('17:00:00')->addHours($durasiJam)->format('H:i:s');

                    // Hitung nominal lembur (1.5x jam pertama, 2x sisanya)
                    $hourlyRate = $gajiPokok / 173;
                    if ($durasiJam <= 1) {
                        $nominalLembur = $durasiJam * $hourlyRate * 1.5;
                    } else {
                        $nominalLembur = (1 * $hourlyRate * 1.5) + (($durasiJam - 1) * $hourlyRate * 2);
                    }
                    $totalLembur += $nominalLembur;

                    DB::table('lembur')->insert([
                        'id_pegawai'    => $pegId,
                        'tanggal_lembur'=> $tglLembur->format('Y-m-d'),
                        'jam_mulai'     => $jamMulaiLembur,
                        'jam_selesai'   => $jamSelesaiLembur,
                        'durasi'        => $durasiJam,
                        'keterangan'    => 'Lembur penyelesaian deadline proyek',
                        'status'        => 'approved',
                        'approved_at'   => $tglLembur->format('Y-m-d') . ' 20:00:00',
                        'approved_by'   => 1,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }

                // --- Hitung PPh 21 ---
                // Ambil PTKP
                $ptkpNominal = 54000000; // default TK/0
                if ($pegRaw['id_ptkp_status'] == $ptkpK0) $ptkpNominal = 58500000;
                if ($pegRaw['id_ptkp_status'] == $ptkpK1) $ptkpNominal = 63000000;

                $grossSalary     = $gajiPokok + $totalTunjangan + $totalLembur - $denda;
                $annualGross     = $grossSalary * 12;
                $pkpPerTahun     = max(0, $annualGross - $ptkpNominal);
                $pajakPerTahun   = $this->hitungPPh21($pkpPerTahun);
                $pajakPerBulan  = round($pajakPerTahun / 12, 2);

                // --- Gaji Bersih ---
                $gajiBersih = $grossSalary - $totalPotongan - $pajakPerBulan;

                // --- Simpan Penggajian ---
                DB::table('penggajian')->insert([
                    'id_pegawai'     => $pegId,
                    'periode'        => $periode['label'],
                    'gaji_pokok'     => $gajiPokok,
                    'total_tunjangan'=> $totalTunjangan,
                    'total_potongan' => $totalPotongan + $denda, // potongan + denda alpha/telat
                    'lembur'         => round($totalLembur, 0),
                    'pajak_pph21'    => $pajakPerBulan,
                    'gaji_bersih'    => round($gajiBersih, 0),
                    'tanggal_transfer'=> $periode['transfer'],
                    'status'         => $periode['status'],
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        $this->command->info('');
        $this->command->info('✅ DEMO DATA BERHASIL DIBUAT!');
        $this->command->info('-------------------------------------------');
        $this->command->info('📅 Periode yang dibuat:');
        foreach ($periodeList as $p) {
            $this->command->info("   • {$p['label']} → Transfer: {$p['transfer']} [{$p['status']}]");
        }
        $this->command->info('👥 Pegawai: ' . count($pegawaiIds));
        $this->command->info('-------------------------------------------');
        $this->command->info('💡 Sistem penggajian:');
        $this->command->info('   • Gaji dihitung PER BULAN (bukan per hari)');
        $this->command->info('   • Tanggal transfer = akhir bulan');
        $this->command->info('   • Komponen: Gaji Pokok + Tunjangan + Lembur - Potongan - PPh21');
        $this->command->info('-------------------------------------------');
    }

    /**
     * Hitung PPh 21 berdasarkan PKP tahunan (tarif progresif 2024)
     */
    private function hitungPPh21(float $pkp): float
    {
        if ($pkp <= 0) return 0;

        if ($pkp <= 60_000_000) {
            return $pkp * 0.05;
        } elseif ($pkp <= 250_000_000) {
            return (60_000_000 * 0.05) + (($pkp - 60_000_000) * 0.15);
        } elseif ($pkp <= 500_000_000) {
            return (60_000_000 * 0.05) + (190_000_000 * 0.15) + (($pkp - 250_000_000) * 0.25);
        } elseif ($pkp <= 5_000_000_000) {
            return (60_000_000 * 0.05) + (190_000_000 * 0.15) + (250_000_000 * 0.25) + (($pkp - 500_000_000) * 0.30);
        } else {
            return (60_000_000 * 0.05) + (190_000_000 * 0.15) + (250_000_000 * 0.25) + (4_500_000_000 * 0.30) + (($pkp - 5_000_000_000) * 0.35);
        }
    }
}
