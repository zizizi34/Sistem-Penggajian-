<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        return view('student.payroll.index');
    }
}
