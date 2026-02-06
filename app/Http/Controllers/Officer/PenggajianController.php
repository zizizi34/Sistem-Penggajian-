<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use Illuminate\Http\Request;

class PenggajianController extends Controller
{
    public function index()
    {
        $penggajian = Penggajian::with('pegawai')->get();
        return view('officer.penggajian.index', compact('penggajian'));
    }
}
