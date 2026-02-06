<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Penggajian;
use App\Models\Potongan;
use App\Models\Tunjangan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $recentPenggajian = collect([]);
            try {
                $recentPenggajian = Penggajian::with('pegawai')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                // Jika query error, return empty collection
                $recentPenggajian = collect([]);
            }
            
            return view('administrator.dashboard', [
                'totalPegawai' => Pegawai::count(),
                'totalDepartemen' => Departemen::count(),
                'totalJabatan' => Jabatan::count(),
                'totalTunjangan' => Tunjangan::count(),
                'totalPotongan' => Potongan::count(),
                'totalPenggajian' => Penggajian::count(),
                'recentPenggajian' => $recentPenggajian,
            ]);
        } catch (\Exception $e) {
            // Jika ada error, return dashboard dengan data minimal
            return view('administrator.dashboard', [
                'totalPegawai' => 0,
                'totalDepartemen' => 0,
                'totalJabatan' => 0,
                'totalTunjangan' => 0,
                'totalPotongan' => 0,
                'totalPenggajian' => 0,
                'recentPenggajian' => collect([]),
            ]);
        }
    }
}

