<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use Illuminate\Http\Request;

class PenggajianController extends Controller
{
    public function index()
    {
        $penggajian = Penggajian::with('pegawai')->get();
        return view('administrator.penggajian.index', compact('penggajian'));
    }

    public function show(Penggajian $penggajian)
    {
        return view('administrator.penggajian.show', compact('penggajian'));
    }

    public function calculate(Request $request)
    {
        $periode = $request->periode ?: now()->format('Y-m');
        $salaryService = app(\App\Services\SalaryCalculationService::class);
        $pegawais = \App\Models\Pegawai::where('status_pegawai', 'aktif')->get();

        $count = 0;
        foreach ($pegawais as $pegawai) {
            $result = $salaryService->calculateMonthlySalary($pegawai, $periode);
            if ($result['status'] === 'success') {
                $salaryService->saveSalaryCalculation($pegawai, $periode, $result);
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data gaji berhasil dihitung untuk periode ' . $periode);
    }
}
