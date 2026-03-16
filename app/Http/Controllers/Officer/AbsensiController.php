<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\BaseController;
use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Officer AbsensiController - Department Scoped
 * 
 * Controller untuk Petugas (Officer) mengelola absensi.
 * 
 * Batasan:
 * - HANYA bisa input & manage absensi untuk pegawai di departemen sendiri
 * - TIDAK bisa edit/delete yang sudah di-approve
 * - HANYA approve di level departemen (tidak approve penggajian)
 * 
 * @author Your Name
 * @version 1.0
 */
class AbsensiController extends BaseController
{
    /**
     * Display absensi untuk departemen Petugas
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $today = now()->format('Y-m-d');
            $currentTime = now()->format('H:i:s');
            $todayCarbon = \Carbon\Carbon::now()->startOfDay();
            $yesterday   = $todayCarbon->copy()->subDay();

            // ===== AUTO-INSERT ALPHA UNTUK SEMUA PEGAWAI DI DEPARTEMEN =====
            $jadwalDept = \App\Models\JadwalKerja::where('id_departemen', $departemenId)->first();
            $hariKerjaStr = $jadwalDept ? ($jadwalDept->hari ?? 'Senin-Jumat') : 'Senin-Jumat';
            $workingDaysMap = [
                'senin' => 1, 'selasa' => 2, 'rabu' => 3, 'kamis' => 4,
                'jumat' => 5, 'sabtu' => 6, 'minggu' => 0
            ];
            $allowedDaysOfficer = [1, 2, 3, 4, 5];
            $hariStrOfficer = strtolower($hariKerjaStr);
            if (str_contains($hariStrOfficer, '-')) {
                $parts = array_map('trim', explode('-', $hariStrOfficer));
                if (count($parts) == 2 && isset($workingDaysMap[$parts[0]]) && isset($workingDaysMap[$parts[1]])) {
                    $allowedDaysOfficer = [];
                    $curr = $workingDaysMap[$parts[0]];
                    $endDay = $workingDaysMap[$parts[1]];
                    while (true) {
                        $allowedDaysOfficer[] = $curr % 7;
                        if ($curr % 7 == $endDay % 7) break;
                        $curr++;
                        if ($curr > 14) break;
                    }
                }
            }

            // Ambil semua pegawai aktif di departemen ini
            $pegawaiDept = Pegawai::where('id_departemen', $departemenId)
                ->where('status_pegawai', 'aktif')
                ->get();

            foreach ($pegawaiDept as $pg) {
                $pgJoinDate = $pg->tgl_masuk
                    ? \Carbon\Carbon::parse($pg->tgl_masuk)->startOfDay()
                    : $todayCarbon->copy()->subMonths(6)->startOfDay();
                $maxLookback = $todayCarbon->copy()->subMonths(6)->startOfDay();
                $alphaStart = $pgJoinDate->greaterThan($maxLookback) ? $pgJoinDate : $maxLookback;

                // Tentukan batas absensi untuk pegawai ini (cek lembur)
                $isPgLembur = \App\Models\Lembur::where('id_pegawai', $pg->id_pegawai)->whereDate('tanggal_lembur', $today)->exists();
                $pgBatas    = $isPgLembur ? '21:00:00' : ($jadwalDept->jam_pulang ?? '17:00:00');
                $isPgClosed = now()->format('H:i:s') > $pgBatas;
                
                $pgCheckUntil = $isPgClosed ? $todayCarbon : $yesterday;

                if ($alphaStart->greaterThan($pgCheckUntil)) continue;

                $existingPgDates = Absensi::where('id_pegawai', $pg->id_pegawai)
                    ->whereBetween('tanggal_absensi', [
                        $alphaStart->format('Y-m-d'),
                        $pgCheckUntil->format('Y-m-d')
                    ])
                    ->pluck('tanggal_absensi')
                    ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
                    ->toArray();

                $alphaPeriod = \Carbon\CarbonPeriod::create($alphaStart, $pgCheckUntil);
                $alphaRows = [];
                foreach ($alphaPeriod as $date) {
                    if (!in_array($date->dayOfWeek, $allowedDaysOfficer)) continue;
                    $dateStr = $date->format('Y-m-d');
                    if (!in_array($dateStr, $existingPgDates)) {
                        $alphaRows[] = [
                            'id_pegawai'      => $pg->id_pegawai,
                            'tanggal_absensi' => $dateStr,
                            'status'          => 'alpha',
                            'keterangan'      => 'Tanpa Keterangan',
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }
                if (!empty($alphaRows)) {
                    DB::table('absensi')->insertOrIgnore($alphaRows);
                }
            }
            // ================================================================

            // AUTO-CLOSE Logic for Today's unclosed records
            $openAttendances = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->whereDate('tanggal_absensi', $today)
              ->whereNull('jam_pulang')
              ->whereIn('status', ['hadir', 'terlambat'])
              ->get();

            foreach ($openAttendances as $att) {
                // Ambil jadwal kerja pegawai ini
                $jadwal = \App\Models\JadwalKerja::where('id_departemen', $departemenId)->first();
                $isLembur = \App\Models\Lembur::where('id_pegawai', $att->id_pegawai)
                    ->whereDate('tanggal_lembur', $today)
                    ->exists();
                
                $batasAbsensi = $isLembur ? '21:00:00' : ($jadwal->jam_pulang ?? '17:00:00');

                if ($currentTime > $batasAbsensi) {
                    $statusAbsensi = $isLembur ? 'Lembur tetapi Lupa Absen Pulang' : 'Lupa Absen Pulang';
                    $jamPulang = $batasAbsensi;
                    $jadwalPulang = $jadwal->jam_pulang ?? '17:00:00';
                    
                    $att->update([
                        'jam_pulang' => $jamPulang,
                        'status' => $statusAbsensi,
                        'keterangan' => ($att->keterangan ? $att->keterangan . '; ' : '') . '[Sistem] Auto-close (Melewati Batas Absensi)'
                    ]);
                    
                    if ($isLembur) {
                        $lemburRecord = \App\Models\Lembur::where('id_pegawai', $att->id_pegawai)
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
            }

            // Re-define query for displaying results
            $query = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->with(['pegawai.departemen']);

            if ($request->has('tanggal_dari')) {
                $query->whereDate('tanggal_absensi', '>=', $request->tanggal_dari);
            }

            if ($request->has('tanggal_sampai')) {
                $query->whereDate('tanggal_absensi', '<=', $request->tanggal_sampai);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $absensi = $query->orderBy('tanggal_absensi', 'desc')->get();

            $this->logActivity('read', 'Absensi', null, 'View departemen absensi list');

            return view('officer.absensi.index', compact('absensi'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create absensi untuk pegawai di departemen
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Validate input
            $validated = $request->validate([
                'id_pegawai' => 'required|exists:pegawai,id_pegawai',
                'tanggal_absensi' => 'required|date',
                'jam_masuk' => 'nullable|date_format:H:i',
                'jam_pulang' => 'nullable|date_format:H:i',
                'status' => 'required|in:hadir,sakit,izin,alpha',
                'keterangan' => 'nullable|string|max:255',
            ]);

            // Security check: pegawai harus di departemen Petugas
            $pegawai = Pegawai::where('id_pegawai', $validated['id_pegawai'])
                ->where('id_departemen', $departemenId)
                ->first();

            if (!$pegawai) {
                return back()->with('error', 'Pegawai tidak ada di departemen Anda');
            }

            // Cek duplikasi
            $existing = Absensi::where('id_pegawai', $validated['id_pegawai'])
                ->whereDate('tanggal_absensi', $validated['tanggal_absensi'])
                ->first();

            if ($existing) {
                return back()->with('error', 'Absensi sudah ada untuk tanggal tersebut');
            }

            // Create
            $absensi = Absensi::create($validated);

            // Log
            $this->logActivity('create', 'Absensi', $absensi->id_absensi, 
                'Create absensi ' . $pegawai->nama_pegawai,
                null,
                $absensi->toArray()
            );

            return back()->with('success', 'Absensi berhasil dibuat');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update absensi (hanya draft)
     * 
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Get absensi
            $absensi = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            // Prevent edit if approved
            if ($absensi->status === 'approved') {
                return back()->with('error', 'Tidak bisa edit absensi yang sudah di-approve');
            }

            // Prevent izin if already clocked in
            if ($request->status === 'izin' && !empty($absensi->jam_masuk)) {
                return back()->with('error', 'Pegawai sudah masuk, tidak bisa diberikan izin.');
            }

            // Validate
            $validated = $request->validate([
                'jam_masuk' => 'nullable|date_format:H:i',
                'jam_pulang' => 'nullable|date_format:H:i',
                'status' => 'required|in:hadir,sakit,izin,alpha',
                'keterangan' => 'nullable|string|max:255',
            ]);

            // Save old values
            $oldValues = $absensi->toArray();

            // Update
            $absensi->update($validated);

            // Jika jam_pulang diisi, finalisasi record lembur pending jika ada
            if (!empty($validated['jam_pulang'])) {
                $lemburOtomatis = \App\Models\Lembur::where('id_pegawai', $absensi->id_pegawai)
                    ->whereDate('tanggal_lembur', $absensi->tanggal_absensi)
                    ->where('status', 'pending')
                    ->whereNotNull('jam_mulai')
                    ->first();

                if ($lemburOtomatis) {
                    $jamMulai   = \Carbon\Carbon::parse($absensi->tanggal_absensi . ' ' . $lemburOtomatis->jam_mulai);
                    $jamSelesai = \Carbon\Carbon::parse($absensi->tanggal_absensi . ' ' . $validated['jam_pulang']);
                    $durasiJam  = round($jamMulai->diffInMinutes($jamSelesai) / 60, 2);

                    $lemburOtomatis->jam_selesai = $validated['jam_pulang'];
                    $lemburOtomatis->durasi      = max(0, $durasiJam);
                    $lemburOtomatis->save();
                }
            }

            // Log
            $this->logActivity('update', 'Absensi', $id, 'Update absensi', $oldValues, $absensi->toArray());

            return back()->with('success', 'Absensi berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Approve absensi untuk departemen
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Get absensi
            $absensi = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            // Save old
            $oldValues = $absensi->toArray();

            // Update
            $absensi->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $officer->id,
            ]);

            // Log
            $this->logActivity('update', 'Absensi', $id, 'Approve absensi', $oldValues, $absensi->toArray());

            return back()->with('success', 'Absensi berhasil di-approve');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete absensi (hanya draft)
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Get absensi
            $absensi = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            // Prevent delete if approved
            if ($absensi->status === 'approved') {
                return back()->with('error', 'Tidak bisa delete absensi yang sudah di-approve');
            }

            // Log
            $this->logActivity('delete', 'Absensi', $id, 'Delete absensi', $absensi->toArray());

            // Delete
            $absensi->delete();

            return back()->with('success', 'Absensi berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get summary & stats untuk departemen
     * 
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $bulan = $request->input('bulan', now()->month);
            $tahun = $request->input('tahun', now()->year);

            // Stats absensi
            $stats = [
                'total_pegawai' => Pegawai::where('id_departemen', $departemenId)->where('status_pegawai', 'aktif')->count(),
                'hadir' => Absensi::whereMonth('tanggal_absensi', $bulan)
                    ->whereYear('tanggal_absensi', $tahun)
                    ->where('status', 'hadir')
                    ->whereHas('pegawai', fn($q) => $q->where('id_departemen', $departemenId))
                    ->count(),
                'sakit' => Absensi::whereMonth('tanggal_absensi', $bulan)
                    ->whereYear('tanggal_absensi', $tahun)
                    ->where('status', 'sakit')
                    ->whereHas('pegawai', fn($q) => $q->where('id_departemen', $departemenId))
                    ->count(),
                'izin' => Absensi::whereMonth('tanggal_absensi', $bulan)
                    ->whereYear('tanggal_absensi', $tahun)
                    ->where('status', 'izin')
                    ->whereHas('pegawai', fn($q) => $q->where('id_departemen', $departemenId))
                    ->count(),
                'alpha' => Absensi::whereMonth('tanggal_absensi', $bulan)
                    ->whereYear('tanggal_absensi', $tahun)
                    ->where('status', 'alpha')
                    ->whereHas('pegawai', fn($q) => $q->where('id_departemen', $departemenId))
                    ->count(),
            ];

            // Log
            $this->logActivity('read', 'Absensi', null, 'View department absensi summary');

            return $this->responseSuccess($stats, 'Summary absensi berhasil diambil');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
