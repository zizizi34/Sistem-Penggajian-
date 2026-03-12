<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;

$pegawais = Pegawai::all();
$outOfRange = [];

foreach ($pegawais as $p) {
    if ($p->gaji_pokok < $p->jabatan->min_gaji || $p->gaji_pokok > $p->jabatan->max_gaji) {
        $outOfRange[] = [
            'id' => $p->id_pegawai,
            'nama' => $p->nama_pegawai,
            'gaji' => $p->gaji_pokok,
            'min' => $p->jabatan->min_gaji,
            'max' => $p->jabatan->max_gaji
        ];
    }
}

if (count($outOfRange) > 0) {
    echo "Found " . count($outOfRange) . " employees with salary out of range:\n";
    foreach ($outOfRange as $o) {
        echo "ID: {$o['id']} | Name: {$o['nama']} | Current: {$o['gaji']} | Range: {$o['min']} - {$o['max']}\n";
    }
} else {
    echo "All employees have salaries within their jabatan's range.\n";
}
