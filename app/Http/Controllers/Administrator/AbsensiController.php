<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\BaseController;
use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;

/**
 * AbsensiController - Production Ready Implementation
 * 
 * Controller untuk mengelola data absensi.
 * 
 * Permission Matrix:
 * - Super Admin: View all, Create, Edit, Delete, Approve all
 * - Petugas (Officer): View own dept, Create, Edit, Approve own dept
 * - Pegawai (Employee): View own only
 * 
 * Features:
 * - Automatic data filtering berdasarkan role
 * - Permission checking di setiap action
 * - Activity logging untuk audit trail
 * - Proper response formatting
 * 
 * @author Your Name
 * @version 1.0
 */
class AbsensiController extends BaseController
{
    /**
     * Display a listing of absensi
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check permission
        $this->authorize('absensi.view', 'Anda tidak memiliki akses untuk melihat absensi');

        try {
            $query = Absensi::with(['pegawai', 'pegawai.departemen']);

            // Filter berdasarkan role
            if ($this->isOfficer()) {
                // Officer hanya lihat departemen sendiri
                $query->whereHas('pegawai', function ($q) {
                    $q->where('id_departemen', $this->getDepartmentScope());
                });

                // Log activity
                $this->logActivity('read', 'Absensi', null, 'View absensi departemen sendiri');
            } elseif ($this->isPegawai()) {
                // Pegawai hanya lihat data pribadi
                $query->where('id_pegawai', $this->getPegawaiScope());

                // Log activity
                $this->logActivity('read', 'Absensi', null, 'View absensi pribadi');
            } else {
                // Super Admin lihat semua
                $this->logActivity('read', 'Absensi', null, 'View semua absensi');
            }

            // Filter tambahan dari request
            if ($request->has('pegawai_id') && $this->isSuperAdmin()) {
                $query->where('id_pegawai', $request->pegawai_id);
            }

            if ($request->has('tanggal_dari')) {
                $query->whereDate('tanggal_absensi', '>=', $request->tanggal_dari);
            }

            if ($request->has('tanggal_sampai')) {
                $query->whereDate('tanggal_absensi', '<=', $request->tanggal_sampai);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Pagination
            $perPage = $request->input('per_page', 15);
            $absensis = $query->orderBy('tanggal_absensi', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data absensi berhasil diambil',
                'data' => $absensis,
            ]);
        } catch (\Exception $e) {
            $this->logActivity('read', 'Absensi', null, 'Error: ' . $e->getMessage());
            return $this->responseError('Error mengambil data absensi', 500);
        }
    }

    /**
     * Show the form for creating a new absensi
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check permission
        $this->authorize('absensi.create', 'Anda tidak memiliki akses untuk membuat absensi');

        try {
            // Get pegawai list
            $query = Pegawai::active();

            // Officer hanya lihat pegawai di departemen sendiri
            if ($this->isOfficer()) {
                $query->where('id_departemen', $this->getDepartmentScope());
            }

            $pegawais = $query->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'pegawais' => $pegawais,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->responseError('Error memuat form', 500);
        }
    }

    /**
     * Store a newly created absensi
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check permission
        $this->authorize('absensi.create', 'Anda tidak memiliki akses untuk membuat absensi');

        try {
            // Validate input
            $validated = $request->validate([
                'id_pegawai' => 'required|exists:pegawai,id_pegawai',
                'tanggal_absensi' => 'required|date',
                'jam_masuk' => 'nullable|date_format:H:i',
                'jam_pulang' => 'nullable|date_format:H:i',
                'status' => 'required|in:hadir,sakit,izin,alpha',
                'keterangan' => 'nullable|string|max:255',
            ]);

            // Security: Officer hanya bisa input untuk pegawai di departemen sendiri
            if ($this->isOfficer()) {
                $pegawai = Pegawai::find($validated['id_pegawai']);
                if (!$pegawai || $pegawai->id_departemen !== $this->getDepartmentScope()) {
                    return $this->responseForbidden('Anda hanya bisa input absensi untuk pegawai di departemen sendiri');
                }
            }

            // Cek duplikasi
            $existing = Absensi::where('id_pegawai', $validated['id_pegawai'])
                ->whereDate('tanggal_absensi', $validated['tanggal_absensi'])
                ->first();

            if ($existing) {
                return $this->responseError('Absensi sudah ada untuk tanggal tersebut', 400);
            }

            // Create absensi
            $absensi = Absensi::create($validated);

            // Log activity
            $this->logActivity('create', 'Absensi', $absensi->id_absensi, 
                'Create absensi for pegawai ' . $absensi->pegawai->nama_pegawai, 
                null, 
                $absensi->toArray()
            );

            return $this->responseSuccess($absensi, 'Absensi berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseError('Validasi gagal', 422, $e->errors());
        } catch (\Exception $e) {
            $this->logActivity('create', 'Absensi', null, 'Error: ' . $e->getMessage());
            return $this->responseError('Error membuat absensi', 500);
        }
    }

    /**
     * Display the specified absensi
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Check permission
        $this->authorize('absensi.view', 'Anda tidak memiliki akses untuk melihat absensi');

        try {
            $query = Absensi::with(['pegawai', 'pegawai.departemen']);

            // Filter berdasarkan role
            if ($this->isOfficer()) {
                $query->whereHas('pegawai', function ($q) {
                    $q->where('id_departemen', $this->getDepartmentScope());
                });
            } elseif ($this->isPegawai()) {
                $query->where('id_pegawai', $this->getPegawaiScope());
            }

            $absensi = $query->findOrFail($id);

            // Log activity
            $this->logActivity('read', 'Absensi', $id, 'View absensi detail');

            return $this->responseSuccess($absensi);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->responseNotFound('Absensi tidak ditemukan');
        } catch (\Exception $e) {
            return $this->responseError('Error mengambil data absensi', 500);
        }
    }

    /**
     * Show the form for editing the specified absensi
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Check permission
        $this->authorize('absensi.edit', 'Anda tidak memiliki akses untuk edit absensi');

        try {
            $query = Absensi::with(['pegawai', 'pegawai.departemen']);

            // Filter berdasarkan role
            if ($this->isOfficer()) {
                $query->whereHas('pegawai', function ($q) {
                    $q->where('id_departemen', $this->getDepartmentScope());
                });
            }

            $absensi = $query->findOrFail($id);

            // Prevent editing if already approved (Super Admin can override)
            if ($absensi->status === 'approved' && !$this->isSuperAdmin()) {
                return $this->responseError('Absensi yang sudah diapprove tidak dapat diedit', 400);
            }

            return $this->responseSuccess($absensi);
        } catch (\Exception $e) {
            return $this->responseError('Error mengambil data', 500);
        }
    }

    /**
     * Update the specified absensi
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check permission
        $this->authorize('absensi.edit', 'Anda tidak memiliki akses untuk edit absensi');

        try {
            $validated = $request->validate([
                'jam_masuk' => 'nullable|date_format:H:i',
                'jam_pulang' => 'nullable|date_format:H:i',
                'status' => 'required|in:hadir,sakit,izin,alpha',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $query = Absensi::query();

            // Filter berdasarkan role
            if ($this->isOfficer()) {
                $query->whereHas('pegawai', function ($q) {
                    $q->where('id_departemen', $this->getDepartmentScope());
                });
            }

            $absensi = $query->findOrFail($id);

            // Save old values untuk logging
            $oldValues = $absensi->toArray();

            // Update
            $absensi->update($validated);

            // Log activity
            $this->logActivity('update', 'Absensi', $id, 
                'Update absensi', 
                $oldValues, 
                $absensi->toArray()
            );

            return $this->responseSuccess($absensi, 'Absensi berhasil diupdate');
        } catch (\Exception $e) {
            $this->logActivity('update', 'Absensi', $id, 'Error: ' . $e->getMessage());
            return $this->responseError('Error update absensi', 500);
        }
    }

    /**
     * Approve absensi
     * 
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function approve($id, Request $request)
    {
        // Check permission untuk approve
        $this->authorize('absensi.approve', 'Anda tidak memiliki akses untuk approve absensi');

        try {
            $query = Absensi::query();

            // Filter berdasarkan role
            if ($this->isOfficer()) {
                $query->whereHas('pegawai', function ($q) {
                    $q->where('id_departemen', $this->getDepartmentScope());
                });
            }

            $absensi = $query->findOrFail($id);

            // Save old values
            $oldValues = $absensi->toArray();

            // Update status
            $absensi->update(['status' => 'approved', 'approved_by' => $this->user->id_user ?? $this->user->id]);

            // Log activity
            $this->logActivity('update', 'Absensi', $id, 
                'Approve absensi', 
                $oldValues, 
                $absensi->toArray()
            );

            return $this->responseSuccess($absensi, 'Absensi berhasil diapprove');
        } catch (\Exception $e) {
            $this->logActivity('update', 'Absensi', $id, 'Error approve: ' . $e->getMessage());
            return $this->responseError('Error approve absensi', 500);
        }
    }

    /**
     * Delete absensi
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check permission
        $this->authorize('absensi.delete', 'Anda tidak memiliki akses untuk hapus absensi');

        try {
            $query = Absensi::query();

            // Filter berdasarkan role
            if ($this->isOfficer()) {
                $query->whereHas('pegawai', function ($q) {
                    $q->where('id_departemen', $this->getDepartmentScope());
                });
            }

            $absensi = $query->findOrFail($id);

            // Log sebelum delete
            $this->logActivity('delete', 'Absensi', $id, 
                'Delete absensi for pegawai ' . $absensi->pegawai->nama_pegawai, 
                $absensi->toArray()
            );

            $absensi->delete();

            return $this->responseSuccess(null, 'Absensi berhasil dihapus');
        } catch (\Exception $e) {
            $this->logActivity('delete', 'Absensi', $id, 'Error: ' . $e->getMessage());
            return $this->responseError('Error hapus absensi', 500);
        }
    }
}
