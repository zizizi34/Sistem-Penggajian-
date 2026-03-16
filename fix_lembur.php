<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lemburs = App\Models\Lembur::where('status', 'rejected')->get();
foreach($lemburs as $l) {
    if (!$l->jam_selesai || $l->jam_selesai == '00:00:00') {
        $absen = App\Models\Absensi::where('id_pegawai', $l->id_pegawai)
            ->whereDate('tanggal_absensi', $l->tanggal_lembur)
            ->first();
        if($absen && $absen->jam_pulang) {
            $l->update([
                'jam_mulai' => $absen->jam_pulang,
                'jam_selesai' => $absen->jam_pulang,
                'keterangan' => '[Sistem] Pegawai pulang cepat sebelum jadwal lembur di mulai'
            ]);
        }
    }
}
echo "Done";
