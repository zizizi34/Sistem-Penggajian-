<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\BaseController;
use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;

/**
 * Officer AbsensiController - Department Scoped
 * 
 * Controller untuk Petugas (Officer) mengelola absensi.
 * 
 * Batasan:
 * - HANYA bisa input & manage absensi untuk pegawai di departemen sendiri
 * - TIDAK bisa edit/delete yang sudah di-approve
 * - HANYA approve di level departemen (tidak approve penggajian)
 * 
 * @author Your Name
 * @version 1.0
 */
class AbsensiController extends BaseController
{
    /**
     * Display absensi untuk departemen Petugas
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Query absensi untuk departemen ini
            $query = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->with(['pegawai.departemen']);

            if ($request->has('tanggal_dari')) {
                $query->whereDate('tanggal_absensi', '>=', $request->tanggal_dari);
            }

            if ($request->has('tanggal_sampai')) {
                $query->whereDate('tanggal_absensi', '<=', $request->tanggal_sampai);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $absensi = $query->orderBy('tanggal_absensi', 'desc')->get();

            $this->logActivity('read', 'Absensi', null, 'View departemen absensi list');

            return view('officer.absensi.index', compact('absensi'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create absensi untuk pegawai di departemen
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Validate input
            $validated = $request->validate([
                'id_pegawai' => 'required|exists:pegawai,id_pegawai',
                'tanggal_absensi' => 'required|date',
                'jam_masuk' => 'nullable|date_format:H:i',
                'jam_pulang' => 'nullable|date_format:H:i',
                'status' => 'required|in:hadir,sakit,izin,alpha',
                'keterangan' => 'nullable|string|max:255',
            ]);

            // Security check: pegawai harus di departemen Petugas
            $pegawai = Pegawai::where('id_pegawai', $validated['id_pegawai'])
                ->where('id_departemen', $departemenId)
                ->first();

            if (!$pegawai) {
                return $this->responseForbidden('Pegawai tidak ada di departemen Anda');
            }

            // Cek duplikasi
            $existing = Absensi::where('id_pegawai', $validated['id_pegawai'])
                ->whereDate('tanggal_absensi', $validated['tanggal_absensi'])
                ->first();

            if ($existing) {
                return $this->responseError('Absensi sudah ada untuk tanggal tersebut', 400);
            }

            // Create
            $absensi = Absensi::create($validated);

            // Log
            $this->logActivity('create', 'Absensi', $absensi->id_absensi, 
                'Create absensi ' . $pegawai->nama_pegawai,
                null,
                $absensi->toArray()
            );

            return $this->responseSuccess($absensi, 'Absensi berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseError('Validasi gagal', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Update absensi (hanya draft)
     * 
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Get absensi
            $absensi = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            // Prevent edit if approved
            if ($absensi->status === 'approved') {
                return $this->responseError('Tidak bisa edit absensi yang sudah di-approve', 400);
            }

            // Validate
            $validated = $request->validate([
                'jam_masuk' => 'nullable|date_format:H:i',
                'jam_pulang' => 'nullable|date_format:H:i',
                'status' => 'required|in:hadir,sakit,izin,alpha',
                'keterangan' => 'nullable|string|max:255',
            ]);

            // Save old values
            $oldValues = $absensi->toArray();

            // Update
            $absensi->update($validated);

            // Log
            $this->logActivity('update', 'Absensi', $id, 'Update absensi', $oldValues, $absensi->toArray());

            return $this->responseSuccess($absensi, 'Absensi berhasil diupdate');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Approve absensi untuk departemen
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Get absensi
            $absensi = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            // Save old
            $oldValues = $absensi->toArray();

            // Update
            $absensi->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $officer->id,
            ]);

            // Log
            $this->logActivity('update', 'Absensi', $id, 'Approve absensi', $oldValues, $absensi->toArray());

            return $this->responseSuccess($absensi, 'Absensi berhasil di-approve');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Delete absensi (hanya draft)
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            // Get absensi
            $absensi = Absensi::whereHas('pegawai', function ($q) use ($departemenId) {
                $q->where('id_departemen', $departemenId);
            })->findOrFail($id);

            // Prevent delete if approved
            if ($absensi->status === 'approved') {
                return $this->responseError('Tidak bisa delete absensi yang sudah di-approve', 400);
            }

            // Log
            $this->logActivity('delete', 'Absensi', $id, 'Delete absensi', $absensi->toArray());

            // Delete
            $absensi->delete();

            return $this->responseSuccess(null, 'Absensi berhasil dihapus');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Get summary & stats untuk departemen
     * 
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
        try {
            $officer = auth('officer')->user();
            $departemenId = $officer->id_departemen;

            $bulan = $request->input('bulan', now()->month);
            $tahun = $request->input('tahun', now()->year);

            // Stats absensi
            $stats = [
                'total_pegawai' => Pegawai::where('id_departemen', $departemenId)->where('status_pegawai', 'aktif')->count(),
                'hadir' => Absensi::whereMonth('tanggal_absensi', $bulan)
                    ->whereYear('tanggal_absensi', $tahun)
                    ->where('status', 'hadir')
                    ->whereHas('pegawai', fn($q) => $q->where('id_departemen', $departemenId))
                    ->count(),
                'sakit' => Absensi::whereMonth('tanggal_absensi', $bulan)
                    ->whereYear('tanggal_absensi', $tahun)
                    ->where('status', 'sakit')
                    ->whereHas('pegawai', fn($q) => $q->where('id_departemen', $departemenId))
                    ->count(),
                'izin' => Absensi::whereMonth('tanggal_absensi', $bulan)
                    ->whereYear('tanggal_absensi', $tahun)
                    ->where('status', 'izin')
                    ->whereHas('pegawai', fn($q) => $q->where('id_departemen', $departemenId))
                    ->count(),
                'alpha' => Absensi::whereMonth('tanggal_absensi', $bulan)
                    ->whereYear('tanggal_absensi', $tahun)
                    ->where('status', 'alpha')
                    ->whereHas('pegawai', fn($q) => $q->where('id_departemen', $departemenId))
                    ->count(),
            ];

            // Log
            $this->logActivity('read', 'Absensi', null, 'View department absensi summary');

            return $this->responseSuccess($stats, 'Summary absensi berhasil diambil');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
