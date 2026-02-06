<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('student.attendance.index');
    }
}
