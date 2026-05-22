<?php

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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.custom'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('courses', CourseController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('students', StudentController::class)->except(['show']);
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('attendances', AttendanceController::class)->except(['show', 'edit', 'update']);
    Route::resource('employee-attendances', EmployeeAttendanceController::class)->except(['show', 'edit', 'update']);
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('fee_invoices', FeeInvoiceController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('salary_slips', SalarySlipController::class)->only(['index', 'create', 'store', 'destroy']);
});
