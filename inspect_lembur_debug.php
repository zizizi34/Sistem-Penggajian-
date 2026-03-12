<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lembur = App\Models\Lembur::where('id_pegawai', 6)->whereDate('tanggal_lembur', '2026-03-11')->first();
if ($lembur) {
    print_r($lembur->toArray());
} else {
    echo "No lembur record found for Narendra today.\n";
}
