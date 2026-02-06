<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Potongan;
use Illuminate\Http\Request;

class PotonganController extends Controller
{
    public function index()
    {
        $potongan = Potongan::all();
        return view('officer.potongan.index', compact('potongan'));
    }
}
