<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Pegawai;
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

    public function show($id)
    {
        $departemen = Departemen::findOrFail($id);
        return view('administrator.departemen.show', compact('departemen'));
    }

    public function edit($id)
    {
        $departemen = Departemen::findOrFail($id);
        return view('administrator.departemen.edit', compact('departemen'));
    }

    public function update(Request $request, $id)
    {
        $departemen = Departemen::findOrFail($id);
        $departemen->update($request->validate([
            'nama_departemen' => 'required|string',
            'manager_departemen' => 'nullable|integer'
        ]));
        return redirect()->route('administrators.departemen.index')->with('success', 'Departemen berhasil diubah');
    }

    public function destroy($id)
    {
        try {
            // Get departemen by ID explicitly
            $departemen = Departemen::findOrFail($id);
            
            // Check if there are related Jabatan records
            $jabatanCount = Jabatan::where('id_departemen', $id)->count();
            
            // Check if there are related Pegawai records
            $pegawaiCount = Pegawai::where('id_departemen', $id)->count();
            
            if ($jabatanCount > 0 || $pegawaiCount > 0) {
                return redirect()->route('administrators.departemen.index')
                    ->with('warning', 'Tidak dapat menghapus departemen karena masih memiliki data terkait (Jabatan: ' . $jabatanCount . ', Pegawai: ' . $pegawaiCount . ')');
            }
            
            // Delete the departemen
            $departemen->delete();
            
            return redirect()->route('administrators.departemen.index')
                ->with('success', 'Departemen berhasil dihapus');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('administrators.departemen.index')
                ->with('error', 'Departemen tidak ditemukan');
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Delete departemen database error: ' . $e->getMessage());
            return redirect()->route('administrators.departemen.index')
                ->with('error', 'Gagal menghapus departemen: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Delete departemen exception: ' . $e->getMessage());
            return redirect()->route('administrators.departemen.index')
                ->with('error', 'Gagal menghapus departemen: ' . $e->getMessage());
        }
    }
}
