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

    public function show($id)
    {
        $ptkpStatus = PtkpStatus::findOrFail($id);
        return view('administrator.ptkp-status.show', compact('ptkpStatus'));
    }

    public function edit($id)
    {
        $ptkpStatus = PtkpStatus::findOrFail($id);
        return view('administrator.ptkp-status.edit', compact('ptkpStatus'));
    }

    public function update(Request $request, $id)
    {
        $ptkpStatus = PtkpStatus::findOrFail($id);
        $ptkpStatus->update($request->validate([
            'kode_ptkp_status' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.ptkp-status.index')->with('success', 'Status PTKP berhasil diubah');
    }

    public function destroy($id)
    {
        try {
            $ptkpStatus = PtkpStatus::findOrFail($id);
            $ptkpStatus->delete();
            return redirect()->route('administrators.ptkp-status.index')->with('success', 'Status PTKP berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('administrators.ptkp-status.index')->with('error', 'Status PTKP tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('Delete ptkp status error: ' . $e->getMessage());
            return redirect()->route('administrators.ptkp-status.index')->with('error', 'Gagal menghapus status PTKP');
        }
    }
}
