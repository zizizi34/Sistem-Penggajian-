<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;

$pegawais = Pegawai::all();
$adjustedCount = 0;

foreach ($pegawais as $p) {
    $min = $p->jabatan->min_gaji;
    $max = $p->jabatan->max_gaji;
    
    if ($p->gaji_pokok < $min) {
        echo "Adjusting [{$p->nama_pegawai}]: {$p->gaji_pokok} -> {$min} (Min)\n";
        $p->gaji_pokok = $min;
        $p->save();
        $adjustedCount++;
    } elseif ($p->gaji_pokok > $max) {
        echo "Adjusting [{$p->nama_pegawai}]: {$p->gaji_pokok} -> {$max} (Max)\n";
        $p->gaji_pokok = $max;
        $p->save();
        $adjustedCount++;
    }
}

echo "\nTotal adjusted: {$adjustedCount} employees.\n";
