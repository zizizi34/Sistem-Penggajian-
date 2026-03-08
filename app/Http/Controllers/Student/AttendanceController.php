<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\BaseController;
use App\Models\Absensi;
use Illuminate\Http\Request;

/**
 * AttendanceController - Student/Pegawai Self-Service
 * 
 * Controller untuk Pegawai (Student) melakukan self-service absensi.
 * 
 * Features:
 * - View absensi history pribadi
 * - Check-in & check-out dengan foto
 * - Request koreksi absensi
 * - Activity logging
 * 
 * Batasan:
 * - HANYA bisa lihat & manage absensi pribadi
 * - TIDAK bisa edit yang sudah di-approve oleh officer
 * - Foto mandatory untuk check-in/out
 * 
 * @author Your Name
 * @version 1.0
 */
class AttendanceController extends BaseController
{
    /**
     * Show attendance history
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = auth('student')->user();
            $pegawaiId = $user->id_pegawai;
            $today = now()->format('Y-m-d');

            // Absensi hari ini
            $attendance = Absensi::where('id_pegawai', $pegawaiId)
                ->whereDate('tanggal_absensi', $today)
                ->first();

            // Statistik keseluruhan untuk bulan ini
            $currentMonth = \Carbon\Carbon::now()->month;
            $currentYear = \Carbon\Carbon::now()->year;

            $totalHadir  = Absensi::where('id_pegawai', $pegawaiId)
                ->whereMonth('tanggal_absensi', $currentMonth)
                ->whereYear('tanggal_absensi', $currentYear)
                ->whereIn('status', ['hadir', 'terlambat'])->count();

            $totalIzin   = Absensi::where('id_pegawai', $pegawaiId)
                ->whereMonth('tanggal_absensi', $currentMonth)
                ->whereYear('tanggal_absensi', $currentYear)
                ->where('status', 'izin')->count();

            // Hitung Total Tidak Masuk berdasarkan Jadwal Kerja bulan ini (sampai hari ini)
            $totalTidakMasuk = 0;
            $jadwal = \App\Models\JadwalKerja::where('id_departemen', $user->pegawai->id_departemen ?? null)->first();
            
            if ($jadwal) {
                $hariKerja = strtolower($jadwal->hari);
                $workingDaysMap = [
                    'senin' => 1, 'selasa' => 2, 'rabu' => 3, 'kamis' => 4, 'jumat' => 5, 'sabtu' => 6, 'minggu' => 0
                ];
                $allowedDays = [];
                if (str_contains($hariKerja, '-')) {
                    $parts = array_map('trim', explode('-', $hariKerja));
                    if (count($parts) == 2 && isset($workingDaysMap[$parts[0]]) && isset($workingDaysMap[$parts[1]])) {
                        $start = $workingDaysMap[$parts[0]];
                        $end = $workingDaysMap[$parts[1]];
                        if ($start == 0) $start = 7;
                        if ($end == 0) $end = 7;
                        for ($i = $start; $i <= $end; $i++) {
                            $allowedDays[] = $i % 7;
                        }
                    }
                }
                if (empty($allowedDays)) {
                    $allowedDays = [1, 2, 3, 4, 5]; // Default Senin-Jumat
                }
                
                $totalWorkingDays = 0;
                // Hitung jumlah hari kerja dari tanggal 1 sampai hari ini di bulan berjalan
                for ($day = 1; $day <= \Carbon\Carbon::now()->day; $day++) {
                    $date = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, $day);
                    if (in_array($date->dayOfWeek, $allowedDays)) {
                        $totalWorkingDays++;
                    }
                }
                
                $totalTidakMasuk = max(0, $totalWorkingDays - ($totalHadir + $totalIzin));
            }

            // Query history dengan filter
            $query = Absensi::where('id_pegawai', $pegawaiId);

            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_absensi', '>=', $request->tanggal_dari);
            }

            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_absensi', '<=', $request->tanggal_sampai);
            }

            if ($request->filled('status') && $request->status !== 'semua') {
                $query->where('status', $request->status);
            }

            $history = $query->orderBy('tanggal_absensi', 'desc')->paginate(10)->withQueryString();

            // Log
            $this->logActivity('read', 'Absensi', null, 'View personal attendance');

            return view('student.attendance.index', compact(
                'attendance',
                'history',
                'totalHadir',
                'totalIzin',
                'totalTidakMasuk'
            ));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Check in / Check out
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validate
            $validated = $request->validate([
                'foto' => 'required|image|max:10240',
                'type' => 'required|in:masuk,pulang',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'catatan' => 'nullable|string|max:255',
            ]);

            $user = auth('student')->user();
            $pegawaiId = $user->id_pegawai;
            $today = now()->format('Y-m-d');
            $time = now()->format('H:i:s');

            // Store foto
            $photoPath = $request->file('foto')->store('attendance_photos/' . date('Y/m/d'), 'public');

            if ($validated['type'] === 'masuk') {
                // Check if already checked in
                $exists = Absensi::where('id_pegawai', $pegawaiId)
                    ->whereDate('tanggal_absensi', $today)
                    ->exists();

                if ($exists) {
                    return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini');
                }

                // Create
                $absensi = Absensi::create([
                    'id_pegawai' => $pegawaiId,
                    'tanggal_absensi' => $today,
                    'jam_masuk' => $time,
                    'status' => 'hadir',
                    'foto_masuk' => $photoPath,
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                // Log
                $this->logActivity('create', 'Absensi', $absensi->id_absensi, 'Check-in attendance');

                return back()->with('success', 'Absensi masuk berhasil');

            } else {
                // Check out
                $attendance = Absensi::where('id_pegawai', $pegawaiId)
                    ->whereDate('tanggal_absensi', $today)
                    ->first();

                if (!$attendance) {
                    return back()->with('error', 'Anda belum melakukan absensi masuk hari ini');
                }

                if ($attendance->jam_pulang) {
                    return back()->with('error', 'Anda sudah melakukan absensi pulang hari ini');
                }

                // Prevent edit if approved
                if ($attendance->status === 'approved') {
                    return back()->with('error', 'Absensi sudah di-approve, tidak bisa diubah');
                }

                // Save old values for logging
                $oldValues = $attendance->toArray();

                // Update
                $attendance->update([
                    'jam_pulang' => $time,
                    'foto_pulang' => $photoPath,
                ]);

                // Finalisasi record lembur jika ada lembur otomatis yang masih pending
                $lemburOtomatis = \App\Models\Lembur::where('id_pegawai', $pegawaiId)
                    ->whereDate('tanggal_lembur', $today)
                    ->where('status', 'pending')
                    ->whereNotNull('jam_mulai')
                    ->first();

                if ($lemburOtomatis) {
                    $jamMulai   = \Carbon\Carbon::parse($today . ' ' . $lemburOtomatis->jam_mulai);
                    $jamSelesai = \Carbon\Carbon::parse($today . ' ' . $time);
                    $durasiJam  = round($jamMulai->diffInMinutes($jamSelesai) / 60, 2);

                    $lemburOtomatis->jam_selesai = $time;
                    $lemburOtomatis->durasi      = $durasiJam;
                    $lemburOtomatis->save();
                }

                // Log
                $this->logActivity('update', 'Absensi', $attendance->id_absensi, 'Check-out attendance', $oldValues, $attendance->toArray());

                return back()->with('success', 'Absensi pulang berhasil');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Request koreksi absensi
     * 
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function requestCorrection($id, Request $request)
    {
        try {
            $user = auth('student')->user();
            $pegawaiId = $user->id_pegawai;

            // Validate
            $validated = $request->validate([
                'tipe_koreksi' => 'required|in:jam_masuk,jam_pulang,status',
                'nilai_baru' => 'required|string',
                'alasan' => 'required|string|max:500',
            ]);

            // Get absensi - hanya pribadi
            $absensi = Absensi::where('id_pegawai', $pegawaiId)
                ->findOrFail($id);

            // Prevent correction if approved
            if ($absensi->status === 'approved') {
                return $this->responseError('Tidak bisa koreksi absensi yang sudah di-approve', 400);
            }

            // Create correction request (store di temp table atau add column)
            // Simplified: just update dengan flag
            $absensi->update([
                'correction_requested' => true,
                'correction_type' => $validated['tipe_koreksi'],
                'correction_value' => $validated['nilai_baru'],
                'correction_reason' => $validated['alasan'],
                'correction_requested_at' => now(),
            ]);

            // Log
            $this->logActivity('update', 'Absensi', $id, 'Request correction', ['before' => 'no correction'], ['after' => 'correction requested']);

            return $this->responseSuccess($absensi, 'Permintaan koreksi dikirim ke officer');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Get today's summary
     * 
     * @return \Illuminate\Http\Response
     */
    public function todaySummary()
    {
        try {
            $user = auth('student')->user();
            $pegawaiId = $user->id_pegawai;
            $today = now()->format('Y-m-d');

            $today_attendance = Absensi::where('id_pegawai', $pegawaiId)
                ->whereDate('tanggal_absensi', $today)
                ->with(['pegawai.jabatan', 'pegawai.departemen'])
                ->first();

            // Log
            $this->logActivity('read', 'Absensi', null, 'View today summary');

            return $this->responseSuccess([
                'attendance' => $today_attendance,
                'checked_in' => $today_attendance ? true : false,
                'checked_out' => $today_attendance && $today_attendance->jam_pulang ? true : false,
            ]);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
