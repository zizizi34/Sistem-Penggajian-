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

        // Hitung Statistik Absensi untuk ditunjukkan di Slip Gaji (Berdasarkan Data Real di Database)
        $stats = [
            'hadir' => Absensi::where('id_pegawai', $user->id_pegawai)
                        ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->whereIn('status', ['hadir', 'terlambat', 'lembur', 'pulang cepat', 'lupa absen pulang', 'lembur tetapi lupa absen pulang'])
                        ->count(),
            'izin'  => Absensi::where('id_pegawai', $user->id_pegawai)
                        ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->whereIn('status', ['izin', 'sakit'])
                        ->count(),
            'alpha' => Absensi::where('id_pegawai', $user->id_pegawai)
                        ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->where('status', 'alpha')
                        ->count(),
        ];

        return view('student.payroll.show', compact('penggajian', 'stats'));
    }
}
