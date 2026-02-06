<?php

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\PayrollController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\ProfileSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:student')->name('students.')->prefix('student')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('payroll', PayrollController::class)->only('index');
    Route::resource('attendance', AttendanceController::class)->only('index');

    Route::controller(ProfileSettingController::class)->group(function () {
        Route::get('/profile/settings', 'index')->name('profile-settings.index');
        Route::put('/profile/settings', 'update')->name('profile-settings.update');
    });
});
