<?php

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\PayrollController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\ProfileSettingController;
use Illuminate\Support\Facades\Route;

/**
 * Student Routes - Production Ready
 * 
 * Routes untuk Pegawai (Employee/Student) dengan self-service access.
 * 
 * Middleware:
 * - auth:student: Authenticate via student guard
 * 
 * Features:
 * - View personal attendance
 * - Check-in / Check-out with photo
 * - Request attendance correction
 * - View personal salary slip & breakdown
 * - Edit limited profile fields
 */
Route::middleware(['auth:student'])->name('students.')->prefix('student')->group(function () {
    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Attendance - Personal only
    Route::controller(AttendanceController::class)->group(function () {
        Route::get('/attendance', 'index')->name('attendance.index');
        Route::get('/attendance/today', 'todaySummary')->name('attendance.today');
        Route::post('/attendance/checkin', 'store')->name('attendance.store');
        Route::post('/attendance/{id}/request-correction', 'requestCorrection')->name('attendance.request-correction');
    });

    // Payroll / Salary Slip - Personal only
    Route::resource('payroll', PayrollController::class)->only('index');

    // Profile Settings
    Route::controller(ProfileSettingController::class)->group(function () {
        Route::get('/profile/settings', 'index')->name('profile-settings.index');
        Route::put('/profile/settings', 'update')->name('profile-settings.update');
    });
});
