<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\BaseController;
use App\Models\Lembur;
use App\Models\Pegawai;
use Illuminate\Http\Request;

/**
 * Officer LemburController - Department Scoped
 * 
 * Controller untuk Petugas (Officer) mengelola lembur.
 * Sama pattern seperti AbsensiController.
 * 
 * @author Your Name
 * @version 1.0
 */
class LemburController extends BaseController
{
    /**
     * Display lembur untuk departemen Petugas
     */
    public function index(Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $query = Lembur::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->with(['pegawai.departemen']);

            if ($request->has('tanggal_dari')) {
                $query->whereDate('tanggal_lembur', '>=', $request->tanggal_dari);
            }

            if ($request->has('tanggal_sampai')) {
                $query->whereDate('tanggal_lembur', '<=', $request->tanggal_sampai);
            }

            $lembur = $query->orderBy('tanggal_lembur', 'desc')->get();

            // Ambil daftar pegawai di departemen ini untuk dropdown "Beri Lembur"
            $employees = Pegawai::where('id_departemen', $departemenId)
                ->orderBy('nama_pegawai')
                ->get();

            $this->logActivity('read', 'Lembur', null, 'View departemen lembur list');

            return view('officer.lembur.index', compact('lembur', 'employees'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store lembur
     */
    public function store(Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $validated = $request->validate([
                'id_pegawai' => 'required|exists:pegawai,id_pegawai',
                'tanggal_lembur' => 'required|date',
                'jam_mulai' => 'nullable|date_format:H:i',
                'jam_selesai' => 'nullable|date_format:H:i',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $pegawai = Pegawai::where('id_pegawai', $validated['id_pegawai'])
                ->where('id_departemen', $departemenId)
                ->first();

            if (!$pegawai) {
                return $this->responseForbidden('Pegawai tidak ada di departemen Anda');
            }

            $existing = Lembur::where('id_pegawai', $validated['id_pegawai'])
                ->whereDate('tanggal_lembur', $validated['tanggal_lembur'])
                ->first();

            if ($existing) {
                return back()->with('error', 'Lembur sudah ada untuk tanggal tersebut');
            }

            $hasAbsenPulang = \App\Models\Absensi::where('id_pegawai', $validated['id_pegawai'])
                ->whereDate('tanggal_absensi', $validated['tanggal_lembur'])
                ->whereNotNull('jam_pulang')
                ->exists();

            if ($hasAbsenPulang) {
                return back()->with('error', 'Pegawai sudah absen pulang, tidak bisa diberikan jadwal lembur.');
            }

            $validated['status'] = 'pending';
            $lembur = Lembur::create($validated);

            $this->logActivity('create', 'Lembur', $lembur->id_lembur, 
                'Create lembur ' . $pegawai->nama_pegawai,
                null,
                $lembur->toArray()
            );

            if ($request->ajax()) {
                return $this->responseSuccess($lembur, 'Lembur berhasil dibuat', 201);
            }
            
            return back()->with('success', 'Berhasil memberikan jatah lembur kepada ' . $pegawai->nama_pegawai);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return $this->responseError('Validasi gagal', 422, $e->errors());
            }
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return $this->responseError($e->getMessage(), 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show lembur detail
     */
    public function show($id)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $lembur = Lembur::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->with(['pegawai', 'pegawai.departemen'])
                ->findOrFail($id);

            $this->logActivity('read', 'Lembur', $id, 'View lembur detail');

            return $this->responseSuccess($lembur);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Update lembur
     */
    public function update($id, Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $lembur = Lembur::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            if ($lembur->status === 'approved') {
                return $this->responseError('Tidak bisa edit lembur yang sudah di-approve', 400);
            }

            $validated = $request->validate([
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $oldValues = $lembur->toArray();
            $lembur->update($validated);

            $this->logActivity('update', 'Lembur', $id, 'Update lembur', $oldValues, $lembur->toArray());

            return $this->responseSuccess($lembur, 'Lembur berhasil diupdate');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Approve lembur
     */
    public function approve($id)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $lembur = Lembur::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            $oldValues = $lembur->toArray();

            $lembur->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $officer->id,
            ]);

            $this->logActivity('update', 'Lembur', $id, 'Approve lembur', $oldValues, $lembur->toArray());

            return back()->with('success', 'Lembur berhasil di-approve');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete lembur
     */
    public function destroy($id)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $lembur = Lembur::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            if ($lembur->status === 'approved') {
                return $this->responseError('Tidak bisa delete lembur yang sudah di-approve', 400);
            }

            $this->logActivity('delete', 'Lembur', $id, 'Delete lembur', $lembur->toArray());
            $lembur->delete();

            return back()->with('success', 'Lembur berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
