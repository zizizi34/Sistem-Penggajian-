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
        $student = auth('student')->user();
        
        return view('student.dashboard', [
            'myPayrollCount' => Penggajian::where('id_pegawai', $student->id_pegawai ?? $student->id)->count(),
            'myAttendanceCount' => Absensi::where('id_pegawai', $student->id_pegawai ?? $student->id)->count(),
            'myPayrolls' => Penggajian::where('id_pegawai', $student->id_pegawai ?? $student->id)->take(5)->get(),
        ]);
    }
}

