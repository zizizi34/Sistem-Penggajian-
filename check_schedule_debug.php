<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pegawai = App\Models\Pegawai::where('nama_pegawai', 'like', '%Narendra%')->first();
if ($pegawai) {
    echo "Pegawai: " . $pegawai->nama_pegawai . "\n";
    echo "Departemen: " . $pegawai->departemen->nama_departemen . "\n";
    $jadwal = $pegawai->departemen->jadwalKerja;
    if ($jadwal) {
        echo "Hari: " . $jadwal->hari . "\n";
        echo "Jam Masuk: " . $jadwal->jam_masuk . "\n";
        echo "Jam Pulang: " . $jadwal->jam_pulang . "\n";
        echo "Toleransi: " . $jadwal->toleransi_terlambat . "\n";
    } else {
        echo "Jadwal tidak ditemukan.\n";
    }
} else {
    echo "Pegawai tidak ditemukan.\n";
}
