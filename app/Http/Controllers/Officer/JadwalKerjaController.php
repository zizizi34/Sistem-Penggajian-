<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\JadwalKerja;
use Illuminate\Http\Request;

class JadwalKerjaController extends Controller
{
    /**
     * Ambil ID departemen petugas yang sedang login.
     */
    private function getOfficerDeptId(): int
    {
        return (int) auth('officer')->user()->id_departemen;
    }

    /**
     * Tampilkan jadwal kerja HANYA untuk departemen petugas sendiri.
     */
    public function index()
    {
        $deptId      = $this->getOfficerDeptId();
        $departemen  = Departemen::findOrFail($deptId);   // Info departemen sendiri
        $jadwals     = JadwalKerja::with('departemen')
                            ->where('id_departemen', $deptId)
                            ->get();

        return view('officer.jadwal.index', compact('jadwals', 'departemen'));
    }

    /**
     * Simpan jadwal — id_departemen selalu diset ke departemen petugas sendiri
     * agar tidak bisa manipulasi ke departemen lain.
     */
    public function store(Request $request)
    {
        $deptId = $this->getOfficerDeptId();

        $validated = $request->validate([
            'hari'                 => 'required|string',
            'jam_masuk'            => 'required',
            'jam_pulang'           => 'required',
            'toleransi_terlambat'  => 'required|integer|min:0',
        ]);

        // Paksa departemen sesuai petugas — input dari form diabaikan
        $validated['id_departemen'] = $deptId;

        JadwalKerja::updateOrCreate(
            ['id_departemen' => $deptId],
            $validated
        );

        return redirect()->back()->with('success', 'Jadwal kerja berhasil disimpan.');
    }

    /**
     * Hapus jadwal — hanya boleh hapus jadwal milik departemen sendiri.
     */
    public function destroy($id)
    {
        $deptId = $this->getOfficerDeptId();

        $jadwal = JadwalKerja::where('id_jadwal', $id)
                              ->where('id_departemen', $deptId)
                              ->firstOrFail();

        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal kerja berhasil dihapus.');
    }
}
