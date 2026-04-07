<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\PtkpStatus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil Role ID untuk Pegawai
        $rolePegawai = DB::table('role')->where('nama_role', 'Pegawai')->first();
        if (!$rolePegawai) {
            $this->command->error("Role 'Pegawai' tidak ditemukan. Harap jalankan RoleAndPermissionSeeder terlebih dahulu.");
            return;
        }

        // 2. Ambil data pendukung
        $jabatans = Jabatan::all();
        $ptkp = PtkpStatus::first(); // Default saja jika ada

        if ($jabatans->isEmpty()) {
            $this->command->error("Data Jabatan kosong. Harap isi data jabatan terlebih dahulu.");
            return;
        }

        $this->command->info("Memulai seeding data pegawai dengan password: 12345678");

        // 3. Buat Pegawai
        $employees = [
            ['nama' => 'Ahmad Fauzi', 'email' => 'ahmadfauzi@gmail.com'],
            ['nama' => 'Budi Santoso', 'email' => 'budisantoso@gmail.com'],
            ['nama' => 'Cahyo Nugroho', 'email' => 'cahyonugroho@gmail.com'],
            ['nama' => 'Dedi Kurniawan', 'email' => 'dedikurniawan@gmail.com'],
            ['nama' => 'Eko Prasetyo', 'email' => 'ekoprasetyo@gmail.com'],
            ['nama' => 'Fajar Ramadhan', 'email' => 'fajarramadhan@gmail.com'],
            ['nama' => 'Gilang Saputra', 'email' => 'gilangsaputra@gmail.com'],
            ['nama' => 'Hendra Wijaya', 'email' => 'hendrawijaya@gmail.com'],
            ['nama' => 'Indra Gunawan', 'email' => 'indragunawan@gmail.com'],
            ['nama' => 'Joko Susanto', 'email' => 'jokosusanto@gmail.com'],
            ['nama' => 'Kurniawan Putra', 'email' => 'kurniawanputra@gmail.com'],
            ['nama' => 'Lukman Hakim', 'email' => 'lukmanhakim@gmail.com'],
            ['nama' => 'Muhammad Rizki', 'email' => 'muhammadrizki@gmail.com'],
            ['nama' => 'Nanda Saputra', 'email' => 'nandasaputra@gmail.com'],
            ['nama' => 'Oki Setiawan', 'email' => 'okisetiawan@gmail.com'],
            ['nama' => 'Putra Pratama', 'email' => 'putrapratama@gmail.com'],
            ['nama' => 'Rizal Maulana', 'email' => 'rizalmaulana@gmail.com'],
            ['nama' => 'Satria Nugraha', 'email' => 'satrianugraha@gmail.com'],
            ['nama' => 'Taufik Hidayat', 'email' => 'taufikhidayat@gmail.com'],
            ['nama' => 'Yusuf Firmansyah', 'email' => 'yusuffirmansyah@gmail.com'],
        ];

        foreach ($employees as $emp) {
            $jabatan = $jabatans->random(); // Ambil jabatan secara acak

            $nik = fake()->unique()->numerify('##########');
            $nama = $emp['nama'];
            $email = $emp['email'];

            $pegawai = Pegawai::updateOrCreate(
                ['email_pegawai' => $email],
                [
                    'id_departemen' => $jabatan->id_departemen,
                    'id_jabatan' => $jabatan->id_jabatan,
                    'id_ptkp_status' => $ptkp ? $ptkp->id_ptkp_status : null,
                    'nik_pegawai' => $nik,
                    'nama_pegawai' => $nama,
                    'jenis_kelamin' => fake()->randomElement(['L', 'P']),
                    'tanggal_lahir' => fake()->date('Y-m-d', '-20 years'),
                    'alamat' => fake()->address(),
                    'no_hp' => '08' . fake()->numerify('##########'),
                    'status_pegawai' => 'aktif',
                    'tgl_masuk' => fake()->date('Y-m-d', '-2 years'),
                    'gaji_pokok' => $jabatan->min_gaji ?? 4000000,
                ]
            );

            // 4. Buat Akun User untuk Pegawai
            User::updateOrCreate(
                ['email_user' => $email],
                [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'id_role' => $rolePegawai->id_role,
                    'password_user' => Hash::make('12345678'),
                ]
            );
        }

        $this->command->info("Selesai! Semua pegawai baru memiliki password: 12345678");
    }
}
