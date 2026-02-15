<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\Pegawai;
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

    public function show($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('administrator.jabatan.show', compact('jabatan'));
    }

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $departemen = Departemen::all();
        return view('administrator.jabatan.edit', compact('jabatan', 'departemen'));
    }

    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update($request->validate([
            'nama_jabatan' => 'required|string',
            'min_gaji' => 'nullable|numeric',
            'max_gaji' => 'nullable|numeric',
            'id_departemen' => 'nullable|integer'
        ]));
        return redirect()->route('administrators.jabatan.index')->with('success', 'Jabatan berhasil diubah');
    }

    public function destroy($id)
    {
        try {
            $jabatan = Jabatan::findOrFail($id);
            
            // Check if there are related Pegawai records
            $pegawaiCount = Pegawai::where('id_jabatan', $id)->count();
            
            if ($pegawaiCount > 0) {
                return redirect()->route('administrators.jabatan.index')
                    ->with('warning', 'Tidak dapat menghapus jabatan karena masih memiliki ' . $pegawaiCount . ' pegawai terkait');
            }
            
            $jabatan->delete();
            return redirect()->route('administrators.jabatan.index')->with('success', 'Jabatan berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('administrators.jabatan.index')->with('error', 'Jabatan tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('Delete jabatan error: ' . $e->getMessage());
            return redirect()->route('administrators.jabatan.index')->with('error', 'Gagal menghapus jabatan');
        }
    }
}
