<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Pegawai;
use App\Models\JadwalKerja;
use App\Models\Absensi;

$user = User::where('email_user', 'elnoah@gmail.com')->first();
if (!$user) {
    die("User not found\n");
}
$pegawai = Pegawai::find($user->id_pegawai);
if (!$pegawai) {
    die("Pegawai not found\n");
}

$jadwal = JadwalKerja::where('id_departemen', $pegawai->id_departemen)->first();
if (!$jadwal) {
    echo "No JadwalKerja found for dept " . $pegawai->id_departemen . "\n";
} else {
    echo "ID Jadwal: " . $jadwal->id_jadwal . "\n";
    echo "Dept ID: " . $jadwal->id_departemen . "\n";
    echo "Jam Masuk: " . $jadwal->jam_masuk . "\n";
    echo "Jam Pulang: " . $jadwal->jam_pulang . "\n";
}

$today = '2026-03-11';
$absensi = Absensi::where('id_pegawai', $pegawai->id_pegawai)->where('tanggal_absensi', $today)->first();
if ($absensi) {
    echo "Today attendance status: " . $absensi->status . "\n";
    echo "Jam Masuk User: " . $absensi->jam_masuk . "\n";
    echo "Jam Pulang User: " . $absensi->jam_pulang . "\n";
    
    // Manual Fix
    $absensi->status = 'Pulang Cepat';
    $absensi->save();
    echo "FIXED status to: " . $absensi->status . "\n";
} else {
    echo "No attendance found for $today\n";
}
