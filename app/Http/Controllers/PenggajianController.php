<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Pegawai;
use App\Services\SalaryCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk mengelola Penggajian
 * 
 * Kontrol akses per role:
 * - Admin HRD: Full access
 * - Manager: View & Approve (departemen sendiri)
 * - Direktur: View & Approve (semua)
 * - Pegawai: View own only
 */
class PenggajianController extends Controller
{
    protected $salaryService;

    public function __construct(SalaryCalculationService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    /**
     * List penggajian dengan role-based access
     * 
     * Admin HRD & Manager: Lihat semua (atau departemen sendiri)
     * Direktur: Lihat semua
     * Pegawai: Lihat gaji sendiri
     */
    public function index(Request $request)
    {
        // Check permission
        if (!$request->user()->hasAnyPermission(['gaji.view', 'gaji.view_own'])) {
            return $this->unauthorizedResponse('Anda tidak memiliki akses untuk melihat data gaji');
        }

        $query = Penggajian::with(['pegawai', 'pegawai.departemen', 'pegawai.jabatan']);

        // Filter berdasarkan role
        if ($request->user()->hasRole('Pegawai')) {
            // Pegawai hanya bisa lihat gaji sendiri
            $query->where('id_pegawai', $request->user()->id_pegawai);
        } elseif ($request->user()->hasRole('Manager')) {
            // Manager lihat pegawai di departemennya (assuming user has departemen relationship)
            // TODO: Setup relasi User -> Departemen
            // $query->whereHas('pegawai', function($q) {
            //     $q->where('id_departemen', auth()->user()->departemen->id_departemen);
            // });
        }

        // Filter berdasarkan periode jika ada
        if ($request->periode) {
            $query->where('periode', $request->periode);
        }

        // Pagination
        $penggajian = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $penggajian
        ]);
    }

    /**
     * Show detail penggajian
     */
    public function show(Request $request, $penggajianId)
    {
        $penggajian = Penggajian::with(['pegawai', 'pegawai.departemen', 'pegawai.jabatan'])->find($penggajianId);

        if (!$penggajian) {
            return $this->notFoundResponse('Penggajian tidak ditemukan');
        }

        // Check permission
        if ($request->user()->hasRole('Pegawai')) {
            // Pegawai hanya bisa lihat gaji sendiri
            if ($penggajian->id_pegawai !== $request->user()->id_pegawai) {
                return $this->unauthorizedResponse('Anda hanya bisa melihat slip gaji Anda sendiri');
            }
        } elseif (!$request->user()->hasAnyPermission(['gaji.view', 'gaji.view_own'])) {
            return $this->unauthorizedResponse('Anda tidak memiliki akses untuk melihat data gaji');
        }

        return response()->json([
            'status' => 'success',
            'data' => $penggajian
        ]);
    }

    /**
     * Hitung gaji untuk satu pegawai
     * 
     * Permission: gaji.create (Admin HRD only)
     */
    public function calculate(Request $request)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.create')) {
            return $this->unauthorizedResponse('Anda tidak memiliki akses untuk membuat perhitungan gaji');
        }

        // Validate input
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id_pegawai',
            'periode' => 'required|regex:/^\d{4}-\d{2}$/'
        ]);

        try {
            // Ambil data pegawai
            $pegawai = Pegawai::find($validated['pegawai_id']);

            // Hitung gaji
            $result = $this->salaryService->calculateMonthlySalary(
                $pegawai,
                $validated['periode']
            );

            if ($result['status'] !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 422);
            }

            // Log action
            Log::info('User ' . $request->user()->name . ' membuat perhitungan gaji', [
                'pegawai_id' => $pegawai->id_pegawai,
                'pegawai_nama' => $pegawai->nama_pegawai,
                'periode' => $validated['periode']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Gaji berhasil dihitung',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating salary', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghitung gaji: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan hasil perhitungan gaji ke database
     * 
     * Permission: gaji.create (Admin HRD only)
     */
    public function store(Request $request)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.create')) {
            return $this->unauthorizedResponse('Anda tidak memiliki akses untuk membuat perhitungan gaji');
        }

        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id_pegawai',
            'periode' => 'required|regex:/^\d{4}-\d{2}$/',
            'gaji_pokok' => 'required|numeric',
            'total_tunjangan' => 'required|numeric',
            'total_potongan' => 'required|numeric',
            'lembur' => 'required|numeric',
            'pajak_pph21' => 'required|numeric',
            'gaji_bersih' => 'required|numeric',
        ]);

        try {
            // Cek apakah sudah ada perhitungan gaji untuk period tersebut
            $existing = Penggajian::where([
                'id_pegawai' => $validated['pegawai_id'],
                'periode' => $validated['periode']
            ])->first();

            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Perhitungan gaji untuk periode ini sudah ada',
                    'existing_record' => $existing->id_penggajian
                ], 409);
            }

            // Create penggajian
            $penggajian = Penggajian::create([
                'id_pegawai' => $validated['pegawai_id'],
                'periode' => $validated['periode'],
                'gaji_pokok' => $validated['gaji_pokok'],
                'total_tunjangan' => $validated['total_tunjangan'],
                'total_potongan' => $validated['total_potongan'],
                'lembur' => $validated['lembur'],
                'pajak_pph21' => $validated['pajak_pph21'],
                'gaji_bersih' => $validated['gaji_bersih'],
                'status' => 'draft'
            ]);

            // Log action
            Log::info('User ' . $request->user()->name . ' menyimpan perhitungan gaji', [
                'penggajian_id' => $penggajian->id_penggajian,
                'pegawai_id' => $validated['pegawai_id'],
                'periode' => $validated['periode']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Perhitungan gaji berhasil disimpan',
                'data' => $penggajian
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saving salary calculation', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan perhitungan gaji'
            ], 500);
        }
    }

    /**
     * Update (Edit) perhitungan gaji
     * 
     * Permission: gaji.edit (Admin HRD only)
     * Constraint: Hanya draft status yang bisa diedit
     */
    public function update(Request $request, $penggajianId)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.edit')) {
            return $this->unauthorizedResponse('Anda tidak memiliki akses untuk edit perhitungan gaji');
        }

        $penggajian = Penggajian::find($penggajianId);

        if (!$penggajian) {
            return $this->notFoundResponse('Penggajian tidak ditemukan');
        }

        // Hanya draft yang bisa diedit
        if ($penggajian->status !== 'draft') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya perhitungan gaji dengan status DRAFT yang bisa diedit'
            ], 422);
        }

        $validated = $request->validate([
            'gaji_pokok' => 'numeric',
            'total_tunjangan' => 'numeric',
            'total_potongan' => 'numeric',
            'lembur' => 'numeric',
            'pajak_pph21' => 'numeric',
            'gaji_bersih' => 'numeric',
        ]);

        try {
            $penggajian->update($validated);

            Log::info('User ' . $request->user()->name . ' mengubah perhitungan gaji', [
                'penggajian_id' => $penggajianId
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Perhitungan gaji berhasil diupdate',
                'data' => $penggajian
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating salary calculation', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate perhitungan gaji'
            ], 500);
        }
    }

    /**
     * Approve perhitungan gaji
     * 
     * Permission: gaji.approve (Admin HRD, Direktur)
     */
    public function approve(Request $request, $penggajianId)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.approve')) {
            return $this->unauthorizedResponse('Anda tidak memiliki akses untuk approve perhitungan gaji');
        }

        $penggajian = Penggajian::find($penggajianId);

        if (!$penggajian) {
            return $this->notFoundResponse('Penggajian tidak ditemukan');
        }

        try {
            $penggajian->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $request->user()->id
            ]);

            Log::info('User ' . $request->user()->name . ' approve perhitungan gaji', [
                'penggajian_id' => $penggajianId,
                'pegawai_id' => $penggajian->id_pegawai,
                'periode' => $penggajian->periode
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Perhitungan gaji berhasil di-approve',
                'data' => $penggajian
            ]);
        } catch (\Exception $e) {
            Log::error('Error approving salary calculation', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat approve perhitungan gaji'
            ], 500);
        }
    }

    /**
     * Hitung gaji batch (semua pegawai sekaligus)
     * 
     * Permission: gaji.create (Admin HRD only)
     */
    public function calculateBatch(Request $request)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.create')) {
            return $this->unauthorizedResponse('Anda tidak memiliki akses untuk membuat perhitungan gaji');
        }

        $validated = $request->validate([
            'periode' => 'required|regex:/^\d{4}-\d{2}$/'
        ]);

        try {
            // Ambil semua pegawai yang aktif
            $pegawais = Pegawai::where('status_pegawai', 'aktif')->get();

            $results = [
                'success' => 0,
                'failed' => 0,
                'errors' => [],
                'data' => []
            ];

            foreach ($pegawais as $pegawai) {
                // Cek apakah sudah ada perhitungan untuk periode ini
                $existing = Penggajian::where([
                    'id_pegawai' => $pegawai->id_pegawai,
                    'periode' => $validated['periode']
                ])->exists();

                if ($existing) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'pegawai_id' => $pegawai->id_pegawai,
                        'nama' => $pegawai->nama_pegawai,
                        'message' => 'Perhitungan gaji untuk periode ini sudah ada'
                    ];
                    continue;
                }

                // Hitung gaji
                $calculationResult = $this->salaryService->calculateMonthlySalary(
                    $pegawai,
                    $validated['periode']
                );

                if ($calculationResult['status'] === 'success') {
                    // Simpan ke database
                    $this->salaryService->saveSalaryCalculation(
                        $pegawai,
                        $validated['periode'],
                        $calculationResult
                    );

                    $results['success']++;
                    $results['data'][] = [
                        'pegawai_id' => $pegawai->id_pegawai,
                        'nama' => $pegawai->nama_pegawai,
                        'gaji_bersih' => $calculationResult['gaji_bersih']
                    ];
                } else {
                    $results['failed']++;
                    $results['errors'][] = [
                        'pegawai_id' => $pegawai->id_pegawai,
                        'nama' => $pegawai->nama_pegawai,
                        'message' => $calculationResult['message']
                    ];
                }
            }

            Log::info('User ' . $request->user()->name . ' melakukan batch calculation gaji', [
                'periode' => $validated['periode'],
                'total_success' => $results['success'],
                'total_failed' => $results['failed']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Batch calculation selesai',
                'summary' => [
                    'total' => count($pegawais),
                    'success' => $results['success'],
                    'failed' => $results['failed']
                ],
                'data' => $results['data'],
                'errors' => $results['errors']
            ]);
        } catch (\Exception $e) {
            Log::error('Error in batch salary calculation', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat batch calculation'
            ], 500);
        }
    }

    /**
     * Print slip gaji
     * 
     * Permission: gaji.print_slip (semua role except view-only)
     */
    public function printSlip(Request $request, $penggajianId)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.print_slip')) {
            return $this->unauthorizedResponse('Anda tidak memiliki akses untuk print slip gaji');
        }

        $penggajian = Penggajian::with(['pegawai', 'pegawai.departemen', 'pegawai.jabatan', 'pegawai.ptkpStatus'])
            ->find($penggajianId);

        if (!$penggajian) {
            return $this->notFoundResponse('Penggajian tidak ditemukan');
        }

        // Check ownership untuk pegawai
        if ($request->user()->hasRole('Pegawai')) {
            if ($penggajian->id_pegawai !== $request->user()->id_pegawai) {
                return $this->unauthorizedResponse('Anda hanya bisa print slip gaji Anda sendiri');
            }
        }

        // Return PDF atau HTML format
        // TODO: Implementasi PDF generation (gunakan DomPDF atau TCPDF)

        return response()->json([
            'status' => 'success',
            'message' => 'Slip gaji siap diprint',
            'data' => $penggajian
        ]);
    }

    /**
     * Helper: Unauthorized response
     */
    private function unauthorizedResponse($message)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], 403);
    }

    /**
     * Helper: Not found response
     */
    private function notFoundResponse($message)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], 404);
    }
}
