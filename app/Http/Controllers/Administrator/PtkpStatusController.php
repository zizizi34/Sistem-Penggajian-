<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\PtkpStatus;
use Illuminate\Http\Request;

class PtkpStatusController extends Controller
{
    public function index()
    {
        $ptkpStatus = PtkpStatus::all();
        return view('administrator.ptkp-status.index', compact('ptkpStatus'));
    }

    public function create()
    {
        return view('administrator.ptkp-status.create');
    }

    public function store(Request $request)
    {
        PtkpStatus::create($request->validate([
            'kode_ptkp_status' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.ptkp-status.index')->with('success', 'Status PTKP berhasil ditambah');
    }

    public function edit(PtkpStatus $ptkpStatus)
    {
        return view('administrator.ptkp-status.edit', compact('ptkpStatus'));
    }

    public function update(Request $request, PtkpStatus $ptkpStatus)
    {
        $ptkpStatus->update($request->validate([
            'kode_ptkp_status' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.ptkp-status.index')->with('success', 'Status PTKP berhasil diubah');
    }

    public function destroy(PtkpStatus $ptkpStatus)
    {
        $ptkpStatus->delete();
        return redirect()->route('administrators.ptkp-status.index')->with('success', 'Status PTKP berhasil dihapus');
    }
}
