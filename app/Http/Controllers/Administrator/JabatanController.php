<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Departemen;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatan = Jabatan::with('departemen')->get();
        return view('administrator.jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        $departemen = Departemen::all();
        return view('administrator.jabatan.create', compact('departemen'));
    }

    public function store(Request $request)
    {
        Jabatan::create($request->validate([
            'nama_jabatan' => 'required|string',
            'min_gaji' => 'nullable|numeric',
            'max_gaji' => 'nullable|numeric',
            'id_departemen' => 'nullable|integer'
        ]));
        return redirect()->route('administrators.jabatan.index')->with('success', 'Jabatan berhasil ditambah');
    }

    public function edit(Jabatan $jabatan)
    {
        $departemen = Departemen::all();
        return view('administrator.jabatan.edit', compact('jabatan', 'departemen'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $jabatan->update($request->validate([
            'nama_jabatan' => 'required|string',
            'min_gaji' => 'nullable|numeric',
            'max_gaji' => 'nullable|numeric',
            'id_departemen' => 'nullable|integer'
        ]));
        return redirect()->route('administrators.jabatan.index')->with('success', 'Jabatan berhasil diubah');
    }

    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();
        return redirect()->route('administrators.jabatan.index')->with('success', 'Jabatan berhasil dihapus');
    }
}
