<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JadwalKerja;
$out = '';
foreach(JadwalKerja::all() as $j){
    $out .= "Dept: {$j->id_departemen}, Hari: {$j->hari}, Jam: {$j->jam_masuk} - {$j->jam_pulang}\n";
}
file_put_contents(__DIR__ . '/test_jadwal.txt', $out);
