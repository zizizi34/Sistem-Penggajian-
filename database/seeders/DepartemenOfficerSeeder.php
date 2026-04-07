<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departemen;
use App\Models\Officer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DepartemenOfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Software Development' => [
                [
                    'name' => 'Narendra Fatin Fahrezi',
                    'email' => 'naren@gmail.com',
                ],
                [
                    'name' => 'Alysse Orvino Rayhan',
                    'email' => 'vino@gmail.com',
                ],
            ],
            'Quality Assurance' => [
                [
                    'name' => 'Elnoah Agustinus Markus Manalu',
                    'email' => 'elnoah@gmail.com',
                ],
            ],
            'Infrastructure & Security' => [
                [
                    'name' => 'Firmansyah Riza Afifudin',
                    'email' => 'firman@gmail.com',
                ],
            ],
            'Project Management' => [
                [
                    'name' => 'Abdullah Rudi Athaya',
                    'email' => 'abdul@gmail.com',
                ],
            ],
            'Human Resources' => [
                [
                    'name' => 'Oktavian Bagas Nugroho',
                    'email' => 'okta@gmail.com',
                ],
            ],
        ];

        $password = Hash::make('12345678');

        foreach ($data as $deptName => $petugasList) {
            $departemen = Departemen::where('nama_departemen', $deptName)->first();

            if ($departemen) {
                foreach ($petugasList as $petugas) {
                    // Pastikan tidak duplikat email
                    if (Officer::where('email', $petugas['email'])->exists()) {
                        $this->command->warn("Petugas dengan email {$petugas['email']} sudah ada, melewati...");
                        continue;
                    }

                    Officer::create([
                        'id_departemen' => $departemen->id_departemen,
                        'name' => $petugas['name'],
                        'email' => $petugas['email'],
                        'password' => $password, // Password demo: 12345678
                        'phone_number' => '08' . fake()->numerify('##########'),
                    ]);
                }
            }
            else {
                $this->command->warn("Departemen {$deptName} tidak ditemukan!");
            }
        }

        $this->command->info('Berhasil membuat petugas baru untuk departemen terkait.');
    }
}
