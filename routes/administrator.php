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
use Illuminate\Support\Facades\Route;

Route::middleware('auth:administrator')->name('administrators.')->prefix('administrator')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Data Master
    Route::resource('departemen', DepartemenController::class);
    Route::resource('jabatan', JabatanController::class);
    Route::resource('tunjangan', TunjanganController::class);
    Route::resource('potongan', PotonganController::class);
    Route::resource('ptkp-status', PtkpStatusController::class)->parameters(['ptkp-status' => 'ptkpStatus']);

    // Payroll & Pegawai
    Route::resource('pegawai', PegawaiController::class)->only('index', 'show');
    Route::resource('penggajian', PenggajianController::class)->only('index', 'show');

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
