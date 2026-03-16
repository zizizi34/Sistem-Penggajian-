<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;
use App\Models\Absensi;
$pg = Pegawai::first();
Absensi::where('id_pegawai', $pg->id_pegawai)->whereDate('tanggal_absensi', '2026-03-13')->delete();
echo "Deleted 13 March for " . $pg->nama_pegawai . "\n";
