<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\BaseController;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $todayCarbon = \Carbon\Carbon::now()->startOfDay();

            // ============= AUTO-INSERT ALPHA UNTUK HARI YANG SUDAH LEWAT =============
            // Ambil jadwal kerja untuk menentukan hari kerja
            $jadwalForAlpha = \App\Models\JadwalKerja::where('id_departemen', $user->pegawai?->id_departemen)->first();
            $hariKerjaStr = $jadwalForAlpha ? ($jadwalForAlpha->hari ?? 'Senin-Jumat') : 'Senin-Jumat';

            // Tentukan hari kerja yang allowed
            $workingDaysMap = [
                'senin' => 1, 'selasa' => 2, 'rabu' => 3, 'kamis' => 4,
                'jumat' => 5, 'sabtu' => 6, 'minggu' => 0
            ];
            $allowedDays = [1, 2, 3, 4, 5]; // default Senin-Jumat
            $hariStr = strtolower($hariKerjaStr);
            if (str_contains($hariStr, '-')) {
                $parts = array_map('trim', explode('-', $hariStr));
                if (count($parts) == 2 && isset($workingDaysMap[$parts[0]]) && isset($workingDaysMap[$parts[1]])) {
                    $allowedDays = [];
                    $curr = $workingDaysMap[$parts[0]];
                    $endDay = $workingDaysMap[$parts[1]];
                    while (true) {
                        $allowedDays[] = $curr % 7;
                        if ($curr % 7 == $endDay % 7) break;
                        $curr++;
                        if ($curr > 14) break;
                    }
                }
            }

            // Tentukan tanggal mulai pengecekan alpha (dari tgl_masuk pegawai atau 3 bulan lalu, mana yang lebih baru)
            $joinDate = $user->pegawai?->tgl_masuk
                ? \Carbon\Carbon::parse($user->pegawai->tgl_masuk)->startOfDay()
                : $todayCarbon->copy()->subMonths(3)->startOfDay();

            // Batasi maksimum 6 bulan ke belakang agar tidak terlalu berat
            $maxLookback = $todayCarbon->copy()->subMonths(6)->startOfDay();
            $alphaCheckStart = $joinDate->greaterThan($maxLookback) ? $joinDate : $maxLookback;

            // Tentukan tanggal akhir pengecekan (kemarin, atau hari ini jika sudah tutup)
            // Hitung batas absensi hari ini
            $jadwalForToday = \App\Models\JadwalKerja::where('id_departemen', $user->pegawai?->id_departemen)->first();
            $lemburRecordForToday = \App\Models\Lembur::where('id_pegawai', $pegawaiId)->whereDate('tanggal_lembur', $today)->first();
            $isLemburToday = $lemburRecordForToday ? true : false;
            $batasAbsensi   = ($isLemburToday && $lemburRecordForToday->jam_selesai) 
                ? $lemburRecordForToday->jam_selesai 
                : ($isLemburToday ? '21:00:00' : ($jadwalForToday->jam_pulang ?? '17:00:00'));
            $isClosedToday  = now()->format('H:i:s') > $batasAbsensi;
            
            $checkUntil = $isClosedToday ? $todayCarbon : $todayCarbon->copy()->subDay();

            if ($alphaCheckStart->lessThanOrEqualTo($checkUntil)) {
                $existingDates = Absensi::where('id_pegawai', $pegawaiId)
                    ->whereBetween('tanggal_absensi', [
                        $alphaCheckStart->format('Y-m-d'),
                        $checkUntil->format('Y-m-d')
                    ])
                    ->pluck('tanggal_absensi')
                    ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
                    ->toArray();

                // Iterasi setiap hari dari alphaCheckStart sampai checkUntil
                $checkPeriod = \Carbon\CarbonPeriod::create($alphaCheckStart, $checkUntil);
                $alphaToInsert = [];
                foreach ($checkPeriod as $date) {
                    // Lewati hari non-kerja
                    if (!in_array($date->dayOfWeek, $allowedDays)) {
                        continue;
                    }
                    $dateStr = $date->format('Y-m-d');
                    // Jika tidak ada record untuk tanggal ini, tandai sebagai alpha
                    if (!in_array($dateStr, $existingDates)) {
                        $alphaToInsert[] = [
                            'id_pegawai'       => $pegawaiId,
                            'tanggal_absensi'  => $dateStr,
                            'status'           => 'alpha',
                            'keterangan'       => 'Tanpa Keterangan',
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ];
                    }
                }

                // Batch insert alpha records jika ada
                if (!empty($alphaToInsert)) {
                    \Illuminate\Support\Facades\DB::table('absensi')->insertOrIgnore($alphaToInsert);
                }
            }
            // ========================================================================

            // Absensi hari ini (ambil setelah proses alpha selesai)
            $attendance = Absensi::where('id_pegawai', $pegawaiId)
                ->whereDate('tanggal_absensi', $today)
                ->first();

            // Filter Tanggal - Default ke bulan terakhir yang punya data (bukan selalu bulan ini)
            if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
                $dateFrom = $request->filled('tanggal_dari') ? $request->tanggal_dari : \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $dateTo   = $request->filled('tanggal_sampai') ? $request->tanggal_sampai : \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
            } else {
                // Cek apakah bulan ini ada data
                $currentMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $currentMonthEnd   = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
                $currentMonthCount = Absensi::where('id_pegawai', $pegawaiId)
                    ->whereBetween('tanggal_absensi', [$currentMonthStart, $currentMonthEnd])
                    ->count();

                if ($currentMonthCount > 0) {
                    // Bulan ini ada data → tampilkan bulan ini
                    $dateFrom = $currentMonthStart;
                    $dateTo   = $currentMonthEnd;
                } else {
                    // Bulan ini TIDAK ada data → cari bulan terakhir yang ada data
                    $lastRecord = Absensi::where('id_pegawai', $pegawaiId)
                        ->orderByDesc('tanggal_absensi')
                        ->first();
                    if ($lastRecord) {
                        $lastDate = \Carbon\Carbon::parse($lastRecord->tanggal_absensi);
                        $dateFrom = $lastDate->copy()->startOfMonth()->format('Y-m-d');
                        $dateTo   = $lastDate->copy()->endOfMonth()->format('Y-m-d');
                    } else {
                        // Tidak ada data sama sekali
                        $dateFrom = $currentMonthStart;
                        $dateTo   = $currentMonthEnd;
                    }
                }
            }


            // Ambil label bulan
            $currentMonthName = \Carbon\Carbon::parse($dateTo)->translatedFormat('F Y');

            // Ambil jadwal kerja departemen (hanya SEKALI)
            $jadwal = \App\Models\JadwalKerja::where('id_departemen', $user->pegawai?->id_departemen)->first();

            // Tentukan rentang AKTUAL untuk perhitungan statistik
            // (tidak boleh melewati hari ini karena masa depan belum terjadi)
            $startDate = \Carbon\Carbon::parse($dateFrom)->startOfDay();
            $endDate   = \Carbon\Carbon::parse($dateTo)->startOfDay();
            $today     = \Carbon\Carbon::now()->startOfDay();
            if ($endDate->greaterThan($today)) {
                $endDate = $today;
            }

            // Pertimbangkan tanggal masuk pegawai
            $joinDate = $user->pegawai?->tgl_masuk
                ? \Carbon\Carbon::parse($user->pegawai->tgl_masuk)->startOfDay()
                : $startDate;
            if ($joinDate->greaterThan($startDate)) {
                $startDate = $joinDate;
            }

            // Hitung hari kerja yang sudah berlalu (dari startDate sampai today)
            $workingDays = 0;
            if ($startDate->lessThanOrEqualTo($endDate)) {
                $workingDaysMap = [
                    'senin' => 1, 'selasa' => 2, 'rabu' => 3, 'kamis' => 4,
                    'jumat' => 5, 'sabtu' => 6, 'minggu' => 0
                ];
                $allowedDays = [1, 2, 3, 4, 5]; // Default Senin-Jumat
                if ($jadwal && $jadwal->hari) {
                    $hariStr = strtolower($jadwal->hari);
                    if (str_contains($hariStr, '-')) {
                        $parts = array_map('trim', explode('-', $hariStr));
                        if (count($parts) == 2 && isset($workingDaysMap[$parts[0]]) && isset($workingDaysMap[$parts[1]])) {
                            $allowedDays = [];
                            $curr = $workingDaysMap[$parts[0]];
                            $endDay = $workingDaysMap[$parts[1]];
                            while (true) {
                                $allowedDays[] = $curr % 7;
                                if ($curr % 7 == $endDay % 7) break;
                                $curr++;
                                if ($curr > 14) break; // safety
                            }
                        }
                    }
                }
                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
                foreach ($period as $date) {
                    if (in_array($date->dayOfWeek, $allowedDays)) {
                        $workingDays++;
                    }
                }
            }

            // Hitung statistik dari RANGE TANGGAL FILTER (bukan range aktual)
            // agar angka yang ditampilkan sesuai dengan filter yang dipilih
            $baseQuery = Absensi::where('id_pegawai', $pegawaiId)
                ->whereBetween('tanggal_absensi', [$dateFrom, $dateTo]);

            $totalHadir = (clone $baseQuery)->whereIn('status', [
                'hadir', 'Hadir', 'terlambat', 'Terlambat', 'Lembur', 'lembur',
                'Pulang Cepat', 'pulang cepat', 'Lupa Absen Pulang', 'lupa absen pulang',
                'Lembur tetapi Lupa Absen Pulang'
            ])->count();

            $totalIzin = (clone $baseQuery)->whereIn('status', ['izin', 'Izin', 'sakit', 'Sakit'])->count();

            $alphaRecorded = (clone $baseQuery)->whereIn('status', ['alpha', 'Alpha'])->count();

            // Karena alpha sudah di-insert ke DB secara otomatis, kita cukup menggunakan count dari DB
            $totalTidakMasuk = $alphaRecorded;


            // Query history dengan filter
            $query = Absensi::where('id_pegawai', $pegawaiId);

            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_absensi', '>=', $request->tanggal_dari);
            }

            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_absensi', '<=', $request->tanggal_sampai);
            }

            if ($request->filled('status') && $request->status !== 'semua') {
                if ($request->status === 'hadir') {
                    $query->whereIn('status', ['hadir', 'Hadir', 'terlambat', 'Terlambat', 'lembur', 'Lembur', 'pulang cepat', 'Pulang Cepat', 'lupa absen pulang', 'Lupa Absen Pulang', 'Lembur tetapi Lupa Absen Pulang']);
                } elseif ($request->status === 'tidak_hadir') {
                    $query->whereIn('status', ['alpha', 'Alpha', 'izin', 'Izin', 'sakit', 'Sakit', 'tidak masuk']);
                } else {
                    $query->where('status', $request->status);
                }
            }

            $history = $query->orderBy('tanggal_absensi', 'desc')->paginate(10)->withQueryString();

            // ============= LOGIC BATAS ABSENSI HARI INI =============
            $lemburRecordForTodayAlt = \App\Models\Lembur::where('id_pegawai', $pegawaiId)
                ->whereDate('tanggal_lembur', $today)
                ->first();
            $isLembur = $lemburRecordForTodayAlt ? true : false;
            
            $batasAbsensi = ($isLembur && $lemburRecordForTodayAlt->jam_selesai) 
                ? $lemburRecordForTodayAlt->jam_selesai 
                : ($isLembur ? '21:00:00' : ($jadwal->jam_pulang ?? '17:00:00'));
            $isClosed = now()->format('H:i:s') > $batasAbsensi;

            // Rule 3: Jika sudah melewati batas dan belum absen pulang, auto-close sekarang
            if ($attendance && !$attendance->jam_pulang && $isClosed && in_array($attendance->status, ['hadir', 'terlambat'])) {
                $statusAbsensi = $isLembur ? 'Lembur tetapi Lupa Absen Pulang' : 'Lupa Absen Pulang';
                $jamPulang = $batasAbsensi;
                $jadwalPulang = $jadwal->jam_pulang ?? '17:00:00';
                
                $attendance->update([
                    'jam_pulang' => $jamPulang,
                    'status' => $statusAbsensi,
                    'keterangan' => ($attendance->keterangan ? $attendance->keterangan . '; ' : '') . '[Sistem] Auto-close (Melewati Batas Absensi)'
                ]);
                
                if ($isLembur) {
                    $lemburRecord = \App\Models\Lembur::where('id_pegawai', $pegawaiId)
                        ->whereDate('tanggal_lembur', $today)
                        ->first();
                    if ($lemburRecord) {
                        $startLembur = \Carbon\Carbon::parse($today . ' ' . $jadwalPulang);
                        $endLembur = \Carbon\Carbon::parse($today . ' ' . $jamPulang);
                        $durasiLembur = round($startLembur->diffInMinutes($endLembur) / 60, 2);
                        
                        $lemburRecord->update([
                            'jam_mulai' => $jadwalPulang,
                            'jam_selesai' => $jamPulang,
                            'durasi' => $durasiLembur,
                            'status' => 'pending'
                        ]);
                    }
                }
            }
            // ========================================================

            // Penugasan lembur hari ini (Hanya jika belum absen pulang)
            $overtimeNotification = null;
            if (!$attendance || !$attendance->jam_pulang) {
                $overtimeNotification = \App\Models\Lembur::where('id_pegawai', $pegawaiId)
                    ->whereDate('tanggal_lembur', $today)
                    ->where('status', 'pending')
                    ->first();
            }

            // Log
            $this->logActivity('read', 'Absensi', null, 'View personal attendance');

            return view('student.attendance.index', compact(
                'attendance',
                'history',
                'totalHadir',
                'totalIzin',
                'totalTidakMasuk',
                'overtimeNotification',
                'isClosed',
                'jadwal',
                'currentMonthName'
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

            // 1. CEK BATAS WAKTU ABSENSI (Rule 4 & 5)
            $jadwal = \App\Models\JadwalKerja::where('id_departemen', $user->pegawai->id_departemen ?? null)->first();
            $jadwalPulang = $jadwal->jam_pulang ?? '17:00:00';
            
            $lemburRecordStore = \App\Models\Lembur::where('id_pegawai', $pegawaiId)
                ->whereDate('tanggal_lembur', $today)
                ->first();
            $hasLembur = $lemburRecordStore ? true : false;
                
            $batasAbsensi = ($hasLembur && $lemburRecordStore->jam_selesai) 
                ? $lemburRecordStore->jam_selesai 
                : ($hasLembur ? '21:00:00' : $jadwalPulang);

            // Jika sudah absen masuk & pulang hari ini, tidak boleh absen lagi
            $attendance = Absensi::where('id_pegawai', $pegawaiId)
                ->whereDate('tanggal_absensi', $today)
                ->first();

            if ($attendance && $attendance->jam_masuk && $attendance->jam_pulang) {
                return back()->with('info', 'Anda sudah menyelesaikan absensi hari ini.');
            }

            if ($time > $batasAbsensi && !$hasLembur) {
                return back()->with('error', 'Absensi hari ini sudah ditutup. Silakan melakukan absensi kembali pada hari kerja berikutnya.');
            }

            // Store foto
            $photoPath = $request->file('foto')->store('attendance_photos/' . date('Y/m/d'), 'public');

            if ($validated['type'] === 'masuk') {
                // AUTO-CLOSE: Cek apakah ada absensi yang belum absen pulang dari hari-hari sebelumnya
                $unclosedAttendance = Absensi::where('id_pegawai', $pegawaiId)
                    ->where('tanggal_absensi', '<', $today)
                    ->whereNull('jam_pulang')
                    ->whereNotNull('jam_masuk')
                    ->get();

                foreach ($unclosedAttendance as $old) {
                    // Ambil jadwal kerja
                    $jadwal = \App\Models\JadwalKerja::where('id_departemen', $user->pegawai->id_departemen ?? null)->first();
                    $jadwalPulang = $jadwal->jam_pulang ?? '17:00:00';

                    // Rule 3 & 4: Cek apakah ada record lembur (notifikasi dari petugas)
                    $lembur = \App\Models\Lembur::where('id_pegawai', $pegawaiId)
                        ->whereDate('tanggal_lembur', $old->tanggal_absensi)
                        ->first();

                    if ($lembur) {
                        // Rule 4: Ada Notifikasi Lembur tapi Lupa Absen Pulang
                        $endTime = '21:00:00';
                        $statusAbsensi = 'Lembur tetapi Lupa Absen Pulang';
                        
                        // Hitung lembur: 21:00 - jadwal_pulang
                        $startLembur = \Carbon\Carbon::parse($old->tanggal_absensi . ' ' . $jadwalPulang);
                        $endLembur = \Carbon\Carbon::parse($old->tanggal_absensi . ' ' . $endTime);
                        $durasiLembur = round($startLembur->diffInMinutes($endLembur) / 60, 2);

                        $lembur->update([
                            'jam_mulai'   => $jadwalPulang,
                            'jam_selesai' => $endTime,
                            'durasi'      => $durasiLembur,
                            'status'      => 'pending',
                            'keterangan'  => ($lembur->keterangan ? $lembur->keterangan . '; ' : '') . '[Sistem] Auto-close (Lupa Absen)'
                        ]);
                    } else {
                        // Rule 2: Tidak ada Notifikasi Lembur & Lupa Absen Pulang
                        $endTime = $jadwalPulang;
                        $statusAbsensi = 'Lupa Absen Pulang';
                    }

                    $old->update([
                        'jam_pulang' => $endTime,
                        'status'     => $statusAbsensi,
                        'keterangan' => ($old->keterangan ? $old->keterangan . '; ' : '') . '[Sistem] Auto-close saat absen masuk baru'
                    ]);
                }

                // Check if already checked in (exclude record with status 'alpha' because it can be overwritten)
                $attendanceToday = Absensi::where('id_pegawai', $pegawaiId)
                    ->whereDate('tanggal_absensi', $today)
                    ->first();

                if ($attendanceToday && !in_array($attendanceToday->status, ['alpha', 'Alpha'])) {
                    return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini');
                }

                if ($attendanceToday && in_array($attendanceToday->status, ['alpha', 'Alpha'])) {
                    // Overwrite alpha record
                    $attendanceToday->update([
                        'jam_masuk' => $time,
                        'status' => 'hadir',
                        'foto_masuk' => $photoPath,
                        'catatan' => $validated['catatan'] ?? null,
                    ]);
                    $absensi = $attendanceToday;
                } else {
                    // Create new
                    $absensi = Absensi::create([
                        'id_pegawai' => $pegawaiId,
                        'tanggal_absensi' => $today,
                        'jam_masuk' => $time,
                        'status' => 'hadir',
                        'foto_masuk' => $photoPath,
                        'catatan' => $validated['catatan'] ?? null,
                    ]);
                }

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

                // Rule 5: Cek jika ada notifikasi lembur
                $lembur = \App\Models\Lembur::where('id_pegawai', $pegawaiId)
                    ->whereDate('tanggal_lembur', $today)
                    ->first();

                // Logic Status (Lembur vs Pulang Cepat vs Hadir)
                $jadwal = \App\Models\JadwalKerja::where('id_departemen', $user->pegawai->id_departemen ?? null)->first();
                $jadwalPulang = $jadwal->jam_pulang ?? '17:00:00';
                $statusAbsensi = $attendance->status;

                if ($lembur) {
                    $startLembur = \Carbon\Carbon::parse($today . ' ' . $jadwalPulang);
                    $endLembur = \Carbon\Carbon::parse($today . ' ' . $time);
                    
                    if ($endLembur->greaterThan($startLembur)) {
                        // Mereka melakukan lembur
                        $limitLembur = \Carbon\Carbon::parse($today . ' 21:00:00');
                        if ($endLembur->greaterThan($limitLembur)) {
                            $endLembur = $limitLembur;
                        }
                        $durasiLembur = round($startLembur->diffInMinutes($endLembur) / 60, 2);
                        
                        $lembur->update([
                            'jam_mulai'   => $jadwalPulang,
                            'jam_selesai' => $endLembur->format('H:i:s'),
                            'durasi'      => $durasiLembur,
                            'status'      => 'pending'
                        ]);
                        $statusAbsensi = 'Lembur';
                    } else {
                        // Mereka pulang sebelum jadwal pulang normal padahal ada jatah lembur
                        // Maka batalkan lembur & status menjadi Pulang Cepat
                        $lembur->update([
                            'status' => 'rejected', 
                            'keterangan' => '[Sistem] Pegawai pulang cepat sebelum jadwal lembur di mulai',
                            'jam_mulai' => $time,
                            'jam_selesai' => $time,
                            'durasi' => 0
                        ]);
                        if ($time < $jadwalPulang) {
                            $statusAbsensi = 'Pulang Cepat';
                        }
                    }
                } else {
                    // Tidak ada lembur, cek jika pulang cepat
                    if ($time < $jadwalPulang) {
                        $statusAbsensi = 'Pulang Cepat';
                    }
                }

                // Update
                $attendance->update([
                    'jam_pulang' => $time,
                    'foto_pulang' => $photoPath,
                    'status' => $statusAbsensi,
                ]);

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
