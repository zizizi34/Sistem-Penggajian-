<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;

try {
    $count = Pegawai::count();
    echo "Total Pegawai: $count\n";
    
    // Test one insert
    $pegawai = Pegawai::first();
    if ($pegawai) {
        Absensi::create([
            'id_pegawai' => $pegawai->id_pegawai,
            'tanggal_absensi' => '2026-01-01',
            'status' => 'hadir',
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00'
        ]);
        echo "Test insert success\n";
    } else {
        echo "No pegawai found!\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    // echo $e->getTraceAsString();
}
