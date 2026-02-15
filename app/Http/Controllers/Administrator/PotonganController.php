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

    public function show($id)
    {
        $potongan = Potongan::findOrFail($id);
        return view('administrator.potongan.show', compact('potongan'));
    }

    public function edit($id)
    {
        $potongan = Potongan::findOrFail($id);
        return view('administrator.potongan.edit', compact('potongan'));
    }

    public function update(Request $request, $id)
    {
        $potongan = Potongan::findOrFail($id);
        $potongan->update($request->validate([
            'nama_potongan' => 'required|string',
            'nominal' => 'required|numeric'
        ]));
        return redirect()->route('administrators.potongan.index')->with('success', 'Potongan berhasil diubah');
    }

    public function destroy($id)
    {
        try {
            $potongan = Potongan::findOrFail($id);
            $potongan->delete();
            return redirect()->route('administrators.potongan.index')->with('success', 'Potongan berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('administrators.potongan.index')->with('error', 'Potongan tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('Delete potongan error: ' . $e->getMessage());
            return redirect()->route('administrators.potongan.index')->with('error', 'Gagal menghapus potongan');
        }
    }
}
