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
use App\Http\Controllers\ProspectController;
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

// Direct login routes
Route::get('/student', [AuthController::class, 'showStudentLogin'])->name('login.student');
Route::get('/students/login', function () {
    return redirect()->route('login.student');
});
Route::get('/staff', [AuthController::class, 'showStaffLogin'])->name('login.staff');
Route::get('/admin', [AuthController::class, 'showAdminLogin'])->name('login.admin');
Route::get('/superadmin', function () {
    return redirect()->route('login.admin');
});


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

Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return 'Migrations run successfully! Output: <pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Error running migrations: ' . $e->getMessage();
    }
});

Route::get('/clear-all', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return 'All caches completely cleared! Try logging in now.';
});

Route::get('/test-student', function () {
    $role = \App\Models\Role::firstOrCreate(['slug' => 'student'], ['role_name' => 'Student', 'status' => 1]);
    $user = \App\Models\User::updateOrCreate(
        ['email' => 'student@example.com'],
        [
            'name' => 'Test Student',
            'username' => 'teststudent',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role_id' => $role->id,
            'status' => 1
        ]
    );
    
    $student = \App\Models\Student::updateOrCreate(
        ['user_id' => $user->id],
        [
            'first_name' => 'Test',
            'last_name' => 'Student',
            'admission_no' => 'STD001',
            'roll_no' => 'R001',
            'status' => 1,
            'admission_date' => now()
        ]
    );
    
    return 'Test student ready! <br><br><b>Login URL:</b> <a href="/fees-manager/fees-manager-laravel/public/students">/students</a><br><b>Email:</b> student@example.com<br><b>Student ID:</b> STD001<br><b>Password:</b> password123';
});

Route::get('/run-migrations', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return 'Migrations ran successfully! You can now use the biometric features.';
});

Route::middleware(['auth.custom'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/clear-cache', [DashboardController::class, 'clearCache'])->name('clear-cache');
    Route::post('/profile/photo', [AuthController::class, 'updateProfilePic'])->name('profile.photo.update');
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
    Route::get('sub-admins/{user}/password', [SubAdminController::class, 'showPassword'])->name('sub-admins.password');
    Route::post('sub-admins/{user}/password-update', [SubAdminController::class, 'updatePassword'])->name('sub-admins.password.update');
    Route::resource('sub-admins', SubAdminController::class)->except(['show']);
    
    Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    
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
    
    Route::post('credentials/{credential}/toggle-portal', [CredentialController::class, 'togglePortal'])->name('credentials.toggle-portal');
    Route::get('credentials/{credential}/impersonate', [CredentialController::class, 'impersonate'])->name('credentials.impersonate');
    Route::resource('credentials', CredentialController::class)->except(['show']);
    Route::get('stop-impersonating', function () {
        if (session()->has('admin_impersonator_id')) {
            $adminId = session()->pull('admin_impersonator_id');
            $admin = \App\Models\User::find($adminId);
            if ($admin) {
                app(\App\Http\Controllers\AuthController::class)->completeLogin($admin, $admin->role?->slug ?? 'superadmin');
                return redirect()->route('credentials.index')->with('success', 'Switched back to Admin successfully.');
            }
        }
        return redirect()->route('dashboard');
    })->name('stop-impersonating');
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

    // Meeting Management
    Route::resource('departments', \App\Http\Controllers\DepartmentController::class)->except(['create', 'show', 'edit']);
    Route::resource('meetings', \App\Http\Controllers\MeetingController::class);
    Route::post('meetings/participants/{participant}/status', [\App\Http\Controllers\MeetingController::class, 'updateStatus'])->name('meetings.updateStatus');
    Route::post('meetings/{meeting}/chat', [\App\Http\Controllers\MeetingController::class, 'storeMessage'])->name('meetings.chat.store');
    Route::get('meetings/{meeting}/chat', [\App\Http\Controllers\MeetingController::class, 'getMessages'])->name('meetings.chat.index');
    Route::post('meetings/{meeting}/files', [\App\Http\Controllers\MeetingController::class, 'storeFile'])->name('meetings.files.store');
    Route::get('meetings/join/{id}', [\App\Http\Controllers\MeetingController::class, 'joinMeeting'])->name('meetings.join');
    Route::post('meetings/join/{id}/heartbeat', [\App\Http\Controllers\MeetingController::class, 'heartbeat'])->name('meetings.join.heartbeat');
    Route::post('meetings/join/{id}/leave', [\App\Http\Controllers\MeetingController::class, 'leaveMeeting'])->name('meetings.join.leave');


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
        Route::post('/pay-now', [\App\Http\Controllers\StudentPortalController::class, 'payNow'])->name('student.pay-now');
        Route::post('/confirm-payment', [\App\Http\Controllers\StudentPortalController::class, 'submitPaymentConfirmation'])->name('student.confirm-payment');
        Route::get('/attendance', [\App\Http\Controllers\FaceAttendanceController::class, 'captureView'])->name('student.attendance.capture');
    });

    // Staff Portal
    Route::prefix('staff')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StaffPortalController::class, 'dashboard'])->name('staff.dashboard');
        Route::get('/attendance', [\App\Http\Controllers\FaceAttendanceController::class, 'captureView'])->name('staff.attendance.capture');
        Route::get('/offer-letters', [\App\Http\Controllers\StaffPortalController::class, 'offerLetters'])->name('staff.offer-letters');
        Route::get('/leave', [\App\Http\Controllers\StaffPortalController::class, 'leaveApplications'])->name('staff.leave');
        Route::get('/income', [\App\Http\Controllers\StaffPortalController::class, 'incomeRecords'])->name('staff.income');
        Route::post('/profile/update', [\App\Http\Controllers\StaffPortalController::class, 'updateProfile'])->name('staff.profile.update');
    });

    // Face Attendance API Route (Inside Auth for CSRF protection and session validation)
    Route::post('/api/attendance/face-check', [\App\Http\Controllers\FaceAttendanceController::class, 'store'])->name('attendance.face.store');

    // Chatbot query route
    Route::get('/api/chatbot/query', [\App\Http\Controllers\ChatbotController::class, 'query'])->name('api.chatbot.query');

    // Tasks routes
    Route::post('/tasks/{id}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.update_status');
    Route::resource('tasks', \App\Http\Controllers\TaskController::class)->except(['show', 'edit', 'update']);

    // Internal Messaging Suite routes
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/chat/{user?}', [\App\Http\Controllers\MessageController::class, 'chat'])->name('messages.chat');
    Route::post('/messages/chat/send', [\App\Http\Controllers\MessageController::class, 'storeChat'])->name('messages.chat.store');
    Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/{id}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.read');
    Route::delete('/messages/{id}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
    Route::put('/messages/{id}', [\App\Http\Controllers\MessageController::class, 'updateMessage'])->name('messages.update_msg');
    // AJAX polling for live chat
    Route::get('/api/messages/poll/{userId}', [\App\Http\Controllers\MessageController::class, 'pollChat'])->name('messages.poll');
    Route::get('/api/messages/unread-count', [\App\Http\Controllers\MessageController::class, 'unreadCount'])->name('messages.unread-count');
    Route::get('/api/messages/inbox-poll', [\App\Http\Controllers\MessageController::class, 'pollInbox'])->name('messages.inbox-poll');
    Route::get('/messages/full', [\App\Http\Controllers\MessageController::class, 'fullPage'])->name('messages.full');

    // Connections Routes
    Route::get('/connections', [\App\Http\Controllers\EmployeeConnectionController::class, 'index'])->name('connections.index');
    Route::post('/connections', [\App\Http\Controllers\EmployeeConnectionController::class, 'store'])->name('connections.store');
    Route::put('/connections/{connection}', [\App\Http\Controllers\EmployeeConnectionController::class, 'update'])->name('connections.update');

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
