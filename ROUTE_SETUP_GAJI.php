<?php

/**
 * ROUTE SETUP UNTUK SISTEM PENGGAJIAN
 * 
 * Tambahkan route berikut ke routes/api.php atau routes/officer.php
 * Sesuaikan dengan struktur route Anda
 */

// ============= ROUTE PENGGAJIAN (SALARY/GAJI) =============

use App\Http\Controllers\PenggajianController;

/**
 * Group route untuk penggajian
 * Semua route memerlukan authentication
 */
Route::middleware('auth:sanctum')->group(function () {
    
    // ============= LIST & VIEW =============
    
    /**
     * GET /api/gaji
     * List penggajian
     * 
     * Akses:
     * - Admin HRD: Semua pegawai
     * - Manager: Pegawai di departemennya
     * - Direktur: Semua pegawai
     * - Pegawai: Gaji sendiri saja
     * 
     * Query params:
     * - periode: YYYY-MM (opsional, untuk filter)
     * - page: halaman (opsional, default 1)
     */
    Route::get('/gaji', [PenggajianController::class, 'index'])
        ->middleware('permission:gaji.view');

    /**
     * GET /api/gaji/{id}
     * Detail penggajian
     * 
     * Akses: Same as index
     */
    Route::get('/gaji/{penggajianId}', [PenggajianController::class, 'show'])
        ->middleware('permission:gaji.view');

    // ============= CALCULATE & CREATE =============
    
    /**
     * POST /api/gaji/calculate
     * Hitung gaji satu pegawai (show calculate result tanpa save)
     * 
     * Akses: Admin HRD only
     * 
     * Request body:
     * {
     *   "pegawai_id": 1,
     *   "periode": "2026-01"
     * }
     * 
     * Response:
     * {
     *   "status": "success",
     *   "data": {
     *     "gaji_pokok": 15000000,
     *     "tunjangan": { "total": 2500000, "detail": [...] },
     *     "lembur": { "total_jam": 10, "nominal": 1000000 },
     *     "potongan": { "non_pajak": 900000, "detail": [...] },
     *     "pajak_pph21": 932500,
     *     "gaji_bersih": 16467500
     *   }
     * }
     */
    Route::post('/gaji/calculate', [PenggajianController::class, 'calculate'])
        ->middleware('permission:gaji.create');

    /**
     * POST /api/gaji
     * Simpan perhitungan gaji ke database
     * 
     * Akses: Admin HRD only
     * 
     * Request body:
     * {
     *   "pegawai_id": 1,
     *   "periode": "2026-01",
     *   "gaji_pokok": 15000000,
     *   "total_tunjangan": 2500000,
     *   "total_potongan": 900000,
     *   "lembur": 1000000,
     *   "pajak_pph21": 932500,
     *   "gaji_bersih": 16467500
     * }
     */
    Route::post('/gaji', [PenggajianController::class, 'store'])
        ->middleware('permission:gaji.create');

    /**
     * POST /api/gaji/batch
     * Hitung gaji semua pegawai (batch processing)
     * 
     * Akses: Admin HRD only
     * 
     * Request body:
     * {
     *   "periode": "2026-01"
     * }
     * 
     * Response: Detail hasil perhitungan semua pegawai
     */
    Route::post('/gaji/batch', [PenggajianController::class, 'calculateBatch'])
        ->middleware('permission:gaji.create');

    // ============= EDIT & UPDATE =============
    
    /**
     * PUT /api/gaji/{id}
     * Update perhitungan gaji
     * 
     * Akses: Admin HRD only
     * Constraint: Hanya draft status yang bisa diedit
     * 
     * Request body:
     * {
     *   "gaji_pokok": 15000000,
     *   "total_tunjangan": 2500000,
     *   ...
     * }
     */
    Route::put('/gaji/{penggajianId}', [PenggajianController::class, 'update'])
        ->middleware('permission:gaji.edit');

    // ============= APPROVE =============
    
    /**
     * POST /api/gaji/{id}/approve
     * Approve perhitungan gaji
     * 
     * Akses: Admin HRD, Direktur only
     * 
     * Status transition: draft -> approved
     */
    Route::post('/gaji/{penggajianId}/approve', [PenggajianController::class, 'approve'])
        ->middleware('permission:gaji.approve');

    // ============= PRINT & EXPORT =============
    
    /**
     * GET /api/gaji/{id}/print
     * Print slip gaji (PDF/HTML)
     * 
     * Akses: Semua user (dengan permission check)
     * Constraint: Pegawai hanya bisa print gaji sendiri
     * 
     * Query params:
     * - format: pdf atau html (default: pdf)
     */
    Route::get('/gaji/{penggajianId}/print', [PenggajianController::class, 'printSlip'])
        ->middleware('permission:gaji.print_slip');

});

// ============= AUTHORIZATION NOTES =============

/*
Penjelasan Permission di Middleware:

1. 'permission:gaji.view'
   - Mengecek apakah user memiliki permission 'gaji.view'
   - Jika tidak, return 403 Forbidden

2. 'permission:gaji.create'
   - Mengecek apakah user memiliki permission 'gaji.create'
   - Hanya Admin HRD yang memiliki ini

3. Implementasi di Middleware (app/Http/Middleware/CheckPermission.php)
   - Mengecek auth()->user()->hasPermission($permission)
   - Return 403 jika tidak memiliki permission

4. Additional checks di Controller:
   - Role-based filtering (contoh: Pegawai hanya lihat gaji sendiri)
   - Business logic validation (contoh: hanya draft yang bisa diedit)
*/

// ============= CONTOH PERMINTAAN DARI CLIENT =============

/*
1. ADMIN HRD - Hitung gaji se-departemen
   POST http://localhost/api/gaji/batch
   Header: Authorization: Bearer {token}
   Body: {
     "periode": "2026-01"
   }

2. MANAGER - Lihat absensi tim
   GET http://localhost/api/gaji?periode=2026-01
   Header: Authorization: Bearer {token}
   (akan filter otomatis untuk departemen manager)

3. DIREKTUR - Approve gaji
   POST http://localhost/api/gaji/1/approve
   Header: Authorization: Bearer {token}

4. PEGAWAI - Lihat slip gaji sendiri
   GET http://localhost/api/gaji/1
   Header: Authorization: Bearer {token}
   (akan check id_pegawai = auth user's pegawai_id)

5. PEGAWAI - Print slip gaji
   GET http://localhost/api/gaji/1/print?format=pdf
   Header: Authorization: Bearer {token}
*/
