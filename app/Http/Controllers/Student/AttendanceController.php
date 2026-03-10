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

            // Filter Tanggal
            $dateFrom = $request->input('tanggal_dari', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
            $dateTo   = $request->input('tanggal_sampai', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));

            // Ambil bulan dan tahun dari range (untuk default label)
            $currentMonth = \Carbon\Carbon::parse($dateTo)->month;
            $currentYear = \Carbon\Carbon::parse($dateTo)->year;

            $totalHadir  = Absensi::where('id_pegawai', $pegawaiId)
                ->whereBetween('tanggal_absensi', [$dateFrom, $dateTo])
                ->whereIn('status', ['hadir', 'terlambat'])->count();

            $totalIzin   = Absensi::where('id_pegawai', $pegawaiId)
                ->whereBetween('tanggal_absensi', [$dateFrom, $dateTo])
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
                        
                        // Handle range melintasi minggu (misal: Sabtu-Selasa)
                        $current = $start;
                        while(true) {
                            $allowedDays[] = $current % 7;
                            if ($current % 7 == $end % 7) break;
                            $current++;
                            if ($current > 100) break; // Safety break
                        }
                    }
                } elseif (str_contains($hariKerja, ',')) {
                    $parts = array_map('trim', explode(',', $hariKerja));
                    foreach($parts as $p) {
                        if (isset($workingDaysMap[$p])) {
                            $allowedDays[] = $workingDaysMap[$p];
                        }
                    }
                } else {
                    // Cek satu hari saja
                    $dayTrim = trim($hariKerja);
                    if (isset($workingDaysMap[$dayTrim])) {
                        $allowedDays[] = $workingDaysMap[$dayTrim];
                    }
                }
                if (empty($allowedDays)) {
                    $allowedDays = [1, 2, 3, 4, 5]; // Default Senin-Jumat
                }
                
                $totalWorkingDays = 0;
                
                // Tentukan tanggal mulai hitung (start of range vs join date)
                // Gunakan tgl_masuk dari data pegawai, jika tidak ada baru fallback ke created_at
                $joinDate = $user->pegawai->tgl_masuk ? \Carbon\Carbon::parse($user->pegawai->tgl_masuk) : $user->created_at;
                
                $calcStart = \Carbon\Carbon::parse($dateFrom);
                if ($joinDate && $joinDate->greaterThan($calcStart)) {
                    $calcStart = $joinDate;
                }

                // Tentukan tanggal akhir hitung (end of range vs today)
                $calcEnd = \Carbon\Carbon::parse($dateTo);
                if ($calcEnd->isFuture()) {
                    $calcEnd = \Carbon\Carbon::now();
                }

                // Hitung jumlah hari kerja dalam range yang ditentukan
                // HANYA hitung hari yang masuk dalam jadwal kerja departemen
                for ($d = $calcStart->copy(); $d->lte($calcEnd); $d->addDay()) {
                    if (in_array($d->dayOfWeek, $allowedDays)) {
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
                // AUTO-CLOSE: Cek apakah ada absensi yang belum absen pulang dari hari-hari sebelumnya
                $unclosedAttendance = Absensi::where('id_pegawai', $pegawaiId)
                    ->where('tanggal_absensi', '<', $today)
                    ->whereNull('jam_pulang')
                    ->get();

                foreach ($unclosedAttendance as $old) {
                    // Cari apakah ada record lembur otomatis (pending) untuk hari tersebut
                    $lembur = \App\Models\Lembur::where('id_pegawai', $pegawaiId)
                        ->whereDate('tanggal_lembur', $old->tanggal_absensi)
                        ->where('status', 'pending')
                        ->first();

                    $endTime = null;
                    if ($lembur && $lembur->jam_selesai) {
                        $endTime = $lembur->jam_selesai;
                    } else {
                        // Jika tidak ada deteksi lembur, gunakan jam pulang jadwal sebagai fallback
                        $jadwal = \App\Models\JadwalKerja::where('id_departemen', $user->pegawai->id_departemen ?? null)->first();
                        $endTime = $jadwal->jam_pulang ?? '17:00:00';
                    }

                    $old->update([
                        'jam_pulang' => $endTime,
                        'keterangan' => ($old->keterangan ? $old->keterangan . '; ' : '') . '[Sistem] Auto-close saat absen masuk baru'
                    ]);
                }

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
