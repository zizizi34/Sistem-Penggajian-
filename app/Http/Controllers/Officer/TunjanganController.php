<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Tunjangan;
use Illuminate\Http\Request;

class TunjanganController extends Controller
{
    public function index()
    {
        $tunjangan = Tunjangan::all();
        return view('officer.tunjangan.index', compact('tunjangan'));
    }
}
