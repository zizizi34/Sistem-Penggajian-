<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\BaseController;
use App\Models\Lembur;
use App\Models\Pegawai;
use Illuminate\Http\Request;

/**
 * Administrator LemburController - Production Ready
 * 
 * Controller untuk Super Admin mengelola lembur dari semua departemen.
 * 
 * @author Your Name
 * @version 1.0
 */
class LemburController extends BaseController
{
    /**
     * Display all lembur
     */
    public function index(Request $request)
    {
        $this->authorize('lembur.view', 'Anda tidak memiliki akses untuk melihat lembur');

        try {
            $query = Lembur::with(['pegawai', 'pegawai.departemen', 'pegawai.jabatan']);

            if ($request->has('pegawai_id')) {
                $query->where('id_pegawai', $request->pegawai_id);
            }

            if ($request->has('departemen_id')) {
                $query->whereHas('pegawai', function ($q) {
                    $q->where('id_departemen', request('departemen_id'));
                });
            }

            if ($request->has('tanggal_dari')) {
                $query->whereDate('tanggal_lembur', '>=', $request->tanggal_dari);
            }

            if ($request->has('tanggal_sampai')) {
                $query->whereDate('tanggal_lembur', '<=', $request->tanggal_sampai);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $perPage = $request->input('per_page', 20);
            $lemburs = $query->orderBy('tanggal_lembur', 'desc')->paginate($perPage);

            $this->logActivity('read', 'Lembur', null, 'View semua lembur');

            return response()->json([
                'status' => 'success',
                'message' => 'Data lembur berhasil diambil',
                'data' => $lemburs,
            ]);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Create form data
     */
    public function create()
    {
        $this->authorize('lembur.create', 'Anda tidak memiliki akses untuk membuat lembur');

        try {
            $pegawais = Pegawai::where('status_pegawai', 'aktif')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'pegawais' => $pegawais,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Store lembur
     */
    public function store(Request $request)
    {
        $this->authorize('lembur.create', 'Anda tidak memiliki akses untuk membuat lembur');

        try {
            $validated = $request->validate([
                'id_pegawai' => 'required|exists:pegawai,id_pegawai',
                'tanggal_lembur' => 'required|date',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $existing = Lembur::where('id_pegawai', $validated['id_pegawai'])
                ->whereDate('tanggal_lembur', $validated['tanggal_lembur'])
                ->first();

            if ($existing) {
                return $this->responseError('Lembur sudah ada untuk tanggal tersebut', 400);
            }

            $lembur = Lembur::create($validated);

            $this->logActivity('create', 'Lembur', $lembur->id_lembur, 
                'Create lembur',
                null,
                $lembur->toArray()
            );

            return $this->responseSuccess($lembur, 'Lembur berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseError('Validasi gagal', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Show lembur
     */
    public function show($id)
    {
        $this->authorize('lembur.view', 'Anda tidak memiliki akses');

        try {
            $lembur = Lembur::with(['pegawai', 'pegawai.departemen'])
                ->findOrFail($id);

            $this->logActivity('read', 'Lembur', $id, 'View detail');

            return $this->responseSuccess($lembur);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Edit form data
     */
    public function edit($id)
    {
        $this->authorize('lembur.edit', 'Anda tidak memiliki akses');

        try {
            $lembur = Lembur::findOrFail($id);

            if ($lembur->status === 'approved' && !$this->isSuperAdmin()) {
                return $this->responseError('Lembur yang sudah di-approve tidak dapat diedit', 400);
            }

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
        $this->authorize('lembur.edit', 'Anda tidak memiliki akses');

        try {
            $validated = $request->validate([
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $lembur = Lembur::findOrFail($id);

            $oldValues = $lembur->toArray();
            $lembur->update($validated);

            $this->logActivity('update', 'Lembur', $id, 'Update', $oldValues, $lembur->toArray());

            return $this->responseSuccess($lembur, 'Lembur berhasil diupdate');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Approve lembur (Super Admin)
     */
    public function approve($id)
    {
        $this->authorize('lembur.approve', 'Anda tidak memiliki akses untuk approve');

        try {
            $lembur = Lembur::findOrFail($id);

            $oldValues = $lembur->toArray();

            $lembur->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $this->user->id_user ?? $this->user->id,
            ]);

            $this->logActivity('update', 'Lembur', $id, 'Approve', $oldValues, $lembur->toArray());

            return $this->responseSuccess($lembur, 'Lembur berhasil di-approve');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Delete lembur
     */
    public function destroy($id)
    {
        $this->authorize('lembur.delete', 'Anda tidak memiliki akses');

        try {
            $lembur = Lembur::findOrFail($id);

            $this->logActivity('delete', 'Lembur', $id, 'Delete', $lembur->toArray());

            $lembur->delete();

            return $this->responseSuccess(null, 'Lembur berhasil dihapus');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
