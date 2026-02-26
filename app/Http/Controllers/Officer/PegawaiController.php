<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        $officer = auth('officer')->user();
        $query = Pegawai::with('departemen', 'jabatan');
        
        if ($officer->id_departemen) {
            $query->where('id_departemen', $officer->id_departemen);
        }
        
        $pegawai = $query->get();
        return view('officer.pegawai.index', compact('pegawai'));
    }
}
