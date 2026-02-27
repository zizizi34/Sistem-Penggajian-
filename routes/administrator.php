<?php

use App\Http\Controllers\Administrator\DashboardController;
use App\Http\Controllers\Administrator\DepartemenController;
use App\Http\Controllers\Administrator\JabatanController;
use App\Http\Controllers\Administrator\TunjanganController;
use App\Http\Controllers\Administrator\PotonganController;
use App\Http\Controllers\Administrator\PtkpStatusController;
use App\Http\Controllers\Administrator\PegawaiController;
use App\Http\Controllers\Administrator\PenggajianController;
use App\Http\Controllers\Administrator\OfficerController;
use App\Http\Controllers\Administrator\ProfileSettingController;
use App\Http\Controllers\Administrator\UserController;
use App\Http\Controllers\Administrator\JadwalKerjaController;
use App\Http\Controllers\Administrator\AbsensiController;
use App\Http\Controllers\Administrator\LemburController;
use Illuminate\Support\Facades\Route;

/**
 * Administrator Routes - Production Ready
 * 
 * Routes untuk Super Admin dengan full access to all resources.
 * 
 * Middleware:
 * - auth:administrator: Authenticate via administrator guard
 * - role.access: Check role-based access (Super Admin)
 * 
 * Features:
 * - Full CRUD for all resources
 * - User & Role Management
 * - Master Data Management
 * - Payroll Calculation & Approval
 * - System Settings
 * - Activity Logging & Audit Trail
 */
Route::middleware(['auth:administrator', 'role.access'])->name('administrators.')->prefix('administrator')->group(function () {
    // Dashboard & Overview
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ==================== DATA MASTER ====================
    
    // Departemen
    Route::resource('departemen', DepartemenController::class);

    // Jabatan
    Route::resource('jabatan', JabatanController::class);

    // Jadwal Kerja
    Route::resource('jadwal-kerja', JadwalKerjaController::class)->only(['index', 'store', 'destroy']);

    // PTKP Status
    Route::resource('ptkp-status', PtkpStatusController::class);

    // ==================== KOMPONEN GAJI ====================
    
    // Tunjangan
    Route::resource('tunjangan', TunjanganController::class);

    // Potongan
    Route::resource('potongan', PotonganController::class);

    // ==================== EMPLOYEE MANAGEMENT ====================
    
    // Pegawai
    Route::resource('pegawai', PegawaiController::class);

    // ==================== ATTENDANCE & OVERTIME ====================
    
    // Absensi - Full management
    Route::controller(AbsensiController::class)->group(function () {
        Route::get('/absensi', 'index')->name('absensi.index');
        Route::get('/absensi/create', 'create')->name('absensi.create');
        Route::post('/absensi', 'store')->name('absensi.store');
        Route::get('/absensi/{id}', 'show')->name('absensi.show');
        Route::get('/absensi/{id}/edit', 'edit')->name('absensi.edit');
        Route::put('/absensi/{id}', 'update')->name('absensi.update');
        Route::post('/absensi/{id}/approve', 'approve')->name('absensi.approve');
        Route::delete('/absensi/{id}', 'destroy')->name('absensi.destroy');
    });

    // Lembur - Full management
    Route::controller(LemburController::class)->group(function () {
        Route::get('/lembur', 'index')->name('lembur.index');
        Route::get('/lembur/create', 'create')->name('lembur.create');
        Route::post('/lembur', 'store')->name('lembur.store');
        Route::get('/lembur/{id}', 'show')->name('lembur.show');
        Route::get('/lembur/{id}/edit', 'edit')->name('lembur.edit');
        Route::put('/lembur/{id}', 'update')->name('lembur.update');
        Route::post('/lembur/{id}/approve', 'approve')->name('lembur.approve');
        Route::delete('/lembur/{id}', 'destroy')->name('lembur.destroy');
    });

    // ==================== PAYROLL MANAGEMENT ====================
    
    // Penggajian / Payroll
    Route::controller(PenggajianController::class)->group(function () {
        Route::get('/penggajian', 'index')->name('penggajian.index');
        Route::get('/penggajian/{id}', 'show')->name('penggajian.show');
        
        // Batch operations
        Route::post('/penggajian/calculate', 'calculate')->name('penggajian.calculate');
        Route::post('/penggajian/approve', 'approve')->name('penggajian.approve');
        Route::post('/penggajian/post', 'post')->name('penggajian.post');
        Route::post('/penggajian/generate-slip', 'generateSlip')->name('penggajian.generate-slip');
        Route::get('/penggajian/{id}/slip', 'downloadSlip')->name('penggajian.download-slip');
        Route::post('/penggajian/export', 'export')->name('penggajian.export');
    });

    // ==================== SYSTEM MANAGEMENT ====================
    
    // User Management
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    // Officer Management
    Route::resource('officers', OfficerController::class)->except(['create', 'show', 'edit']);

    // Profile Settings
    Route::controller(ProfileSettingController::class)->group(function () {
        Route::get('/profile/settings', 'index')->name('profile-settings.index');
        Route::put('/profile/settings', 'update')->name('profile-settings.update');
    });
});
