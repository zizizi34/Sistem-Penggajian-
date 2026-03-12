<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Jabatan;

$jabatans = Jabatan::all(['id_jabatan', 'nama_jabatan', 'min_gaji', 'max_gaji']);
foreach ($jabatans as $j) {
    echo "ID: {$j->id_jabatan} | Name: {$j->nama_jabatan} | Min: {$j->min_gaji} | Max: {$j->max_gaji}\n";
}
