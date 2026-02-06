<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Penggajian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('officer.dashboard', [
            'totalPegawai' => Pegawai::count(),
            'totalDepartemen' => Departemen::count(),
            'totalJabatan' => Jabatan::count(),
            'totalPenggajian' => Penggajian::count(),
            'recentPenggajian' => Penggajian::with('pegawai')->take(5)->get(),
        ]);
    }
}

