<?php

use App\Http\Controllers\StudentExpenseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeInvoiceController;
use App\Http\Controllers\SalarySlipController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubAdminController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingCourseController;
use App\Http\Controllers\BiometricController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientInvoiceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

// ZKTeco ADMS (Push) Webhook Routes
Route::get('/iclock/cdata', [\App\Http\Controllers\ZKTecoADMSController::class, 'handshake']);
Route::post('/iclock/cdata', [\App\Http\Controllers\ZKTecoADMSController::class, 'receiveData']);
Route::get('/iclock/getrequest', [\App\Http\Controllers\ZKTecoADMSController::class, 'getRequest']);
Route::post('/iclock/devicecmd', [\App\Http\Controllers\ZKTecoADMSController::class, 'deviceCmd']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Registration Routes
Route::get('/register/student', [\App\Http\Controllers\RegistrationController::class, 'showStudentRegistration'])->name('register.student');
Route::post('/register/student', [\App\Http\Controllers\RegistrationController::class, 'registerStudent']);
Route::get('/register/staff', [\App\Http\Controllers\RegistrationController::class, 'showStaffRegistration'])->name('register.staff');
Route::post('/register/staff', [\App\Http\Controllers\RegistrationController::class, 'registerStaff']);

Route::get('/reset-admin', function () {
    $user = \App\Models\User::find(1);
    if ($user) {
        $user->email = 'superadmin@gmail.com';
        $user->password = \Illuminate\Support\Facades\Hash::make('admin123');
        $user->save();
        return 'Admin credentials reset successfully! You can now log in with email: superadmin@gmail.com and password: admin123';
    }
    return 'Admin user not found!';
});

Route::get('/clear-all', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return 'All caches completely cleared! Try logging in now.';
});

Route::get('/run-migrations', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return 'Migrations ran successfully! You can now use the biometric features.';
});

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
    Route::resource('employees', EmployeeController::class);
    Route::post('courses/{id}/restore', [CourseController::class, 'restore'])->name('courses.restore');
    Route::get('biometric', [BiometricController::class, 'index'])->name('biometric.index');
    Route::post('biometric', [BiometricController::class, 'update'])->name('biometric.update');
    Route::post('biometric/test', [BiometricController::class, 'testConnection'])->name('biometric.test');
    Route::post('biometric/sync', [BiometricController::class, 'syncLogs'])->name('biometric.sync');
    
    Route::resource('courses', CourseController::class)->except(['show']);
    Route::post('students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::get('students/{student}/fee-report', [StudentController::class, 'feeReport'])->name('students.fee-report');
    Route::resource('students', StudentController::class);
    
    Route::resource('credentials', CredentialController::class)->except(['show']);
    Route::get('attendances/live', [AttendanceController::class, 'live'])->name('attendances.live');
    Route::post('attendances/generate-fines', [AttendanceController::class, 'generateFines'])->name('attendances.generate-fines');
    Route::resource('attendances', AttendanceController::class)->except(['show', 'edit', 'update']);
    Route::resource('employee-attendances', EmployeeAttendanceController::class)->except(['show', 'edit', 'update']);
    Route::post('expenses/{id}/restore', [ExpenseController::class, 'restore'])->name('expenses.restore');
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('fee_invoices/restore-all', [FeeInvoiceController::class, 'restoreAll'])->name('fee_invoices.restore_all');
    Route::post('fee_invoices/{id}/restore', [FeeInvoiceController::class, 'restore'])->name('fee_invoices.restore');
    Route::get('api/students/{id}/fee-info', [FeeInvoiceController::class, 'studentFeeInfo'])->name('api.students.fee-info');
    Route::get('fee_invoices/monthly', [FeeInvoiceController::class, 'monthlyFee'])->name('fee_invoices.monthly');
    Route::get('api/students/{id}/monthly-status', [FeeInvoiceController::class, 'studentMonthlyStatus'])->name('api.students.monthly-status');
    
    Route::get('fee_invoices/bulk-generate', [FeeInvoiceController::class, 'showBulkGenerate'])->name('fee_invoices.bulk-generate');
    Route::post('fee_invoices/bulk-generate', [FeeInvoiceController::class, 'bulkGenerate'])->name('fee_invoices.bulk-generate.post');
    Route::post('fee_invoices/{id}/receive-payment', [FeeInvoiceController::class, 'receivePayment'])->name('fee_invoices.receive-payment');
    Route::resource('fee_invoices', FeeInvoiceController::class)->only(['index', 'create', 'store', 'destroy', 'show']);

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::post('salary_slips/{id}/restore', [SalarySlipController::class, 'restore'])->name('salary_slips.restore');
    Route::resource('salary_slips', SalarySlipController::class)->only(['index', 'create', 'store', 'destroy', 'show']);

    // Client Management
    Route::post('clients/{id}/restore', [ClientController::class, 'restore'])->name('clients.restore');
    Route::resource('clients', ClientController::class);

    // Client Invoices
    Route::post('client_invoices/{id}/restore', [ClientInvoiceController::class, 'restore'])->name('client_invoices.restore');
    Route::resource('client_invoices', ClientInvoiceController::class)->only(['index', 'create', 'store', 'destroy', 'show']);

    // Prospect Management Routes
    Route::get('prospects/create', [ProspectController::class, 'create'])->name('prospects.create');
    Route::post('prospects', [ProspectController::class, 'store'])->name('prospects.store');
    Route::get('prospects/{id}/invoice', [ProspectController::class, 'invoice'])->name('prospects.invoice');
    Route::post('prospects/{id}/pay', [ProspectController::class, 'pay'])->name('prospects.pay');
    Route::post('prospects/{id}/fine', [ProspectController::class, 'addFine'])->name('prospects.fine');

    // Salary Calculation API
    Route::get('salary_slips/calculate_deduction', [SalarySlipController::class, 'calculateDeduction'])->name('salary_slips.calculate_deduction');

    // Student Portal
    Route::prefix('student')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StudentPortalController::class, 'dashboard'])->name('student.dashboard');
        Route::post('/select-course', [\App\Http\Controllers\StudentPortalController::class, 'selectCourse'])->name('student.select-course');
        Route::get('/attendance', [\App\Http\Controllers\FaceAttendanceController::class, 'captureView'])->name('student.attendance.capture');
    });

    // Staff Portal
    Route::prefix('staff')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StaffPortalController::class, 'dashboard'])->name('staff.dashboard');
        Route::get('/attendance', [\App\Http\Controllers\FaceAttendanceController::class, 'captureView'])->name('staff.attendance.capture');
        Route::get('/offer-letters', [\App\Http\Controllers\StaffPortalController::class, 'offerLetters'])->name('staff.offer-letters');
        Route::get('/leave', [\App\Http\Controllers\StaffPortalController::class, 'leaveApplications'])->name('staff.leave');
        Route::get('/income', [\App\Http\Controllers\StaffPortalController::class, 'incomeRecords'])->name('staff.income');
    });

    // Face Attendance API Route (Inside Auth for CSRF protection and session validation)
    Route::post('/api/attendance/face-check', [\App\Http\Controllers\FaceAttendanceController::class, 'store'])->name('attendance.face.store');

    // Chatbot query route
    Route::get('/api/chatbot/query', [\App\Http\Controllers\ChatbotController::class, 'query'])->name('api.chatbot.query');

    // Tasks routes
    Route::post('/tasks/{id}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.update_status');
    Route::resource('tasks', \App\Http\Controllers\TaskController::class)->except(['show', 'edit', 'update']);

    // Daily Updates routes
    Route::resource('daily-updates', \App\Http\Controllers\DailyUpdateController::class)->only(['index', 'store']);

    Route::get('trainings/export/csv', [TrainingController::class, 'exportCsv'])->name('trainings.export.csv');
    Route::get('trainings/analytics', [TrainingController::class, 'analytics'])->name('trainings.analytics');
    Route::post('trainings/{id}/restore', [TrainingController::class, 'restore'])->name('trainings.restore');
    Route::resource('trainings', TrainingController::class)->only(['index', 'create', 'store', 'destroy', 'show']);

    Route::post('training_courses/{id}/restore', [TrainingCourseController::class, 'restore'])->name('training_courses.restore');
    Route::resource('training_courses', TrainingCourseController::class)->except(['show']);
});

// WattVision Electrical Monitoring Routes
Route::prefix('wattvision')->group(function () {
    Route::get('/', [\App\Http\Controllers\WattVisionController::class, 'dashboard'])->name('wattvision.dashboard');
    Route::get('/login', [\App\Http\Controllers\WattVisionController::class, 'login'])->name('wattvision.login');
});
