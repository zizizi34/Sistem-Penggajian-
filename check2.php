<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK PERIODE LABEL DI DATABASE ===\n";
$periods = DB::table('penggajian')->select('periode')->distinct()->orderBy('periode')->get();
foreach ($periods as $p) {
    echo "  '{$p->periode}'\n";
}

echo "\n=== CEK KONVERSI toPeriodeLabel ===\n";
$bulanId = [
    1=>'Januari', 2=>'Februari', 3=>'Maret',    4=>'April',
    5=>'Mei',     6=>'Juni',     7=>'Juli',      8=>'Agustus',
    9=>'September',10=>'Oktober',11=>'November', 12=>'Desember',
];
$tests = ['2025-11', '2025-12', '2026-01', '2026-02', '2026-03'];
foreach ($tests as $raw) {
    $dt = Carbon\Carbon::createFromFormat('Y-m', $raw);
    $label = $bulanId[(int)$dt->format('n')] . ' ' . $dt->format('Y');
    echo "  '$raw' → '$label'\n";
}
