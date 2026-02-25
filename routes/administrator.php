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
use Illuminate\Support\Facades\Route;

Route::middleware('auth:administrator')->name('administrators.')->prefix('administrator')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Data Master - Tunjangan
    Route::get('tunjangan', [TunjanganController::class, 'index'])->name('tunjangan.index');
    Route::get('tunjangan/create', [TunjanganController::class, 'create'])->name('tunjangan.create');
    Route::post('tunjangan', [TunjanganController::class, 'store'])->name('tunjangan.store');
    Route::get('tunjangan/{id}', [TunjanganController::class, 'show'])->name('tunjangan.show');
    Route::get('tunjangan/{id}/edit', [TunjanganController::class, 'edit'])->name('tunjangan.edit');
    Route::put('tunjangan/{id}', [TunjanganController::class, 'update'])->name('tunjangan.update');
    Route::delete('tunjangan/{id}', [TunjanganController::class, 'destroy'])->name('tunjangan.destroy');

    // Data Master - Potongan
    Route::get('potongan', [PotonganController::class, 'index'])->name('potongan.index');
    Route::get('potongan/create', [PotonganController::class, 'create'])->name('potongan.create');
    Route::post('potongan', [PotonganController::class, 'store'])->name('potongan.store');
    Route::get('potongan/{id}', [PotonganController::class, 'show'])->name('potongan.show');
    Route::get('potongan/{id}/edit', [PotonganController::class, 'edit'])->name('potongan.edit');
    Route::put('potongan/{id}', [PotonganController::class, 'update'])->name('potongan.update');
    Route::delete('potongan/{id}', [PotonganController::class, 'destroy'])->name('potongan.destroy');

    // Data Master - Ptkp Status
    Route::get('ptkp-status', [PtkpStatusController::class, 'index'])->name('ptkp-status.index');
    Route::get('ptkp-status/create', [PtkpStatusController::class, 'create'])->name('ptkp-status.create');
    Route::post('ptkp-status', [PtkpStatusController::class, 'store'])->name('ptkp-status.store');
    Route::get('ptkp-status/{id}', [PtkpStatusController::class, 'show'])->name('ptkp-status.show');
    Route::get('ptkp-status/{id}/edit', [PtkpStatusController::class, 'edit'])->name('ptkp-status.edit');
    Route::put('ptkp-status/{id}', [PtkpStatusController::class, 'update'])->name('ptkp-status.update');
    Route::delete('ptkp-status/{id}', [PtkpStatusController::class, 'destroy'])->name('ptkp-status.destroy');

    // Data Master - Departemen
    Route::resource('departemen', DepartemenController::class);

    // Data Master - Jadwal Kerja
    Route::resource('jadwal-kerja', JadwalKerjaController::class)->only(['index', 'store', 'destroy']);
    
    // Data Master - Jabatan
    Route::get('jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('jabatan/create', [JabatanController::class, 'create'])->name('jabatan.create');
    Route::post('jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::get('jabatan/{id}', [JabatanController::class, 'show'])->name('jabatan.show');
    Route::get('jabatan/{id}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
    Route::put('jabatan/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('jabatan/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');

    // Payroll & Pegawai
    Route::resource('pegawai', PegawaiController::class)->only('index', 'show');
    Route::resource('penggajian', PenggajianController::class)->only('index', 'show');
    Route::post('penggajian/calculate', [PenggajianController::class, 'calculate'])->name('penggajian.calculate');

    Route::resource('users', UserController::class)->except(
        'create',
        'show',
        'edit'
    );

    Route::resource('officers', OfficerController::class)->except(
        'create',
        'show',
        'edit'
    );

    Route::controller(ProfileSettingController::class)->group(function () {
        Route::get('/profile/settings', 'index')->name('profile-settings.index');
        Route::put('/profile/settings', 'update')->name('profile-settings.update');
    });
});
