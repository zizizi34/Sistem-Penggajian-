<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Absensi;
use App\Models\Student;

$student = Student::where('email_user', 'elnoah@gmail.com')->first();
if ($student) {
    $absensi = Absensi::where('id_pegawai', $student->id_pegawai)
                      ->where('tanggal_absensi', '2026-03-11')
                      ->first();
    if ($absensi) {
        $absensi->status = 'Pulang Cepat';
        $absensi->save();
        echo "Status updated to: " . $absensi->status . "\n";
    } else {
        echo "No attendance found for today.\n";
    }
} else {
    echo "Student not found.\n";
}
