<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Penggajian;
use App\Models\Lembur; // Import Lembur model
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $officer = auth('officer')->user();
        $isRestricted = !is_null($officer->id_departemen);
        $idDept = $officer->id_departemen;

        $todayDate = now()->format('Y-m-d');
        
        $absensiQuery = \App\Models\Absensi::with(['pegawai.departemen'])
            ->where('tanggal_absensi', $todayDate);
            
        if ($isRestricted) {
           $absensiQuery->whereHas('pegawai', function($q) use ($idDept) {
               $q->where('id_departemen', $idDept);
           });
        }
           
        $absensis = $absensiQuery->get();
        
        // Ambil SEMUA jadwal tanpa filter hari — karena nilai hari di DB adalah rentang
        // seperti 'Senin-Jumat', bukan nama hari tunggal. Filter hari tidak relevan di sini.
        $jadwalQuery = \App\Models\JadwalKerja::query();
        if ($isRestricted) {
            $jadwalQuery->where('id_departemen', $idDept);
        }
        $jadwals = $jadwalQuery->get()->keyBy('id_departemen');
        
        $terlambatList = [];
        foreach ($absensis as $ab) {
            if (!$ab->pegawai || !$ab->pegawai->id_departemen) continue;
            $jadwal = $jadwals[$ab->pegawai->id_departemen] ?? null;
            if ($jadwal && $ab->jam_masuk) {
                $masukTime = \Carbon\Carbon::parse($ab->jam_masuk);
                $jadwalMasuk = \Carbon\Carbon::parse($jadwal->jam_masuk);
                $toleransi = $jadwal->toleransi_terlambat ?? 0;
                
                $limitMasuk = $jadwalMasuk->copy()->addMinutes($toleransi);
                if ($masukTime->greaterThan($limitMasuk)) {
                    $ab->terlambat_menit = $masukTime->diffInMinutes($limitMasuk);
                    $terlambatList[] = $ab;
                }
            }
        }
        
        // Logic for detecting automatic overtime
        // Ambil pegawai yang sudah masuk tapi belum pulang (Hari ini dan H-1)
        $overtimeCandidatesQuery = \App\Models\Absensi::with(['pegawai.departemen'])
            ->whereBetween('tanggal_absensi', [now()->subDays(1)->format('Y-m-d'), $todayDate])
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang');
            
        if ($isRestricted) {
            $overtimeCandidatesQuery->whereHas('pegawai', function($q) use ($idDept) {
                $q->where('id_departemen', $idDept);
            });
        }
        $overtimeCandidates = $overtimeCandidatesQuery->get();
        
        $overtimeList = [];
        $currentTime = Carbon::now();
        
        foreach ($overtimeCandidates as $ab) {
            if (!$ab->pegawai || !$ab->pegawai->id_departemen) continue;
            $jadwal = $jadwals[$ab->pegawai->id_departemen] ?? null;
            if ($jadwal && $jadwal->jam_pulang) {
                // Gunakan tanggal absensi terkait, bukan hari ini
                $absensiDate = $ab->tanggal_absensi;
                $jadwalPulang = Carbon::parse($absensiDate . ' ' . $jadwal->jam_pulang);
                
                // Cek waktu referensi: jika sudah beda hari dengan absensi, cap di 23:59 (akhir hari)
                $refTime = $currentTime->copy();
                if ($currentTime->format('Y-m-d') > $absensiDate) {
                    $refTime = Carbon::parse($absensiDate . ' 23:59:59');
                }

                // Jika jam sekarang (atau batas akhir hari) sudah melewati jam jadwal pulang → dianggap lembur
                if ($refTime->greaterThan($jadwalPulang)) {
                    $overtimeMenit = (int) $refTime->diffInMinutes($jadwalPulang);
                    $ab->overtime_menit = $overtimeMenit;
                    $overtimeList[] = $ab;

                    // Auto-create record lembur di database jika belum ada untuk tanggal tersebut
                    $existingLembur = Lembur::where('id_pegawai', $ab->id_pegawai)
                        ->whereDate('tanggal_lembur', $absensiDate)
                        ->first();

                    if (!$existingLembur) {
                        Lembur::create([
                            'id_pegawai'     => $ab->id_pegawai,
                            'tanggal_lembur' => $absensiDate,
                            'jam_mulai'      => $jadwal->jam_pulang, // Lembur mulai dari jam jadwal pulang
                            'jam_selesai'    => $refTime->format('H:i:s'), 
                            'durasi'         => round($overtimeMenit / 60, 2),
                            'keterangan'     => 'Lembur otomatis terdeteksi - belum absen pulang',
                            'status'         => 'pending',
                        ]);
                    } else {
                        // Update jam selesai sementara jika masih pending
                        if ($existingLembur->status === 'pending') {
                            $existingLembur->jam_selesai = $refTime->format('H:i:s');
                            $existingLembur->durasi      = round($overtimeMenit / 60, 2);
                            $existingLembur->save();
                        }
                    }
                }
            }
        }
        
        // Load lemburList SETELAH auto-create agar data terbaru langsung muncul di dashboard
        $lemburListQuery = \Illuminate\Support\Facades\DB::table('lembur')
            ->join('pegawai', 'lembur.id_pegawai', '=', 'pegawai.id_pegawai')
            ->where('tanggal_lembur', $todayDate)
            ->select('lembur.*', 'pegawai.nama_pegawai');
            
        if ($isRestricted) {
            $lemburListQuery->where('pegawai.id_departemen', $idDept);
        }
        $lemburList = $lemburListQuery->get();
        
        $pegawaiQuery = Pegawai::query();
        $penggajianQuery = Penggajian::with('pegawai');
        
        if ($isRestricted) {
            $pegawaiQuery->where('id_departemen', $idDept);
            $penggajianQuery->whereHas('pegawai', function($q) use ($idDept) {
               $q->where('id_departemen', $idDept);
           });
        }

        return view('officer.dashboard', [
            'totalPegawai' => $pegawaiQuery->count(),
            'totalDepartemen' => $isRestricted ? 1 : Departemen::count(),
            'totalJabatan' => $isRestricted ? Jabatan::where('id_departemen', $idDept)->count() : Jabatan::count(),
            'totalPenggajian' => $penggajianQuery->count(),
            'recentPenggajian' => $penggajianQuery->take(5)->get(),
            'totalHadir' => $absensis->whereIn('status', ['hadir', 'approved'])->count(),
            'recentAbsensi' => $absensis->sortByDesc('jam_masuk')->take(5),
            'terlambatList' => collect($terlambatList)->sortByDesc('jam_masuk')->values(),
            'lemburList' => $lemburList,
            'overtimeList' => collect($overtimeList)->sortByDesc('overtime_menit')->values(),
        ]);
    }
}
