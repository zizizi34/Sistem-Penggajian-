<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $updated = DB::table('absensi')->where('id_absensi', 132)->update(['status' => 'Pulang Cepat']);
    echo "Update result: $updated\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
