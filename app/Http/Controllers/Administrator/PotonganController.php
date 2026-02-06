<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Potongan;
use Illuminate\Http\Request;

class PotonganController extends Controller
{
    public function index()
    {
        $potongan = Potongan::all();
        return view('administrator.potongan.index', compact('potongan'));
    }

    public function create()
    {
        return view('administrator.potongan.create');
    }

    public function store(Request $request)
    {
        Potongan::create($request->validate([
            'nama_potongan' => 'required|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.potongan.index')->with('success', 'Potongan berhasil ditambah');
    }

    public function edit(Potongan $potongan)
    {
        return view('administrator.potongan.edit', compact('potongan'));
    }

    public function update(Request $request, Potongan $potongan)
    {
        $potongan->update($request->validate([
            'nama_potongan' => 'required|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.potongan.index')->with('success', 'Potongan berhasil diubah');
    }

    public function destroy(Potongan $potongan)
    {
        $potongan->delete();
        return redirect()->route('administrators.potongan.index')->with('success', 'Potongan berhasil dihapus');
    }
}
