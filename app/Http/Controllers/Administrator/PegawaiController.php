<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::with('departemen', 'jabatan')->get();
        return view('administrator.pegawai.index', compact('pegawai'));
    }

    public function show(Pegawai $pegawai)
    {
        return view('administrator.pegawai.show', compact('pegawai'));
    }
}
