<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jabatans = App\Models\Jabatan::all(['id_jabatan', 'nama_jabatan'])->toArray();
file_put_contents('tmp/jabatans.json', json_encode($jabatans, JSON_PRETTY_PRINT));
