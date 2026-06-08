<?php


use App\Http\Controllers\StudentExpenseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeInvoiceController;
use App\Http\Controllers\SalarySlipController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.custom'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/clear-cache', [DashboardController::class, 'clearCache'])->name('clear-cache');
    Route::get('student-expenses', [StudentExpenseController::class, 'index'])->name('student_expenses.index');
    
    // Export CSV and PDF routes (must be defined BEFORE resource controllers so they aren't caught by wildcard resource routes)
    Route::get('students/export/csv', [StudentController::class, 'exportCsv'])->name('students.export.csv');
    Route::get('students/export/pdf', [StudentController::class, 'exportPdf'])->name('students.export.pdf');
    Route::get('employees/export/csv', [EmployeeController::class, 'exportCsv'])->name('employees.export.csv');
    Route::get('employees/export/pdf', [EmployeeController::class, 'exportPdf'])->name('employees.export.pdf');
    Route::get('attendances/export/csv', [AttendanceController::class, 'exportCsv'])->name('attendances.export.csv');
    Route::get('attendances/export/pdf', [AttendanceController::class, 'exportPdf'])->name('attendances.export.pdf');
    Route::get('employee-attendances/export/csv', [EmployeeAttendanceController::class, 'exportCsv'])->name('employee-attendances.export.csv');
    Route::get('employee-attendances/export/pdf', [EmployeeAttendanceController::class, 'exportPdf'])->name('employee-attendances.export.pdf');

    Route::get('sub-admins/trash', [SubAdminController::class, 'trash'])->name('sub-admins.trash');
    Route::post('sub-admins/{id}/restore', [SubAdminController::class, 'restore'])->name('sub-admins.restore');
    Route::resource('sub-admins', SubAdminController::class)->except(['show']);
    
    Route::get('backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
    Route::post('backups/create', [\App\Http\Controllers\BackupController::class, 'create'])->name('backups.create');
    Route::get('backups/{fileName}/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
    Route::delete('backups/{fileName}', [\App\Http\Controllers\BackupController::class, 'destroy'])->name('backups.destroy');
    Route::post('backups/{fileName}/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
    Route::post('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::post('courses/{id}/restore', [CourseController::class, 'restore'])->name('courses.restore');
    Route::resource('courses', CourseController::class)->except(['show']);
    Route::post('students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::resource('students', StudentController::class)->except(['show']);
    Route::resource('attendances', AttendanceController::class)->except(['show', 'edit', 'update']);
    Route::resource('employee-attendances', EmployeeAttendanceController::class)->except(['show', 'edit', 'update']);
    Route::post('expenses/{id}/restore', [ExpenseController::class, 'restore'])->name('expenses.restore');
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('fee_invoices/{id}/restore', [FeeInvoiceController::class, 'restore'])->name('fee_invoices.restore');
    Route::resource('fee_invoices', FeeInvoiceController::class)->only(['index', 'create', 'store', 'destroy', 'show']);
    Route::post('salary_slips/{id}/restore', [SalarySlipController::class, 'restore'])->name('salary_slips.restore');
    Route::resource('salary_slips', SalarySlipController::class)->only(['index', 'create', 'store', 'destroy', 'show']);
});
