<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth('student')->user();
        $today = now()->format('Y-m-d');
        
        $attendance = Absensi::where('id_pegawai', $user->id_pegawai)
                             ->where('tanggal_absensi', $today)
                             ->first();

        return view('student.attendance.index', compact('attendance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:10240', // Max 10MB
            'type' => 'required|in:masuk,pulang',
        ]);

        $user = auth('student')->user();
        $today = now()->format('Y-m-d');
        $time = now()->format('H:i:s');

        // Create directory if not exists (though store() does it usually)
        // Store file
        $path = $request->file('foto')->store('attendance_photos', 'public');

        if ($request->type === 'masuk') {
            // Check if already checked in
            $exists = Absensi::where('id_pegawai', $user->id_pegawai)
                             ->where('tanggal_absensi', $today)
                             ->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'Anda sudah melakukan absensi masuk hari ini.');
            }

            Absensi::create([
                'id_pegawai' => $user->id_pegawai,
                'tanggal_absensi' => $today,
                'jam_masuk' => $time,
                'status' => 'hadir',
                'foto_masuk' => $path,
            ]);

            return redirect()->back()->with('success', 'Absensi Masuk Berhasil!');

        } else {
            // Check out logic
            $attendance = Absensi::where('id_pegawai', $user->id_pegawai)
                                 ->where('tanggal_absensi', $today)
                                 ->first();

            if (!$attendance) {
                return redirect()->back()->with('error', 'Anda belum melakukan absensi masuk hari ini.');
            }

            if ($attendance->jam_pulang) {
                return redirect()->back()->with('error', 'Anda sudah melakukan absensi pulang hari ini.');
            }

            $attendance->update([
                'jam_pulang' => $time,
                'foto_pulang' => $path,
            ]);

            return redirect()->back()->with('success', 'Absensi Pulang Berhasil!');
        }
    }
}
