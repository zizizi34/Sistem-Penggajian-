<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use App\Models\Absensi;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $user = auth('student')->user();
        if (!$user || !$user->id_pegawai) {
            abort(403, 'Unauthorized access');
        }

        $payrolls = Penggajian::with(['pegawai.jabatan', 'pegawai.departemen'])
            ->where('id_pegawai', $user->id_pegawai)
            ->orderBy('periode', 'desc')
            ->get();

        return view('student.payroll.index', compact('payrolls'));
    }

    public function show($id)
    {
        $user = auth('student')->user();
        if (!$user || !$user->id_pegawai) {
            abort(403, 'Unauthorized access');
        }

        $penggajian = Penggajian::with(['pegawai.jabatan', 'pegawai.departemen'])
            ->where('id_pegawai', $user->id_pegawai)
            ->findOrFail($id);

        // Map bulan Indonesia ke angka
        $bulanMap = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];

        // Parsing periode "Maret 2026"
        $startDate = now()->startOfMonth();
        $parts = explode(' ', $penggajian->periode);
        if (count($parts) == 2) {
            $month = $bulanMap[$parts[0]] ?? now()->month;
            $year = $parts[1];
            $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        }

        $endDate = $startDate->copy()->endOfMonth();
        
        // Sesuaikan rentang dengan tgl_masuk pegawai (jangan hitung alpha sebelum join)
        $joinDate = $penggajian->pegawai->tgl_masuk ? \Carbon\Carbon::parse($penggajian->pegawai->tgl_masuk) : $user->created_at;
        $calcStart = $startDate->copy();
        if ($joinDate && $joinDate->greaterThan($calcStart)) { $calcStart = $joinDate; }
        
        $calcEnd = $endDate->copy();
        if ($calcEnd->isFuture()) { $calcEnd = \Carbon\Carbon::now(); }

        // Hitung Statistik Absensi (Berdasarkan Data Real di Database)
        $absences = Absensi::where('id_pegawai', $user->id_pegawai)
                        ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get();

        $present = 0; $izin = 0; $sakit = 0; $alphaDb = 0; $lateCount = 0;
        $jadwal = $penggajian->pegawai->departemen->jadwalKerja ?? null;
        $standardIn = $jadwal ? $jadwal->jam_masuk : '08:00:00';
        $tolerance = $jadwal ? $jadwal->toleransi_terlambat : 0;

        foreach ($absences as $ab) {
            $statusLower = strtolower($ab->status);
            if (in_array($statusLower, ['hadir', 'terlambat', 'lupa absen pulang', 'lembur', 'lembur tetapi lupa absen pulang'])) {
                $present++;
                if ($ab->jam_masuk) {
                    $entryTime = strtotime($ab->jam_masuk);
                    $limitTime = strtotime($standardIn) + ($tolerance * 60);
                    if ($entryTime > $limitTime) {
                        $lateCount++;
                    }
                }
            } elseif ($statusLower === 'izin') {
                $izin++;
            } elseif ($statusLower === 'sakit') {
                $sakit++;
            } elseif ($statusLower === 'alpha') {
                $alphaDb++;
            }
        }

        // Hitung hari kerja
        $salaryService = app(\App\Services\SalaryCalculationService::class);
        $reflection = new \ReflectionClass($salaryService);
        $method = $reflection->getMethod('countWorkingDays');
        $method->setAccessible(true);
        $workingDays = $method->invoke($salaryService, $calcStart, $calcEnd, $jadwal ? $jadwal->hari : 'Senin-Jumat');

        $totalRecordedDays = $present + $izin + $sakit + $alphaDb;
        $missingDays = max(0, $workingDays - $totalRecordedDays);
        $totalAlpha = $alphaDb + $missingDays;

        $stats = [
            'hadir' => $present,
            'izin'  => $izin + $sakit,
            'alpha' => $totalAlpha,
        ];

        // Rincian Potongan
        // Singkronisasi Gaji Pokok untuk tampilan rincian (sesuaikan dengan SalaryCalculationService)
        $baseSalaryCalculated = $penggajian->gaji_pokok;
        if ($penggajian->pegawai->jabatan) {
            if ($baseSalaryCalculated < $penggajian->pegawai->jabatan->min_gaji || $baseSalaryCalculated > $penggajian->pegawai->jabatan->max_gaji) {
                $baseSalaryCalculated = $penggajian->pegawai->jabatan->min_gaji;
            }
        }

        $dailySalary = $baseSalaryCalculated / 22;
        $potonganAlpha = $totalAlpha * $dailySalary;
        $potonganTelat = $lateCount * 25000;
        
        $potonganKhusus = 0;
        $potonganLain = [];
        $pKhususDb = $penggajian->pegawai->potongans ?? collect([]);
        foreach($pKhususDb as $pk) {
            $potonganKhusus += $pk->nominal;
            $potonganLain[] = ['nama' => $pk->nama_potongan, 'nominal' => $pk->nominal];
        }

        $rincianPotongan = current([
            'alpha_count' => $totalAlpha,
            'alpha_nominal' => round($potonganAlpha, 2),
            'telat_count' => $lateCount,
            'telat_nominal' => $potonganTelat,
            'lain_lain' => $potonganLain,
            'total_lain' => $potonganKhusus
        ]);
        
        $rincianPotongan = [
            'alpha_count' => $totalAlpha,
            'alpha_nominal' => round($potonganAlpha, 2),
            'telat_count' => $lateCount,
            'telat_nominal' => $potonganTelat,
            'lain_lain' => $potonganLain,
            'total_lain' => $potonganKhusus
        ];

        return view('student.payroll.show', compact('penggajian', 'stats', 'rincianPotongan'));
    }
}
