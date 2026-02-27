<?php

use App\Http\Controllers\Officer\DashboardController;
use App\Http\Controllers\Officer\DepartemenController;
use App\Http\Controllers\Officer\JabatanController;
use App\Http\Controllers\Officer\TunjanganController;
use App\Http\Controllers\Officer\JadwalKerjaController;
use App\Http\Controllers\Officer\PotonganController;
use App\Http\Controllers\Officer\PegawaiController;
use App\Http\Controllers\Officer\PenggajianController;
use App\Http\Controllers\Officer\ProfileSettingController;
use App\Http\Controllers\Officer\AbsensiController;
use App\Http\Controllers\Officer\LemburController;
use Illuminate\Support\Facades\Route;

/**
 * Officer Routes - Production Ready
 * 
 * Routes untuk Petugas (HR Officer) dengan department-based access control.
 * 
 * Middleware:
 * - auth:officer: Authenticate via officer guard
 * - department.scope: Auto filter data by officer's department
 * 
 * Features:
 * - Input & approve absensi for own department
 * - Input & approve lembur for own department
 * - View reports for own department only
 * - Cannot edit master data or system settings
 */
Route::middleware(['auth:officer', 'department.scope'])->name('officers.')->prefix('officer')->group(function () {
    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Data Master (READONLY)
    Route::resource('departemen', DepartemenController::class)->only('index');
    Route::resource('jabatan', JabatanController::class)->only('index');
    Route::resource('tunjangan', TunjanganController::class)->only('index');
    Route::resource('potongan', PotonganController::class)->only('index');
    Route::resource('jadwal-kerja', JadwalKerjaController::class)->except(['create', 'show', 'edit', 'update']);

    // Absensi - Full CRUD & Approve untuk departemen sendiri
    Route::controller(AbsensiController::class)->group(function () {
        Route::get('/absensi', 'index')->name('absensi.index');
        Route::post('/absensi', 'store')->name('absensi.store');
        Route::get('/absensi/{id}', 'show')->name('absensi.show');
        Route::put('/absensi/{id}', 'update')->name('absensi.update');
        Route::post('/absensi/{id}/approve', 'approve')->name('absensi.approve');
        Route::delete('/absensi/{id}', 'destroy')->name('absensi.destroy');
        Route::get('/absensi-summary', 'summary')->name('absensi.summary');
    });

    // Lembur - Full CRUD & Approve untuk departemen sendiri
    Route::controller(LemburController::class)->group(function () {
        Route::get('/lembur', 'index')->name('lembur.index');
        Route::post('/lembur', 'store')->name('lembur.store');
        Route::get('/lembur/{id}', 'show')->name('lembur.show');
        Route::put('/lembur/{id}', 'update')->name('lembur.update');
        Route::post('/lembur/{id}/approve', 'approve')->name('lembur.approve');
        Route::delete('/lembur/{id}', 'destroy')->name('lembur.destroy');
    });

    // Payroll & Pegawai (READONLY)
    Route::resource('pegawai', PegawaiController::class)->only('index', 'show');
    Route::resource('penggajian', PenggajianController::class)->only('index', 'show');

    // Profile Settings
    Route::controller(ProfileSettingController::class)->group(function () {
        Route::get('/profile/settings', 'index')->name('profile-settings.index');
        Route::put('/profile/settings', 'update')->name('profile-settings.update');
    });
});
