<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\Officer;

$officer = Officer::where('email', 'naren@gmail.com')->first();
$deptId = $officer->id_departemen;

echo "Officer Dept ID: " . $deptId . "\n";
echo "Total Absensi: " . Absensi::count() . "\n";

$count = Absensi::whereHas('pegawai', function ($q) use ($deptId) {
    $q->where('id_departemen', $deptId);
})->count();

echo "Absensi in Dept " . $deptId . ": " . $count . "\n";

$pegawaiSuggest = Pegawai::where('id_departemen', $deptId)->pluck('id_pegawai')->toArray();
echo "Pegawai IDs in Dept " . $deptId . ": " . implode(', ', $pegawaiSuggest) . "\n";

$sampleAbsensi = Absensi::whereIn('id_pegawai', $pegawaiSuggest)->limit(1)->first();
if ($sampleAbsensi) {
    echo "Sample Absensi ID: " . $sampleAbsensi->id_absensi . " Date: " . $sampleAbsensi->tanggal_absensi . "\n";
} else {
    echo "No sample absensi found for these pegawais\n";
}
