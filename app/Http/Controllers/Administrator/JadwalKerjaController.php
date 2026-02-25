<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\JadwalKerja;
use Illuminate\Http\Request;

class JadwalKerjaController extends Controller
{
    public function index()
    {
        $jadwals = JadwalKerja::with('departemen')->get();
        $departemens = Departemen::all();
        return view('administrator.jadwal.index', compact('jadwals', 'departemens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'hari' => 'required|string',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_terlambat' => 'required|integer|min:0',
        ]);

        JadwalKerja::updateOrCreate(
            ['id_departemen' => $validated['id_departemen']],
            $validated
        );

        return redirect()->back()->with('success', 'Jadwal kerja berhasil disimpan');
    }

    public function destroy($id)
    {
        $jadwal = JadwalKerja::findOrFail($id);
        $jadwal->delete();
        return redirect()->back()->with('success', 'Jadwal kerja berhasil dihapus');
    }
}
