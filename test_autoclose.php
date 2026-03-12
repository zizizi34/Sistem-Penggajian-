<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$departemenId = 1; // Sesuaikan dengan departemen Narendra
$today = '2026-03-11';
$currentTime = '11:03:33';

$openAttendances = App\Models\Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
    $q->where('id_departemen', $departemenId);
})->whereDate('tanggal_absensi', $today)
  ->whereNull('jam_pulang')
  ->get();

echo "Found " . $openAttendances->count() . " open attendances.\n";

foreach ($openAttendances as $att) {
    $jadwal = \App\Models\JadwalKerja::where('id_departemen', $departemenId)->first();
    $isLembur = \App\Models\Lembur::where('id_pegawai', $att->id_pegawai)
        ->whereDate('tanggal_lembur', $today)
        ->exists();
    
    $batasAbsensi = $isLembur ? '21:00:00' : ($jadwal->jam_pulang ?? '17:00:00');

    echo "Checking Att ID " . $att->id_absensi . ": Now($currentTime) > Batas($batasAbsensi)?\n";

    if ($currentTime > $batasAbsensi) {
        $statusAbsensi = $isLembur ? 'Lembur tetapi Lupa Absen Pulang' : 'Lupa Absen Pulang';
        $jamPulang = $batasAbsensi;
        
        $res = $att->update([
            'jam_pulang' => $jamPulang,
            'status' => $statusAbsensi,
            'keterangan' => '[Test] Auto-close'
        ]);
        
        echo "Update result: " . ($res ? 'SUCCESS' : 'FAILED') . "\n";
    } else {
        echo "Not past deadline yet.\n";
    }
}
