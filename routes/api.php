<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiStudentController;
use App\Http\Controllers\Api\ApiCourseController;
use App\Http\Controllers\Api\ApiAttendanceController;
use App\Http\Controllers\Api\ApiFeeController;
use App\Http\Controllers\Api\ApiDashboardController;
use App\Http\Controllers\Api\ZktecoController;

/*
|--------------------------------------------------------------------------
| Secure Backend API Routes (v1)
|--------------------------------------------------------------------------
|
| Protected by Rate Limiting, Security Headers, Input Sanitization,
| API Secret verification, and Laravel Sanctum Token Authentication.
|
*/

// Health Check Endpoint (Public)
Route::get('/health', function () {
    return response()->json([
        'status'    => 'ok',
        'service'   => 'Netcoder FMS ERP Backend API',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// =========================================================================
// API Version 1 (v1)
// =========================================================================
Route::prefix('v1')->group(function () {

    // ---------------------------------------------------------------------
    // Public Authentication Endpoint (Rate Limited: 10 per minute)
    // ---------------------------------------------------------------------
    Route::post('/auth/login', [ApiAuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('api.v1.auth.login');

    // ---------------------------------------------------------------------
    // Protected Endpoints: Secured via API Secret or Sanctum Bearer Token
    // ---------------------------------------------------------------------
    Route::middleware(['api.secret'])->group(function () {

        // Current User Profile & Logout
        Route::get('/auth/me', [ApiAuthController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [ApiAuthController::class, 'logout'])->name('api.v1.auth.logout');

        // Dashboard Metrics & Audit Logs
        Route::get('/dashboard/stats', [ApiDashboardController::class, 'stats'])->name('api.v1.dashboard.stats');
        Route::get('/dashboard/recent-activity', [ApiDashboardController::class, 'recentActivity'])->name('api.v1.dashboard.recent-activity');

        // Student Management & Enrollment Sync
        Route::get('/students', [ApiStudentController::class, 'index'])->name('api.v1.students.index');
        Route::post('/students', [ApiStudentController::class, 'store'])->name('api.v1.students.store');
        Route::get('/students/{id}', [ApiStudentController::class, 'show'])->name('api.v1.students.show');
        Route::put('/students/{id}', [ApiStudentController::class, 'update'])->name('api.v1.students.update');
        Route::delete('/students/{id}', [ApiStudentController::class, 'destroy'])->name('api.v1.students.destroy');
        Route::post('/students/sync-enrollment', [ApiStudentController::class, 'syncEnrollment'])->name('api.v1.students.sync-enrollment');

        // Course Management
        Route::get('/courses', [ApiCourseController::class, 'index'])->name('api.v1.courses.index');
        Route::post('/courses', [ApiCourseController::class, 'store'])->name('api.v1.courses.store');
        Route::get('/courses/{id}', [ApiCourseController::class, 'show'])->name('api.v1.courses.show');
        Route::put('/courses/{id}', [ApiCourseController::class, 'update'])->name('api.v1.courses.update');
        Route::delete('/courses/{id}', [ApiCourseController::class, 'destroy'])->name('api.v1.courses.destroy');

        // Attendance Management
        Route::get('/attendance', [ApiAttendanceController::class, 'index'])->name('api.v1.attendance.index');
        Route::post('/attendance/record', [ApiAttendanceController::class, 'record'])->name('api.v1.attendance.record');
        Route::get('/attendance/student/{studentId}', [ApiAttendanceController::class, 'studentSummary'])->name('api.v1.attendance.student-summary');

        // Fee Invoicing & Payment Processing
        Route::get('/fees/invoices', [ApiFeeController::class, 'index'])->name('api.v1.fees.invoices.index');
        Route::post('/fees/invoices', [ApiFeeController::class, 'store'])->name('api.v1.fees.invoices.store');
        Route::get('/fees/invoices/{id}', [ApiFeeController::class, 'show'])->name('api.v1.fees.invoices.show');
        Route::post('/fees/invoices/{id}/pay', [ApiFeeController::class, 'receivePayment'])->name('api.v1.fees.invoices.pay');
        Route::get('/fees/student/{studentId}', [ApiFeeController::class, 'studentInvoices'])->name('api.v1.fees.student-invoices');
    });
});

// Compatibility endpoints for frontend LMS (laravel-sahil) content sync
Route::get('/courses', [ApiCourseController::class, 'index'])->middleware('throttle:60,1');
Route::get('/online-courses', [ApiCourseController::class, 'onlineCourses'])->middleware('throttle:60,1');

// LMS Course Bundles API Endpoints (Sync & Management)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/lms/course-bundles', [\App\Http\Controllers\CourseBundleController::class, 'index'])->name('api.lms.course_bundles.index');
    Route::post('/lms/course-bundles', [\App\Http\Controllers\CourseBundleController::class, 'store'])->name('api.lms.course_bundles.store');
    Route::get('/lms/course-bundles/{id}', [\App\Http\Controllers\CourseBundleController::class, 'show'])->name('api.lms.course_bundles.show');
    Route::put('/lms/course-bundles/{id}', [\App\Http\Controllers\CourseBundleController::class, 'update'])->name('api.lms.course_bundles.update');
    Route::delete('/lms/course-bundles/{id}', [\App\Http\Controllers\CourseBundleController::class, 'destroy'])->name('api.lms.course_bundles.destroy');
    Route::post('/lms/course-bundles/{id}/status', [\App\Http\Controllers\CourseBundleController::class, 'toggleStatus'])->name('api.lms.course_bundles.status');
});

// Legacy Sanctum user endpoint
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ZKTeco Push Biometric Endpoints (Rate Limited & Protected)
Route::middleware('throttle:120,1')->group(function () {
    Route::post('/zkteco/attendance', [ZktecoController::class, 'receivePush']);
    Route::get('/zkteco/attendance', [ZktecoController::class, 'handleDeviceCheck']);
});
