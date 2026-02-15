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

        return view('student.dashboard', [
            'myPayrollCount' => Penggajian::where('id_pegawai', $student->id_pegawai ?? $student->id)->count(),
            'myAttendanceCount' => Absensi::where('id_pegawai', $student->id_pegawai ?? $student->id)->count(),
            'myPayrolls' => Penggajian::where('id_pegawai', $student->id_pegawai ?? $student->id)->take(5)->get(),
            'todayAttendance' => $todayAttendance,
        ]);
    }
}

