<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Course;
use App\Models\FeeInvoice;
use App\Models\Attendance;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiDashboardController extends Controller
{
    /**
     * Get system-wide operational statistics.
     */
    public function stats()
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 1)->count();
        $totalEmployees = Employee::count();
        $totalCourses = Course::where('status', true)->count();

        // Financials
        $currentMonth = now()->format('F');
        $currentYear = (int)now()->format('Y');

        $feesCollectedThisMonth = FeeInvoice::where('billing_month', $currentMonth)
            ->where('billing_year', $currentYear)
            ->sum('paid_amount');

        $totalFeesCollected = FeeInvoice::sum('paid_amount');
        $totalFeesPending = FeeInvoice::where('status', '!=', 'Paid')->sum('due_amount');

        // Today's attendance
        $today = now()->toDateString();
        $todayAttendanceCount = Attendance::whereDate('attendance_date', $today)->count();
        $todayPresentCount = Attendance::whereDate('attendance_date', $today)
            ->whereIn('status', ['Present', 'Late'])
            ->count();

        $attendanceRate = $todayAttendanceCount > 0
            ? round(($todayPresentCount / $todayAttendanceCount) * 100, 1)
            : 0;

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'counts' => [
                'total_students'   => $totalStudents,
                'active_students'  => $activeStudents,
                'total_employees'  => $totalEmployees,
                'total_courses'    => $totalCourses,
            ],
            'financials' => [
                'current_month'              => $currentMonth,
                'fees_collected_this_month'  => (float)$feesCollectedThisMonth,
                'total_fees_collected'       => (float)$totalFeesCollected,
                'total_fees_pending'         => (float)$totalFeesPending,
            ],
            'today_attendance' => [
                'date'              => $today,
                'total_logged'      => $todayAttendanceCount,
                'present_or_late'   => $todayPresentCount,
                'attendance_rate'   => $attendanceRate,
            ]
        ]);
    }

    /**
     * Get recent activities and audit trail.
     */
    public function recentActivity()
    {
        $recentEnrollments = Student::with('course:id,name')
            ->latest()
            ->limit(5)
            ->get(['id', 'first_name', 'last_name', 'admission_no', 'course_id', 'created_at']);

        $recentPayments = FeeInvoice::with('student:id,first_name,last_name,admission_no')
            ->where('paid_amount', '>', 0)
            ->latest('updated_at')
            ->limit(5)
            ->get(['id', 'student_id', 'invoice_no', 'paid_amount', 'payment_method', 'status', 'updated_at']);

        $recentAuditLogs = [];
        try {
            $recentAuditLogs = AdminActivityLog::latest()->limit(10)->get();
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'recent_enrollments' => $recentEnrollments,
            'recent_payments'    => $recentPayments,
            'recent_audit_logs'  => $recentAuditLogs,
        ]);
    }
}

