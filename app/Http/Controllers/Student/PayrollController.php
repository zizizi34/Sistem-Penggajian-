<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $user = auth('student')->user();
        if (!$user || !$user->id_pegawai) {
            abort(403, 'Unauthorized access');
        }

        $payrolls = Penggajian::with(['pegawai.jabatan', 'pegawai.departemen'])
            ->where('id_pegawai', $user->id_pegawai)
            ->orderBy('periode', 'desc')
            ->get();

        return view('student.payroll.index', compact('payrolls'));
    }
}
