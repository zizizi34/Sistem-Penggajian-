<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Jabatan;

$rules = [
    1 => ['min' => 4000000, 'max' => 6000000],
    2 => ['min' => 6000000, 'max' => 9000000],
    3 => ['min' => 9000000, 'max' => 13000000],
    4 => ['min' => 13000000, 'max' => 18000000],
    5 => ['min' => 18000000, 'max' => 25000000],
];

function getLevel($name) {
    if (preg_match('/Head|Director/i', $name)) return 5;
    if (preg_match('/Manager/i', $name)) return 4;
    if (preg_match('/Lead/i', $name)) return 3;
    if (preg_match('/Specialist/i', $name)) return 3;
    if (preg_match('/Senior/i', $name)) return 2;
    return 1;
}

$jabatans = Jabatan::all();
$updatedCount = 0;

foreach ($jabatans as $j) {
    $level = getLevel($j->nama_jabatan);
    $salary = $rules[$level];
    
    $j->min_gaji = $salary['min'];
    $j->max_gaji = $salary['max'];
    $j->save();
    
    echo "Updated [{$j->nama_jabatan}] -> Level {$level} (Min: {$salary['min']}, Max: {$salary['max']})\n";
    $updatedCount++;
}

echo "\nTotal updated: {$updatedCount} records.\n";
