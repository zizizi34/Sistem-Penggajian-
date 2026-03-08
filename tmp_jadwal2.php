<?php
$s = App\Models\JadwalKerja::get()->pluck('hari', 'id_departemen')->toArray();
var_dump($s);
