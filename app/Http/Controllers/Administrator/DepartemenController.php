<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemen = Departemen::all();
        return view('administrator.departemen.index', compact('departemen'));
    }

    public function create()
    {
        return view('administrator.departemen.create');
    }

    public function store(Request $request)
    {
        Departemen::create($request->validate([
            'nama_departemen' => 'required|string',
            'manager_departemen' => 'nullable|integer'
        ]));
        return redirect()->route('administrators.departemen.index')->with('success', 'Departemen berhasil ditambah');
    }

    public function edit(Departemen $departemen)
    {
        return view('administrator.departemen.edit', compact('departemen'));
    }

    public function update(Request $request, Departemen $departemen)
    {
        $departemen->update($request->validate([
            'nama_departemen' => 'required|string',
            'manager_departemen' => 'nullable|integer'
        ]));
        return redirect()->route('administrators.departemen.index')->with('success', 'Departemen berhasil diubah');
    }

    public function destroy(Departemen $departemen)
    {
        $departemen->delete();
        return redirect()->route('administrators.departemen.index')->with('success', 'Departemen berhasil dihapus');
    }
}
