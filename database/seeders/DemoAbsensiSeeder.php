<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pegawai;
use App\Models\Absensi;
use Carbon\Carbon;

class DemoAbsensiSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=== MENGGENERATE DATA ABSENSI DEMO ===');
        
        // Kosongkan tabel absensi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('absensi')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $pegawais = Pegawai::all();
        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::create(2026, 2, 28); // Hingga akhir februari

        foreach ($pegawais as $pegawai) {
            // Mundurkan tgl_masuk ke 1 Januari 2026 
            $pegawai->tgl_masuk = '2026-01-01';
            $pegawai->save();

            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Lewati sabtu dan minggu
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                $rand = rand(1, 100);
                
                // Distribusi: 80% Hadir, 5% Terlambat, 5% Izin, 5% Sakit, 5% Alpha
                if ($rand <= 80) {
                    Absensi::create([
                        'id_pegawai' => $pegawai->id_pegawai,
                        'tanggal_absensi' => $currentDate->format('Y-m-d'),
                        'jam_masuk' => '07:55:00',
                        'jam_pulang' => '17:05:00',
                        'status' => 'hadir',
                    ]);
                } elseif ($rand <= 85) {
                    Absensi::create([
                        'id_pegawai' => $pegawai->id_pegawai,
                        'tanggal_absensi' => $currentDate->format('Y-m-d'),
                        'jam_masuk' => '08:20:00', // Terlambat
                        'jam_pulang' => '17:00:00',
                        'status' => 'terlambat',
                    ]);
                } elseif ($rand <= 90) {
                    Absensi::create([
                        'id_pegawai' => $pegawai->id_pegawai,
                        'tanggal_absensi' => $currentDate->format('Y-m-d'),
                        'status' => 'izin',
                        'keterangan' => 'Keperluan keluarga',
                    ]);
                } elseif ($rand <= 95) {
                    Absensi::create([
                        'id_pegawai' => $pegawai->id_pegawai,
                        'tanggal_absensi' => $currentDate->format('Y-m-d'),
                        'status' => 'sakit',
                        'keterangan' => 'Demam dan flu',
                    ]);
                } else {
                    // Alpha (Bisa record ke DB dengan status alpha, atau dikosongkan)
                    // Kita insert sebagai alpha agar eksplisit terlihat di history
                    Absensi::create([
                        'id_pegawai' => $pegawai->id_pegawai,
                        'tanggal_absensi' => $currentDate->format('Y-m-d'),
                        'status' => 'alpha',
                        'keterangan' => 'Tanpa Keterangan',
                    ]);
                }
                
                $currentDate->addDay();
            }
        }

        // Hapus history penggajian bulan Februari dan Maret yang masih pending agar HR bisa hitung ulang
        DB::table('penggajian')->truncate();

        $this->command->info('✅ Data absensi sebulan terakhir berhasil digenerate.');
        $this->command->info('✅ Tgl Masuk pegawai di-set ke 1 Jan 2026.');
        $this->command->info('✅ Data penggajian di-reset. Silakan ke menu Penggajian -> Hitung Gaji kembali.');
    }
}
