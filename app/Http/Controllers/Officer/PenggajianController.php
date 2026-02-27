<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use Illuminate\Http\Request;

class PenggajianController extends Controller
{
    public function index()
    {
        $officer = auth('officer')->user();
        $departemenId = $officer->id_departemen;

        $penggajian = Penggajian::whereHas('pegawai', function ($q) use ($departemenId) {
            $q->where('id_departemen', $departemenId);
        })->with('pegawai')->get();

        return view('officer.penggajian.index', compact('penggajian'));
    }

    public function show($id)
    {
        $officer = auth('officer')->user();
        $departemenId = $officer->id_departemen;

        $penggajian = Penggajian::whereHas('pegawai', function ($q) use ($departemenId) {
            $q->where('id_departemen', $departemenId);
        })->with('pegawai')->findOrFail($id);

        return view('officer.penggajian.show', compact('penggajian'));
    }
}
