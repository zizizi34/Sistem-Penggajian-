<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

$pegawai = Pegawai::first();
echo "Pegawai ID: " . $pegawai->id_pegawai . "\n";

$departemenId = $pegawai->id_departemen;
$todayCarbon = Carbon::now()->startOfDay();
$yesterday = $todayCarbon->copy()->subDay();

// Ambil jadwal kerja untuk menentukan hari kerja
$jadwalForAlpha = JadwalKerja::where('id_departemen', $departemenId)->first();
$hariKerjaStr = $jadwalForAlpha ? ($jadwalForAlpha->hari ?? 'Senin-Jumat') : 'Senin-Jumat';
echo "Jadwal kerja: " . $hariKerjaStr . "\n";

$workingDaysMap = [
    'senin' => 1, 'selasa' => 2, 'rabu' => 3, 'kamis' => 4,
    'jumat' => 5, 'sabtu' => 6, 'minggu' => 0
];
$allowedDays = [1, 2, 3, 4, 5]; // default Senin-Jumat
$hariStr = strtolower($hariKerjaStr);
if (str_contains($hariStr, '-')) {
    $parts = array_map('trim', explode('-', $hariStr));
    if (count($parts) == 2 && isset($workingDaysMap[$parts[0]]) && isset($workingDaysMap[$parts[1]])) {
        $allowedDays = [];
        $curr = $workingDaysMap[$parts[0]];
        $endDay = $workingDaysMap[$parts[1]];
        while (true) {
            $allowedDays[] = $curr % 7;
            if ($curr % 7 == $endDay % 7)
                break;
            $curr++;
            if ($curr > 14)
                break;
        }
    }
}
echo "Allowed days (numeric): " . implode(',', $allowedDays) . "\n";

$joinDate = $pegawai->tgl_masuk
    ?Carbon::parse($pegawai->tgl_masuk)->startOfDay()
    : $todayCarbon->copy()->subMonths(3)->startOfDay();

$maxLookback = $todayCarbon->copy()->subMonths(6)->startOfDay();
$alphaCheckStart = $joinDate->greaterThan($maxLookback) ? $joinDate : $maxLookback;

$today = now()->format('Y-m-d');
$isLemburToday = \App\Models\Lembur::where('id_pegawai', $pegawai->id_pegawai)->whereDate('tanggal_lembur', $today)->exists();
$batasAbsensi = $isLemburToday ? '21:00:00' : ($jadwalForAlpha->jam_pulang ?? '17:00:00');
$isClosedToday = now()->format('H:i:s') > $batasAbsensi;
$checkUntil = $isClosedToday ? $todayCarbon : $todayCarbon->copy()->subDay();

$output = '';

echo "Alpha Check Start: " . $alphaCheckStart->format('Y-m-d') . "\n";
echo "Check Until: " . $checkUntil->format('Y-m-d') . "\n";

if ($alphaCheckStart->lessThanOrEqualTo($checkUntil)) {
    $existingDates = Absensi::where('id_pegawai', $pegawai->id_pegawai)
        ->whereBetween('tanggal_absensi', [
        $alphaCheckStart->format('Y-m-d'),
        $checkUntil->format('Y-m-d')
    ])
        ->pluck('tanggal_absensi')
        ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
        ->toArray();

    $output .= "Existing dates count: " . count($existingDates) . "\n";

    $checkPeriod = CarbonPeriod::create($alphaCheckStart, $checkUntil);
    $alphaToInsert = [];
    foreach ($checkPeriod as $date) {
        if (!in_array($date->dayOfWeek, $allowedDays)) {
            continue;
        }
        $dateStr = $date->format('Y-m-d');
        if (!in_array($dateStr, $existingDates)) {
            $alphaToInsert[] = $dateStr;
        }
    }

    $output .= "Missing dates to insert as Alpha:\n";
    $output .= print_r($alphaToInsert, true);
}
file_put_contents(__DIR__ . '/test_alpha.txt', $output);
