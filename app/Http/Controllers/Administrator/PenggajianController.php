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
}
