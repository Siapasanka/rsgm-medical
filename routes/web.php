<?php

use App\Http\Controllers\AccountUserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Akses pasien untuk superadmin/admin/dokter
    Route::middleware('role:superadmin,admin,dokter')->group(function () {
        Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('patients/{patient}', [PatientController::class, 'show'])->whereNumber('patient')->name('patients.show');
        Route::get('patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('patients/{patient}/edit', [PatientController::class, 'edit'])->whereNumber('patient')->name('patients.edit');
        Route::put('patients/{patient}', [PatientController::class, 'update'])->whereNumber('patient')->name('patients.update');
        Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->whereNumber('patient')->name('patients.destroy');
    });

    Route::middleware('role:dokter')->group(function () {
        Route::resource('registrations', RegistrationController::class);
        Route::get('registrations/export/daily-pdf', [RegistrationController::class, 'exportDailyPdf'])
            ->name('registrations.export.daily-pdf');

        Route::resource('medical-records', MedicalRecordController::class);
        Route::delete('medical-records/{medical_record}/photos/{photo}', [MedicalRecordController::class, 'destroyPhoto'])
            ->name('medical-records.photos.destroy');
        Route::get('medical-records/export/daily-pdf', [MedicalRecordController::class, 'exportDailyPdf'])
            ->name('medical-records.export.daily-pdf');
        Route::get('medical-records/{medical_record}/export/pdf', [MedicalRecordController::class, 'exportSinglePdf'])
            ->name('medical-records.export.single-pdf');
    });

    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/export/csv', [AuditLogController::class, 'exportCsv'])->name('audit-logs.export.csv');
        Route::get('audit-logs/export/pdf', [AuditLogController::class, 'exportPdf'])->name('audit-logs.export.pdf');

        Route::get('account-users', [AccountUserController::class, 'index'])->name('account-users.index');
        Route::post('account-users', [AccountUserController::class, 'store'])->name('account-users.store');
        Route::get('account-users/{user}/edit', [AccountUserController::class, 'edit'])->name('account-users.edit');
        Route::patch('account-users/{user}', [AccountUserController::class, 'update'])->name('account-users.update');
        Route::patch('account-users/{user}/reset-password', [AccountUserController::class, 'resetPassword'])->name('account-users.reset-password');
        Route::delete('account-users/{user}', [AccountUserController::class, 'destroy'])->name('account-users.destroy');
    });
});

require __DIR__.'/auth.php';
