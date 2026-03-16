<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;
use App\Models\Absensi;
$pg = Pegawai::first();
$recs = Absensi::where('id_pegawai', $pg->id_pegawai)
    ->whereIn('tanggal_absensi', ['2026-03-12', '2026-03-13', '2026-03-14', '2026-03-15'])
    ->get();
$out = '';
foreach($recs as $r) {
    $out .= "{$r->tanggal_absensi} -> {$r->status}\n";
}
file_put_contents(__DIR__ . '/test_absen_march.txt', $out);
