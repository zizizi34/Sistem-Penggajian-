<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Absensi;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            // Gunakan delete() alih-alih truncate() untuk menghindari masalah foreign key lock pada beberapa driver
            DB::table('absensi')->delete();

            $pegawais = Pegawai::all();
            if ($pegawais->isEmpty()) {
                $this->command->warn('Tidak ada data pegawai. Silakan jalankan seeder pegawai terlebih dahulu.');
                return;
            }

            // Rentang waktu: Januari 2026 - Maret 2026
            $startDate = Carbon::create(2026, 1, 1);
            $endDate = Carbon::create(2026, 3, 31);
            $period = CarbonPeriod::create($startDate, $endDate);

            $this->command->info('Memulai seeding data absensi (Januari - Maret 2026)...');

            $absensiData = [];
            $totalInserted = 0;

            foreach ($period as $date) {
                // Lewati hari Minggu (perusahaan libur)
                if ($date->dayOfWeek === Carbon::SUNDAY) {
                    continue;
                }

                foreach ($pegawais as $pegawai) {
                    $rand = rand(1, 100);
                    
                    $status = 'hadir';
                    $jamMasuk = null;
                    $jamPulang = null;
                    $keterangan = null;

                    if ($rand > 95) { // 5% Alpha
                        $status = 'alpha';
                        $keterangan = 'Tanpa Keterangan';
                    } elseif ($rand > 90) { // 5% Izin
                        $status = 'izin';
                        $keterangan = 'Izin Keperluan Mendesak';
                    } elseif ($rand > 85) { // 5% Sakit (Hadir tapi ada keterangan)
                        $status = 'hadir';
                        $keterangan = 'Sakit (Surat Dokter Terlampir)';
                        // Anggap jam masuk/pulang kosong atau tetap ada? Biasanya kalau sakit tidak hadir tapi di sistem ini mungkin 'hadir' statusnya khusus.
                        // Namun biasanya sakit itu status tersendiri. Jika di sistem hanya ada hadir/izin/alpha, maka sakit masuk izin/alpha.
                        // Berdasarkan request: "izin, alpha dan masuk". Jadi saya gunakan itu.
                        $status = 'izin';
                        $keterangan = 'Sakit';
                    } else {
                        $status = 'hadir';
                        // Jam masuk antara 07:15 - 08:30 (beberapa terlambat)
                        $h = 7;
                        $m = rand(15, 59);
                        if (rand(1, 10) > 8) { // 20% probabilitas terlambat
                            $h = 8;
                            $m = rand(1, 30);
                        }
                        $jamMasuk = Carbon::createFromTime($h, $m, rand(0, 59))->format('H:i:s');
                        // Jam pulang antara 17:00 - 18:30
                        $jamPulang = Carbon::createFromTime(17, rand(0, 90), rand(0, 59))->format('H:i:s');
                    }

                    $absensiData[] = [
                        'id_pegawai' => $pegawai->id_pegawai,
                        'tanggal_absensi' => $date->format('Y-m-d'),
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Insert per batch 200 record untuk efisiensi memori
                    if (count($absensiData) >= 200) {
                        DB::table('absensi')->insert($absensiData);
                        $totalInserted += count($absensiData);
                        $absensiData = [];
                    }
                }
            }

            // Insert sisa data
            if (count($absensiData) > 0) {
                DB::table('absensi')->insert($absensiData);
                $totalInserted += count($absensiData);
            }

            $this->command->info("Seeding selesai! Total $totalInserted data absensi berhasil ditambahkan.");
        } catch (\Exception $e) {
            $this->command->error('Gagal menjalankan seeder: ' . $e->getMessage());
        }
    }
}
