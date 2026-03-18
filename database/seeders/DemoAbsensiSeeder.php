<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\Lembur;
use Carbon\Carbon;

class DemoAbsensiSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=== MENGGENERATE DATA ABSENSI DEMO ===');
        
        // Kosongkan tabel absensi dan lembur
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('absensi')->truncate();
        DB::table('lembur')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $pegawais = Pegawai::all();
        $startDate = Carbon::create(2026, 1, 1);
        // Hingga KEMARIN - hari ini akan dihandle oleh masing-masing pegawai saat login
        $endDate = Carbon::now()->subDay()->startOfDay();

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
                // Alpha TIDAK di-insert di sini karena sistem akan auto-detect hari tanpa record sebagai alpha
                if ($rand <= 80) {
                    Absensi::create([
                        'id_pegawai' => $pegawai->id_pegawai,
                        'tanggal_absensi' => $currentDate->format('Y-m-d'),
                        'jam_masuk' => '07:55:00',
                        'jam_pulang' => '17:05:00',
                        'status' => 'hadir',
                    ]);

                    // Ada kemungkinan lembur (15% jika hadir normal)
                    if (rand(1, 100) <= 15) {
                        $durasiLembur = rand(1, 3);
                        $jamMulai = '17:00:00';
                        $jamSelesai = Carbon::parse($jamMulai)->addHours($durasiLembur)->format('H:i:s');
                        
                        Lembur::create([
                            'id_pegawai' => $pegawai->id_pegawai,
                            'tanggal_lembur' => $currentDate->format('Y-m-d'),
                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                            'durasi' => $durasiLembur,
                            'keterangan' => 'Lembur penyelesaian tugas tambahan',
                            'status' => 'approved',
                            'approved_at' => $currentDate->format('Y-m-d') . ' 20:00:00',
                            'approved_by' => 1, // Super Admin
                        ]);
                    }
                } elseif ($rand <= 85) {
                    Absensi::create([
                        'id_pegawai' => $pegawai->id_pegawai,
                        'tanggal_absensi' => $currentDate->format('Y-m-d'),
                        'jam_masuk' => '08:20:00', // Terlambat
                        'jam_pulang' => '17:00:00',
                        'status' => 'hadir', // Tetap hadir, tapi terlambat
                        'keterangan' => 'Terlambat',
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
                }
                // else: 5% Alpha - TIDAK di-insert, biarkan sistem auto-detect
                
                $currentDate->addDay();
            }
        }

        // Hapus history penggajian agar HR bisa hitung ulang
        DB::table('penggajian')->truncate();

        $this->command->info('✅ Data absensi dan lembur dari 1 Jan 2026 s/d kemarin (' . $endDate->format('d M Y') . ') berhasil digenerate.');
        $this->command->info('✅ Tgl Masuk pegawai di-set ke 1 Jan 2026.');
        $this->command->info('✅ Alpha tidak di-insert - sistem akan auto-detect saat pegawai login.');
        $this->command->info('✅ Data penggajian di-reset. Silakan ke menu Penggajian -> Hitung Gaji kembali.');
    }
}

