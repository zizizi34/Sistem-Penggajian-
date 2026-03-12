<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Penggajian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('DashboardController hit.');
        $student = auth('student')->user();
        if (!$student) {
             \Illuminate\Support\Facades\Log::error('Student user not found in DashboardController.');
             return redirect()->route('login');
        }
        \Illuminate\Support\Facades\Log::info('Student: ' . $student->id_user . ' - ' . $student->email_user);
        
        $today = now()->format('Y-m-d');
        $todayAttendance = Absensi::where('id_pegawai', $student->id_pegawai)
                                  ->where('tanggal_absensi', $today)
                                  ->first();

        // Ambil penugasan lembur hari ini (Hanya jika belum absen pulang)
        $overtimeNotification = null;
        if (!$todayAttendance || !$todayAttendance->jam_pulang) {
            $overtimeNotification = \App\Models\Lembur::where('id_pegawai', $student->id_pegawai)
                                        ->whereDate('tanggal_lembur', $today)
                                        ->where('status', 'pending')
                                        ->first();
        }

        $currentPeriod = now()->format('Y-m');

        // Logic Batas Absensi
        $jadwal = \App\Models\JadwalKerja::where('id_departemen', $student->pegawai->id_departemen ?? null)->first();
        $isLembur = \App\Models\Lembur::where('id_pegawai', $student->id_pegawai)
                        ->whereDate('tanggal_lembur', $today)
                        ->exists();
        $batasAbsensi = $isLembur ? '21:00:00' : ($jadwal->jam_pulang ?? '17:00:00');
        $isClosed = (now()->format('H:i:s') > $batasAbsensi) || ($todayAttendance && $todayAttendance->jam_pulang);

        return view('student.dashboard', [
            'myPayrollCount' => Penggajian::where('id_pegawai', $student->id_pegawai)
                                        ->count(),
            'myAttendanceCount' => Absensi::where('id_pegawai', $student->id_pegawai)
                                        ->whereMonth('tanggal_absensi', now()->month)
                                        ->whereYear('tanggal_absensi', now()->year)
                                        ->count(),
            'myPayrolls' => Penggajian::where('id_pegawai', $student->id_pegawai)
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get(),
            'todayAttendance' => $todayAttendance,
            'overtimeNotification' => $overtimeNotification,
            'isClosed' => $isClosed,
        ]);
    }
}

