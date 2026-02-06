<?php

use App\Http\Controllers\Officer\DashboardController;
use App\Http\Controllers\Officer\DepartemenController;
use App\Http\Controllers\Officer\JabatanController;
use App\Http\Controllers\Officer\TunjanganController;
use App\Http\Controllers\Officer\PotonganController;
use App\Http\Controllers\Officer\PegawaiController;
use App\Http\Controllers\Officer\PenggajianController;
use App\Http\Controllers\Officer\ProfileSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:officer')->name('officers.')->prefix('officer')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Data Master
    Route::resource('departemen', DepartemenController::class)->only('index');
    Route::resource('jabatan', JabatanController::class)->only('index');
    Route::resource('tunjangan', TunjanganController::class)->only('index');
    Route::resource('potongan', PotonganController::class)->only('index');

    // Payroll & Pegawai
    Route::resource('pegawai', PegawaiController::class)->only('index');
    Route::resource('penggajian', PenggajianController::class)->only('index');

    Route::controller(ProfileSettingController::class)->group(function () {
        Route::get('/profile/settings', 'index')->name('profile-settings.index');
        Route::put('/profile/settings', 'update')->name('profile-settings.update');
    });
});
