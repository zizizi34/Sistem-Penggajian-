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

    public function show($id)
    {
        $tunjangan = Tunjangan::findOrFail($id);
        return view('administrator.tunjangan.show', compact('tunjangan'));
    }

    public function edit($id)
    {
        $tunjangan = Tunjangan::findOrFail($id);
        return view('administrator.tunjangan.edit', compact('tunjangan'));
    }

    public function update(Request $request, $id)
    {
        $tunjangan = Tunjangan::findOrFail($id);
        $tunjangan->update($request->validate([
            'nama_tunjangan' => 'required|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.tunjangan.index')->with('success', 'Tunjangan berhasil diubah');
    }

    public function destroy($id)
    {
        try {
            $tunjangan = Tunjangan::findOrFail($id);
            $tunjangan->delete();
            return redirect()->route('administrators.tunjangan.index')->with('success', 'Tunjangan berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('administrators.tunjangan.index')->with('error', 'Tunjangan tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('Delete tunjangan error: ' . $e->getMessage());
            return redirect()->route('administrators.tunjangan.index')->with('error', 'Gagal menghapus tunjangan');
        }
    }
}
