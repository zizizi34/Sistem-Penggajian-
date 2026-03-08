<?php
foreach(App\Models\JadwalKerja::get() as $jk) {
    echo "ID Dept: " . $jk->id_departemen . " | Hari: " . $jk->hari . "\n";
}
