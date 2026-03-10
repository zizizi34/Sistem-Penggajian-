<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pegawai;
use App\Models\Departemen;

$dept = Departemen::where('nama_departemen', 'Infrastructure & Security')->first();
if ($dept) {
    echo "Dept: " . $dept->nama_departemen . " (ID: " . $dept->id_departemen . ")\n";
    $pegawais = Pegawai::where('id_departemen', $dept->id_departemen)->get();
    echo "Total: " . count($pegawais) . " employees\n";
    foreach ($pegawais as $p) {
        echo "- " . $p->nama_pegawai . " | Status: " . $p->status_pegawai . " | ID: " . $p->id_pegawai . "\n";
    }
} else {
    echo "Dept not found\n";
}
