<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Change ENUM to VARCHAR to support 'Pulang Cepat' and other statuses
    DB::statement("ALTER TABLE absensi MODIFY status VARCHAR(255)");
    echo "SUCCESS: status column changed to VARCHAR(255)\n";
    
    // Now retry the update for today's record
    $now = '2026-03-11';
    $updated = DB::table('absensi')->where('tanggal_absensi', $now)->update(['status' => 'Pulang Cepat']);
    echo "Updated $updated record(s) for $now to 'Pulang Cepat'\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
