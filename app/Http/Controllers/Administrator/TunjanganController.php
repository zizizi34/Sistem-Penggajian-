<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Tunjangan;
use Illuminate\Http\Request;

class TunjanganController extends Controller
{
    public function index()
    {
        $tunjangan = Tunjangan::all();
        return view('administrator.tunjangan.index', compact('tunjangan'));
    }

    public function create()
    {
        return view('administrator.tunjangan.create');
    }

    public function store(Request $request)
    {
        Tunjangan::create($request->validate([
            'nama_tunjangan' => 'required|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.tunjangan.index')->with('success', 'Tunjangan berhasil ditambah');
    }

    public function edit(Tunjangan $tunjangan)
    {
        return view('administrator.tunjangan.edit', compact('tunjangan'));
    }

    public function update(Request $request, Tunjangan $tunjangan)
    {
        $tunjangan->update($request->validate([
            'nama_tunjangan' => 'required|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.tunjangan.index')->with('success', 'Tunjangan berhasil diubah');
    }

    public function destroy(Tunjangan $tunjangan)
    {
        $tunjangan->delete();
        return redirect()->route('administrators.tunjangan.index')->with('success', 'Tunjangan berhasil dihapus');
    }
}
