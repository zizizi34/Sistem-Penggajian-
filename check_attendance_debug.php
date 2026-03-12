<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pegawai = App\Models\Pegawai::where('nama_pegawai', 'like', '%Narendra%')->first();
if ($pegawai) {
    echo "Pegawai: " . $pegawai->nama_pegawai . " (ID: " . $pegawai->id_pegawai . ")\n";
    $att = $pegawai->absensis()->whereDate('tanggal_absensi', '2026-03-11')->first();
    if ($att) {
        echo "Absensi ID: " . $att->id_absensi . "\n";
        echo "Jam Masuk: " . $att->jam_masuk . "\n";
        echo "Jam Pulang: " . ($att->jam_pulang ?? 'NULL') . "\n";
        echo "Status: " . $att->status . "\n";
        echo "Approved: " . ($att->approved_at ? 'Yes' : 'No') . "\n";
    } else {
        echo "Absensi hari ini tidak ditemukan.\n";
    }
} else {
    echo "Pegawai tidak ditemukan.\n";
}
