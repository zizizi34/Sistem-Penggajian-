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

        $overtimeList = [];
        $currentTime = \Carbon\Carbon::now();
        foreach ($absensis as $ab) {
            if (!$ab->jam_pulang) {
                $jadwal = $jadwals[$ab->pegawai->id_departemen ?? null] ?? null;
                $jadwalPulangTime = $jadwal ? \Carbon\Carbon::parse($jadwal->jam_pulang) : null;
                
                $activeLembur = $lemburList->where('id_pegawai', $ab->id_pegawai)
                    ->where('status', 'pending')
                    ->where('jam_selesai', null)
                    ->first();

                if ($activeLembur && $jadwalPulangTime && $currentTime->greaterThan($jadwalPulangTime)) {
                    $ab->overtime_menit = $jadwalPulangTime->diffInMinutes($currentTime);
                    $overtimeList[] = $ab;
                }
            }
        }

        $isHrOfficer = false;
        if ($officer->departemen) {
            $namaMyDept = strtolower($officer->departemen->nama_departemen);
            $isHrOfficer = str_contains($namaMyDept, 'human resources') || str_contains($namaMyDept, 'hr');
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
            'lemburList' => collect($lemburList),
            'overtimeList' => collect($overtimeList),
            'isHrOfficer' => $isHrOfficer
        ]);
    }
}
